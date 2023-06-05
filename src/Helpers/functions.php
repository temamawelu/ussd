<?php
use App\Models\User;

if('saveUser'){
    function saveUser($userData){
        User::create([
            'name' => 'Cyril Aguvasu',
            'msisdn' => $userData['msisdn'],
            'locale' => 'en'
        ]);
    }
}