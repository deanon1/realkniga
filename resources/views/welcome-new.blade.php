@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">РЕАЛКНИГА</h1>
            <p class="hero-subtitle">Минималистичная онлайн-платформа по продаже и подбору книг в Беларуси</p>
            <div class="hero-actions">
                <a href="/catalog" class="btn-primary">Открыть каталог</a>
                <a href="#features" class="btn-secondary">Узнать больше</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="book-stack">
                <div class="book book-1"></div>
                <div class="book book-2"></div>
                <div class="book book-3"></div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features">
    <div class="container">
        <div class="features-slider-container">
            <div class="features-content">
                <h2 class="section-title">ПОЧЕМУ ВЫБИРАЮТ НАС</h2>
                <p class="features-subtitle">РеалКнига - это не просто онлайн-платформа по продаже и подбору книг, это ваш проводник в мир знаний и приключений</p>
            </div>
            
            <div class="features-slider-wrapper">
                <div class="features-slider">
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">📚</div>
                            <h3 class="feature-title">МИНИМАЛИЗМ</h3>
                            <p class="feature-description">Чистый дизайн без отвлекающих элементов</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">⭐</div>
                            <h3 class="feature-title">КАЧЕСТВО</h3>
                            <p class="feature-description">Только лучшие книги от проверенных издательств</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🚀</div>
                            <h3 class="feature-title">СКОРОСТЬ</h3>
                            <p class="feature-description">Быстрая доставка по всей Беларуси</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">💎</div>
                            <h3 class="feature-title">УДОБСТВО</h3>
                            <p class="feature-description">Простой интерфейс и удобный заказ</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🔒</div>
                            <h3 class="feature-title">БЕЗОПАСНОСТЬ</h3>
                            <p class="feature-description">Защищенные платежи и конфиденциальность данных</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🎯</div>
                            <h3 class="feature-title">ВЫБОР</h3>
                            <p class="feature-description">Огромный ассортимент книг на любой вкус</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">💰</div>
                            <h3 class="feature-title">ЦЕНЫ</h3>
                            <p class="feature-description">Доступные цены и регулярные скидки</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">📦</div>
                            <h3 class="feature-title">СЕРВИС</h3>
                            <p class="feature-description">Отличное обслуживание и поддержка клиентов</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Books Section -->
<section class="popular-books">
    <div class="container">
        <h2 class="section-title">ПОПУЛЯРНЫЕ КНИГИ</h2>
        
        <!-- Поиск -->
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Поиск книг..." id="homeSearch">
        </div>
        
        <div class="book-grid" id="homeBookGrid">
            @php
                // Берем книги с is_popular=1, но не более 8
                $popularBooks = $books->where('is_popular', true)->take(8);
            @endphp
            @foreach($popularBooks as $book)
            <div class="book-card fade-in" data-title="{{ $book->title }}" data-price="{{ $book->price }}" data-author="{{ $book->author }}">
                <div class="book-badge popular-badge">Популярная</div>
                <img src="{{ $book->image ? asset($book->image) : 'https://via.placeholder.com/200x300/e0e0e0/666666?text=Book+Cover' }}" alt="{{ $book->title }}" onerror="this.src='https://via.placeholder.com/200x300/e0e0e0/666666?text=Book+Cover'">
                <div class="book-card-content">
                    <h3>{{ $book->title }}</h3>
                    <p>{{ $book->author ?? 'Неизвестный автор' }}</p>
                    <p class="book-price">{{ number_format($book->price, 2, ',', ' ') }} BYN</p>
                    <div class="book-buttons">
                        <button class="btn-about" onclick="showBookInfo({{ $book->id }})">О книге</button>
                        <a href="/cart/add/{{ $book->id }}" class="btn-add">В корзину</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="section-actions">
            <a href="/catalog" class="view-all-btn">Смотреть все книги</a>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="reviews">
    <div class="container">
        <div class="reviews-header">
            <h2 class="section-title">ОТЗЫВЫ О КНИГАХ</h2>
            <p class="section-subtitle">Что говорят наши клиенты о заказанных книгах</p>
        </div>
        
        <div class="reviews-container">
            <div class="reviews-grid" id="reviewsGrid">
                <!-- Отзывы будут загружены через JavaScript -->
                <div class="no-reviews" id="noReviewsPlaceholder" style="display: none;">
                    <div class="no-reviews-icon">📖</div>
                    <h3>Пока нет отзывов о книгах</h3>
                    <p>Оставьте отзыв о заказанной книге и поделитесь мнением с другими!</p>
                </div>
            </div>
            
            <div class="reviews-actions">
                <a href="/profile" class="btn-secondary">Мои заказы</a>
            </div>
        </div>
    </div>
