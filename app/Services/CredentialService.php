<?php 

namespace App\Services;

use App\Models\Credential;
use App\Models\CredentialAccessLog;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CredentialService
{
    public function createCredential(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            // Calculate password strength
            if (isset($data['password'])) {
                $tempCredential = new Credential();
                $data['password_strength'] = $tempCredential->calculatePasswordStrength($data['password']);
            }

            $data['created_by'] = $user->id;
            $data['updated_by'] = $user->id;

            $credential = Credential::create($data);

            // Log the creation
            SystemLog::logAction(
                'credential_created',
                'credentials',
                "Credential '{$credential->name}' created",
                'info',
                $user->id
            );

            return $credential;
        });
    }

    public function updateCredential(Credential $credential, array $data, User $user)
    {
        return DB::transaction(function () use ($credential, $data, $user) {
            $originalData = $credential->toArray();

            // Calculate password strength if password is being updated
            if (isset($data['password']) && $data['password'] !== $credential->password) {
                $data['password_strength'] = $credential->calculatePasswordStrength($data['password']);
            }

            $data['updated_by'] = $user->id;
            $credential->update($data);

            // Calculate changes for logging
            $changes = [];
            foreach ($data as $key => $value) {
                if ($key !== 'password' && isset($originalData[$key]) && $originalData[$key] !== $value) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $value
                    ];
                }
            }

            // Log the update
            CredentialAccessLog::logAccess(
                $credential->id,
                $user->id,
                'edit',
                null,
                $changes
            );

            SystemLog::logAction(
                'credential_updated',
                'credentials',
                "Credential '{$credential->name}' updated",
                'info',
                $user->id
            );

            return $credential;
        });
    }

    public function deleteCredential(Credential $credential, User $user)
    {
        return DB::transaction(function () use ($credential, $user) {
            $credentialName = $credential->name;

            // Log the deletion
            CredentialAccessLog::logAccess(
                $credential->id,
                $user->id,
                'delete'
            );

            SystemLog::logAction(
                'credential_deleted',
                'credentials',
                "Credential '{$credentialName}' deleted",
                'warning',
                $user->id
            );

            $credential->delete();

            return true;
        });
    }

    public function viewCredential(Credential $credential, User $user, $otpRequestId = null)
    {
        // Update last accessed timestamp
        $credential->updateLastAccessed();

        // Log the access
        CredentialAccessLog::logAccess(
            $credential->id,
            $user->id,
            'view',
            $otpRequestId
        );

        return $credential;
    }

    public function copyCredential(Credential $credential, User $user, $field, $otpRequestId = null)
    {
        // Log the copy action
        CredentialAccessLog::logAccess(
            $credential->id,
            $user->id,
            'copy',
            $otpRequestId,
            ['field' => $field]
        );

        SystemLog::logAction(
            'credential_copied',
            'credentials',
            "Credential field '{$field}' copied from '{$credential->name}'",
            'info',
            $user->id
        );

        return true;
    }

    public function getExpiringCredentials($days = 30)
    {
        return Credential::with(['category', 'creator'])
            ->expiringSoon($days)
            ->active()
            ->orderBy('expires_at', 'asc')
            ->get();
    }

    public function getExpiredCredentials()
    {
        return Credential::with(['category', 'creator'])
            ->expired()
            ->active()
            ->orderBy('expires_at', 'asc')
            ->get();
    }

    public function searchCredentials($query, User $user, $categoryId = null)
    {
        $credentials = Credential::with(['category', 'creator'])
            ->active()
            ->when($categoryId, function ($q) use ($categoryId) {
                return $q->byCategory($categoryId);
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('website_url', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(15);

        // Log the search
        SystemLog::logAction(
            'credentials_searched',
            'credentials',
            "Searched for: '{$query}'" . ($categoryId ? " in category {$categoryId}" : ''),
            'info',
            $user->id
        );

        return $credentials;
    }
}