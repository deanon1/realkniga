<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('orders')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('orders')->findOrFail($id);
        $ordersCount = $user->orders->count();
        $totalSpent = $user->orders->sum('total') / 100;
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_admin' => $user->is_admin,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at->format('d.m.Y H:i')
            ],
            'orders_count' => $ordersCount,
            'total_spent' => number_format($totalSpent, 2, ',', ' ')
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:user,operator,admin',
            'is_active' => 'sometimes|boolean'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_admin = $validated['role'] === 'admin';
        $user->is_active = isset($validated['is_active']) ? $validated['is_active'] : $user->is_active;
        
        $user->save();

        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Переключаем статус активности
        $user->is_active = !$user->is_active;
        
        $user->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Нельзя удалить самого себя
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Нельзя удалить самого себя'], 403);
        }
        
        $user->delete();

        return response()->json(['success' => true]);
    }
}
