<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\BookAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\ProfileController;

// ----------------- Главная и домашняя -----------------
Route::get('/', [HomeController::class, 'index'])->name('home');

// ----------------- Каталог -----------------
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/book/{id}', [CatalogController::class, 'showBookInfo'])->name('catalog.book.info');

// ----------------- Корзина -----------------
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// ----------------- Оформление заказа -----------------
Route::get('/order', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// ----------------- Профиль пользователя -----------------
Route::middleware(['auth'])->prefix('profile')->group(function() {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password.update');
});

// ----------------- Админка и Операторы -----------------
Route::middleware(['auth', 'operator'])->prefix('admin')->group(function() {
    
    // Главная страница
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Книги
    Route::get('/books', [BookAdminController::class, 'index'])->name('admin.books');
    Route::get('/books/{id}', [BookAdminController::class, 'show']);
    Route::get('/books/{id}/edit', [BookAdminController::class, 'edit'])->name('admin.books.edit');
    Route::put('/books/{id}', [BookAdminController::class, 'update'])->name('admin.books.update');
    Route::delete('/books/{id}', [BookAdminController::class, 'destroy'])->name('admin.books.destroy');
    Route::get('/books/create', [BookAdminController::class, 'create'])->name('admin.books.create');
    Route::post('/books', [BookAdminController::class, 'store'])->name('admin.books.store');
    Route::get('/add-new-book', [BookAdminController::class, 'create'])->name('admin.books.create.alt');

    // Заказы
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderAdminController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderAdminController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderAdminController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::delete('/orders/{id}', [\App\Http\Controllers\Admin\OrderAdminController::class, 'destroy'])->name('admin.orders.destroy');
    
    // Управление отзывами
    Route::post('/orders/{order}/review/approve', [\App\Http\Controllers\Admin\OrderAdminController::class, 'approveReview'])->name('admin.orders.review.approve');
    Route::delete('/orders/{order}/review', [\App\Http\Controllers\Admin\OrderAdminController::class, 'deleteReview'])->name('admin.orders.review.delete');
    
    // Пользователи (просмотр)
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
    
    // Статистика
    Route::get('/stats', [App\Http\Controllers\Admin\StatsController::class, 'index'])->name('admin.stats');
    Route::get('/stats/data', [App\Http\Controllers\Admin\StatsController::class, 'getData'])->name('admin.stats.data');
    Route::get('/stats/sales', [App\Http\Controllers\Admin\StatsController::class, 'getSalesData'])->name('admin.stats.sales');
    Route::get('/stats/popular-books', [App\Http\Controllers\Admin\StatsController::class, 'getPopularBooksData'])->name('admin.stats.popular-books');
});

// Пользователи - редактирование (только для администраторов)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function() {
    Route::post('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{id}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
});

// ----------------- Авторизация -----------------
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/resend-code', [AuthController::class, 'resendCode'])->name('resend.code');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ----------------- Отзывы -----------------
Route::middleware(['auth'])->group(function() {
    Route::get('/reviews/create/{order}', [App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews/{order}', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/profile/reviews', [App\Http\Controllers\ReviewController::class, 'userReviews'])->name('reviews.user');
});

Route::get('/books/{book}/reviews', [App\Http\Controllers\ReviewController::class, 'bookReviews'])->name('reviews.book');

// API для главной страницы
Route::get('/api/order-reviews', [App\Http\Controllers\ReviewController::class, 'forHomepage'])->name('api.order.reviews.homepage');

// ----------------- AI-ассистент -----------------
Route::get('/ai-assistant', [App\Http\Controllers\AIBookAssistantController::class, 'index'])->name('ai.assistant.index');
Route::post('/ai-assistant/recommend', [App\Http\Controllers\AIBookAssistantController::class, 'recommend'])->name('ai.assistant.recommend');

// ----------------- Отзывы о сервисе -----------------
Route::middleware(['auth'])->group(function() {
    Route::get('/reviews/service/create', [App\Http\Controllers\ServiceReviewController::class, 'create'])->name('service.reviews.create');
    Route::post('/reviews/service', [App\Http\Controllers\ServiceReviewController::class, 'store'])->name('service.reviews.store');
    Route::get('/reviews/service', [App\Http\Controllers\ServiceReviewController::class, 'index'])->name('service.reviews.index');
});

// API для главной страницы
Route::get('/api/service-reviews', [App\Http\Controllers\ServiceReviewController::class, 'forHomepage'])->name('api.service.reviews.homepage');

    