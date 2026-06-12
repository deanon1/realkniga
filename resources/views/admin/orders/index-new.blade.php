@extends('layouts.app')

@section('title', 'Управление заказами - Админ панель')

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Управление заказами</h1>
            <p class="admin-subtitle">Просмотр и управление заказами клиентов</p>
        </div>
        <div class="admin-stats">
            <div class="stat-mini">
                <span class="stat-number">{{ $orders->count() }}</span>
                <span class="stat-label">Всего заказов</span>
            </div>
            <div class="stat-mini">
                <span class="stat-number">{{ $orders->where('status', 'pending')->count() }}</span>
                <span class="stat-label">Новых</span>
            </div>
            <div class="stat-mini">
                <span class="stat-number">{{ number_format($orders->sum('total') / 100, 2, ',', ' ') }} BYN</span>
                <span class="stat-label">Общая сумма</span>
            </div>
        </div>
        <div class="admin-actions">
            <a href="/admin" class="btn-secondary">
                <span class="btn-icon">←</span>
                Назад к панели
            </a>
        </div>
    </div>

    <div class="admin-content">
        <!-- Фильтры -->
        <div class="admin-filters">
            <div class="filter-group">
                <label class="filter-label">Поиск:</label>
                <input type="text" id="orderSearch" placeholder="Поиск по ID или имени..." class="search-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">Статус:</label>
                <select id="statusFilter" class="filter-select">
                    <option value="">Все статусы</option>
                    <option value="pending">Ожидает</option>
                    <option value="processing">В обработке</option>
                    <option value="completed">Выполнен</option>
                    <option value="cancelled">Отменен</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Дата:</label>
                <select id="dateFilter" class="filter-select">
                    <option value="">Все даты</option>
                    <option value="today">Сегодня</option>
                    <option value="week">За неделю</option>
                    <option value="month">За месяц</option>
                </select>
            </div>
        </div>

        <!-- Таблица заказов -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Клиент</th>
                        <th>Товары</th>
                        <th>Сумма</th>
                        <th>Доставка</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @foreach($orders as $order)
                    <tr class="order-row" data-id="{{ $order->id }}" data-name="{{ $order->user ? $order->user->name : ($order->customer_name ?? 'Гость') }}" data-status="{{ $order->status }}" data-date="{{ $order->created_at->format('Y-m-d') }}">
                        <td>
                            <span class="order-id">#{{ $order->id }}</span>
                        </td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-name">{{ $order->user ? $order->user->name : ($order->customer_name ?? 'Гость') }}</div>
                                <div class="customer-email">{{ $order->user ? $order->user->email : ($order->email ?? 'guest@example.com') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="order-items">
                                @php
                                    $cartItems = $order->cart;
                                    $itemCount = 0;
                                    
                                    if (is_array($cartItems)) {
                                        $itemCount = count($cartItems);
                                    } elseif ($order->items && $order->items->count() > 0) {
                                        $itemCount = $order->items->sum('quantity');
                                    }
                                @endphp
                                <span class="items-count">{{ $itemCount }} {{ $itemCount == 1 ? 'товар' : ($itemCount >= 2 && $itemCount <= 4 ? 'товара' : 'товаров') }}</span>
                                @if($itemCount > 0)
                                <button class="btn-view-items" onclick="toggleOrderItems({{ $order->id }})">📋</button>
                                @endif
                            </div>
                            <div id="orderItems-{{ $order->id }}" class="order-items-list" style="display: none;">
                                @if($cartItems && is_array($cartItems))
                                    @foreach($cartItems as $item)
                                    <div class="order-item">
                                        <span class="item-title">{{ $item['title'] ?? 'Неизвестный товар' }}</span>
                                        <span class="item-quantity">× {{ $item['quantity'] ?? 1 }}</span>
                                        <span class="item-price">{{ number_format(($item['price'] ?? 0) / 100, 2, ',', ' ') }} BYN</span>
                                    </div>
                                    @endforeach
                                @elseif($order->items && $order->items->count() > 0)
                                    @foreach($order->items as $item)
                                    <div class="order-item">
                                        <span class="item-title">{{ $item->book->title ?? 'Неизвестный товар' }}</span>
                                        <span class="item-quantity">× {{ $item->quantity }}</span>
                                        <span class="item-price">{{ number_format($item->price / 100, 2, ',', ' ') }} BYN</span>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="order-total">{{ number_format($order->total / 100, 2, ',', ' ') }} BYN</span>
                        </td>
                        <td>
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
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date-main">{{ $order->created_at->format('d.m.Y') }}</div>
                                <div class="date-time">{{ $order->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="btn-status" onclick="updateOrderStatus({{ $order->id }})">
                                    <span>🔄</span>
                                </button>
                                <a href="/admin/orders/{{ $order->id }}" class="btn-view-small">
                                    <span>👁️</span>
                                </a>
                                <button class="btn-delete-small" onclick="deleteOrder({{ $order->id }})">
                                    <span>🗑️</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($orders->isEmpty())
            <div class="no-data">
                <div class="no-data-icon">📋</div>
                <h3>Заказы не найдены</h3>
                <p>Пока нет заказов в системе</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Модальное окно удаления заказа -->
<div id="deleteOrderModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Подтверждение удаления</h3>
            <button class="modal-close" onclick="hideDeleteOrderModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">🗑️</div>
            <p>Вы уверены, что хотите удалить этот заказ?</p>
            <input type="hidden" id="deleteOrderId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideDeleteOrderModal()">Отмена</button>
            <button class="btn btn-danger" onclick="confirmDeleteOrder()">Удалить</button>
        </div>
    </div>
</div>

<!-- Модальное окно изменения статуса -->
<div id="statusModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3>Изменить статус заказа</h3>
            <button class="modal-close" onclick="closeStatusModal()">×</button>
        </div>
        <div class="modal-body">
            <form id="statusForm">
                @csrf
                <input type="hidden" name="order_id" id="orderId">
                <div class="form-group">
                    <label class="form-label">Новый статус</label>
                    <select name="status" class="form-input" required>
                        <option value="pending">Ожидает обработки</option>
                        <option value="processing">В обработке</option>
                        <option value="completed">Выполнен</option>
                        <option value="cancelled">Отменен</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Комментарий</label>
                    <textarea name="comment" class="form-textarea" rows="3" placeholder="Добавьте комментарий..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeStatusModal()">Отмена</button>
            <button type="button" class="btn-primary" onclick="saveOrderStatus()">Сохранить</button>
        </div>
    </div>
</div>

<script>
// Поиск и фильтрация
document.getElementById('orderSearch').addEventListener('input', filterOrders);
document.getElementById('statusFilter').addEventListener('change', filterOrders);
document.getElementById('dateFilter').addEventListener('change', filterOrders);

function filterOrders() {
    const searchTerm = document.getElementById('orderSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const rows = document.querySelectorAll('.order-row');
    
    rows.forEach(row => {
        const id = row.dataset.id;
        const name = row.dataset.name.toLowerCase();
        const status = row.dataset.status;
        const date = row.dataset.date;
        
        const matchesSearch = !searchTerm || id.includes(searchTerm) || name.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        
        let matchesDate = true;
        if (dateFilter) {
            const orderDate = new Date(date);
            const today = new Date();
            
            switch(dateFilter) {
                case 'today':
                    matchesDate = orderDate.toDateString() === today.toDateString();
                    break;
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matchesDate = orderDate >= weekAgo;
                    break;
                case 'month':
                    const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matchesDate = orderDate >= monthAgo;
                    break;
            }
        }
        
        row.style.display = matchesSearch && matchesStatus && matchesDate ? '' : 'none';
    });
}

function toggleOrderItems(orderId) {
    const itemsList = document.getElementById(`orderItems-${orderId}`);
    itemsList.style.display = itemsList.style.display === 'none' ? 'block' : 'none';
}

function updateOrderStatus(orderId) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

function saveOrderStatus() {
    const form = document.getElementById('statusForm');
    const formData = new FormData(form);
    
    fetch(`/admin/orders/${formData.get('order_id')}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Ошибка при обновлении статуса');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при обновлении статуса');
    });
}

function deleteOrder(orderId) {
    showDeleteOrderModal(orderId);
}

function showDeleteOrderModal(orderId) {
    const modal = document.getElementById('deleteOrderModal');
    const orderIdInput = document.getElementById('deleteOrderId');
    
    if (!modal || !orderIdInput) {
        console.error('Modal elements not found');
        return;
    }
    
    orderIdInput.value = orderId;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideDeleteOrderModal() {
    const modal = document.getElementById('deleteOrderModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function confirmDeleteOrder() {
    const orderId = document.getElementById('deleteOrderId').value;
    hideDeleteOrderModal();
    
    fetch(`/admin/orders/${orderId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
            if (data.success) {
                // Показываем уведомление об успехе
                const notification = document.createElement('div');
                notification.className = 'notification notification-success';
                notification.innerHTML = `
                    <div class="notification-icon">✅</div>
                    <div class="notification-content">
                        <div class="notification-title">Успешно</div>
                        <div class="notification-message">${data.message}</div>
                    </div>
                    <button class="notification-close" onclick="this.parentElement.remove()">×</button>
                `;
                
                document.body.appendChild(notification);
                
                // Показываем анимацию
                setTimeout(() => {
                    notification.classList.add('notification-show');
                }, 100);
                
                // Автоматически скрываем через 3 секунды
                setTimeout(() => {
                    notification.classList.add('notification-hide');
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }, 3000);
                
                // Обновляем страницу
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                alert('Ошибка при удалении заказа');
            }
        })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при удалении заказа');
    });
}

// Обработчики для модального окна удаления заказа
document.addEventListener('DOMContentLoaded', function() {
    const deleteOrderModal = document.getElementById('deleteOrderModal');
    
    // Проверяем что элемент существует
    if (deleteOrderModal) {
        // Закрытие по клику на фон
        deleteOrderModal.addEventListener('click', function(e) {
            if (e.target === deleteOrderModal) {
                hideDeleteOrderModal();
            }
        });
        
        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (deleteOrderModal.style.display === 'flex') {
                    hideDeleteOrderModal();
                }
            }
        });
    }
});
</script>
@endsection
