<?php
use App\Models\User;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

if('saveUser'){
    function saveUser($userData){
        User::create([
            'name' => 'Cyril Aguvasu',
            'msisdn' => $userData['msisdn'],
            'locale' => 'en'
        ]);
    }
}

// validate msisdn
function validateMsisdn($msisdn, $region = "KE"): array
{
    $phoneUtil = PhoneNumberUtil::getInstance();
    try {
        $kenyaNumberProto = $phoneUtil->parse($msisdn, $region);
        $isValid = $phoneUtil->isValidNumber($kenyaNumberProto);

        if ($isValid) {
            $phone = $phoneUtil->format($kenyaNumberProto, PhoneNumberFormat::E164);
            return [
                'isValid' => $isValid,
                'msisdn' => substr($phone, 1)
            ];
        }

        return [
            'isValid' => $isValid,
            'msisdn' => $msisdn
        ];

    } catch (NumberParseException $e) {
        return [
            'isValid' => false,
            'msisdn' => $msisdn
        ];
    }
}