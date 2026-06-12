<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // если работаешь с заказами

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard-new');
    }

    public function orders()
    {
        $orders = \App\Models\Order::with('user')->latest()->get();
        return view('admin.orders.index-new', compact('orders'));
    }

    public function destroyOrder($id)
{
    try {
        $order = Order::findOrFail($id);
        $order->delete();
        
        return redirect()->route('admin.orders')
            ->with('success', 'Заказ успешно удален');
    } catch (\Exception $e) {
        return redirect()->route('admin.orders')
            ->with('error', 'Ошибка при удалении заказа: ' . $e->getMessage());
    }
}

}
