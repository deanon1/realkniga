@extends('layouts.app')

@section('content')

<section class="cart">
    <div class="container">
        <h2 class="section-title">КОРЗИНА</h2>
        
        @if(session('cart') && count(session('cart')) > 0)
        <div class="cart-content">
            <!-- Товары в корзине -->
            <div class="cart-items">
                @php
                    $cart = session('cart', []);
                    $total = 0;
                    $totalItems = 0;
                @endphp
                
                @foreach($cart as $id => $item)
                @php
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                    $totalItems += $item['quantity'];
                @endphp
                
                <div class="cart-item" data-id="{{ $id }}">
                    <div class="item-image">
                        <img src="{{ $item['image'] ?? '/images/default-book.jpg' }}" alt="{{ $item['title'] }}">
                    </div>
                    
                    <div class="item-details">
                        <h3 class="item-title">{{ $item['title'] }}</h3>
                        <p class="item-author">{{ $item['author'] ?? 'Неизвестный автор' }}</p>
                        <p class="item-price">{{ number_format($item['price'], 2, ',', ' ') }} BYN</p>
                    </div>
                    
                    <div class="item-quantity">
                        <label class="quantity-label">Количество:</label>
                        <div class="quantity-controls">
                            <button class="quantity-btn minus" onclick="updateQuantity({{ $id }}, -1)">−</button>
                            <span class="quantity-value">{{ $item['quantity'] }}</span>
                            <button class="quantity-btn plus" onclick="updateQuantity({{ $id }}, 1)">+</button>
                        </div>
                    </div>
                    
                    <div class="item-subtotal">
                        <label class="subtotal-label">Сумма:</label>
                        <p class="subtotal-value">{{ number_format($itemTotal, 2, ',', ' ') }} BYN</p>
                    </div>
                    
                    <div class="item-actions">
                        <button class="remove-btn" onclick="removeFromCart({{ $id }})">Удалить</button>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Итоговая информация -->
            <div class="cart-summary">
                <div class="summary-card">
                    <h3 class="summary-title">ИТОГО</h3>
                    
                    <div class="summary-row">
                        <span class="summary-label">Товаров:</span>
                        <span class="summary-value">{{ $totalItems }} шт.</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Подытог:</span>
                        <span class="summary-value">{{ number_format($total, 2, ',', ' ') }} BYN</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Доставка:</span>
                        <span class="summary-value">Бесплатно</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-row total">
                        <span class="summary-label">К оплате:</span>
                        <span class="summary-value total-amount">{{ number_format($total, 2, ',', ' ') }} BYN</span>
                    </div>
                    
                    <div class="summary-actions">
                        <a href="/catalog" class="continue-btn">Продолжить покупки</a>
                        <a href="/order" class="checkout-btn">Оформить заказ</a>
                    </div>
                </div>
                
                <!-- Промокод -->
                <div class="promo-card">
                    <h4 class="promo-title">ПРОМОКОД</h4>
                    <div class="promo-form">
                        <input type="text" class="promo-input" placeholder="Введите промокод">
                        <button class="promo-btn">Применить</button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Пустая корзина -->
        <div class="empty-cart">
            <div class="empty-icon">🛒</div>
            <h3 class="empty-title">Корзина пуста</h3>
            <p class="empty-text">Добавьте книги в корзину для оформления заказа</p>
            <a href="/catalog" class="shop-btn">Перейти к покупкам</a>
        </div>
        @endif
    </div>
</section>

<!-- Модальное окно подтверждения удаления -->
<div id="remove-modal" class="remove-modal-overlay" style="display: none;">
    <div class="remove-modal">
        <div class="remove-modal-header">
            <h3>Подтверждение удаления</h3>
        </div>
        <div class="remove-modal-content">
            <div class="remove-icon">🗑️</div>
            <p class="remove-message">Удалить этот товар из корзины?</p>
            <p class="remove-item-name"></p>
            
            <div class="remove-actions">
                <button type="button" class="btn btn-cancel" onclick="closeRemoveModal()">Отмена</button>
                <button type="button" class="btn btn-remove" onclick="confirmRemove()">Удалить</button>
            </div>
        </div>
    </div>
</div>

