@extends('layouts.app')

@section('title', 'Статистика - Админ панель')

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Статистика</h1>
            <p class="admin-subtitle">Аналитика и отчеты онлайн-платформы</p>
        </div>
        <div class="admin-actions">
            <a href="/admin" class="btn-secondary">
                <span class="btn-icon">←</span>
                Назад к панели
            </a>
        </div>
    </div>

    <div class="admin-content">
        <!-- Основная статистика -->
        <div class="main-stats">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalBooks">{{ \App\Models\Book::count() }}</div>
                        <div class="stat-label">Всего книг</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalUsers">{{ \App\Models\User::count() }}</div>
                        <div class="stat-label">Всего пользователей</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🛒</div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalOrders">{{ \App\Models\Order::count() }}</div>
                        <div class="stat-label">Всего заказов</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalRevenue">{{ number_format(\App\Models\Order::sum('total') / 100, 2, ',', ' ') }} BYN</div>
                        <div class="stat-label">Общая выручка</div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Детальная статистика -->
        <div class="detailed-stats">
            <h2 class="section-title">Детальная статистика</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ \App\Models\Order::where('status', 'pending')->count() }}</div>
                        <div class="stat-label">Ожидают обработки</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">⚙️</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ \App\Models\Order::where('status', 'processing')->count() }}</div>
                        <div class="stat-label">В обработке</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ \App\Models\Order::where('status', 'completed')->count() }}</div>
                        <div class="stat-label">Выполнено</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">❌</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ \App\Models\Order::where('status', 'cancelled')->count() }}</div>
                        <div class="stat-label">Отменено</div>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ number_format(\App\Models\Book::avg('price') ?? 0, 2, ',', ' ') }}</div>
                        <div class="stat-label">Средняя цена книги</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📖</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ number_format(\App\Models\Book::max('price') ?? 0, 2, ',', ' ') }}</div>
                        <div class="stat-label">Самая дорогая книга</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-content">
                        <div class="stat-number">{{ number_format(\App\Models\Book::min('price') ?? 0, 2, ',', ' ') }}</div>
                        <div class="stat-label">Самая дешевая книга</div>
                    </div>
                </div>
            </div>
        </div>

            </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Глобальные переменные
let salesChart, popularBooksChart;

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('Страница статистики загружена');
    
    // Инициализация графиков
    initSalesChart();
    initPopularBooksChart();
    
    // Загружаем начальные данные
    loadInitialData();
    
    // Обработчики для селектов периодов
    document.getElementById('salesChartPeriod').addEventListener('change', function() {
        console.log('Период продаж изменен:', this.value);
        loadSalesData(this.value);
    });
    
    document.getElementById('popularBooksPeriod').addEventListener('change', function() {
        console.log('Период популярных книг изменен:', this.value);
        loadPopularBooksData(this.value);
    });
});

// Загрузка начальных данных
function loadInitialData() {
    fetch('/admin/stats/data?days=7')
        .then(response => response.json())
        .then(data => {
            console.log('Начальные данные получены:', data);
            updateCharts(data);
        })
        .catch(error => {
            console.error('Ошибка загрузки начальных данных:', error);
        });
}

// Загрузка данных продаж
function loadSalesData(days) {
    fetch(`/admin/stats/sales?days=${days}`)
        .then(response => response.json())
        .then(data => {
            console.log('Данные продаж получены:', data);
            if (data.sales_data) {
                updateSalesChart(data.sales_data);
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки данных продаж:', error);
        });
}


</script>
@endsection
