<?php

return [
    /*
     * Enable / disable Google2FA.
     */
    'enabled' => env('OTP_ENABLED', true),

    /*
     * Lifetime in minutes.
     */
    'lifetime' => env('OTP_LIFETIME', 0),

    /*
     * Renew lifetime at every new request.
     */
    'keep_alive' => env('OTP_KEEP_ALIVE', true),

    /*
     * Auth container binding
     */
    'auth' => 'auth',

    /*
     * 2FA verified session var
     */
    'session_var' => 'google2fa',

    /*
     * One Time Password request input name
     */
    'otp_input' => 'one_time_password',

    /*
     * One Time Password Window
     */
    'window' => 1,

    /*
     * Forbid user to reuse One Time Passwords.
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column name to store the secret
     */
    'otp_secret_column' => 'two_factor_secret',

    /*
     * One Time Password View
     */
    'view' => 'auth.two-factor-verify',

    /*
     * One Time Password error message
     */
    'error_messages' => [
        'wrong_otp' => "The 'One Time Password' typed was wrong.",
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => env('OTP_THROW_EXCEPTION', true),
];