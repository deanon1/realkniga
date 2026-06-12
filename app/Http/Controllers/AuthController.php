<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    protected $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    public function showRegisterForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'name' => 'nullable|string|max:255',
        ]);

        // Сохраняем данные в сессию
        Session::put('registration_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Создаем и отправляем код верификации
        $this->emailVerificationService->createAndSendCode($request->email);

        return response()->json([
            'success' => true,
            'message' => 'Код верификации отправлен на ваш email'
        ]);
    }

    public function verifyEmail(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($this->emailVerificationService->verifyCode($request->email, $request->code)) {
            // Получаем данные из сессии
            $registrationData = Session::get('registration_data');
            
            if ($registrationData && $registrationData['email'] === $request->email) {
                // Создаем пользователя
                $user = User::create([
                    'name' => $registrationData['name'],
                    'email' => $registrationData['email'],
                    'password' => Hash::make($registrationData['password']),
                ]);

                // Очищаем сессию
                Session::forget('registration_data');

                // Авторизуем пользователя
                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Регистрация успешно завершена!',
                    'redirect' => route('home')
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Неверный код верификации или код истек'
        ]);
    }

    public function resendCode(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->emailVerificationService->createAndSendCode($request->email);

        return response()->json([
            'success' => true,
            'message' => 'Код верификации отправлен повторно'
        ]);
    }

    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Неверный логин или пароль']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
