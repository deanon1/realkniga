<?php

namespace App\Services;

use App\Models\EmailVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class EmailVerificationService
{
    /**
     * Создать и отправить код верификации
     */
    public function createAndSendCode(string $email): EmailVerification
    {
        // Удаляем старые коды для этого email
        EmailVerification::where('email', $email)->delete();

        // Генерируем случайный 6-значный код
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Создаем запись в БД
        $verification = EmailVerification::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(15), // Код действителен 15 минут
            'verified' => false,
        ]);

        // Отправляем email
        try {
            Mail::to($email)->send(new VerificationCodeMail($code));
        } catch (\Exception $e) {
            // В случае ошибки отправки, логируем и продолжаем
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }

        return $verification;
    }

    /**
     * Проверить код верификации
     */
    public function verifyCode(string $email, string $code): bool
    {
        $verification = EmailVerification::where('email', $email)
            ->where('code', $code)
            ->first();

        if (!$verification || !$verification->isValid()) {
            return false;
        }

        // Помечаем как подтвержденный
        $verification->verified = true;
        $verification->save();

        return true;
    }

    /**
     * Проверить, есть ли действующий код для email
     */
    public function hasActiveCode(string $email): bool
    {
        return EmailVerification::where('email', $email)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
