<?php

namespace App\Observers;

use App\Models\Credential;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class CredentialObserver
{
    public function created(Credential $credential)
    {
        SystemLog::logAction(
            'credential_created',
            'credentials',
            "Credential '{$credential->name}' created in category '{$credential->category->name}'",
            'info',
            Auth::id()
        );
    }

    public function updated(Credential $credential)
    {
        $changes = $credential->getChanges();
        unset($changes['updated_at'], $changes['password']); // Don't log sensitive data

        if (!empty($changes)) {
            SystemLog::logAction(
                'credential_updated',
                'credentials',
                "Credential '{$credential->name}' updated",
                'info',
                Auth::id(),
                ['changes' => array_keys($changes)]
            );
        }
    }

    public function deleted(Credential $credential)
    {
        SystemLog::logAction(
            'credential_deleted',
            'credentials',
            "Credential '{$credential->name}' deleted",
            'warning',
            Auth::id()
        );
    }
}