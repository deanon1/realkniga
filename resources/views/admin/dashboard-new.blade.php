@extends('layouts.app')

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Панель администратора</h1>
            <p class="admin-subtitle">Управление онлайн-платформой РеалКнига</p>
        </div>
        <div class="admin-user">
            <div class="user-avatar">
                <img src="{{ Auth::check() ? (Auth::user()->avatar ?? '/images/default-avatar.png') : '/images/default-avatar.png' }}" alt="Avatar">
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::check() ? Auth::user()->name : 'Гость' }}</div>
                <div class="user-role">Администратор</div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-content">
                <div class="stat-number">{{ \App\Models\Book::count() }}</div>
                <div class="stat-label">Всего книг</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-content">
                <div class="stat-number">{{ \App\Models\Order::count() }}</div>
                <div class="stat-label">Заказов</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-number">{{ \App\Models\User::count() }}</div>
                <div class="stat-label">Пользователей</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format(\App\Models\Order::sum('total') / 100, 2, ',', ' ') }} BYN</div>
                <div class="stat-label">Общая выручка</div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="quick-actions">
        <h2 class="section-title">Быстрые действия</h2>
        <div class="actions-grid">
            <a href="/admin/books" class="action-card">
                <div class="action-icon">📚</div>
                <div class="action-content">
                    <h3>Управление книгами</h3>
                    <p>Добавление, редактирование, удаление книг</p>
                </div>
            </a>
            <a href="/admin/orders" class="action-card">
                <div class="action-icon">📋</div>
                <div class="action-content">
                    <h3>Заказы</h3>
                    <p>Просмотр и управление заказами</p>
                </div>
            </a>
            <a href="/admin/users" class="action-card">
                <div class="action-icon">👥</div>
                <div class="action-content">
                    <h3>Пользователи</h3>
                    <p>Управление аккаунтами</p>
                </div>
            </a>
            <a href="/admin/stats" class="action-card">
                <div class="action-icon">📊</div>
                <div class="action-content">
                    <h3>Статистика</h3>
                    <p>Аналитика и отчеты</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Последние заказы -->
    <div class="recent-orders">
        <h2 class="section-title">Последние заказы</h2>
        <div class="orders-table">
            @php
                $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
            @endphp
            @if($recentOrders->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Клиент</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user ? $order->user->name : $order->customer_name }}</td>
                        <td>{{ number_format($order->total / 100, 2, ',', ' ') }} BYN</td>
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
                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <a href="/admin/orders/{{ $order->id }}" class="btn-view">Просмотр</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">
                <div class="no-data-icon">📋</div>
                <p>Пока нет заказов</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Последние книги -->
    <div class="recent-books">
        <h2 class="section-title">Последние добавленные книги</h2>
        <div class="books-grid">
            @php
                $recentBooks = \App\Models\Book::latest()->take(6)->get();
            @endphp
            @if($recentBooks->count() > 0)
            @foreach($recentBooks as $book)
            <div class="book-card">
                <div class="book-image">
                    <img src="{{ $book->image ?? '/images/default-book.jpg' }}" alt="{{ $book->title }}">
                </div>
                <div class="book-info">
                    <h3>{{ $book->title }}</h3>
                    <p>{{ $book->author }}</p>
                    <p class="book-price">{{ number_format($book->price, 2, ',', ' ') }} BYN</p>
                    <div class="book-actions">
                        <a href="/admin/books/{{ $book->id }}/edit" class="btn-edit">Редактировать</a>
                        <form method="POST" action="/admin/books/{{ $book->id }}" onsubmit="return confirm('Вы уверены, что хотите удалить эту книгу?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Удалить</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="no-data">
                <div class="no-data-icon">📚</div>
                <p>Пока нет книг</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