</section>

<!-- AI Assistant Widget -->
<div class="ai-assistant-widget" id="aiAssistantWidget">
    <div class="ai-assistant-fab" id="aiAssistantFab">
        <div class="fab-icon">🤖</div>
        <div class="fab-text">Подобрать книгу</div>
    </div>
    
    <div class="ai-assistant-chat" id="aiAssistantChat">
        <div class="chat-header">
            <div class="chat-title">
                <span class="chat-icon">🤖</span>
                <span>Помощник по подбору книг</span>
            </div>
            <button class="chat-close" id="chatClose">✕</button>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <div class="message ai-message">
                <div class="message-avatar">🤖</div>
                <div class="message-content">
                    <p>Здравствуйте! Я помогу вам подобрать идеальную книгу. Расскажите, что вам нравится?</p>
                </div>
            </div>
        </div>
        
        <div class="chat-input-container">
            <div class="input-wrapper">
                <input 
                    type="text" 
                    id="widgetMessageInput" 
                    class="chat-input" 
                    placeholder="Например: детектив Агаты Кристи..."
                    maxlength="500"
                >
                <button id="widgetSendButton" class="send-button">
                    <span class="send-icon">➤</span>
                </button>
                <div class="char-counter-widget" id="widgetCharCounter">0/500</div>
            </div>
        </div>
        
        <div class="chat-suggestions">
            <button class="suggestion-btn" onclick="sendWidgetSuggestion('детектив')">🔍 Детектив</button>
            <button class="suggestion-btn" onclick="sendWidgetSuggestion('любовный роман')">💕 Роман</button>
            <button class="suggestion-btn" onclick="sendWidgetSuggestion('фантастика')">🚀 Фантастика</button>
        </div>
    </div>
</div>

<!-- Модальное окно книги для AI-ассистента -->
<div class="ai-book-modal-overlay" id="aiBookModal">
    <div class="ai-book-modal">
        <div class="ai-modal-header">
            <h3 id="aiModalBookTitle">Название книги</h3>
            <button class="ai-modal-close" id="aiModalClose">✕</button>
        </div>
        <div class="ai-modal-content">
            <div class="ai-modal-book-info">
                <div class="ai-modal-book-image" id="aiModalBookImage">
                    <img src="/storage/app/public/books/sherlock-holmes.jpg" alt="Обложка книги">
                </div>
                <div class="ai-modal-book-details">
                    <div class="ai-modal-author" id="aiModalBookAuthor">Автор</div>
                    <div class="ai-modal-price" id="aiModalBookPrice">0.00 BYN</div>
                    <div class="ai-modal-meta">
                        <span class="ai-modal-genre" id="aiModalBookGenre">Жанр</span>
                        <span class="ai-modal-year" id="aiModalBookYear">Год</span>
                        <span class="ai-modal-pages" id="aiModalBookPages">0 стр.</span>
                    </div>
                    <div class="ai-modal-description" id="aiModalBookDescription">
                        Описание книги...
                    </div>
                </div>
            </div>
        </div>
        <div class="ai-modal-footer">
            <button class="ai-modal-btn-close" id="aiModalBtnClose">Закрыть</button>
        </div>
    </div>
</div>

<!-- Stats Section -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ \App\Models\Book::count() }}</div>
                <div class="stat-label">Книг в каталоге</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Довольных клиентов</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24ч</div>
                <div class="stat-label">Средняя доставка</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">5⭐</div>
                <div class="stat-label">Рейтинг сервиса</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">ГОТОВЫ НАЧАТЬ ЧИТАТЬ?</h2>
            <p class="cta-subtitle">Откройте для себя мир литературы с РеалКнига</p>
            <a href="/catalog" class="btn-primary btn-large">Начать покупки</a>
        </div>
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
// Плавная прокрутка к секции
document.querySelector('a[href="#features"]')?.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('#features').scrollIntoView({
        behavior: 'smooth'
    });
});

// Поиск книг на главной
document.getElementById('homeSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const allBookCards = document.querySelectorAll('#homeBookGrid .book-card, #additionalBookGrid .book-card');
    
    allBookCards.forEach(card => {
        const title = card.dataset.title.toLowerCase();
        const author = card.dataset.author.toLowerCase();
        
        if (title.includes(searchTerm) || author.includes(searchTerm)) {
            card.style.display = 'block';
            card.classList.add('fade-in');
        } else {
            card.style.display = 'none';
        }
    });
});

