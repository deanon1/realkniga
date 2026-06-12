@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <h2>Создать аккаунт</h2>
            
            <div id="error-message" class="alert alert-danger" style="display: none;"></div>
            <div id="success-message" class="alert alert-success" style="display: none;"></div>
            
            <!-- Форма регистрации -->
            <form id="registration-form">
                @csrf
                
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" class="form-input" 
                           required autocomplete="name" autofocus placeholder="Введите ваше имя">
                </div>
                    
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           required autocomplete="email" placeholder="Введите ваш email">
                </div>
                    
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="form-input" 
                           required autocomplete="new-password" placeholder="Придумайте пароль (минимум 6 символов)">
                </div>
                    
                <div class="form-group">
                    <label for="password_confirmation">Подтверждение пароля</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                           required autocomplete="new-password" placeholder="Повторите пароль">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="register-btn">Зарегистрироваться</button>
                    
                    <div class="login-link">
                        Уже есть аккаунт? 
                        <a href="{{ route('login') }}" class="link-primary">Войдите</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Модальное окно верификации -->
<div id="verification-modal" class="verification-modal-overlay" style="display: none;">
    <div class="verification-modal">
        <div class="verification-modal-header">
            <h3>Подтверждение email</h3>
        </div>
        <div class="verification-modal-content">
            <p>Мы отправили код верификации на ваш email. Введите его ниже:</p>
            
            <div class="verification-form">
                <div class="form-group">
                    <label for="verification-code">Код верификации</label>
                    <input type="text" id="verification-code" class="form-input" 
                           maxlength="6" placeholder="000000" autocomplete="off">
                </div>
                
                <div id="verification-error" class="alert alert-danger" style="display: none;"></div>
                
                <div class="verification-actions">
                    <button type="button" id="verify-btn" class="btn btn-primary">Подтвердить</button>
                    <button type="button" id="resend-btn" class="btn btn-secondary">Отправить повторно</button>
                </div>
            </div>
            
            <div class="verification-info">
                <p><small>Код действителен 15 минут. Проверьте папку "Спам", если письмо не пришло.</small></p>
            </div>
        </div>
    </div>
</div>

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

.login-link {
    text-align: center;
    font-size: var(--text-sm);
    color: var(--medium-gray);
}

.link-primary {
    color: var(--accent-blue);
    text-decoration: none;
    font-weight: 500;
}

.link-primary:hover {
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

/* Стили для модального окна верификации */
.verification-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.verification-modal-overlay[style*="flex"] {
    opacity: 1;
    visibility: visible;
}

.verification-modal {
    background: white;
    border-radius: var(--radius-lg);
    max-width: 400px;
    width: 90%;
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.verification-modal-overlay[style*="flex"] .verification-modal {
    transform: scale(1);
}

.verification-modal-header {
    padding: 20px;
    border-bottom: 1px solid var(--very-light-gray);
    text-align: center;
}

.verification-modal-header h3 {
    margin: 0;
    color: var(--primary-black);
    font-size: var(--text-xl);
}

.verification-modal-content {
    padding: 20px;
}

.verification-form {
    margin: 20px 0;
}

.verification-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

.verification-info {
    text-align: center;
    margin-top: 20px;
    color: var(--medium-gray);
}

.btn-secondary {
    background: var(--medium-gray);
    color: white;
}

.btn-secondary:hover {
    background: var(--primary-dark);
    color: white;
}

.btn-secondary:disabled {
    background: #9ca3af;
    color: #6b7280;
    cursor: not-allowed;
    opacity: 0.7;
}

.alert-success {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #166534;
}

@media (max-width: 768px) {
    .verification-modal {
        width: 95%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registration-form');
    const verificationModal = document.getElementById('verification-modal');
    const verificationCodeInput = document.getElementById('verification-code');
    const verifyBtn = document.getElementById('verify-btn');
    const resendBtn = document.getElementById('resend-btn');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const verificationError = document.getElementById('verification-error');
    
    let currentEmail = '';

    // Обработка формы регистрации
    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        hideMessages();
        
        const formData = new FormData(registrationForm);
        const data = Object.fromEntries(formData);
        
        // Валидация на клиенте
        if (data.password !== data.password_confirmation) {
            showError('Пароли не совпадают');
            return;
        }
        
        if (data.password.length < 6) {
            showError('Пароль должен содержать минимум 6 символов');
            return;
        }
        
        // Отправка запроса на регистрацию
        fetch('{{ route("register") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                currentEmail = data.email;
                showVerificationModal();
                showSuccess(result.message);
            } else {
                showError(result.message || 'Произошла ошибка при регистрации');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Произошла ошибка. Попробуйте еще раз.');
        });
    });

    // Обработка верификации
    verifyBtn.addEventListener('click', function() {
        const code = verificationCodeInput.value.trim();
        
        if (!code || code.length !== 6) {
            showVerificationError('Введите 6-значный код');
            return;
        }
        
        hideVerificationError();
        
        fetch('{{ route("verify.email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: currentEmail,
                code: code
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccess(result.message);
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1000);
            } else {
                showVerificationError(result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showVerificationError('Произошла ошибка. Попробуйте еще раз.');
        });
    });

    // Повторная отправка кода
    resendBtn.addEventListener('click', function() {
        // Показываем индикатор загрузки
        const originalText = resendBtn.textContent;
        resendBtn.textContent = 'Отправка...';
        resendBtn.disabled = true;
        
        fetch('{{ route("resend.code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                email: currentEmail
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccess('✅ ' + result.message);
                hideVerificationError();
                verificationCodeInput.value = '';
                verificationCodeInput.focus();
            } else {
                showVerificationError(result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showVerificationError('Произошла ошибка. Попробуйте еще раз.');
        })
        .finally(() => {
            // Возвращаем кнопку в исходное состояние
            setTimeout(() => {
                resendBtn.textContent = originalText;
                resendBtn.disabled = false;
            }, 1500);
        });
    });

    // Форматирование ввода кода (только цифры)
    verificationCodeInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Функции для работы с сообщениями
    function showVerificationModal() {
        verificationModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        verificationCodeInput.focus();
    }

    function hideVerificationModal() {
        verificationModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
    }

    function showVerificationError(message) {
        verificationError.textContent = message;
        verificationError.style.display = 'block';
    }

    function hideVerificationError() {
        verificationError.style.display = 'none';
    }

    function hideMessages() {
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
        verificationError.style.display = 'none';
    }

    // Закрытие модального окна по клику на фон
    verificationModal.addEventListener('click', function(e) {
        if (e.target === verificationModal) {
            hideVerificationModal();
        }
    });

    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && verificationModal.style.display === 'flex') {
            hideVerificationModal();
        }
    });
});
</script>
@endsection
