@extends('layouts.app')

@section('title', 'AI-ассистент по подбору книг')

@section('content')
<div class="ai-assistant-container">
    <div class="ai-header">
        <div class="ai-avatar">
            <div class="ai-icon">🤖</div>
        </div>
        <div class="ai-info">
            <h1>Книжный AI-ассистент</h1>
            <p>Я помогу вам подобрать идеальную книгу по вашему описанию</p>
        </div>
    </div>

    <div class="chat-container">
        <div class="chat-messages" id="chatMessages">
            <div class="message ai-message">
                <div class="message-avatar">🤖</div>
                <div class="message-content">
                    <p>Здравствуйте! Я ваш персональный ассистент по подбору книг. Расскажите мне, какие книги вам нравятся, и я подберу для вас лучшие варианты из нашего каталога.</p>
                    <p>Вы можете упомянуть:</p>
                    <ul>
                        <li>Любимых авторов</li>
                        <li>Предпочитаемые жанры (роман, детектив, фантастика и т.д.)</li>
                        <li>Настроение (хочется чего-то легкого или серьезного)</li>
                        <li>Любимые темы или сюжеты</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="chat-input-container">
            <div class="input-wrapper">
                <input 
                    type="text" 
                    id="messageInput" 
                    class="chat-input" 
                    placeholder="Например: Хочу детектив Агаты Кристи или что-то в стиле фантастики..."
                    maxlength="500"
                >
                <button id="sendButton" class="send-button">
                    <span class="send-icon">➤</span>
                </button>
            </div>
            <div class="input-hint">
                <span id="charCount">0</span> / 500 символов
            </div>
        </div>
    </div>

    <div class="suggestions">
        <div class="suggestions-title">Популярные запросы:</div>
        <div class="suggestion-chips">
            <button class="suggestion-chip" onclick="sendSuggestion('хочу хороший детектив')">
                🔍 Хороший детектив
            </button>
            <button class="suggestion-chip" onclick="sendSuggestion('любовный роман')">
                💕 Любовный роман
            </button>
            <button class="suggestion-chip" onclick="sendSuggestion('фантастика про космос')">
                🚀 Фантастика про космос
            </button>
            <button class="suggestion-chip" onclick="sendSuggestion('исторический роман')">
                📚 Исторический роман
            </button>
            <button class="suggestion-chip" onclick="sendSuggestion('что-то легкое для отдыха')">
                ☕ Легкое для отдыха
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const charCount = document.getElementById('charCount');

    // Счетчик символов
    messageInput.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 450) {
            charCount.classList.add('warning');
        } else {
            charCount.classList.remove('warning');
        }
    });

    // Отправка по Enter
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Кнопка отправки
    sendButton.addEventListener('click', sendMessage);

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        // Добавляем сообщение пользователя
        addMessage(message, 'user');
        
        // Очищаем поле ввода
        messageInput.value = '';
        charCount.textContent = '0';
        charCount.classList.remove('warning');

        // Показываем индикатор набора текста
        showTypingIndicator();

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
            hideTypingIndicator();
            
            // Показываем исправленное сообщение, если были опечатки
            if (data.corrected_message) {
                addMessage(`Я понял ваш запрос как: "${data.corrected_message}"`, 'ai');
            }
            
            // Добавляем ответ AI
            addMessage(data.message, 'ai');
            
            // Добавляем карточки с рекомендациями
            if (data.recommendations && data.recommendations.length > 0) {
                addRecommendationCards(data.recommendations);
            }
        })
        .catch(error => {
            hideTypingIndicator();
            console.error('Error:', error);
            addMessage('Извините, произошла ошибка. Попробуйте еще раз.', 'ai');
        });
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.textContent = sender === 'user' ? '👤' : '🤖';
        
        const content = document.createElement('div');
        content.className = 'message-content';
        
        // Форматируем текст с поддержкой переноса строк и жирного текста
        const formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        
        content.innerHTML = `<p>${formattedText}</p>`;
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(content);
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message ai-message typing-indicator';
        typingDiv.id = 'typingIndicator';
        
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

    function hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.remove();
        }
    }

    function addRecommendationCards(recommendations) {
        const cardsContainer = document.createElement('div');
        cardsContainer.className = 'recommendation-cards';
        
        recommendations.forEach((rec, index) => {
            const book = rec.book;
            const card = document.createElement('div');
            card.className = 'book-card';
            card.innerHTML = `
                <div class="book-cover">
                    <img src="${book.cover_image ? '/storage/' + book.cover_image : '/images/default-book.jpg'}" 
                         alt="${book.title}" 
                         onerror="this.src='/images/default-book.jpg'">
                </div>
                <div class="book-info">
                    <h4 class="book-title">${book.title}</h4>
                    <p class="book-author">${book.author}</p>
                    <p class="book-price">${number_format(book.price / 100, 2)} BYN</p>
                    <p class="recommendation-reason">${rec.reason}</p>
                    <div class="book-actions">
                        <button class="btn-view" onclick="viewBook(${book.id})">Посмотреть</button>
                        <button class="btn-add-to-cart" onclick="addToCart(${book.id})">В корзину</button>
                    </div>
                </div>
            `;
            
            cardsContainer.appendChild(card);
        });
        
        chatMessages.appendChild(cardsContainer);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Функции для кнопок книг
    window.viewBook = function(bookId) {
        window.open(`/books/${bookId}`, '_blank');
    };

    window.addToCart = function(bookId) {
        // Здесь можно добавить логику добавления в корзину
        showNotification('Книга добавлена в корзину!', 'success');
    };

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">
                    ${type === 'success' ? '✅' : '❌'}
                </span>
                <span class="notification-message">${message}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }

    // Функция для отправки подсказок
    window.sendSuggestion = function(text) {
        console.log('sendSuggestion called with:', text);
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        
        console.log('messageInput:', messageInput);
        console.log('sendButton:', sendButton);
        
        if (messageInput && sendButton) {
            messageInput.value = text;
            messageInput.dispatchEvent(new Event('input'));
            sendButton.click();
        } else {
            console.error('Elements not found');
        }
    };
    
    // Делаем функцию доступной глобально
    window.sendSuggestionDebug = window.sendSuggestion;
});
</script>

