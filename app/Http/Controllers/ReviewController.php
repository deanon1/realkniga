<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Показать форму создания отзыва
    public function create($orderId)
    {
        $order = Order::with('items.book')->findOrFail($orderId);
        
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        // Проверяем, что заказ выполнен
        if ($order->status !== 'completed') {
            abort(403, 'Отзыв можно оставить только для выполненного заказа');
        }
        
        // Проверяем, что отзыв еще не оставлен
        $existingReview = Review::where('order_id', $orderId)->first();
        if ($existingReview) {
            return redirect()->route('profile.index')->with('info', 'Вы уже оставили отзыв на этот заказ');
        }
        
        return view('reviews.create', compact('order'));
    }
    
    // Сохранить отзыв
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Проверяем, что заказ принадлежит пользователю
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        // Проверяем, что отзыв еще не оставлен
        $existingReview = Review::where('order_id', $orderId)->first();
        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'Вы уже оставили отзыв на этот заказ']);
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
            'book_id' => 'nullable|exists:books,id'
        ]);
        
        // Создаем отзыв с фильтрацией
        $reviewData = array_merge($validated, [
            'user_id' => auth()->id(),
            'order_id' => $orderId,
        ]);
        
        $review = Review::createWithFilter($reviewData);
        
        // Если отзыв содержит мат, он не будет автоматически одобрен
        if ($review->contains_profanity) {
            return response()->json([
                'success' => true, 
                'message' => 'Отзыв отправлен на модерацию (обнаружен нецензурный контент)',
                'requires_moderation' => true,
                'redirect_to' => route('profile.index')
            ]);
        }
        
        // Автоматически одобряем отзывы без мата
        $review->is_approved = true;
        $review->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Отзыв успешно опубликован!',
            'redirect_to' => route('profile.index')
        ]);
    }
    
    // Показать отзывы пользователя
    public function userReviews()
    {
        $reviews = Review::where('user_id', auth()->id())
            ->with(['order', 'book'])
            ->latest()
            ->paginate(10);
            
        return view('reviews.user', compact('reviews'));
    }
    
    // Показать отзывы для книги
    public function bookReviews($bookId)
    {
        $reviews = Review::approved()
            ->where('book_id', $bookId)
            ->with('user')
            ->latest()
            ->paginate(10);
            
        $averageRating = Review::getAverageRating($bookId);
        
        return view('reviews.book', compact('reviews', 'averageRating', 'bookId'));
    }
    
    // Показать отзывы о заказах для главной страницы
    public function forHomepage()
    {
        $reviews = Review::where('is_approved', true)
            ->with(['user', 'book'])
            ->latest()
            ->limit(6)
            ->get();
            
        return $reviews;
    }
}
