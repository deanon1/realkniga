@extends('layouts.app')

@section('title', 'Редактировать книгу - Админ панель')

@section('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Редактировать книгу</h1>
            <p class="admin-subtitle">{{ $book->title }}</p>
        </div>
        <div class="admin-actions">
            <a href="/admin/books" class="btn-secondary">
                <span class="btn-icon">←</span>
                Назад к списку
            </a>
        </div>
    </div>

    <div class="admin-content">
        <form method="POST" action="/admin/books/{{ $book->id }}" class="admin-form" enctype="multipart/form-data" id="bookEditForm">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Левая колонка -->
                <div class="form-column">
                    <div class="form-group">
                        <label class="form-label">Название книги *</label>
                        <input type="text" name="title" class="form-input" required 
                               value="{{ $book->title }}" placeholder="Введите название книги">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Автор *</label>
                        <input type="text" name="author" class="form-input" required 
                               value="{{ $book->author }}" placeholder="Введите автора">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Жанр</label>
                        <select name="genre" class="form-input" required>
                            <option value="fiction" {{ $book->genre == 'fiction' ? 'selected' : '' }}>Художественная литература</option>
                            <option value="science" {{ $book->genre == 'science' ? 'selected' : '' }}>Научная фантастика</option>
                            <option value="detective" {{ $book->genre == 'detective' ? 'selected' : '' }}>Детективы</option>
                            <option value="romance" {{ $book->genre == 'romance' ? 'selected' : '' }}>Романы</option>
                            <option value="thriller" {{ $book->genre == 'thriller' ? 'selected' : '' }}>Триллеры</option>
                            <option value="biography" {{ $book->genre == 'biography' ? 'selected' : '' }}>Биографии</option>
                            <option value="history" {{ $book->genre == 'history' ? 'selected' : '' }}>История</option>
                            <option value="psychology" {{ $book->genre == 'psychology' ? 'selected' : '' }}>Психология</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Цена (BYN) *</label>
                            <input type="number" name="price" class="form-input" required 
                                   step="0.01" min="0" value="{{ $book->price }}" placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Год издания</label>
                            <input type="number" name="year" class="form-input" 
                                   value="{{ $book->year ?? '' }}" placeholder="2024">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Количество страниц</label>
                        <input type="number" name="pages" class="form-input" 
                               min="1" value="{{ $book->pages ?? '300' }}" placeholder="300">
                    </div>
                </div>
                
                <!-- Правая колонка -->
                <div class="form-column">
                    <div class="form-group">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-input" 
                               value="{{ $book->isbn ?? '' }}" placeholder="978-0-123456-78-9">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Издательство</label>
                        <input type="text" name="publisher" class="form-input" 
                               value="{{ $book->publisher ?? '' }}" placeholder="Название издательства">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Язык</label>
                        <select name="language" class="form-input">
                            <option value="ru" {{ $book->language == 'ru' ? 'selected' : '' }}>Русский</option>
                            <option value="be" {{ $book->language == 'be' ? 'selected' : '' }}>Белорусский</option>
                            <option value="en" {{ $book->language == 'en' ? 'selected' : '' }}>Английский</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Категория книги</label>
                        <div style="display: flex; gap: 20px; margin-top: 8px;">
                            <label class="form-label" style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="category" value="none" class="form-checkbox" {{ (!$book->is_new && !$book->is_popular) ? 'checked' : '' }} style="margin-right: 8px;">
                                Обычная книга
                            </label>
                            <label class="form-label" style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="category" value="new" class="form-checkbox" {{ $book->is_new ? 'checked' : '' }} style="margin-right: 8px;">
                                Новинка
                            </label>
                            <label class="form-label" style="display: flex; align-items: center; cursor: pointer;">
                                <input type="radio" name="category" value="popular" class="form-checkbox" {{ $book->is_popular ? 'checked' : '' }} style="margin-right: 8px;">
                                Популярная книга
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Обложка книги</label>
                        
                        @if($book->image)
                            <!-- Текущее изображение -->
                            <div id="currentImageSection" class="current-image-section">
                                <div class="current-image-container">
                                    <img src="{{ asset($book->image) }}?v={{ time() }}" alt="Текущая обложка" class="current-image">
                                </div>
                                <div class="image-controls">
                                    <button type="button" class="btn-remove-image" onclick="removeCurrentImage()">
                                        🗑️ Удалить обложку
                                    </button>
                                    <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                                </div>
                            </div>
                        @endif
                        
                        <!-- Секция загрузки нового изображения (скрыта по умолчанию) -->
                        <div id="uploadSection" class="upload-section" style="display: {{ $book->image ? 'none' : 'block' }};">
                            <input type="file" name="image" id="imageInput" accept="image/*" class="image-input" onchange="handleImageSelect(event)">
                            <label for="imageInput" class="upload-label">
                                <div class="upload-icon">📷</div>
                                <div class="upload-text">Добавить обложку</div>
                                <div class="upload-hint">JPEG, PNG, WebP до 50MB</div>
                            </label>
                        </div>
                        
                        <!-- Предпросмотр нового изображения -->
                        <div id="imagePreview" class="image-preview"></div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Описание книги</label>
                <textarea name="description" class="form-textarea" rows="6" 
                          placeholder="Введите описание книги...">{{ $book->description ?? '' }}</textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <span class="btn-icon">💾</span>
                    Сохранить изменения
                </button>
                <a href="/admin/books" class="btn-secondary">
                    <span class="btn-icon">❌</span>
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Проверяем форму перед отправкой
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookEditForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission detected');
            console.log('Form method:', form.method);
            console.log('Form action:', form.action);
            
            // Проверяем наличие поля _method
            const methodField = form.querySelector('input[name="_method"]');
            console.log('Method field:', methodField ? methodField.value : 'NOT FOUND');
            
            // Проверяем CSRF токен
            const csrfField = form.querySelector('input[name="_token"]');
            console.log('CSRF field:', csrfField ? 'PRESENT' : 'NOT FOUND');
        });
    }
});

