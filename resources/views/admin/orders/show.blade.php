@extends('layouts.app')

@section('title', 'Заказ #' . $order->id . ' - Админ панель')

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Заказ #{{ $order->id }}</h1>
            <p class="admin-subtitle">Детальная информация о заказе</p>
        </div>
        <div class="admin-actions">
            <a href="/admin/orders" class="btn-secondary">
                <span class="btn-icon">←</span>
                Назад к списку
            </a>
        </div>
    </div>

    <div class="admin-content">
        <div class="order-details">
            <!-- Информация о заказе -->
            <div class="detail-section">
                <h2 class="section-title">Информация о заказе</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label class="detail-label">ID заказа:</label>
                        <span class="detail-value">#{{ $order->id }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Статус:</label>
                        <span class="status-badge status-{{ $order->status }}">
                            @if($order->status === 'pending')
                                Ожидает обработки
                            @elseif($order->status === 'processing')
                                В обработке
                            @elseif($order->status === 'completed')
                                Выполнен
                            @elseif($order->status === 'cancelled')
                                Отменен
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Дата создания:</label>
                        <span class="detail-value">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Общая сумма:</label>
                        <span class="detail-value">{{ number_format($order->total / 100, 2, ',', ' ') }} BYN</span>
                    </div>
                </div>
            </div>

            <!-- Информация о клиенте -->
            <div class="detail-section">
                <h2 class="section-title">Информация о клиенте</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label class="detail-label">Имя:</label>
                        <span class="detail-value">{{ $order->user ? $order->user->name : ($order->customer_name ?? 'Гость') }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Email:</label>
                        <span class="detail-value">{{ $order->user ? $order->user->email : ($order->email ?? 'Не указан') }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Телефон:</label>
                        <span class="detail-value">{{ $order->phone ?? 'Не указан' }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Способ доставки:</label>
                        <span class="detail-value">
                            @if($order->delivery_type === 'belarusian_post')
                                <span class="delivery-badge delivery-belarusian-post">
                                    🏤 Белпочта
                                </span>
                            @elseif($order->delivery_type === 'euro_post')
                                <span class="delivery-badge delivery-euro-post">
                                    📦 Европочта
                                </span>
                            @else
                                <span class="delivery-badge delivery-unknown">
                                    ❓ Не указан
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Способ оплаты:</label>
                        <span class="detail-value">
                            <span class="payment-badge payment-cash">
                                💵 Наложенный платеж
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Адрес доставки:</label>
                        <span class="detail-value">{{ $order->address ?? 'Не указан' }}</span>
                    </div>
                    @if($order->delivery_type === 'belarusian_post' && $order->region)
                    <div class="detail-item">
                        <label class="detail-label">Регион:</label>
                        <span class="detail-value">{{ $order->region }}</span>
                    </div>
                    @endif
                    @if($order->delivery_type === 'euro_post' && $order->euro_post_address)
                    <div class="detail-item">
                        <label class="detail-label">Пункт выдачи Европочты:</label>
                        <span class="detail-value">{{ $order->euro_post_address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Товары в заказе -->
            <div class="detail-section">
                <h2 class="section-title">Товары в заказе</h2>
                <div class="order-items">
                    @php
                        // Используем только OrderItems как основной источник данных
                        $allItems = collect();
                        
                        // Добавляем товары из OrderItems (основной источник)
                        if ($order->items && $order->items->count() > 0) {
                            foreach ($order->items as $item) {
                                $allItems->push([
                                    'title' => $item->book->title ?? 'Книга удалена',
                                    'author' => $item->book->author ?? '-',
                                    'price' => $item->price / 100, // Конвертируем в BYN
                                    'quantity' => $item->quantity,
                                    'total' => ($item->price / 100) * $item->quantity
                                ]);
                            }
                        }
                        // Если OrderItems нет, используем cart как запасной вариант
                        elseif (is_array($order->cart)) {
                            foreach ($order->cart as $item) {
                                $itemPrice = $item['price'] ?? 0;
                                $itemQuantity = $item['quantity'] ?? 1;
                                $allItems->push([
                                    'title' => $item['title'] ?? 'Неизвестный товар',
                                    'author' => '-', // В cart нет автора
                                    'price' => $itemPrice / 100, // Конвертируем в BYN
                                    'quantity' => $itemQuantity,
                                    'total' => ($itemPrice / 100) * $itemQuantity
                                ]);
                            }
                        }
                        
                        $totalItemsCount = $allItems->count();
                    @endphp
                    
                    @if($totalItemsCount > 0)
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Название книги</th>
                                    <th>Автор</th>
                                    <th>Цена</th>
                                    <th>Количество</th>
                                    <th>Сумма</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allItems as $item)
                                    <tr>
                                        <td>{{ $item['title'] }}</td>
                                        <td>{{ $item['author'] ?? '-' }}</td>
                                        <td>{{ number_format($item['price'], 2, ',', ' ') }} BYN</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($item['total'], 2, ',', ' ') }} BYN</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Итого:</strong></td>
                                    <td><strong>{{ number_format($order->total / 100, 2, ',', ' ') }} BYN</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <h3>Товары не найдены</h3>
                            <p>В этом заказе нет товаров</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Управление статусом -->
            <div class="detail-section">
                <h2 class="section-title">Управление заказом</h2>
                <div class="status-controls">
                    <div class="status-form">
                        <div class="filter-group">
                            <label class="filter-label">Изменить статус:</label>
                            <select id="statusSelect" class="filter-select">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Ожидает обработки</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>В обработке</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Выполнен</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Отменен</option>
                            </select>
                        </div>
                        <button type="button" id="updateStatusBtn" class="btn-primary">Обновить статус</button>
                    </div>
                </div>
            </div>
        </div>

            <!-- Отзыв о заказе -->
            <div class="detail-section">
                <h2 class="section-title">Отзыв о заказе</h2>
                @if($review)
                    <div class="review-card" id="reviewCard">
                        <div class="review-header">
                            <div class="review-info">
                                <div class="review-author">
                                    <strong>{{ $review->user->name }}</strong>
                                    <span class="review-email">{{ $review->user->email }}</span>
                                </div>
                                <div class="review-meta">
                                    <div class="review-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $review->rating ? 'active' : '' }}">★</span>
                                        @endfor
                                    </div>
                                    <div class="review-date">
                                        {{ $review->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="review-status">
                                @if($review->is_approved)
                                    <span class="status-badge status-approved">✅ Одобрен</span>
                                @else
                                    <span class="status-badge status-pending">⏳ На модерации</span>
                                @endif
                                @if($review->contains_profanity)
                                    <span class="status-badge status-warning">⚠️ Содержит нецензурную лексику</span>
                                @endif
                            </div>
                        </div>
                        <div class="review-content">
                            <div class="review-text">
                                <h4>Текст отзыва:</h4>
                                <p>{{ $review->filtered_text ?: $review->review_text }}</p>
                            </div>
                            @if($review->contains_profanity && $review->review_text !== $review->filtered_text)
                                <div class="review-original">
                                    <h4>Оригинальный текст:</h4>
                                    <p class="original-text">{{ $review->review_text }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="review-actions">
                            @if(!$review->is_approved)
                                <button class="btn btn-success" onclick="approveReview({{ $order->id }})">
                                    <span>✅</span>
                                    Одобрить отзыв
                                </button>
                            @endif
                            <button class="btn btn-danger" onclick="deleteReview({{ $order->id }})">
                                <span>🗑️</span>
                                Удалить отзыв
                            </button>
                        </div>
                    </div>
                @else
                    <div class="empty-review">
                        <div class="empty-icon">💬</div>
                        <h3>Отзыв не найден</h3>
                        <p>Пользователь еще не оставил отзыв на этот заказ</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Модальные окна для управления отзывами -->
<div id="approveModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Подтверждение одобрения</h3>
            <button class="modal-close" onclick="hideApproveModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">✅</div>
            <p>Вы уверены, что хотите одобрить этот отзыв?</p>
            <p class="modal-subtitle">После одобрения отзыв будет виден всем пользователям</p>
            <input type="hidden" id="approveOrderId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideApproveModal()">Отмена</button>
            <button class="btn btn-success" onclick="confirmApprove()">Одобрить</button>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Подтверждение удаления</h3>
            <button class="modal-close" onclick="hideDeleteModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">🗑️</div>
            <p>Вы уверены, что хотите удалить этот отзыв?</p>
            <input type="hidden" id="deleteOrderId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideDeleteModal()">Отмена</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Удалить</button>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения обновления статуса -->
<div id="statusConfirmModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Подтверждение изменения статуса</h3>
            <button class="modal-close" onclick="closeStatusConfirmModal()">×</button>
        </div>
        <div class="modal-body">
            <p>Вы уверены, что хотите изменить статус заказа?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeStatusConfirmModal()">Отмена</button>
            <button type="button" class="btn-primary" id="confirmStatusBtn">Подтвердить</button>
        </div>
    </div>
</div>

<!-- Модальное окно успешного обновления -->
<div id="statusSuccessModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Статус обновлен</h3>
            <button class="modal-close" onclick="closeStatusSuccessModal()">×</button>
        </div>
        <div class="modal-body">
            <p>Статус заказа успешно обновлен</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-primary" onclick="closeStatusSuccessModal()">OK</button>
        </div>
    </div>
</div>

<style>
.detail-section {
    background-color: var(--pure-white);
    border: 1px solid var(--very-light-gray);
    border-radius: var(--radius-xl);
    padding: var(--space-8);
    margin-bottom: var(--space-6);
    box-shadow: var(--shadow-sm);
}

.section-title {
    font-size: var(--text-xl);
    font-weight: 700;
    color: var(--primary-black);
    margin-bottom: var(--space-6);
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-4);
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.detail-label {
    font-size: var(--text-sm);
    font-weight: 600;
    color: var(--medium-gray);
}

.detail-value {
    font-size: var(--text-base);
    color: var(--primary-black);
}

.status-controls {
    display: flex;
    justify-content: center;
    padding: var(--space-4) 0;
}

.status-form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-4);
    width: 100%;
    max-width: 300px;
}

.status-form .form-group {
    flex: 1;
    max-width: 300px;
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: var(--space-8);
    color: var(--medium-gray);
}

.empty-icon {
    font-size: 48px;
    margin-bottom: var(--space-4);
}

.text-right {
    text-align: right;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript загружен');
    
    const updateStatusBtn = document.getElementById('updateStatusBtn');
    const statusSelect = document.getElementById('statusSelect');
    const confirmStatusBtn = document.getElementById('confirmStatusBtn');
    
    console.log('Элементы найдены:', {
        updateStatusBtn: !!updateStatusBtn,
        statusSelect: !!statusSelect,
        confirmStatusBtn: !!confirmStatusBtn
    });
    
    // Проверяем что элементы существуют
    if (!updateStatusBtn || !statusSelect || !confirmStatusBtn) {
        console.error('Не найдены необходимые элементы');
        return;
    }
    
    // Обработчик кнопки "Обновить статус"
    updateStatusBtn.addEventListener('click', function(e) {
        console.log('Кнопка обновления нажата');
        e.preventDefault(); // Предотвращаем стандартное поведение
        
        const newStatus = statusSelect.value;
        const currentStatus = '{{ $order->status }}';
        
        console.log('Статусы:', { newStatus, currentStatus });
        
        // Если статус не изменился, ничего не делаем
        if (newStatus === currentStatus) {
            alert('Статус не изменился');
            return;
        }
        
        // Показываем модальное окно подтверждения
        showStatusConfirmModal(newStatus);
    });
    
    // Обработчик кнопки подтверждения
    confirmStatusBtn.addEventListener('click', function(e) {
        console.log('Кнопка подтверждения нажата');
        e.preventDefault(); // Предотвращаем стандартное поведение
        
        const newStatus = statusSelect.value;
        console.log('Обновление статуса на:', newStatus);
        updateOrderStatus(newStatus);
    });
});

// Функции для работы с модальными окнами
function showStatusConfirmModal(newStatus) {
    console.log('Показ модального окна подтверждения');
    document.getElementById('statusConfirmModal').style.display = 'flex';
}

function closeStatusConfirmModal() {
    console.log('Закрытие модального окна подтверждения');
    document.getElementById('statusConfirmModal').style.display = 'none';
}

function showStatusSuccessModal(newStatus) {
    console.log('Показ модального окна успеха');
    document.getElementById('statusSuccessModal').style.display = 'flex';
}

function closeStatusSuccessModal() {
    console.log('Закрытие модального окна успеха и перезагрузка');
    document.getElementById('statusSuccessModal').style.display = 'none';
    // Обновляем страницу
    location.reload();
}

// Функция получения текста статуса
function getStatusText(status) {
    const statusMap = {
        'pending': 'Ожидает обработки',
        'processing': 'В обработке',
        'completed': 'Выполнен',
        'cancelled': 'Отменен'
    };
    return statusMap[status] || status;
}

// Функция обновления статуса заказа
function updateOrderStatus(newStatus) {
    console.log('Начало обновления статуса');
    const orderId = {{ $order->id }};
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    console.log('Данные для запроса:', { orderId, newStatus, token: token ? 'found' : 'not found' });
    
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        console.log('Ответ получен:', response.status, response.statusText);
        return response.json();
    })
    .then(data => {
        console.log('Данные обработаны:', data);
        if (data.success) {
            // Закрываем модальное окно подтверждения
            closeStatusConfirmModal();
            // Показываем модальное окно успеха
            showStatusSuccessModal(newStatus);
        } else {
            alert('Ошибка при обновлении статуса: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при обновлении статуса: ' + error.message);
    });
}

// Функции для управления отзывами
function approveReview(orderId) {
    showApproveModal(orderId);
}

function deleteReview(orderId) {
    showDeleteModal(orderId);
}

function showApproveModal(orderId) {
    const modal = document.getElementById('approveModal');
    const orderIdInput = document.getElementById('approveOrderId');
    orderIdInput.value = orderId;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideApproveModal() {
    const modal = document.getElementById('approveModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function confirmApprove() {
    const orderId = document.getElementById('approveOrderId').value;
    hideApproveModal();
    
    fetch(`/admin/orders/${orderId}/review/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при одобрении отзыва', 'error');
    });
}

function showDeleteModal(orderId) {
    const modal = document.getElementById('deleteModal');
    const orderIdInput = document.getElementById('deleteOrderId');
    orderIdInput.value = orderId;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function confirmDelete() {
    const orderId = document.getElementById('deleteOrderId').value;
    hideDeleteModal();
    
    fetch(`/admin/orders/${orderId}/review`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при удалении отзыва', 'error');
    });
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

// Обработчики для модальных окон отзывов
document.addEventListener('DOMContentLoaded', function() {
    const approveModal = document.getElementById('approveModal');
    const deleteModal = document.getElementById('deleteModal');
    
    // Закрытие по клику на фон
    approveModal.addEventListener('click', function(e) {
        if (e.target === approveModal) {
            hideApproveModal();
        }
    });
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            hideDeleteModal();
        }
    });
    
    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (approveModal.style.display === 'flex') {
                hideApproveModal();
            }
            if (deleteModal.style.display === 'flex') {
                hideDeleteModal();
            }
        }
    });
});

</script>

<style>
/* Стили для отзывов */
.review-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.review-info {
    flex: 1;
}

.review-author strong {
    color: #1f2937;
    font-size: 16px;
    display: block;
    margin-bottom: 4px;
}

.review-email {
    color: #6b7280;
    font-size: 14px;
}

.review-meta {
    display: flex;
    gap: 15px;
    align-items: center;
}

.review-rating {
    display: flex;
    gap: 2px;
}

.star {
    color: #d1d5db;
    font-size: 16px;
}

.star.active {
    color: #fbbf24;
}

.review-date {
    color: #6b7280;
    font-size: 12px;
}

.review-status {
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: flex-end;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.status-approved {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-pending {
    background: #fefce8;
    color: #854d0e;
    border: 1px solid #fde68a;
}

.status-warning {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.review-content {
    margin-bottom: 20px;
}

.review-text h4,
.review-original h4 {
    color: #374151;
    font-size: 14px;
    margin-bottom: 8px;
}

.review-text p {
    color: #1f2937;
    line-height: 1.6;
    margin: 0;
}

.review-original {
    margin-top: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.original-text {
    color: #6b7280;
    font-style: italic;
    margin: 0;
}

.review-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.empty-review {
    text-align: center;
    padding: 40px 20px;
    color: #6b7280;
}

.empty-review .empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.empty-review h3 {
    color: #374151;
    margin-bottom: 8px;
}

.empty-review p {
    margin: 0;
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
    .review-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .review-status {
        align-items: flex-start;
    }
    
    .review-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .notification {
        right: 10px;
        left: 10px;
        min-width: auto;
        max-width: none;
    }
}

/* Стили для модальных окон отзывов */
.modal-overlay {
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

.modal-overlay[style*="flex"] {
    opacity: 1;
    visibility: visible;
}

.modal {
    background: white;
    border-radius: 12px;
    max-width: 400px;
    width: 90%;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-overlay[style*="flex"] .modal {
    transform: scale(1);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    color: #1f2937;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.modal-body {
    padding: 30px 20px;
    text-align: center;
}

.modal-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.modal-body p {
    margin: 0 0 10px 0;
    color: #374151;
    font-size: 16px;
    font-weight: 500;
}

.modal-subtitle {
    color: #6b7280 !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    margin-bottom: 0 !important;
}

.modal-warning {
    color: #dc2626 !important;
    font-weight: 500 !important;
    font-size: 14px !important;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 100px;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

@media (max-width: 768px) {
    .modal {
        width: 95%;
        margin: 20px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
@endsection
