<?php
namespace App\Http\Controllers;

use App\Models\Book;

class HomeController extends Controller
{
    public function index()
    {
        $books = Book::latest()->take(20)->get();
        return view('welcome-new', compact('books'));
    }
}
