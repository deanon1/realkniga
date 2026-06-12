<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create()
    {
        $cart = session('cart');
        $user = Auth::user();
        
        return view('order.create', compact('cart', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'delivery_type' => 'required|in:belarusian_post,euro_post',
        ]);

        // Дополнительная валидация в зависимости от способа доставки
        if ($request->delivery_type === 'belarusian_post') {
            $request->validate([
                'address' => 'required|string|max:255',
                'region' => 'required|string|max:255',
            ]);
        } else {
            $request->validate([
                'euro_post_address' => 'required|string|max:255',
            ]);
        }

        $cart = session('cart');
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Формируем адрес в зависимости от способа доставки
        $deliveryAddress = '';
        if ($request->delivery_type === 'belarusian_post') {
            $deliveryAddress = $request->address . ', ' . $request->region;
        } else {
            $deliveryAddress = 'Европочта: ' . $request->euro_post_address;
        }

        $order = Order::create([
            'customer_name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $deliveryAddress,
            'region' => $request->delivery_type === 'belarusian_post' ? $request->region : null,
            'total' => $total * 100, // Сохраняем в копейках
            'user_id' => auth()->id(),
            'cart' => $cart, // Сохраняем корзину как JSON
            'status' => 'pending',
            'delivery_type' => $request->delivery_type,
            'euro_post_address' => $request->delivery_type === 'euro_post' ? $request->euro_post_address : null,
            'payment_type' => 'cash_on_delivery', // Всегда наложенный платеж
        ]);

        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $id,
                'quantity' => $item['quantity'],
                'price' => $item['price'] * 100, // Сохраняем в копейках
            ]);
        }

        session()->forget('cart');

        return redirect('/')
        ->with('success', 'Ваш заказ оформлен! Мы свяжемся с вами в ближайшее время.');
    }
}
