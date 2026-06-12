@extends('layouts.app')

@section('title', 'Оставить отзыв')

@section('content')
<div class="container">
    <div class="review-form-container">
        <div class="form-header">
            <h1>Оставить отзыв о заказе #{{ $order->id }}</h1>
            <p>Поделитесь впечатлениями о покупках</p>
        </div>

        <div class="order-summary">
            <h3>Заказ #{{ $order->id }}</h3>
            <div class="order-items">
                @foreach($order->items as $item)
                    <div class="order-item">
                        <div class="item-info">
                            <h4>{{ $item->book->title }}</h4>
                            <p>Автор: {{ $item->book->author }}</p>
                            <p>Количество: {{ $item->quantity }} × {{ number_format($item->price / 100, 2) }} BYN</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="order-total">
                <strong>Итого: {{ number_format($order->total / 100, 2) }} BYN</strong>
            </div>
        </div>

        <form id="reviewForm" class="review-form">
            @csrf
            <input type="hidden" name="book_id" id="bookId" value="{{ $order->items->first()->book_id ?? '' }}">
            
            <div class="form-group">
                <label class="form-label">Оценка *</label>
                <div class="rating-container">
                    <div class="stars" id="starRating">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="star" data-rating="{{ $i }}">★</span>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" value="5" required>
                    <div class="rating-text" id="ratingText">Отлично</div>
                </div>
            </div>

            <div class="form-group">
                <label for="review_text" class="form-label">Ваш отзыв *</label>
                <textarea 
                    id="review_text" 
                    name="review_text" 
                    class="form-textarea" 
                    rows="6" 
                    placeholder="Расскажите о вашем впечатлении от покупки..."
                    required
                    minlength="10"
                    maxlength="1000"
                ></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 1000 символов
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">Отмена</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Отправить отзыв
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Рейтинг звезд
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.getElementById('ratingText');
    
    const ratingTexts = {
        1: 'Плохо',
        2: 'Нормально', 
        3: 'Хорошо',
        4: 'Очень хорошо',
        5: 'Отлично'
    };
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            updateStars(rating);
            ratingText.textContent = ratingTexts[rating];
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            updateStars(rating);
        });
    });
    
    document.getElementById('starRating').addEventListener('mouseleave', function() {
        updateStars(parseInt(ratingInput.value));
    });
    
    function updateStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }
    
    // Счетчик символов
    const textarea = document.getElementById('review_text');
    const charCount = document.getElementById('charCount');
    
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 900) {
            charCount.classList.add('warning');
        } else {
            charCount.classList.remove('warning');
        }
    });
    
    // Отправка формы
    const form = document.getElementById('reviewForm');
    const submitBtn = document.getElementById('submitBtn');
    const orderId = {{ $order->id }};
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Отправка...';
        
        const formData = new FormData(form);
        
        fetch(`/reviews/${orderId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                
                // Перенаправляем в профиль
                setTimeout(() => {
                    window.location.href = data.redirect_to || '/profile';
                }, 2000);
            } else {
                showNotification(data.message, 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Отправить отзыв';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при отправке отзыва', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Отправить отзыв';
        });
    });
    
    function validateForm() {
        const rating = parseInt(ratingInput.value);
        const reviewText = textarea.value.trim();
        
        if (rating < 1 || rating > 5) {
            showNotification('Пожалуйста, поставьте оценку', 'error');
            return false;
        }
        
        if (reviewText.length < 10) {
            showNotification('Отзыв должен содержать минимум 10 символов', 'error');
            return false;
        }
        
        if (reviewText.length > 1000) {
            showNotification('Отзыв не должен превышать 1000 символов', 'error');
            return false;
        }
        
        return true;
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">
                    ${type === 'success' ? '✅' : '❌'}
                </span>
                <span class="notification-message">${message}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }
    
    // Инициализация
    updateStars(5);
});
</script>

<style>
.review-form-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.form-header h1 {
    color: #1f2937;
    margin-bottom: 10px;
}

.form-header p {
    color: #6b7280;
    font-size: 16px;
}

.order-summary {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.order-summary h3 {
    margin-bottom: 15px;
    color: #1f2937;
}

.order-item {
    padding: 15px 0;
    border-bottom: 1px solid #e5e7eb;
}

.order-item:last-child {
    border-bottom: none;
}

.item-info h4 {
    margin-bottom: 5px;
    color: #1f2937;
}

.item-info p {
    margin: 3px 0;
    color: #6b7280;
    font-size: 14px;
}

.order-total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #e5e7eb;
    text-align: right;
    font-size: 18px;
    color: #1f2937;
}

.review-form {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.rating-container {
    display: flex;
    align-items: center;
    gap: 15px;
}

.stars {
    display: flex;
    gap: 5px;
}

.star {
    font-size: 24px;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.2s ease;
}

.star:hover {
    color: #fbbf24;
}

.star.active {
    color: #fbbf24;
}

.rating-text {
    font-weight: 600;
    color: #6b7280;
}

.form-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    resize: vertical;
    font-family: inherit;
}

.form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.char-counter {
    text-align: right;
    margin-top: 5px;
    font-size: 12px;
    color: #6b7280;
}

.char-counter.warning {
    color: #ef4444;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #2563eb;
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

/* Уведомления */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 16px;
    min-width: 300px;
    max-width: 500px;
    z-index: 9999;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
    border-left: 4px solid #3b82f6;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification-success {
    border-left-color: #10b981;
}

.notification-error {
    border-left-color: #ef4444;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.notification-icon {
    font-size: 18px;
    flex-shrink: 0;
}

.notification-message {
    flex: 1;
    color: #1f2937;
    font-size: 14px;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.notification-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

@media (max-width: 768px) {
    .review-form-container {
        margin: 20px auto;
        padding: 0 15px;
    }
    
    .review-form {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        text-align: center;
    }
    
    .notification {
        right: 10px;
        left: 10px;
        min-width: auto;
        max-width: none;
    }
}
</style>
@endsection
