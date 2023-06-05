<?php

namespace App\Repositories;

use Stilinski\Ussd\Helpers\CollectionHelper;
use App\Models\User;
use Stilinski\Ussd\Repositories\ActivityLibrary;

class Handler
{
    // distinguish if it's a new user (first dial) or a registered user
    static function activityHome(ActivityLibrary $activityLibrary, $params)
    {
        if (array_key_exists('newUser', $activityLibrary->userInput) && $activityLibrary->userInput['newUser']) {
            return self::activityHomeNewUser($activityLibrary, $params);
        }
        return self::activityHomeExistingUser($activityLibrary, $params);
    }

    // existing user
    static function activityHomeExistingUser(ActivityLibrary $activityLibrary, $params)
    {
        $activityLibrary->message = __('ussd.welcome_new_user', ['name' => $activityLibrary->user->first_name]);
        $menu = [
            1 => [
                'text' => __('ussd.item_home'),
                'activity' => 'activityHome',
            ],
            2 => [
                'text' => __('ussd.item_choose_language'),
                'activity' => 'activityChooseLanguage',
            ],
            3 => [
                'text' => __('ussd.item_quit'),
                'activity' => 'activityQuit',
            ]
        ];
        $activityLibrary->menuItems = $menu;
        return self::activityReturnValidMessage($activityLibrary);
    }

    // new user
    static function activityHomeNewUser(ActivityLibrary $activityLibrary, $params)
    {
        $activityLibrary->message = __('ussd.welcome_registered_user', ['name' => '']);
        $menu = [
            1 => [
                'text' => __('ussd.item_choose_language'),
                'activity' => 'activityChooseLanguage',
            ],
            2 => [
                'text' => __('ussd.item_quit'),
                'activity' => 'activityQuit',
            ]
        ];

        $activityLibrary->menuItems = $menu;
        return self::activityReturnValidMessage($activityLibrary);
    }

    // exit the application
    static function activityQuit(ActivityLibrary $activityLibrary, $params)
    {
        $activityLibrary->message = __('ussd.item_message_quit');
        $activityLibrary->end = true;
        return self::activityReturnValidMessage($activityLibrary);
    }

    // choose language settings
    public static function activityChooseLanguage(ActivityLibrary $activityLibrary, $params)
    {
        $activityLibrary->message = __('ussd.item_choose_language');
        $languageLookUp = ['1' => 'en', '2' => 'sw'];
        $activityLibrary->userInput['localeLookup'] = $languageLookUp;

        $activityLibrary->menuItems = [
            '1' => [
                'text' => __('ussd.item_language_english'),
                'activity' => 'activityChangeLanguage'
            ],
            '2' => [
                'text' => __('ussd.item_language_swahili'),
                'activity' => 'activityChangeLanguage'
            ],
            '0' => [
                'text' => __('ussd.item_navigation_home'),
                'activity' => 'activityHome'
            ]
        ];

        return self::activityReturnValidMessage($activityLibrary);
    }

    // change language
    public static function activityChangeLanguage(ActivityLibrary $activityLibrary, $params)
    {
        if (!collect($activityLibrary->userInput['localeLookup'])->has($activityLibrary->ussdString)) {
            return self::activityHome($activityLibrary, $params);
        }

        $locale = $activityLibrary->userInput['localeLookup'][$activityLibrary->ussdString];
        $activityLibrary->user->locale = $locale;
        $activityLibrary->user->save();

        $activityLibrary->setLang();

        $activityLibrary->message = __('ussd.item_locale_saved');

        $activityLibrary->menuItems = [
            '0' => [
                'text' => __('ussd.item_navigation_home'),
                'activity' => 'activityHome'
            ]
        ];
        return self::activityReturnValidMessage($activityLibrary);
    }

   // returned message
    public static function activityReturnValidMessage(ActivityLibrary $activityLibrary)
    {
        if ($activityLibrary->invalidInput) {
            $activityLibrary->message = __('ussd.enter_valid_input') . " $activityLibrary->ussdString\n$activityLibrary->message";
        }

        return $activityLibrary;
    }

    /**
     * ALL YOUR OTHER MENUS WILL BE WRITTEN HERE... FEEL FREE TO PLAY AROUND
     */
}