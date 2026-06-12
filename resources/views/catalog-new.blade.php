@extends('layouts.app')

@section('content')

<section class="catalog">
    <div class="container">
        <h2 class="section-title">КАТАЛОГ КНИГ</h2>
        
        <!-- Поиск -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Поиск книг..." id="bookSearch">
        </div>
        
        <!-- Фильтры -->
        <div class="filters-container">
            <div class="filter-group">
                <label class="filter-label">Жанр:</label>
                <select id="genreFilter" class="filter-select">
                    <option value="">Все жанры</option>
                    <option value="fiction">Художественная литература</option>
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
                <label class="filter-label">Цена:</label>
                <select id="priceFilter" class="filter-select">
                    <option value="">Любая цена</option>
                    <option value="0-10">До 10 BYN</option>
                    <option value="10-20">10 - 20 BYN</option>
                    <option value="20-30">20 - 30 BYN</option>
                    <option value="30-50">30 - 50 BYN</option>
                    <option value="50+">Более 50 BYN</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Год:</label>
                <select id="yearFilter" class="filter-select">
                    <option value="">Все годы</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                    <option value="older">Раньше 2020</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Сортировка:</label>
                <select id="sortSelect" class="filter-select">
                    <option value="default">По умолчанию</option>
                    <option value="new">Новинки</option>
                    <option value="popular">Популярные</option>
                    <option value="title-asc">По названию (А-Я)</option>
                    <option value="title-desc">По названию (Я-А)</option>
                    <option value="price-asc">По цене (сначала дешевые)</option>
                    <option value="price-desc">По цене (сначала дорогие)</option>
                    <option value="author">По автору (А-Я)</option>
                    <option value="author-desc">По автору (Я-А)</option>
                    <option value="year-desc">По году (новые)</option>
                </select>
            </div>
        </div>
        
        <div class="buttons-wrapper">
            <div class="buttons-container buttons-left">
                <button id="resetFilters" class="reset-btn">Сбросить</button>
            </div>
            <div class="buttons-container buttons-right">
                <button id="applyFilters" class="apply-btn">Применить</button>
            </div>
        </div>
        
        <div class="book-grid" id="bookGrid">
            @foreach($books as $book)
            <div class="book-card fade-in" data-title="{{ $book->title }}" data-price="{{ $book->price }}" data-author="{{ $book->author }}" data-year="{{ $book->year ?? 2020 }}" data-pages="{{ $book->pages ?? 300 }}" data-genre="{{ $book->genre ?? 'fiction' }}" data-is-new="{{ $book->is_new ?? 0 }}" data-is-popular="{{ $book->is_popular ?? 0 }}">
                @if($book->is_new)
                    <div class="book-badge">Новинка</div>
                @elseif($book->is_popular)
                    <div class="book-badge popular-badge">Популярная</div>
                @endif
                @if($book->image)
                    <img src="{{ $book->image }}" alt="{{ $book->title }}">
                @else
                    <img src="https://via.placeholder.com/200x300/cccccc/666666?text=No+Image" alt="{{ $book->title }}">
                @endif
                <div class="book-card-content">
                    <h3>{{ $book->title }}</h3>
                    <p>{{ $book->author }}</p>
                    <p class="book-price">{{ number_format($book->price, 2, ',', ' ') }} BYN</p>
                    <div class="book-buttons">
                        <button class="btn-about" onclick="showBookInfo({{ $book->id }})">О книге</button>
                        <a href="/cart/add/{{ $book->id }}" class="btn-add">В корзину</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        @if($books->isEmpty())
        <div class="empty-catalog">
            <div class="empty-content">
                <div class="empty-icon">📚</div>
                <h2 class="empty-title">Книги не найдены</h2>
                <p class="empty-text">Похоже, мы еще не добавили книги в наш каталог.</p>
                <p class="empty-subtitle">Но это временно! Скоро здесь появятся лучшие книги для вас.</p>
                <div class="empty-actions">
                    <a href="/catalog" class="empty-btn">Обновить страницу</a>
                    <a href="/" class="empty-btn secondary">На главную</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Модальное окно информации о книге -->
