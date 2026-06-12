<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookAdminController extends Controller
{
    public function index()
    {
        $books = Book::latest()->get();
        return view('admin.books.index-new', compact('books'));
    }

    public function create()
    {
        return view('admin.books.create-new');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'year' => 'nullable|integer',
            'pages' => 'nullable|integer|min:1',
            'isbn' => 'nullable|string|max:20',
            'publisher' => 'nullable|string|max:255',
            'language' => 'nullable|string|in:ru,be,en',
            'category' => 'required|string|in:none,new,popular',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,heic,heif|max:20480'
        ]);

        // Обрабатываем категорию
        $category = $request->input('category');
        $validated['is_new'] = $category == 'new' ? 1 : 0;
        $validated['is_popular'] = $category == 'popular' ? 1 : 0;
        
        \Log::info('STORE: category = ' . $category . ', is_new = ' . $validated['is_new'] . ', is_popular = ' . $validated['is_popular']);

        // Отладка - проверяем есть ли файл
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            \Log::info('Файл изображения найден');
            \Log::info('Размер файла: ' . $file->getSize() . ' bytes');
            \Log::info('MIME тип: ' . $file->getMimeType());
            
            // Проверяем размер файла (максимум 25MB для безопасности)
            if ($file->getSize() > 25 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Файл слишком большой. Максимальный размер: 25MB');
            }
            
            try {
                $imagePath = $file->store('books', 'public');
                \Log::info('Путь к изображению: ' . $imagePath);
                $validated['image'] = '/storage/' . $imagePath;
                \Log::info('Изображение добавлено в validated: ' . $validated['image']);
            } catch (\Exception $e) {
                \Log::error('Ошибка сохранения файла: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ошибка сохранения файла: ' . $e->getMessage());
            }
        } else {
            \Log::info('Файл изображения НЕ найден');
            \Log::info('Все данные запроса: ' . json_encode(array_keys($request->all())));
        }

        $book = Book::create($validated);
        \Log::info('Книга создана с ID: ' . $book->id);
        \Log::info('Путь изображения в БД: ' . $book->image);

        return redirect('/admin/books')->with('success', 'Книга успешно добавлена');
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        return view('admin.books.edit-new', compact('book'));
    }

    public function show($id)
    {
        // Перенаправляем на страницу редактирования, так как отдельная страница просмотра не нужна
        return redirect()->route('admin.books.edit', $id);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'year' => 'nullable|integer',
            'pages' => 'nullable|integer|min:1',
            'isbn' => 'nullable|string|max:20',
            'publisher' => 'nullable|string|max:255',
            'language' => 'nullable|string|in:ru,be,en',
            'category' => 'required|string|in:none,new,popular',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,heic,heif|max:51200'
        ]);

        // Обрабатываем категорию
        $category = $request->input('category');
        $validated['is_new'] = $category == 'new' ? 1 : 0;
        $validated['is_popular'] = $category == 'popular' ? 1 : 0;
        
        \Log::info('UPDATE: category = ' . $category . ', is_new = ' . $validated['is_new'] . ', is_popular = ' . $validated['is_popular']);

        // Сначала обрабатываем удаление изображения если нужно
        if ($request->has('remove_image') && $request->get('remove_image') == '1') {
            // Удаляем старый файл изображения
            if ($book->image) {
                $oldImagePath = str_replace('/storage/', '', $book->image);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                    \Log::info('UPDATE: Старый файл изображения удален: ' . $oldImagePath);
                }
            }
            $validated['image'] = null;
            \Log::info('UPDATE: Изображение будет удалено');
        }

        // Затем обрабатываем загрузку нового изображения если есть
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            \Log::info('UPDATE: Файл изображения найден');
            \Log::info('UPDATE: Размер файла: ' . $file->getSize() . ' bytes');
            \Log::info('UPDATE: MIME тип: ' . $file->getMimeType());
            
            // Проверяем размер файла (максимум 50MB)
            if ($file->getSize() > 50 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Файл слишком большой. Максимальный размер: 50MB');
            }
            
            try {
                $imagePath = $file->store('books', 'public');
                \Log::info('UPDATE: Путь к изображению: ' . $imagePath);
                $validated['image'] = '/storage/' . $imagePath;
                \Log::info('UPDATE: Изображение добавлено в validated: ' . $validated['image']);
            } catch (\Exception $e) {
                \Log::error('UPDATE: Ошибка сохранения файла: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ошибка сохранения файла: ' . $e->getMessage());
            }
        } else {
            \Log::info('UPDATE: Файл изображения НЕ найден');
        }

        // Обновляем книгу
        $book->update($validated);
        \Log::info('UPDATE: Книга обновлена, путь изображения: ' . $book->image);

        return redirect('/admin/books')->with('success', 'Книга успешно обновлена');
    }

    public function destroy($id) {
        Book::destroy($id);
        return redirect('/admin/books');
    }
}
