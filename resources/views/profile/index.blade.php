@extends('layouts.app')

@section('content')
<div class="profile-page">
    <div class="container">
        <div class="profile-header">
            <h1 class="profile-title">Мой профиль</h1>
            <p class="profile-subtitle">Управляйте вашей личной информацией и заказами</p>
        </div>
        
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-content">
            <!-- Боковая панель -->
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Аватар" class="avatar-image">
                        @else
                            {{ strtoupper(substr($user->name ?? $user->email, 0, 2)) }}
                        @endif
                    </div>
                    <h2 class="profile-name">{{ $user->name ?? 'Пользователь' }}</h2>
                    <p class="profile-email">{{ $user->email }}</p>
                    
                    <div class="profile-stats">
                        <div class="profile-stat">
                            <div class="profile-stat-value">{{ $orders->count() }}</div>
                            <div class="profile-stat-label">Заказов</div>
                        </div>
                        <div class="profile-stat">
                            <div class="profile-stat-value">{{ $orders->where('status', 'completed')->count() }}</div>
                            <div class="profile-stat-label">Выполнено</div>
                        </div>
                    </div>
                    
                    <div class="profile-actions">
                        <a href="{{ route('profile.edit') }}" class="profile-btn primary">
                            ✏️ Редактировать профиль
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="profile-btn">
                            🔒 Сменить пароль
                        </a>
                    </div>
                </div>
            </div>

            <!-- Основной контент -->
            <div class="profile-main">
                <!-- Личная информация -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2 class="section-title">Личная информация</h2>
                        <div class="section-actions">
                            <a href="{{ route('profile.edit') }}" class="section-btn">Редактировать</a>
                        </div>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Имя</div>
                            <div class="info-value {{ !$user->name ? 'empty' : '' }}">
                                {{ $user->name ?? 'Не указано' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Телефон</div>
                            <div class="info-value {{ !$user->phone ? 'empty' : '' }}">
                                {{ $user->phone ?? 'Не указан' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Адрес</div>
                            <div class="info-value {{ !$user->address ? 'empty' : '' }}">
                                {{ $user->address ?? 'Не указан' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Регион</div>
                            <div class="info-value {{ !$user->region ? 'empty' : '' }}">
                                {{ $user->region ?? 'Не указан' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Дата регистрации</div>
                            <div class="info-value">{{ $user->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Статус</div>
                            <div class="info-value">
                                @if($user->is_admin)
                                    <span class="status-badge admin">Администратор</span>
                                @else
                                    <span class="status-badge active">Активен</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- История заказов -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2 class="section-title">История заказов</h2>
                        <div class="section-actions">
                            <a href="{{ route('catalog') }}" class="section-btn">Сделать новый заказ</a>
                        </div>
                    </div>
                    
                    @if($orders->count() > 0)
                        <div class="orders-list">
                            @foreach($orders as $order)
                                <div class="order-item">
                                    <div class="order-header">
                                        <div class="order-info">
                                            <div class="order-number">Заказ #{{ $order->id }}</div>
                                            <div class="order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                                        </div>
                                        <div class="order-status">
                                            <div class="order-total">{{ number_format($order->total / 100, 2, ',', ' ') }} BYN</div>
                                            <span class="order-status-badge {{ $order->status }}">
                                                @if($order->status === 'pending') Новый
                                                @elseif($order->status === 'processing') В обработке
                                                @elseif($order->status === 'shipped') Отправлен
                                                @elseif($order->status === 'completed') Доставлен
                                                @elseif($order->status === 'cancelled') Отменен
                                                @else {{ $order->status }} @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-details">
                                        @if($order->customer_name)
                                            <div class="order-detail">
                                                <strong>Получатель:</strong> {{ $order->customer_name }}
                                            </div>
                                        @endif
                                        @if($order->phone)
                                            <div class="order-detail">
                                                <strong>Телефон:</strong> {{ $order->phone }}
                                            </div>
                                        @endif
                                        @if($order->address)
                                            <div class="order-detail">
                                                <strong>Адрес:</strong> {{ $order->address }}
                                                @if($order->region), {{ $order->region }}@endif
                                            </div>
                                        @endif
                                        @if($order->cart)
                                            @php
                                                if (is_array($order->cart)) {
                                                    $cartItems = $order->cart;
                                                } else {
                                                    $cartItems = json_decode($order->cart, true);
                                                }
                                                $itemsCount = is_array($cartItems) ? count($cartItems) : 0;
                                            @endphp
                                            @if($itemsCount > 0)
                                                <div class="order-detail">
                                                    <strong>Книги:</strong> {{ $itemsCount }} шт.
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <!-- Кнопка отзыва -->
                                    @if($order->status === 'completed' && !in_array($order->id, $userReviews))
                                        <div class="order-actions">
                                            <a href="{{ route('reviews.create', $order->id) }}" class="btn-review">
                                                <span class="btn-icon">⭐</span>
                                                Оставить отзыв
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if(in_array($order->id, $userReviews))
                                        <div class="review-status">
                                            <span class="review-left">✅ Отзыв оставлен</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <div class="empty-title">У вас пока нет заказов</div>
                            <div class="empty-text">Перейдите в каталог, чтобы сделать первый заказ</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.btn-review {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #78350f;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.2);
}

.btn-review:hover {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(251, 191, 36, 0.3);
    color: #78350f;
}

.btn-icon {
    font-size: 16px;
}

.review-status {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
    text-align: right;
}

.review-left {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0fdf4;
    color: #166534;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #bbf7d0;
}

@media (max-width: 768px) {
    .btn-review {
        width: 100%;
        justify-content: center;
    }
    
    .review-status {
        text-align: center;
    }
}
</style>
@endsection
