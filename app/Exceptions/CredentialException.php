<?php

namespace App\Exceptions;

use Exception;

class CredentialException extends Exception
{
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage()
            ], 400);
        }

        return redirect()->back()->with('error', $this->getMessage());
    }
}

