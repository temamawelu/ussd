<?php

namespace Stilinski\Ussd\Repositories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stilinski\Ussd\Jobs\SaveMessage;
use Stilinski\Ussd\Models\Activity;
use Stilinski\Ussd\Models\ActivityLog;
use Stilinski\Ussd\Models\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ActivityLibrary
{
    public $msisdn;
    public $sessionId;
    public $invalidInput = false;
    public $ussdString;
    public $originalUssdString;
    public $currentActivity;
    public $currentActivityData = [];
    public $message = '';
    public $next = null;
    public $userInput = [];
    public $defaultMessage;
    public $menuItems = [];
    public $user;
    public $end = false;
    public $attempt = 0;
    protected $flowClass;
    public $activityId = null;

    public function __construct($msisdn, $sessionId, $ussdString, $originalUssdString, $flowClass)
    {
        $this->msisdn = $msisdn;
        $this->ussdString = $ussdString;
        $this->originalUssdString = $originalUssdString;
        $this->sessionId = $sessionId;
        $this->flowClass = $flowClass;
        $this->defaultMessage = __('ussd.default_message');

        $this->saveUssdSession();
        $this->loadUser();
        $this->setLang();
        $this->loadPendingUssdActivity();
        $this->checkAttempt();
        $this->execute();
        $this->saveUssdActivity();
    }

    private function loadUser()
    {
        $this->user = User::where('msisdn', $this->msisdn)->first();

        if ($this->user){
            $this->userInput['newUser'] = false;
        }else {
            $userData = [
                'msisdn'    => $this->msisdn
            ];
            // TODO save new user to DB
            $this->userInput['newUser'] = true;
        }
    }

    public function saveUssdSession()
    {
        // Use try catch coz of duplicate entry
        try {
            Session::create([
                'session_id' => $this->sessionId
            ]);
        } catch (\Exception $exception) {
            //
        }
    }

    public function setLang()
    {
        App::setLocale('en');
        if (array_key_exists('newUser', $this->userInput)){
            if (!$this->userInput['newUser']) {
                App::setLocale($this->user->locale);
            }
        }
    }

    private function loadPendingUssdActivity()
    {
        $pendingActivity = Activity::with(['activityLogs'])
            ->where('msisdn', $this->msisdn)
            ->where('session_id', $this->sessionId)
            ->where('status', false)
            ->first();

        if ($pendingActivity) {
            $this->attempt = 1;
            $this->currentActivity = $pendingActivity->activity;
            if (count($pendingActivity->activityData)) {
                foreach ($pendingActivity->activityData as $item) {
                    $this->currentActivityData[$item->data]  = $item->value;
                }
            }

            $this->next = Arr::get($this->currentActivityData, 'next_default',null);

            $userInputs = Arr::get($this->currentActivityData, 'userinputs');
            if ($userInputs) {
                $this->userInput = (array)json_decode($userInputs, true);
            }
        } else {
            $this->currentActivity = 'activityHome';
        }
    }

    public function checkAttempt()
    {
        if ($this->attempt) {
            $activities = Arr::get($this->currentActivityData, 'nextActivities');
            if (!isset($activities)) {
                return;
            }
            $activities = (array)json_decode($activities);

            if (count($activities) > 1) {
                if (!$this->checkValidMenuInput($activities)) {
                    $this->invalidInput = true;
                } else {
                    $this->currentActivity = $this->next ?: $activities[$this->ussdString];
                }
            } else {
                $this->currentActivity = current($activities);
            }
        }
    }

    public function execute()
    {
        if (is_null($this->currentActivity)) {
            $this->currentActivity = 'activityHome';
        }

        try {
            $this->next = null;
            call_user_func_array(["App\\Repositories\\" . $this->flowClass, $this->currentActivity], ['class' => $this, 'data' => $this->currentActivityData]);
        } catch (\Exception $exception) {
            //log the exceptions if there are any
            $exceptionString = "STILINSKI-USSD EXCEPTION".
                "\nUSSD String: ". $this->ussdString
                ."\n Current Activity: ". $this->currentActivity
                ."\n Original String: ". $this->originalUssdString
                ."\n UserInput: ". json_encode($this->userInput)
                ."\nMenu Items: ". json_encode($this->menuItems)
                ."\nMSISDN: ". $this->msisdn
                ."\nSessionID: ". $this->sessionId
                ."\nError Message: ".$exception->getMessage()
                ."\nStackTrace: ".$exception->getTraceAsString();

            Log::info($exceptionString);
            $this->next = null;
            call_user_func_array(["App\\Repositories\\" . 'Handler', 'activityHome'], ['class' => $this, 'data' => []]);
        }
    }

    private function checkValidMenuInput($activities)
    {
        if ($this->next) {
            return true;
        }
        return array_key_exists($this->ussdString, $activities);
    }

    private function saveUssdActivity()
    {
        $this->archivePrevious();

        $activity = Activity::create([
            'activity' => $this->currentActivity,
            'msisdn' => $this->msisdn,
            'session_id' => $this->sessionId
        ]);

        $this->activityId = $activity->id;

        SaveMessage::dispatch([
            'msisdn'     => $this->msisdn,
            'session_id' => $this->sessionId,
            'activity_id' => $this->activityId,
            'direction'   => 'in',
            'message'    => $this->ussdString
        ])->onQueue('save-ussd-message');

        $data = ActivityLog::create([
            'data'   => 'nextActivities',
            'value' => json_encode($this->menuToData()),
        ]);

        $userInputs = ActivityLog::create([
            'data'   => 'userinputs',
            'value' => json_encode($this->userInput),
        ]);

        $nextDefault = ActivityLog::create([
            'data'   => 'next_default',
            'value' => $this->next,
        ]);

        $activity->activityLog()->saveMany([$data, $userInputs, $nextDefault]);
    }

    private function archivePrevious()
    {
        return Activity::where('msisdn', $this->msisdn)->update([
            'status' => true
        ]);
    }

    private function menuToData()
    {
        $data = [];
        if (count($this->menuItems)) {
            foreach ($this->menuItems as $k => $menuItem) {
                $data[$k] = $menuItem['activity'];
            }
        }

        if ($this->next) {
            $data = collect($data)->put(10, $this->next)->toArray();
        }
        return $data;
    }

    public function finalResponse()
    {
        $finalResponse = '';
        $finalResponse .= strlen($this->message) ? $this->message : $this->defaultMessage;
        $finalResponse .= "\n";
        $finalResponse .= $this->buildMenu();

        SaveMessage::dispatch([
            'msisdn' => $this->msisdn,
            'session_id' => $this->sessionId,
            'activity_id' => $this->activityId,
            'direction' => 'out',
            'message' => $finalResponse
        ])->onQueue('save-ussd-messages');

        return [
            'response' => $finalResponse,
            'endSession' => $this->end,
        ];
    }

    private function buildMenu()
    {
        $str = '';
        foreach ($this->menuItems as $k => $menuItem) {
            $str .= $k.'. '.$menuItem['text']."\n";
        }

        return $str;
    }
}