<div id="bookInfoModal" class="book-modal-overlay" style="display: none;">
    <div class="book-modal">
        <div class="book-modal-header">
            <h3>О книге</h3>
            <button class="book-modal-close" onclick="closeBookInfo()">×</button>
        </div>
        <div class="book-modal-content">
            <div class="book-modal-left">
                <img id="bookModalImage" src="" alt="" class="book-modal-image">
            </div>
            <div class="book-modal-right">
                <h2 id="bookModalTitle"></h2>
                <p class="book-modal-author" id="bookModalAuthor"></p>
                <p class="book-modal-price" id="bookModalPrice"></p>
                
                <div class="book-modal-info">
                    <div class="info-item">
                        <strong>Жанр:</strong>
                        <span id="bookModalGenre"></span>
                    </div>
                    <div class="info-item">
                        <strong>Год издания:</strong>
                        <span id="bookModalYear"></span>
                    </div>
                    <div class="info-item">
                        <strong>Страниц:</strong>
                        <span id="bookModalPages"></span>
                    </div>
                    <div class="info-item">
                        <strong>ISBN:</strong>
                        <span id="bookModalIsbn"></span>
                    </div>
                    <div class="info-item">
                        <strong>Издательство:</strong>
                        <span id="bookModalPublisher"></span>
                    </div>
                    <div class="info-item">
                        <strong>Язык:</strong>
                        <span id="bookModalLanguage"></span>
                    </div>
                </div>
                
                <div class="book-modal-description">
                    <h4>Описание</h4>
                    <p id="bookModalDescription"></p>
                </div>
                
                <div class="book-modal-actions">
                    <a href="#" id="bookModalCartLink" class="btn-add">В корзину</a>
                    <button class="btn-secondary" onclick="closeBookInfo()">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Функция применения всех фильтров
