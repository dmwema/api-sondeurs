<?php

namespace App\Services\Auth;

class AuthService
{
    public static $emailError        = 'email_error';
    public static $passwordError     = 'password_error';

    public function checkRequiredFields (array $fields, array $content): array
    {
        $returnMessage = null;
        $errorType = null;

        foreach ($fields as $key => $message) {
            if (!(
                array_key_exists($key, $content)
                && $content[$key] != null
                && $content[$key] != ''
            )) {
                $returnMessage = $message;
                $errorType = $key . 'Error';
                $errorType = self::$$errorType;
                break;
            }
        }

        return [
            'message' => $returnMessage,
            'errorType' => $errorType
        ];
    }
}