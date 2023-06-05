<?php

namespace Stilinski\Ussd\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberUtil;

class ValidateMsisdn implements ValidationRule
{
    public $model;
    public $column;
    public $source_param;


    public function __construct($model = 'User', $column = 'msisdn', $source_param = 'msisdn')
    {
        $this->model = 'App\\Models\\'.$model;
        $this->column = $column;
        $this->source_param = $source_param;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $validation = validateMsisdn($value);

        if (!$validation['isValid']) {
            $fail($validation['msisdn']. ' is invalid');
        }

        request()->merge([$this->source_param => $validation['msisdn']]);
    }
}