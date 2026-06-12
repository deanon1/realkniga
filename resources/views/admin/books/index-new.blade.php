@extends('layouts.app')

@section('title', 'Управление книгами - Админ панель')

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Управление книгами</h1>
            <p class="admin-subtitle">Добавление, редактирование и удаление книг</p>
        </div>
        <div class="admin-actions">
            <a href="/admin/add-new-book" class="btn-primary">
                <span class="btn-icon">➕</span>
                Добавить книгу
            </a>
            <a href="/admin" class="btn-secondary">
                <span class="btn-icon">←</span>
                Назад к панели
            </a>
        </div>
    </div>

    <div class="admin-content">
        <!-- Фильтры и поиск -->
        <div class="admin-filters">
            <div class="filter-group">
                <input type="text" id="bookSearch" placeholder="Поиск по названию или автору..." class="search-input">
            </div>
            <div class="filter-group">
                <select id="genreFilter" class="filter-select">
                    <option value="">Все жанры</option>
                    <option value="fiction">Художественная</option>
                    <option value="science">Научная фантастика</option>
                    <option value="detective">Детективы</option>
                    <option value="romance">Романы</option>
                    <option value="thriller">Триллеры</option>
                    <option value="biography">Биографии</option>
                    <option value="history">История</option>
                    <option value="psychology">Психология</option>
                </select>
            </div>
            <div class="filter-group">
                <select id="sortBy" class="filter-select">
                    <option value="title">По названию</option>
                    <option value="author">По автору</option>
                    <option value="price-asc">По цене (дешевые)</option>
                    <option value="price-desc">По цене (дорогие)</option>
                </select>
            </div>
        </div>

        <!-- Таблица книг -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Изображение</th>
                        <th>Название</th>
                        <th>Автор</th>
                        <th>Жанр</th>
                        <th>Цена</th>
                        <th>Год</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="booksTableBody">
                    @php
                        $genreNames = [
                            'fiction' => 'Художественная',
                            'science' => 'Научная фантастика',
                            'detective' => 'Детективы',
                            'romance' => 'Романы',
                            'thriller' => 'Триллеры',
                            'biography' => 'Биографии',
                            'history' => 'История',
                            'psychology' => 'Психология'
                        ];
                    @endphp
                    @foreach($books as $book)
                    <tr class="book-row" data-title="{{ $book->title }}" data-author="{{ $book->author }}" data-genre="{{ $book->genre ?? 'fiction' }}" data-price="{{ $book->price }}">
                        <td>
                            <div class="book-image-small">
                                @if($book->image)
                                    <img src="{{ asset($book->image) }}" alt="{{ $book->title }}" onerror="this.src='https://via.placeholder.com/60x80/cccccc/666666?text=No+Image'">
                                @else
                                    <img src="https://via.placeholder.com/60x80/cccccc/666666?text=No+Image" alt="{{ $book->title }}">
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="book-title-cell">
                                <strong>{{ $book->title }}</strong>
                            </div>
                        </td>
                        <td>{{ $book->author }}</td>
                        <td>
                            <span class="genre-badge" data-genre="{{ $book->genre ?? 'fiction' }}">{{ $genreNames[$book->genre] ?? 'Художественная' }}</span>
                        </td>
                        <td>
                            <span class="price">{{ number_format($book->price, 2, ',', ' ') }} BYN</span>
                        </td>
                        <td>{{ $book->year ?? '2024' }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="/admin/books/{{ $book->id }}/edit" class="btn-edit-small">
                                    <span>✏️</span>
                                </a>
                                <form method="POST" action="/admin/books/{{ $book->id }}" onsubmit="return confirm('Вы уверены, что хотите удалить эту книгу?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-small">
                                        <span>🗑️</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($books->isEmpty())
            <div class="no-data">
                <div class="no-data-icon">📚</div>
                <h3>Книги не найдены</h3>
                <p>Добавьте первую книгу в каталог</p>
                <a href="/admin/add-new-book" class="btn-primary">Добавить книгу</a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Поиск и фильтрация
document.getElementById('bookSearch').addEventListener('input', filterBooks);
document.getElementById('genreFilter').addEventListener('change', filterBooks);
document.getElementById('sortBy').addEventListener('change', sortBooks);

function filterBooks() {
    const searchTerm = document.getElementById('bookSearch').value.toLowerCase();
    const genreFilter = document.getElementById('genreFilter').value;
    const rows = document.querySelectorAll('.book-row');
    
    rows.forEach(row => {
        const title = row.dataset.title.toLowerCase();
        const author = row.dataset.author.toLowerCase();
        const genre = row.dataset.genre;
        
        const matchesSearch = !searchTerm || title.includes(searchTerm) || author.includes(searchTerm);
        const matchesGenre = !genreFilter || genre === genreFilter;
        
        row.style.display = matchesSearch && matchesGenre ? '' : 'none';
    });
}

function sortBooks() {
    const sortBy = document.getElementById('sortBy').value;
    const tbody = document.getElementById('booksTableBody');
    const rows = Array.from(tbody.querySelectorAll('.book-row'));
    
    rows.sort((a, b) => {
        switch(sortBy) {
            case 'title':
                return a.dataset.title.localeCompare(b.dataset.title);
            case 'author':
                return a.dataset.author.localeCompare(b.dataset.author);
            case 'price-asc':
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            case 'price-desc':
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            default:
                return 0;
        }
    });
    
    rows.forEach(row => tbody.appendChild(row));
}
</script>
@endsection
