@extends('layouts.app')

@section('content')
<div class="profile-form">
    <div class="form-card">
        <h1 class="form-title">Редактирование профиля</h1>
        
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Аватар -->
            <div class="form-group">
                <label class="form-label">Аватар</label>
                
                <div class="avatar-upload-section">
                    @if($user->avatar)
                        <!-- Текущее изображение -->
                        <div id="currentAvatarSection" class="current-image-section">
                            <div class="current-image-container">
                                <img src="{{ asset('storage/' . $user->avatar) }}?v={{ time() }}" alt="Текущий аватар" class="current-image">
                            </div>
                            <div class="image-controls">
                                <button type="button" class="btn-remove-image" onclick="removeCurrentAvatar()">
                                    Удалить аватар
                                </button>
                                <input type="hidden" name="remove_avatar" id="removeAvatarFlag" value="0">
                            </div>
                        </div>
                    @endif
                    
                    <!-- Секция загрузки нового изображения (скрыта по умолчанию) -->
                    <div id="uploadSection" class="upload-section" style="display: {{ $user->avatar ? 'none' : 'block' }};">
                        <div class="upload-area">
                            <button type="button" class="btn-upload" onclick="document.getElementById('avatarInput').click()">
                                Выбрать файл
                            </button>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="image-input" onchange="handleAvatarSelect(event)">
                            <p class="upload-hint">Форматы: JPG, PNG, GIF. Макс. размер: 50MB</p>
                        </div>
                    </div>
                    
                    <!-- Предпросмотр нового изображения -->
                    <div id="avatarPreview" class="image-preview"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Имя</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" placeholder="Введите ваше имя">
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" placeholder="Введите ваш email" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+375 (XX) XXX-XX-XX">
                @error('phone')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Адрес</label>
                <textarea id="address" name="address" class="form-input" rows="3" placeholder="Введите ваш адрес">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="region" class="form-label">Регион</label>
                <div class="select-wrapper">
                    <select id="region" name="region" class="form-input">
                        <option value="">Выберите регион</option>
                        <option value="Минск" {{ old('region', $user->region) === 'Минск' ? 'selected' : '' }}>Минск</option>
                        <option value="Брестская область" {{ old('region', $user->region) === 'Брестская область' ? 'selected' : '' }}>Брестская область</option>
                        <option value="Витебская область" {{ old('region', $user->region) === 'Витебская область' ? 'selected' : '' }}>Витебская область</option>
                        <option value="Гомельская область" {{ old('region', $user->region) === 'Гомельская область' ? 'selected' : '' }}>Гомельская область</option>
                        <option value="Гродненская область" {{ old('region', $user->region) === 'Гродненская область' ? 'selected' : '' }}>Гродненская область</option>
                        <option value="Могилёвская область" {{ old('region', $user->region) === 'Могилёвская область' ? 'selected' : '' }}>Могилёвская область</option>
                    </select>
                </div>
                @error('region')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('profile.index') }}" class="btn-secondary">← Отмена</a>
                <button type="submit" class="btn-primary">Сохранить изменения</button>
            </div>
        </form>
    </div>
</div>

<script>
function handleAvatarSelect(event) {
    const file = event.target.files[0];
    if (file) {
        // Проверяем размер файла
        if (file.size > 50 * 1024 * 1024) { // 50MB
            alert('Файл слишком большой. Максимальный размер: 50MB');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            // Показываем секцию загрузки и скрываем текущий аватар
            document.getElementById('uploadSection').style.display = 'block';
            document.getElementById('currentAvatarSection').style.display = 'none';
            
            // Показываем превью
            const preview = document.getElementById('avatarPreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Превью аватара" class="preview-image">`;
        };
        reader.readAsDataURL(file);
    }
}

function removeCurrentAvatar() {
    if (confirm('Удалить текущий аватар?')) {
        // Устанавливаем флаг удаления
        document.getElementById('removeAvatarFlag').value = '1';
        
        // Скрываем текущий аватар
        document.getElementById('currentAvatarSection').style.display = 'none';
        
        // Показываем секцию загрузки
        document.getElementById('uploadSection').style.display = 'block';
        
        // Очищаем превью
        document.getElementById('avatarPreview').innerHTML = '';
    }
}

// Клик по превью для выбора файла
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.getElementById('avatarPreview');
    const fileInput = document.getElementById('avatarInput');
    
    if (preview && fileInput) {
        preview.addEventListener('click', function() {
            fileInput.click();
        });
        
        preview.style.cursor = 'pointer';
    }
});
</script>

<style>
/* Конкретные стили для аватара в профиле */
.current-image, .preview-image {
    width: 100px !important;
    height: 100px !important;
    max-width: 100px !important;
    max-height: 100px !important;
    min-width: 100px !important;
    min-height: 100px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.current-image-container, .image-preview {
    display: inline-block !important;
    text-align: center !important;
}

/* Убираем другие возможные стили */
.avatar-upload-section img,
.current-image-section img,
.image-preview img {
    width: 100px !important;
    height: 100px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
}

/* Красивая кнопка удаления аватара */
.btn-remove-image {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    color: white !important;
    border: none !important;
    padding: 12px 20px !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 160px !important;
    justify-content: center !important;
}

.btn-remove-image:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3) !important;
}

.btn-remove-image:active {
    transform: translateY(0) !important;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2) !important;
}

.btn-remove-image:focus {
    outline: 2px solid #fca5a5 !important;
    outline-offset: 2px !important;
}

/* Анимация для иконки мусорки */
.btn-remove-image::before {
    content: '🗑️' !important;
    font-size: 16px !important;
    margin-right: 4px !important;
}

/* Красивая кнопка выбора файла */
.upload-area {
    text-align: center;
    padding: 20px;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    background: #f9fafb;
    transition: all 0.3s ease;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
}

.upload-area:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.btn-upload {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    border: none !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 160px !important;
    justify-content: center !important;
    margin: 0 !important;
    float: none !important;
    position: relative !important;
}

.btn-upload:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3) !important;
}

.btn-upload:active {
    transform: translateY(0) !important;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2) !important;
}

.btn-upload:focus {
    outline: 2px solid #93c5fd !important;
    outline-offset: 2px !important;
}

/* Анимация для иконки камеры */
.btn-upload::before {
    content: '📷' !important;
    font-size: 16px !important;
    margin-right: 4px !important;
}

.upload-hint {
    margin-top: 12px !important;
    font-size: 12px !important;
    color: #6b7280 !important;
    line-height: 1.4 !important;
}

/* Скрываем любой лишний текст после кнопки */
.upload-area .btn-upload + *:not(.image-input):not(.upload-hint) {
    display: none !important;
}

.upload-area .btn-upload ~ *:not(.image-input):not(.upload-hint) {
    display: none !important;
}

/* Убираем все лишние элементы кроме кнопки, input и подсказки */
.upload-area > *:not(button):not(input):not(.upload-hint) {
    display: none !important;
}

</style>
@endsection