<script>
// Обновление количества товара
function updateQuantity(id, change) {
    fetch(`/cart/update/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ change: change })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            location.reload();
        }
    })
    .catch(function(error) { console.error('Error:', error); });
}

// Глобальная переменная для хранения ID удаляемого товара
let itemToRemove = null;

// Удаление товара из корзины
function removeFromCart(id) {
    console.log('Attempting to remove item:', id);
    
    // Получаем информацию о товаре
    const itemElement = document.querySelector(`[data-id="${id}"]`);
    const itemTitle = itemElement ? itemElement.querySelector('.item-title').textContent : '';
    
    // Показываем модальное окно
    showRemoveModal(id, itemTitle);
}

// Показать модальное окно удаления
function showRemoveModal(id, itemTitle) {
    itemToRemove = id;
    const modal = document.getElementById('remove-modal');
    const itemNameElement = modal.querySelector('.remove-item-name');
    
    itemNameElement.textContent = itemTitle;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Фокус на кнопке отмены
    setTimeout(() => {
        modal.querySelector('.btn-cancel').focus();
    }, 100);
}

// Закрыть модальное окно
function closeRemoveModal() {
    const modal = document.getElementById('remove-modal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    itemToRemove = null;
}

// Подтвердить удаление
function confirmRemove() {
    if (!itemToRemove) return;
    
    fetch(`/cart/remove/${itemToRemove}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(function(response) {
        console.log('Response received:', response);
        if (!response.ok) {
            throw new Error('HTTP error! status: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        console.log('Data received:', data);
        if (data.success) {
            closeRemoveModal();
            // Мгновенное обновление страницы
            location.reload();
        } else {
            alert('Ошибка при удалении товара');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('Ошибка при удалении товара');
    });
}

// Обновление итоговой суммы
function updateCartSummary() {
    const subtotalElements = document.querySelectorAll('.subtotal-value');
    let total = 0;
    
    subtotalElements.forEach(function(element) {
        const text = element.textContent;
        const value = parseFloat(text.replace(/[^\d,]/g, '').replace(',', '.'));
        total += value;
    });
    
    const totalElement = document.querySelector('.total-amount');
    if (totalElement) {
        totalElement.textContent = total.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' BYN';
    }
}

// Применение промокода
document.querySelector('.promo-btn')?.addEventListener('click', function() {
    const promoCode = document.querySelector('.promo-input').value;
    if (promoCode.trim()) {
        // Здесь можно добавить логику применения промокода
        alert('Промокод применен!');
    }
});

// Анимация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach(function(item, index) {
        setTimeout(function() {
            item.classList.add('fade-in');
        }, index * 100);
    });
    
    // Закрытие модального окна по клику на фон
    const removeModal = document.getElementById('remove-modal');
    removeModal.addEventListener('click', function(e) {
        if (e.target === removeModal) {
            closeRemoveModal();
        }
    });
    
    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && removeModal.style.display === 'flex') {
            closeRemoveModal();
        }
    });
});
</script>

<style>
/* Стили для модального окна удаления */
.remove-modal-overlay {
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

.remove-modal-overlay[style*="flex"] {
    opacity: 1;
    visibility: visible;
}

.remove-modal {
    background: white;
    border-radius: 12px;
    max-width: 400px;
    width: 90%;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.remove-modal-overlay[style*="flex"] .remove-modal {
    transform: scale(1);
}

.remove-modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    text-align: center;
}

.remove-modal-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 20px;
    font-weight: 600;
}

.remove-modal-content {
    padding: 30px 20px;
    text-align: center;
}

.remove-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.remove-message {
    font-size: 16px;
    color: #374151;
    margin: 0 0 10px 0;
    font-weight: 500;
}

.remove-item-name {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 30px 0;
    font-style: italic;
    max-height: 40px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.remove-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn {
    padding: 10px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 100px;
}

.btn-cancel {
    background: #f3f4f6;
    color: #374151;
}

.btn-cancel:hover {
    background: #e5e7eb;
    color: #1f2937;
}

.btn-remove {
    background: #dc2626;
    color: white;
}

.btn-remove:hover {
    background: #b91c1c;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.btn:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

@media (max-width: 768px) {
    .remove-modal {
        width: 95%;
        margin: 20px;
    }
    
    .remove-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
@endsection
