<?php
use App\Models\User;

if('saveUser'){
    function saveUser($userData){
        User::create([
            'msisdn' => $userData['msisdn'],
            'locale' => 'en'
        ]);
    }
}