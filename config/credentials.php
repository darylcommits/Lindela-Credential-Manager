<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Credential Management Settings
    |--------------------------------------------------------------------------
    */
    
    'password_strength' => [
        'minimum_required' => env('PASSWORD_STRENGTH_REQUIRED', 3),
        'enforce_strength' => env('ENFORCE_PASSWORD_STRENGTH', true),
    ],
    
    'expiration' => [
        'default_days' => env('CREDENTIAL_DEFAULT_EXPIRY_DAYS', 90),
        'warning_days' => env('CREDENTIAL_EXPIRY_WARNING_DAYS', 30),
        'alert_time' => env('CREDENTIAL_ALERT_TIME', '08:00'),
    ],
    
    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'encrypt' => env('BACKUP_ENCRYPTION', true),
        'retention_days' => env('BACKUP_RETENTION_DAYS', 90),
        'disk' => env('BACKUP_DISK', 'local'),
    ],
    
    'access_control' => [
        'max_failed_attempts' => env('MAX_FAILED_ATTEMPTS', 5),
        'lockout_duration' => env('LOCKOUT_DURATION', 300), // 5 minutes
        'session_timeout' => env('SESSION_TIMEOUT_MINUTES', 120),
    ],
    
    'otp' => [
        'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 5),
        'auto_approve_business_hours' => env('OTP_AUTO_APPROVE_BUSINESS_HOURS', true),
        'business_start_hour' => env('BUSINESS_START_HOUR', 8),
        'business_end_hour' => env('BUSINESS_END_HOUR', 18),
    ],
];