<style>
.ai-assistant-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.ai-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    color: white;
}

.ai-avatar {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.ai-icon {
    font-size: 40px;
}

.ai-info h1 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
}

.ai-info p {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
}

.chat-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.chat-messages {
    height: 500px;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    animation: fadeIn 0.3s ease;
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.user-message .message-avatar {
    background: #3b82f6;
    color: white;
}

.ai-message .message-avatar {
    background: #10b981;
    color: white;
}

.message-content {
    flex: 1;
    background: white;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.user-message .message-content {
    background: #3b82f6;
    color: white;
    margin-left: auto;
    max-width: 70%;
}

.ai-message .message-content {
    max-width: 80%;
}

.message-content p {
    margin: 0;
    line-height: 1.5;
}

.message-content ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.message-content li {
    margin-bottom: 5px;
}

.typing-indicator .typing-dots {
    display: flex;
    gap: 4px;
}

.typing-indicator .typing-dots span {
    width: 8px;
    height: 8px;
    background: #6b7280;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator .typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator .typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

.chat-input-container {
    padding: 20px;
    background: white;
    border-top: 1px solid #e5e7eb;
}

.input-wrapper {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
}

.chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: border-color 0.2s ease;
}

.chat-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.send-button {
    width: 48px;
    height: 48px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
}

.send-button:hover {
    background: #2563eb;
}

.send-icon {
    font-size: 18px;
}

.input-hint {
    text-align: right;
    font-size: 12px;
    color: #6b7280;
}

.input-hint.warning {
    color: #ef4444;
}

.suggestions {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.suggestions-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 15px;
}

.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.suggestion-chip {
    padding: 8px 16px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    color: #374151;
}

.suggestion-chip:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.recommendation-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.book-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.book-cover {
    width: 100%;
    height: 150px;
    background: #f3f4f6;
    border-radius: 8px;
    margin-bottom: 15px;
    overflow: hidden;
}

.book-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.book-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 5px 0;
}

.book-author {
    color: #6b7280;
    font-size: 14px;
    margin: 0 0 10px 0;
}

.book-price {
    color: #dc2626;
    font-weight: 600;
    font-size: 18px;
    margin: 0 0 10px 0;
}

.recommendation-reason {
    color: #059669;
    font-size: 12px;
    font-style: italic;
    margin: 0 0 15px 0;
}

.book-actions {
    display: flex;
    gap: 10px;
}

.btn-view, .btn-add-to-cart {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
}

.btn-view {
    background: #f3f4f6;
    color: #374151;
}

.btn-view:hover {
    background: #e5e7eb;
}

.btn-add-to-cart {
    background: #3b82f6;
    color: white;
}

.btn-add-to-cart:hover {
    background: #2563eb;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 16px;
    min-width: 300px;
    max-width: 500px;
    z-index: 9999;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
    border-left: 4px solid #3b82f6;
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification-success {
    border-left-color: #10b981;
}

.notification-error {
    border-left-color: #ef4444;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.notification-icon {
    font-size: 18px;
    flex-shrink: 0;
}

.notification-message {
    flex: 1;
    color: #1f2937;
    font-size: 14px;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.notification-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .ai-assistant-container {
        margin: 20px auto;
        padding: 0 15px;
    }
    
    .ai-header {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .ai-avatar {
        width: 60px;
        height: 60px;
    }
    
    .ai-icon {
        font-size: 30px;
    }
    
    .ai-info h1 {
        font-size: 24px;
    }
    
    .chat-messages {
        height: 400px;
        padding: 15px;
    }
    
    .message-content {
        padding: 12px 15px;
    }
    
    .user-message .message-content {
        max-width: 85%;
    }
    
    .ai-message .message-content {
        max-width: 90%;
    }
    
    .recommendation-cards {
        grid-template-columns: 1fr;
    }
    
    .suggestion-chips {
        justify-content: center;
    }
}
</style>
@endsection
