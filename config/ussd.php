<?php
return [
    'restrict_to_whitelist' => env('RESTRICT_TO_WHITELIST', '#############'),
    'log_ussd_request' => env('LOG_USSD_REQUEST', '#############'),
    'whitelist_msisdns' => env('WHITELIST_MSISDNS', '#############'),
    'end_session_sleep_seconds' => env('END_SESSION_SLEEP_SECONDS', '#############'),
    'ussd_code' => env('USSD_CODE', '#############'),
];