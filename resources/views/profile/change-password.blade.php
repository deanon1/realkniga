@extends('layouts.app')

@section('content')
<div class="profile-form">
    <div class="form-card">
        <h1 class="form-title">Смена пароля</h1>
        
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.change-password.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="current_password" class="form-label">Текущий пароль</label>
                <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Введите текущий пароль" required>
                @error('current_password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Новый пароль</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Введите новый пароль (минимум 6 символов)" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Подтверждение нового пароля</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Повторите новый пароль" required>
                @error('password_confirmation')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('profile.index') }}" class="btn-secondary">← Отмена</a>
                <button type="submit" class="btn-primary">Сменить пароль</button>
            </div>
        </form>
    </div>
</div>
@endsection