// Анимация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    const allBookCards = document.querySelectorAll('.book-card');
    allBookCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 50);
    });
    
    // Анимация статистики
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    });
    
    document.querySelectorAll('.stat-item').forEach(item => {
        observer.observe(item);
    });
});

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
    document.getElementById('bookModalImage').src = '/images/default-book.jpg';
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

// Загрузка отзывов о сервисе
document.addEventListener('DOMContentLoaded', function() {
    loadServiceReviews();
});

function loadServiceReviews() {
    fetch('/api/order-reviews')
        .then(response => response.json())
        .then(reviews => {
            const reviewsGrid = document.getElementById('reviewsGrid');
            const noReviewsPlaceholder = document.getElementById('noReviewsPlaceholder');
            
            if (reviews && reviews.length > 0) {
                displayReviews(reviews);
                noReviewsPlaceholder.style.display = 'none';
            } else {
                // Показываем заглушку если нет отзывов
                noReviewsPlaceholder.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            // В случае ошибки тоже показываем заглушку
            const noReviewsPlaceholder = document.getElementById('noReviewsPlaceholder');
            if (noReviewsPlaceholder) {
                noReviewsPlaceholder.style.display = 'block';
            }
        });
}

function displayReviews(reviews) {
    const reviewsGrid = document.getElementById('reviewsGrid');
    
    const reviewsHTML = reviews.map(review => `
        <div class="review-card">
            <div class="review-stars">
                ${generateStars(review.rating)}
            </div>
            <p class="review-text">${review.filtered_text || review.review_text}</p>
            <div class="review-book">
                <span class="book-label">Книга:</span>
                <span class="book-title">${review.book ? review.book.title : 'Неизвестная книга'}</span>
            </div>
            <div class="review-author">
                <span class="author-name">${review.user.name}</span>
                <span class="review-date">${formatDate(review.created_at)}</span>
            </div>
        </div>
    `).join('');
    
    reviewsGrid.innerHTML = reviewsHTML;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<span class="star ${i <= rating ? 'active' : ''}">★</span>`;
    }
    return stars;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return 'вчера';
    if (diffDays < 7) return `${diffDays} дней назад`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} недель назад`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} месяцев назад`;
    return `${Math.floor(diffDays / 365)} лет назад`;
}

// AI Assistant Widget
document.addEventListener('DOMContentLoaded', function() {
    const aiFab = document.getElementById('aiAssistantFab');
    const aiChat = document.getElementById('aiAssistantChat');
    const chatClose = document.getElementById('chatClose');
    const widgetMessageInput = document.getElementById('widgetMessageInput');
    const widgetSendButton = document.getElementById('widgetSendButton');
    const chatMessages = document.getElementById('chatMessages');
    
    // Открытие/закрытие чата
    aiFab.addEventListener('click', function() {
        aiChat.classList.toggle('open');
        if (aiChat.classList.contains('open')) {
            widgetMessageInput.focus();
        }
    });
    
    chatClose.addEventListener('click', function() {
        aiChat.classList.remove('open');
    });
    
    // Отправка сообщения
    function sendWidgetMessage() {
        const message = widgetMessageInput.value.trim();
        if (!message) return;
        
        // Добавляем сообщение пользователя
        addWidgetMessage(message, 'user');
        widgetMessageInput.value = '';
        
        // Показываем индикатор набора
        showWidgetTypingIndicator();
        
        // Отправляем на сервер
        fetch('/ai-assistant/recommend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            hideWidgetTypingIndicator();
            addWidgetMessage(data.message, 'ai');
            
            if (data.recommendations && data.recommendations.recommendations && data.recommendations.recommendations.length > 0) {
                addWidgetRecommendations(data.recommendations.recommendations);
            }
        })
        .catch(error => {
            hideWidgetTypingIndicator();
            addWidgetMessage('Извините, произошла ошибка. Попробуйте еще раз.', 'ai');
        });
    }
    
    widgetSendButton.addEventListener('click', sendWidgetMessage);
    widgetMessageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendWidgetMessage();
        }
    });
    
    // Счетчик символов
    const widgetCharCounter = document.getElementById('widgetCharCounter');
    
    function updateCharCounter() {
        const currentLength = widgetMessageInput.value.length;
        const maxLength = widgetMessageInput.maxLength;
        widgetCharCounter.textContent = `${currentLength}/${maxLength}`;
        
        // Меняем цвет при приближении к лимиту
        if (currentLength > maxLength * 0.9) {
            widgetCharCounter.style.color = '#dc2626';
        } else if (currentLength > maxLength * 0.7) {
            widgetCharCounter.style.color = '#f59e0b';
        } else {
            widgetCharCounter.style.color = '#9ca3af';
        }
    }
    
    widgetMessageInput.addEventListener('input', updateCharCounter);
    
    // Инициализация счетчика
    updateCharCounter();
    
    // Функции для виджета
    function addWidgetMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.textContent = sender === 'user' ? '👤' : '🤖';
        
        const content = document.createElement('div');
        content.className = 'message-content';
        const formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        content.innerHTML = `<p>${formattedText}</p>`;
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(content);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function showWidgetTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai-message typing-indicator';
        typingDiv.id = 'widgetTypingIndicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">🤖</div>
            <div class="message-content">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function hideWidgetTypingIndicator() {
        const indicator = document.getElementById('widgetTypingIndicator');
        if (indicator) indicator.remove();
    }
    
    function addWidgetRecommendations(recommendations) {
        const cardsContainer = document.createElement('div');
        cardsContainer.className = 'widget-recommendations';
        
        recommendations.forEach((rec, index) => {
            const book = rec['book'];
            const card = document.createElement('div');
            card.className = 'widget-book-card';
            card.innerHTML = `
                <div class="widget-book-info">
                    <h4>${book['title']}</h4>
                    <p>${book['author']}</p>
                    <p class="widget-price">${number_format(book['price'], 2)} BYN</p>
                </div>
                <button class="widget-btn-view" onclick="showBookModal(${book['id']})">О книге</button>
            `;
            cardsContainer.appendChild(card);
        });
        
        chatMessages.appendChild(cardsContainer);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Анимация появления кнопки
    setTimeout(() => {
        aiFab.classList.add('show');
    }, 2000);
});

// Функция форматирования чисел (аналог PHP number_format)
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 2 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (sep && s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((dec || s.length > 1) && s[1] && s[1].length > prec) {
        s[1] = s[1].substr(0, prec);
    }
    if ((dec || s.length > 1) && s[1] && s[1].length < prec) {
        for (var i = s[1].length; i < prec; i++) {
            s[1] += '0';
        }
    }
    return s.join(dec);
}

// Функция перевода жанра на русский
function translateGenreToRussian(genre) {
    const genreTranslations = {
        'detective': 'Детектив',
        'romance': 'Романтика',
        'science': 'Научная фантастика',
        'fiction': 'Художественная литература',
        'history': 'Исторический роман',
        'adventure': 'Приключения',
        'psychological': 'Психологический роман',
        'classic': 'Классика',
        'fantasy': 'Фэнтези',
        'mystery': 'Триллер',
        'crime': 'Криминал',
        'thriller': 'Триллер',
        'sci-fi': 'Научная фантастика',
        'love': 'Романтика',
        // Русские жанры
        'детектив': 'Детектив',
        'романтика': 'Романтика',
        'фантастика': 'Научная фантастика',
        'художественная литература': 'Художественная литература',
        'исторический роман': 'Исторический роман',
        'приключения': 'Приключения',
        'психологический роман': 'Психологический роман',
        'классика': 'Классика',
        'фэнтези': 'Фэнтези',
        'триллер': 'Триллер',
        'криминал': 'Криминал',
        'любовный роман': 'Любовный роман'
    };
    
    return genreTranslations[genre.toLowerCase()] || genre || 'Художественная литература';
}

// Функции для кнопок和建议
function sendWidgetSuggestion(text) {
    const input = document.getElementById('widgetMessageInput');
    input.value = text;
    document.getElementById('widgetSendButton').click();
}

// Функции для модального окна книги
window.showBookModal = function(bookId) {
    // Загружаем данные книги
    fetch(`/catalog/book/${bookId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.book) {
                const book = data.book;
                
                // Заполняем модальное окно
                document.getElementById('aiModalBookTitle').textContent = book.title;
                document.getElementById('aiModalBookAuthor').textContent = book.author;
                document.getElementById('aiModalBookPrice').textContent = book.price + ' BYN';
                document.getElementById('aiModalBookGenre').textContent = book.genre || 'Не указан';
                document.getElementById('aiModalBookYear').textContent = book.year || 'Не указан';
                document.getElementById('aiModalBookPages').textContent = (book.pages || '0') + ' стр.';
                document.getElementById('aiModalBookDescription').textContent = book.description || 'Описание отсутствует';
                
                // Устанавливаем изображение
                const modalImage = document.querySelector('#aiModalBookImage img');
                if (book.image) {
                    modalImage.src = book.image;
                    modalImage.alt = book.title;
                } else {
                    modalImage.src = '/storage/app/public/books/sherlock-holmes.jpg';
                    modalImage.alt = 'Обложка отсутствует';
                }
                
                // Переводим жанр на русский
                const genreElement = document.getElementById('aiModalBookGenre');
                if (genreElement && book.genre) {
                    genreElement.textContent = translateGenreToRussian(book.genre);
                }
                
                // Показываем модальное окно
                document.getElementById('aiBookModal').style.display = 'flex';
            } else {
                // Показываем уведомление об ошибке
                showNotification('Книга не найдена', 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки книги:', error);
            showNotification('Ошибка загрузки данных книги', 'error');
        });
};

