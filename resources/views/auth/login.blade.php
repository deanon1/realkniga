@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <h2>Вход в систему</h2>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Введите ваш email">
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           required autocomplete="current-password" placeholder="Введите ваш пароль">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Запомнить меня
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Войти</button>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">Забыли пароль?</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>

<style>
.auth-section {
    padding: var(--space-20) 0;
    background-color: var(--off-white);
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    flex: 1;
}

.auth-card {
    background: var(--pure-white);
    padding: var(--space-10);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    max-width: 520px;
    margin: 0 auto;
    width: 100%;
}

.auth-title {
    text-align: center;
    margin-bottom: var(--space-8);
    color: var(--primary-black);
    font-size: var(--text-2xl);
    font-weight: 700;
    letter-spacing: -0.025em;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.form-label {
    font-weight: 500;
    color: var(--primary-dark);
    font-size: var(--text-sm);
    letter-spacing: 0.025em;
}

.form-input {
    padding: var(--space-4);
    border: 1px solid var(--very-light-gray);
    border-radius: var(--radius-md);
    font-size: var(--text-base);
    transition: all 0.3s ease;
    background: var(--off-white);
    color: var(--primary-black);
    font-family: inherit;
}

.form-input:focus {
    outline: none;
    border-color: var(--accent-blue);
    background: var(--pure-white);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
    align-items: center;
}

.btn {
    padding: var(--space-3) var(--space-6);
    border: none;
    border-radius: var(--radius-md);
    font-size: var(--text-base);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    display: inline-block;
    font-family: inherit;
    letter-spacing: 0.025em;
    background: var(--pure-white);
    color: var(--primary-black);
}

.btn-primary {
    background: var(--accent-blue);
    color: var(--pure-white);
    width: 100%;
}

.btn-primary:hover {
    background: #1e3a8a;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.forgot-password {
    color: var(--accent-blue);
    text-decoration: none;
    font-size: var(--text-sm);
    transition: color 0.3s ease;
    font-weight: 500;
}

.forgot-password:hover {
    color: #1e3a8a;
    text-decoration: underline;
}

.alert {
    padding: var(--space-4);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-6);
    border: 1px solid;
}

.alert-danger {
    background: #fef2f2;
    border-color: #fecaca;
    color: var(--accent-red);
}

.alert p {
    margin: 0;
    margin-bottom: var(--space-1);
}

.alert p:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .auth-section {
        padding: var(--space-10) var(--space-5);
    }
    
    .auth-card {
        padding: var(--space-8) var(--space-6);
    }
}
</style>
@endsection
