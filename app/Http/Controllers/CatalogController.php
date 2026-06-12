<?php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        
        // Server-side filtering
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('author', 'like', '%' . $searchTerm . '%');
            });
        }
        
        if ($request->filled('genre')) {
            $query->where('genre', $request->input('genre'));
        }
        
        if ($request->filled('price_range')) {
            $priceRange = $request->input('price_range');
            switch ($priceRange) {
                case '0-10':
                    $query->where('price', '<', 10);
                    break;
                case '10-20':
                    $query->whereBetween('price', [10, 19.99]);
                    break;
                case '20-30':
                    $query->whereBetween('price', [20, 29.99]);
                    break;
                case '30-50':
                    $query->whereBetween('price', [30, 49.99]);
                    break;
                case '50+':
                    $query->where('price', '>=', 50);
                    break;
            }
        }
        
        if ($request->filled('year')) {
            $year = $request->input('year');
            if ($year === 'older') {
                $query->where('year', '<', 2020);
            } else {
                $query->where('year', $year);
            }
        }
        
        // Sorting
        $sort = $request->input('sort', 'default');
        switch ($sort) {
            case 'title-asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title-desc':
                $query->orderBy('title', 'desc');
                break;
            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;
            case 'author':
                $query->orderBy('author', 'asc');
                break;
            case 'author-desc':
                $query->orderBy('author', 'desc');
                break;
            case 'year-desc':
                $query->orderBy('year', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $books = $query->get();
        
        return view('catalog-new', compact('books'));
    }
    
    public function showBookInfo($id)
    {
        $book = Book::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'price' => number_format($book->price, 2, ',', ' '),
                'genre' => $book->genre,
                'year' => $book->year,
                'pages' => $book->pages,
                'isbn' => $book->isbn,
                'publisher' => $book->publisher,
                'language' => $book->language,
                'description' => $book->description,
                'image' => $book->image ?? '/images/default-book.jpg',
                'is_new' => $book->is_new,
                'is_popular' => $book->is_popular
            ]
        ]);
    }
}
