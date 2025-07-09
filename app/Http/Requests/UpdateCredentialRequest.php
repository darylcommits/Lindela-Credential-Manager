<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCredentialRequest extends FormRequest
{
    public function authorize()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        return Auth::check() && $user && $user->canAccessCredentials();
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:credential_categories,id',
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'website_url' => 'nullable|url|max:500',
            'backup_codes' => 'nullable|array',
            'backup_codes.*' => 'string|max:100',
            'expires_at' => 'nullable|date',
            'password_never_expires' => 'boolean',
        ];
    }
}