function handleImageSelect(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const uploadSection = document.getElementById('uploadSection');
    
    // Очищаем предыдущий предпросмотр
    preview.innerHTML = '';
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="preview-container">
                    <img src="${e.target.result}" alt="Предпросмотр" class="preview-img">
                    <div class="preview-info">Новое изображение будет сохранено</div>
                    <button type="button" class="btn-clear-preview" onclick="clearImageSelection()">✕ Отменить</button>
                </div>
            `;
            
            // Скрываем секцию загрузки после выбора файла
            uploadSection.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

function clearImageSelection() {
    const fileInput = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');
    const uploadSection = document.getElementById('uploadSection');
    
    fileInput.value = '';
    preview.innerHTML = '';
    
    // Показываем секцию загрузки снова
    uploadSection.style.display = 'block';
}

function removeCurrentImage() {
    const currentSection = document.getElementById('currentImageSection');
    const uploadSection = document.getElementById('uploadSection');
    const removeFlag = document.getElementById('removeImageFlag');
    
    if (currentSection) {
        // Скрываем текущее изображение
        currentSection.style.display = 'none';
        removeFlag.value = '1';
        
        // Показываем секцию загрузки нового изображения
        uploadSection.style.display = 'block';
        
        // Очищаем выбор файла если был
        clearImageSelection();
    }
}
</script>

<style>
.form-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 8px;
    vertical-align: middle;
    cursor: pointer;
}

.form-group label:has(.form-checkbox) {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    margin-bottom: 0;
}

.form-group label:has(.form-checkbox):hover {
    color: var(--accent-blue);
}

/* Стили для изображений */
.current-image-section {
    margin-bottom: 15px;
}

.current-image-container {
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
}

.current-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    object-fit: cover;
    border: 3px solid #e5e7eb;
}

.image-controls {
    text-align: center;
}

.btn-remove-image {
    background: #dc2626;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-remove-image:hover {
    background: #b91c1c;
}

.upload-section {
    margin: 15px 0;
}

.image-input {
    display: none;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 25px;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-label:hover {
    border-color: #9ca3af;
    background: #f3f4f6;
}

.upload-icon {
    font-size: 36px;
    margin-bottom: 8px;
}

.upload-text {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.upload-hint {
    font-size: 13px;
    color: #6b7280;
}

.image-preview {
    margin-top: 15px;
}

.preview-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.preview-img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 6px;
    margin-bottom: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.preview-info {
    font-size: 14px;
    color: #059669;
    margin-bottom: 8px;
    font-weight: 500;
}

.btn-clear-preview {
    background: #6b7280;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-clear-preview:hover {
    background: #4b5563;
}
</style>
@endsection
