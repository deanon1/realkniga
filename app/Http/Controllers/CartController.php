<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add($id)
    {
        $book = Book::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'title' => $book->title,
                'author' => $book->author ?? 'Неизвестный автор',
                'price' => $book->price,
                'quantity' => 1,
                'image' => $book->image ?? '/images/default-book.jpg'
            ];
        }

        session(['cart' => $cart]);
        return redirect('/cart');
    }

    public function index()
    {
        $cart = session('cart', []);
        
        // Очищаем и обновляем корзину от старых записей
        $cleanedCart = [];
        foreach ($cart as $id => $item) {
            try {
                $book = Book::findOrFail($id);
                $cleanedCart[$id] = [
                    'title' => $item['title'] ?? $book->title,
                    'author' => $item['author'] ?? $book->author ?? 'Неизвестный автор',
                    'price' => $item['price'] ?? $book->price,
                    'quantity' => $item['quantity'] ?? 1,
                    'image' => $item['image'] ?? $book->image ?? '/images/default-book.jpg'
                ];
            } catch (\Exception $e) {
                // Пропускаем несуществующие книги
                continue;
            }
        }
        
        // Сохраняем очищенную корзину
        if ($cleanedCart !== $cart) {
            session(['cart' => $cleanedCart]);
            $cart = $cleanedCart;
        }
        
        return view('cart-new', compact('cart'));
    }

    public function remove($id)
    {
        \Log::info('Cart remove called for ID: ' . $id);
        \Log::info('Request method: ' . request()->method());
        \Log::info('Is AJAX: ' . (request()->ajax() ? 'YES' : 'NO'));
        
        $cart = session('cart', []);
        \Log::info('Cart before removal: ' . json_encode(array_keys($cart)));
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
            \Log::info('Cart after removal: ' . json_encode(array_keys($cart)));
            \Log::info('Item removed successfully');
        } else {
            \Log::info('Item not found in cart');
        }
        
        if (request()->ajax()) {
            \Log::info('Returning JSON response');
            return response()->json(['success' => true]);
        }
        
        \Log::info('Returning redirect');
        return back();
    }
    
    public function clear()
    {
        session()->forget('cart');
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect('/cart');
    }
    
    public function update(Request $request, $id)
    {
        $cart = session('cart');
        $change = $request->input('change', 0);
        
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $change;
            
            if ($cart[$id]['quantity'] <= 0) {
                unset($cart[$id]);
            }
            
            session(['cart' => $cart]);
        }
        
        return response()->json(['success' => true]);
    }
}