// Закрытие модального окна
function closeBookModal() {
    document.getElementById('aiBookModal').style.display = 'none';
}

// Показ уведомлений
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `ai-notification ai-notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Показываем уведомление
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Скрываем через 3 секунды
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Инициализация обработчиков модального окна
document.addEventListener('DOMContentLoaded', function() {
    // Закрытие по клику на крестик
    document.getElementById('aiModalClose').addEventListener('click', closeBookModal);
    
    // Закрытие по кнопке "Закрыть"
    document.getElementById('aiModalBtnClose').addEventListener('click', closeBookModal);
    
    // Закрытие по клику на фон
    document.getElementById('aiBookModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBookModal();
        }
    });
    
    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('aiBookModal');
            if (modal.style.display === 'flex') {
                closeBookModal();
            }
        }
    });
});

window.viewBook = function(bookId) {
    window.open(`/catalog/book/${bookId}`, '_blank');
};
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

/* Reviews Section */
.reviews {
    padding: 80px 0;
    background: #f9fafb;
}

.reviews-header {
    text-align: center;
    margin-bottom: 50px;
}

.reviews-container {
    max-width: 1200px;
    margin: 0 auto;
}

.reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.review-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.review-stars {
    display: flex;
    gap: 2px;
    margin-bottom: 15px;
}

.review-stars .star {
    color: #d1d5db;
    font-size: 18px;
    transition: color 0.2s ease;
}

.review-stars .star.active {
    color: #fbbf24;
}

.review-text {
    color: #374151;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 15px;
    font-style: italic;
}

.review-book {
    margin-bottom: 15px;
    padding: 10px;
    background: #f9fafb;
    border-radius: 6px;
    border-left: 3px solid #3b82f6;
}

.book-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-right: 8px;
}

.book-title {
    font-size: 14px;
    color: #1f2937;
    font-weight: 600;
}

.review-author {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.author-name {
    font-weight: 600;
    color: #1f2937;
}

.review-date {
    color: #6b7280;
    font-size: 14px;
}

.reviews-actions {
    text-align: center;
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .reviews {
        padding: 60px 0;
    }
    
    .reviews-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .review-card {
        padding: 20px;
    }
    
    .reviews-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .reviews-actions .btn {
        width: 100%;
        max-width: 250px;
    }
}

.no-reviews {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    border: 2px dashed #e5e7eb;
}

.no-reviews-icon {
    font-size: 48px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-reviews h3 {
    color: #374151;
    margin-bottom: 10px;
    font-size: 20px;
}

.no-reviews p {
    color: #6b7280;
    margin: 0;
    font-size: 16px;
}

/* AI Assistant FAB */
.ai-assistant-fab {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50px;
    padding: 15px 20px;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    z-index: 1001;
    transform: translateY(100px);
    opacity: 0;
}

.ai-assistant-fab.show {
    transform: translateY(0);
    opacity: 1;
}

.ai-assistant-fab:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.fab-icon {
    font-size: 24px;
    animation: bounce 2s infinite;
}

.fab-text {
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* AI Assistant Widget Styles */
.ai-assistant-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1001;
}

.ai-assistant-chat {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 500px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    transform: scale(0.8) translateY(20px);
    opacity: 0;
    transition: all 0.3s ease;
}

.ai-assistant-chat.open {
    display: flex;
    transform: scale(1) translateY(0);
    opacity: 1;
}

.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.chat-icon {
    font-size: 20px;
}

.chat-close {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background 0.2s ease;
}

.chat-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
}

.chat-input-container {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}

.chat-suggestions {
    padding: 10px 15px;
    background: #f8f9fa;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.suggestion-btn {
    padding: 6px 12px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 15px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
}

.suggestion-btn:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.widget-recommendations {
    margin: 15px 0;
}

.widget-book-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-book-info h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #1f2937;
}

.widget-book-info p {
    margin: 0 0 3px 0;
    font-size: 12px;
    color: #6b7280;
}

.widget-price {
    color: #dc2626 !important;
    font-weight: 600;
    font-size: 13px !important;
}

.widget-btn-view {
    padding: 6px 12px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.2s ease;
}

.widget-btn-view:hover {
    background: #2563eb;
}

.widget-btn-view {
    width: 100%;
    margin-top: 10px;
}

/* Модальное окно книги */
.ai-book-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(5px);
}

.ai-book-modal {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.ai-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ai-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.ai-modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
}

.ai-modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.ai-modal-content {
    padding: 20px;
    max-height: 60vh;
    overflow-y: auto;
}

.ai-modal-book-info {
    display: flex;
    gap: 20px;
}

.ai-modal-book-image {
    flex-shrink: 0;
    width: 150px;
    height: 220px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.ai-modal-book-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ai-modal-book-details {
    flex: 1;
}

.ai-modal-author {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 8px;
}

.ai-modal-price {
    font-size: 24px;
    font-weight: bold;
    color: #dc2626;
    margin-bottom: 12px;
}

.ai-modal-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.ai-modal-meta span {
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #374151;
}

.ai-modal-description {
    font-size: 14px;
    line-height: 1.6;
    color: #4b5563;
}

.ai-modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
}

.ai-modal-btn-close {
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.ai-modal-btn-close:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Уведомления */
.ai-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    z-index: 3000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.ai-notification.show {
    transform: translateX(0);
}

.ai-notification-success {
    background: #16a34a;
}

.ai-notification-error {
    background: #dc2626;
}

.ai-notification-info {
    background: #3b82f6;
}

/* Улучшенные стили для поля ввода */
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 25px;
    padding: 8px 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.input-wrapper:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1), 0 4px 12px rgba(0, 0, 0, 0.12);
    transform: translateY(-1px);
}

.chat-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 14px;
    padding: 8px 5px;
    color: #1f2937;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chat-input::placeholder {
    color: #9ca3af;
    font-style: italic;
}

.chat-input:focus::placeholder {
    color: #d1d5db;
}

.send-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    margin-left: 8px;
}

.send-button:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.send-button:active {
    transform: scale(0.95);
}

.send-icon {
    color: white;
    font-size: 16px;
    font-weight: bold;
    display: block;
    line-height: 1;
}

/* Анимация для счетчика символов */
.char-counter-widget {
    position: absolute;
    bottom: -20px;
    right: 15px;
    font-size: 11px;
    color: #9ca3af;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.input-wrapper:focus-within .char-counter-widget {
    opacity: 1;
}

@media (max-width: 768px) {
    .ai-assistant-widget {
        bottom: 20px;
        right: 20px;
    }
    
    .ai-assistant-fab {
        padding: 12px 16px;
    }
    
    .fab-icon {
        font-size: 20px;
    }
    
    .fab-text {
        font-size: 12px;
    }
    
    .ai-assistant-chat {
        width: calc(100vw - 40px);
        height: 60vh;
        right: -10px;
    }
    
    .ai-book-modal {
        width: 95%;
        margin: 20px;
    }
    
    .ai-modal-book-info {
        flex-direction: column;
        gap: 15px;
    }
    
    .ai-modal-book-image {
        width: 100%;
        height: 200px;
        align-self: center;
    }
    
    .ai-modal-content {
        padding: 15px;
    }
    
    .ai-modal-header {
        padding: 15px;
    }
    
    .ai-modal-header h3 {
        font-size: 16px;
    }
    
    .ai-modal-footer {
        padding: 15px;
        flex-direction: column;
    }
    
    .ai-notification {
        right: 10px;
        left: 10px;
        transform: translateY(-100%);
    }
    
    .ai-notification.show {
        transform: translateY(0);
    }
    
    .input-wrapper {
        padding: 10px 12px;
    }
    
    .chat-input {
        font-size: 16px; /* Увеличиваем для мобильных */
    }
    
    .send-button {
        width: 40px;
        height: 40px;
    }
    
    .send-icon {
        font-size: 18px;
    }
}
</style>
@endsection