function applyFilters() {
    const genreFilter = document.getElementById('genreFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const yearFilter = document.getElementById('yearFilter').value;
    const sortValue = document.getElementById('sortSelect').value;
    const searchTerm = document.getElementById('bookSearch').value.toLowerCase();
    
    const bookGrid = document.getElementById('bookGrid');
    
    // Сохраняем оригинальные карточки если их еще нет
    if (!window.originalBookCards) {
        window.originalBookCards = Array.from(bookGrid.querySelectorAll('.book-card'));
    }
    
    // Работаем с оригинальными карточками
    const allBookCards = window.originalBookCards;
    
    // Фильтруем карточки
    let filteredCards = allBookCards.filter(card => {
        const title = card.dataset.title.toLowerCase();
        const author = card.dataset.author.toLowerCase();
        const price = parseFloat(card.dataset.price);
        const year = parseInt(card.dataset.year);
        const genre = card.dataset.genre;
        
        // Поиск
        if (searchTerm.trim() && !title.includes(searchTerm) && !author.includes(searchTerm)) {
            return false;
        }
        
        // Фильтр по жанру
        if (genreFilter && genreFilter !== '') {
            if (!genre || genre.toLowerCase() !== genreFilter.toLowerCase()) {
                return false;
            }
        }
        
        // Фильтр по цене
        if (priceFilter && priceFilter !== '') {
            if (priceFilter === '0-10' && price >= 10) return false;
            if (priceFilter === '10-20' && (price < 10 || price >= 20)) return false;
            if (priceFilter === '20-30' && (price < 20 || price >= 30)) return false;
            if (priceFilter === '30-50' && (price < 30 || price >= 50)) return false;
            if (priceFilter === '50+' && price < 50) return false;
        }
        
        // Фильтр по году
        if (yearFilter && yearFilter !== '') {
            if (yearFilter === 'older' && year >= 2020) return false;
            if (yearFilter !== 'older' && year !== parseInt(yearFilter)) return false;
        }
        
        return true;
    });
    
    // Сортируем отфильтрованные карточки
    switch(sortValue) {
        case 'new':
            // Показываем только новинки
            filteredCards = filteredCards.filter(card => card.dataset.isNew === '1');
            break;
        case 'popular':
            // Показываем только популярные
            filteredCards = filteredCards.filter(card => card.dataset.isPopular === '1');
            break;
        case 'title-asc':
            filteredCards.sort((a, b) => a.dataset.title.localeCompare(b.dataset.title));
            break;
        case 'title-desc':
            filteredCards.sort((a, b) => b.dataset.title.localeCompare(a.dataset.title));
            break;
        case 'price-asc':
            filteredCards.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
            break;
        case 'price-desc':
            filteredCards.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
            break;
        case 'author':
            filteredCards.sort((a, b) => a.dataset.author.localeCompare(b.dataset.author));
            break;
        case 'author-desc':
            filteredCards.sort((a, b) => b.dataset.author.localeCompare(a.dataset.author));
            break;
        case 'year-desc':
            filteredCards.sort((a, b) => parseInt(b.dataset.year) - parseInt(a.dataset.year));
            break;
    }
    
    // Очищаем сетку и показываем результат
    bookGrid.innerHTML = '';
    if (filteredCards.length === 0) {
        bookGrid.innerHTML = `
            <div class="no-results">
                <div class="no-results-content">
                    <div class="no-results-icon">🔍</div>
                    <h3>Книги не найдены</h3>
                    <p>Попробуйте изменить параметры фильтрации или поиска</p>
                    <button class="no-results-btn" onclick="resetAllFilters()">Сбросить все фильтры</button>
                </div>
            </div>
        `;
    } else {
        filteredCards.forEach(card => {
            card.style.display = 'block';
            bookGrid.appendChild(card.cloneNode(true)); // Клонируем карточку
        });
    }
}

// Функция сброса всех фильтров
function resetAllFilters() {
    document.getElementById('bookSearch').value = '';
    document.getElementById('genreFilter').value = '';
    document.getElementById('priceFilter').value = '';
    document.getElementById('yearFilter').value = '';
    document.getElementById('sortSelect').value = 'default';
    
    // Восстанавливаем все оригинальные карточки
    if (window.originalBookCards) {
        const bookGrid = document.getElementById('bookGrid');
        bookGrid.innerHTML = '';
        window.originalBookCards.forEach(card => {
            bookGrid.appendChild(card.cloneNode(true));
        });
    }
}

// Настройка обработчиков событий
function setupEventListeners() {
    const applyFiltersBtn = document.getElementById('applyFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const bookSearch = document.getElementById('bookSearch');
    
    // Применить фильтры по кнопке
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }
    
    // Сброс фильтров
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetAllFilters);
    }
    
    // Поиск - применяем при вводе (это удобно для поиска)
    let searchTimeout;
    if (bookSearch) {
        bookSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                applyFilters();
            }, 500); // Применяем поиск через 500ms после ввода
        });
        
        // Enter в поле поиска тоже применяет фильтры
        bookSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }
}

