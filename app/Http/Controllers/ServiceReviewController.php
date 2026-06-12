<?php

namespace App\Http\Controllers;

use App\Models\ServiceReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceReviewController extends Controller
{
    // Показать форму отзыва о сервисе
    public function create()
    {
        return view('service-reviews.create');
    }
    
    // Сохранить отзыв о сервисе
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
        ]);
        
        // Создаем отзыв с фильтрацией
        $reviewData = array_merge($validated, [
            'user_id' => Auth::id(),
        ]);
        
        $review = ServiceReview::createWithFilter($reviewData);
        
        // Если отзыв содержит мат, он не будет автоматически одобрен
        if ($review->contains_profanity) {
            return response()->json([
                'success' => true, 
                'message' => 'Отзыв отправлен на модерацию (обнаружен нецензурный контент)',
                'requires_moderation' => true
            ]);
        }
        
        // Автоматически одобряем отзывы без мата
        $review->is_approved = true;
        $review->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Отзыв успешно опубликован!'
        ]);
    }
    
    // Показать все отзывы о сервисе
    public function index()
    {
        $reviews = ServiceReview::with('user')
            ->latest()
            ->paginate(10);
            
        $averageRating = ServiceReview::getAverageRating();
        
        return view('service-reviews.index', compact('reviews', 'averageRating'));
    }
    
    // Показать отзывы для главной страницы
    public function forHomepage()
    {
        $reviews = ServiceReview::approved()
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();
            
        return $reviews;
    }
}
