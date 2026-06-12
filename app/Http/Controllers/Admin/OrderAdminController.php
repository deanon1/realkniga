<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;

class OrderAdminController extends Controller
{
    // Список заказов
    public function index()
    {
        $orders = \App\Models\Order::with(['user', 'items.book'])->latest()->get();
        return view('admin.orders.index-new', compact('orders'));
    }

    // Просмотр заказа
    public function show($id)
    {
        $order = \App\Models\Order::with(['user', 'items.book'])->findOrFail($id);
        $review = Review::where('order_id', $id)->with('user')->first();
        return view('admin.orders.show', compact('order', 'review'));
    }

    // Форма добавления нового заказа
    public function create()
    {
        return view('admin.orders.create');
    }

    // Сохраняем новый заказ
    public function store(Request $request)
    {
        Order::create($request->all());
        return redirect('/admin/orders')->with('success', 'Заказ добавлен');
    }
    
    // Обновление статуса заказа
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'comment' => 'nullable|string'
        ]);
        
        $order->update($validated);
        
        return response()->json(['success' => true]);
    }

    // Удаление заказа
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            
            return response()->json(['success' => true, 'message' => 'Заказ успешно удалён']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка при удалении заказа: ' . $e->getMessage()]);
        }
    }

    // Одобрить отзыв
    public function approveReview($orderId)
    {
        try {
            $review = Review::where('order_id', $orderId)->firstOrFail();
            $review->is_approved = true;
            $review->save();
            
            return response()->json(['success' => true, 'message' => 'Отзыв одобрен']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }

    // Удалить отзыв
    public function deleteReview($orderId)
    {
        try {
            $review = Review::where('order_id', $orderId)->firstOrFail();
            $review->delete();
            
            return response()->json(['success' => true, 'message' => 'Отзыв удален']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
}
