<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class SecurityHelper
{
    public static function generateSecurePassword($length = 16)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    public static function calculatePasswordStrength($password)
    {
        $score = 0;
        
        // Length scoring
        if (strlen($password) >= 8) $score++;
        if (strlen($password) >= 12) $score++;
        if (strlen($password) >= 16) $score++;
        
        // Character type scoring
        if (preg_match('/[a-z]/', $password)) $score++;
        if (preg_match('/[A-Z]/', $password)) $score++;
        if (preg_match('/[0-9]/', $password)) $score++;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score++;
        
        return min($score, 5);
    }

    public static function isIpAllowed($ip)
    {
        // Add IP whitelist logic here if needed
        return true;
    }

    public static function logSecurityEvent($event, $details = [])
    {
        // Get the authenticated user ID, or null if not authenticated
        $userId = Auth::check() ? Auth::id() : null;
        
        \App\Models\SystemLog::logAction(
            'security_event',
            'security',
            $event,
            'warning',
            $userId,
            $details
        );
    }
}