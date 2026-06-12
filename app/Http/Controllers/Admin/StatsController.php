<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        return view('admin.stats.index');
    }

    public function getData(Request $request)
    {
        $days = $request->get('days', 30);
        
        $startDate = $days === 'all' ? null : now()->subDays($days);
        
        $booksQuery = Book::query();
        $usersQuery = User::query();
        $ordersQuery = Order::query();
        
        if ($startDate) {
            $booksQuery->where('created_at', '>=', $startDate);
            $usersQuery->where('created_at', '>=', $startDate);
            $ordersQuery->where('created_at', '>=', $startDate);
        }
        
        // Получаем предыдущий период для сравнения
        $prevStartDate = $days === 'all' ? null : now()->subDays($days * 2);
        $prevEndDate = $startDate;
        
        $prevBooksQuery = Book::query();
        $prevUsersQuery = User::query();
        $prevOrdersQuery = Order::query();
        
        if ($prevStartDate && $prevEndDate) {
            $prevBooksQuery->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
            $prevUsersQuery->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
            $prevOrdersQuery->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
        }
        
        $currentBooks = $booksQuery->count();
        $currentUsers = $usersQuery->count();
        $currentOrders = $ordersQuery->count();
        
        // Правильная конвертация суммы
        $currentRevenueRaw = $ordersQuery->sum('total');
        $currentRevenue = $currentRevenueRaw;
        
        $prevBooks = $prevBooksQuery->count();
        $prevUsers = $prevUsersQuery->count();
        $prevOrders = $prevOrdersQuery->count();
        
        // Правильная конвертация суммы для предыдущего периода
        $prevRevenueRaw = $prevOrdersQuery->sum('total');
        $prevRevenue = $prevRevenueRaw;
        
        // Правильная конвертация общей суммы
        $totalRevenueRaw = Order::sum('total');
        $totalRevenue = $totalRevenueRaw;
        
        return response()->json([
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_revenue' => number_format($totalRevenue, 2, ',', ' ') . ' BYN',
            'books_change' => $prevBooks > 0 ? $currentBooks - $prevBooks : $currentBooks,
            'users_change' => $prevUsers > 0 ? $currentUsers - $prevUsers : $currentUsers,
            'orders_change' => $prevOrders > 0 ? $currentOrders - $prevOrders : $currentOrders,
            'revenue_change' => $prevRevenue > 0 ? round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1) . '%' : '0%',
            'sales_data' => $this->getSalesChartData($days),
            'popular_books' => $this->getPopularBooksData($days)
        ]);
    }

    public function getSalesData(Request $request)
    {
        $days = $request->get('days', 30);
        return response()->json(['sales_data' => $this->getSalesChartData($days)]);
    }

    public function getPopularBooksData(Request $request)
    {
        $days = $request->get('days', 30);
        $data = $this->getPopularBooksChartData($days);
        return response()->json(['popular_books' => $data]);
    }

    private function getSalesChartData($days)
    {
        $startDate = now()->subDays($days);
        
        $sales = Order::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];
        
        // Заполняем все даты в периоде
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d.m');
            
            $daySales = $sales->where('date', $date)->first();
            if ($daySales) {
                // Сумма уже в правильном формате
                $data[] = $daySales->total;
            } else {
                $data[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getPopularBooksChartData($days)
    {
        $startDate = now()->subDays($days);
        
        // Получаем популярные книги на основе заказов
        $popularBooks = Order::where('created_at', '>=', $startDate)
            ->whereNotNull('cart')
            ->get()
            ->map(function ($order) {
                $cart = json_decode($order->cart, true);
                return collect($cart)->map(function ($item) {
                    return [
                        'title' => $item['title'],
                        'revenue' => $item['price'] * $item['quantity']
                    ];
                });
            })
            ->flatten()
            ->groupBy('title')
            ->map(function ($items) {
                return $items->sum('revenue');
            })
            ->sortDesc()
            ->take(10);

        return [
            'labels' => $popularBooks->keys()->toArray(),
            'data' => $popularBooks->values()->toArray()
        ];
    }
}
