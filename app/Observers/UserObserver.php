<?php

namespace App\Observers;

use App\Models\User;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user)
    {
        SystemLog::logAction(
            'user_created',
            'admin',
            "User '{$user->name}' ({$user->email}) created with role '{$user->role}'",
            'info',
            Auth::id()
        );
    }

    public function updated(User $user)
    {
        $changes = $user->getChanges();
        unset($changes['updated_at'], $changes['password'], $changes['last_activity_at']);

        if (!empty($changes)) {
            SystemLog::logAction(
                'user_updated',
                'admin',
                "User '{$user->name}' updated",
                'info',
                Auth::id(),
                ['changes' => array_keys($changes)]
            );
        }
    }

    public function deleted(User $user)
    {
        SystemLog::logAction(
            'user_deleted',
            'admin',
            "User '{$user->name}' ({$user->email}) deleted",
            'warning',
            Auth::id()
        );
    }
}