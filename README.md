<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Stilinski Ussd Package

Stilinski Ussd is a modern multi-language (english and swahili) laravel dynamic ussd application framework with expressive, elegant syntax. Very easy to use. It also has a ussd simulator for  easy debugging & maintenance.

## Installation Procedure

### Add the following to your root composer.json

    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:StilinskiCyril/ussd.git"
        }
    ],

### Require the package

    composer require giggsey/libphonenumber-for-php
    composer require doctrine/dbal

### Run the migrations

    php artisan migrate

### Listen to the following queues
    save-ussd-messages - save ussd session data into the database

### Also require these additional packages

## Security Vulnerabilities

If you discover a security vulnerability within the library, please send an e-mail to [Cyril Aguvasu](mailto:aguvasucyril@gmail.com). All security vulnerabilities will be promptly addressed.

## License

This framework is owned and maintained by [Cyril Aguvasu](https://github.com/StilinskiCyril)