// Функция показа информации о книге
function showBookInfo(bookId) {
    // Показываем индикатор загрузки
    const modal = document.getElementById('bookInfoModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Очищаем предыдущие данные
    document.getElementById('bookModalTitle').textContent = 'Загрузка...';
    document.getElementById('bookModalAuthor').textContent = '';
    document.getElementById('bookModalPrice').textContent = '';
    document.getElementById('bookModalImage').src = 'https://via.placeholder.com/200x300/cccccc/666666?text=No+Image';
    document.getElementById('bookModalYear').textContent = '';
    document.getElementById('bookModalPages').textContent = '';
    document.getElementById('bookModalGenre').textContent = '';
    document.getElementById('bookModalIsbn').textContent = '';
    document.getElementById('bookModalPublisher').textContent = '';
    document.getElementById('bookModalLanguage').textContent = '';
    document.getElementById('bookModalDescription').textContent = '';
    
    // Запрашиваем данные из БД
    fetch(`/catalog/book/${bookId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const book = data.book;
                
                // Заполняем модальное окно данными из БД
                document.getElementById('bookModalTitle').textContent = book.title;
                document.getElementById('bookModalAuthor').textContent = book.author;
                document.getElementById('bookModalPrice').textContent = book.price + ' BYN';
                document.getElementById('bookModalImage').src = book.image;
                document.getElementById('bookModalImage').alt = book.title;
                document.getElementById('bookModalYear').textContent = book.year || 'Не указан';
                document.getElementById('bookModalPages').textContent = book.pages || 'Не указано';
                document.getElementById('bookModalGenre').textContent = getGenreName(book.genre);
                document.getElementById('bookModalIsbn').textContent = book.isbn || 'Не указан';
                document.getElementById('bookModalPublisher').textContent = book.publisher || 'Не указано';
                document.getElementById('bookModalLanguage').textContent = book.language || 'Не указан';
                document.getElementById('bookModalDescription').textContent = book.description || 'Описание отсутствует';
                
                // Обновляем ссылку на корзину
                document.getElementById('bookModalCartLink').href = `/cart/add/${book.id}`;
            } else {
                // Если произошла ошибка
                document.getElementById('bookModalTitle').textContent = 'Ошибка';
                document.getElementById('bookModalDescription').textContent = 'Не удалось загрузить информацию о книге';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки данных книги:', error);
            document.getElementById('bookModalTitle').textContent = 'Ошибка';
            document.getElementById('bookModalDescription').textContent = 'Произошла ошибка при загрузке данных';
        });
}

// Функция закрытия модального окна
function closeBookInfo() {
    const modal = document.getElementById('bookInfoModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Возвращаем прокрутку
}

// Функция для получения названия жанра
function getGenreName(genre) {
    const genres = {
        'fiction': 'Художественная литература',
        'science': 'Научная фантастика',
        'detective': 'Детективы',
        'romance': 'Романы',
        'thriller': 'Триллеры',
        'biography': 'Биографии',
        'history': 'История',
        'psychology': 'Психология'
    };
    return genres[genre] || genre;
}

// Закрытие модального окна при клике на фон
document.addEventListener('click', function(event) {
    const modal = document.getElementById('bookInfoModal');
    if (event.target === modal) {
        closeBookInfo();
    }
});

// Закрытие модального окна по клавише Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeBookInfo();
    }
});

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    
    const bookCards = document.querySelectorAll('.book-card');
    bookCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 50);
    });
});
</script>

<style>
.book-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #dc2626;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.book-card {
    position: relative;
}

.book-card:hover .book-badge {
    opacity: 1;
}

.popular-badge {
    background: #2563eb;
}

.book-card-content {
    height: auto;
    min-height: 180px;
    overflow: hidden;
    padding: 15px;
}

.book-card-content h3 {
    white-space: normal;
    overflow: visible;
    text-overflow: initial;
    max-width: 100%;
    line-height: 1.4;
    margin-bottom: 8px;
    font-size: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-card-content p {
    white-space: normal;
    overflow: visible;
    text-overflow: initial;
    max-width: 100%;
    line-height: 1.3;
    margin-bottom: 5px;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-card-content .book-price {
    white-space: nowrap;
    overflow: visible;
    font-weight: 600;
    color: #000;
    margin: 10px 0;
}

/* Исправление обрезания текста в фильтрах */
.filter-select {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 30px !important;
    width: 100%;
}

/* Расширение для фильтра сортировки */
#sortSelect {
    min-width: 180px;
    max-width: 280px;
}

.filter-group {
    flex: 1;
    min-width: 150px;
    max-width: 250px;
}

.filters-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: end;
    width: 100%;
    max-width: 100%;
    justify-content: space-between;
    overflow-x: visible;
    margin-bottom: 15px;
    padding-right: 40px;
}

.filter-group {
    flex: 1;
    min-width: 150px;
    max-width: none;
}

.filter-select {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 30px !important;
    width: 100%;
}

/* Расширение для фильтра сортировки */
#sortSelect {
    min-width: 180px;
    max-width: none;
}

.buttons-wrapper {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 20px;
    gap: 15px;
}

.buttons-container {
    display: flex;
    flex-direction: column;
    gap: 5px;
    align-items: center;
    flex-shrink: 0;
}

.buttons-left {
    justify-content: flex-start;
}

.buttons-right {
    justify-content: flex-end;
}

.apply-btn, .reset-btn {
    flex-shrink: 0 !important;
    white-space: nowrap !important;
    width: 160px !important;
    min-width: 160px !important;
    max-width: 160px !important;
    height: 40px !important;
    min-height: 40px !important;
    max-height: 40px !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    text-align: center !important;
    line-height: 1 !important;
    overflow: hidden !important;
}

@media (max-width: 1024px) {
    .filter-select {
        min-width: 110px;
        max-width: 180px;
    }
    
    #sortSelect {
        min-width: 160px;
        max-width: 220px;
    }
    
    .filter-group {
        min-width: 110px;
        max-width: 180px;
    }
}

@media (max-width: 768px) {
    .filters-container {
        flex-direction: column;
        align-items: center;
        overflow-x: visible;
        margin-bottom: 15px;
    }
    
    .buttons-wrapper {
        flex-direction: column;
        justify-content: center;
        margin-bottom: 15px;
        gap: 10px;
    }
    
    .buttons-container {
        flex-direction: row;
        gap: 10px;
        justify-content: center;
    }
    
    .filter-group {
        min-width: auto;
        max-width: none;
        width: 100%;
        max-width: 300px;
    }
    
    .filter-select {
        min-width: auto;
        max-width: none;
        width: 100%;
    }
    
    #sortSelect {
        min-width: auto;
        max-width: none;
        width: 100%;
    }
    
    .apply-btn, .reset-btn {
        width: 160px !important;
        min-width: 160px !important;
        max-width: 160px !important;
        height: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        flex: none !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        padding: 8px 16px !important;
        font-size: 14px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        line-height: 1 !important;
        overflow: hidden !important;
    }
}

/* Стили для кнопок книг */
.book-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
}

.btn-about {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    text-decoration: none;
}

.btn-about:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

/* Стили для модального окна книги */
.book-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.book-modal-overlay[style*="flex"] {
    opacity: 1;
    visibility: visible;
}

.book-modal {
    background: white;
    border-radius: 8px;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.book-modal-overlay[style*="flex"] .book-modal {
    transform: scale(1);
}

.book-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.book-modal-header h3 {
    margin: 0;
    font-size: 24px;
    color: #111827;
}

.book-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

.book-modal-close:hover {
    background: #f3f4f6;
}

.book-modal-content {
    display: flex;
    padding: 20px;
    gap: 30px;
}

.book-modal-left {
    flex-shrink: 0;
}

.book-modal-image {
    width: 200px;
    height: 300px;
    object-fit: cover;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.book-modal-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.book-modal-right h2 {
    margin: 0;
    font-size: 28px;
    color: #111827;
    line-height: 1.2;
}

.book-modal-author {
    margin: 0;
    font-size: 18px;
    color: #6b7280;
    font-style: italic;
}

.book-modal-price {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #dc2626;
}

.book-modal-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-item strong {
    color: #374151;
    font-size: 14px;
}

.info-item span {
    color: #6b7280;
    font-size: 14px;
}

.book-modal-description {
    margin-top: 10px;
}

.book-modal-description h4 {
    margin: 0 0 10px 0;
    color: #111827;
    font-size: 18px;
}

.book-modal-description p {
    margin: 0;
    color: #4b5563;
    line-height: 1.6;
    font-size: 15px;
}

.book-modal-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.btn-secondary {
    background: #6b7280;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-secondary:hover {
    background: #4b5563;
    color: white;
}

/* Адаптивность для модального окна */
@media (max-width: 768px) {
    .book-modal {
        width: 95%;
        max-height: 95vh;
    }
    
    .book-modal-content {
        flex-direction: column;
        gap: 20px;
    }
    
    .book-modal-left {
        text-align: center;
    }
    
    .book-modal-image {
        width: 150px;
        height: 225px;
        margin: 0 auto;
    }
    
    .book-modal-info {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .book-modal-actions {
        flex-direction: column;
    }
    
    .btn-add, .btn-secondary {
        width: 100%;
        text-align: center;
    }
}
</style>
@endsection
