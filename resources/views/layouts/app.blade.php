<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>РеалКнига — онлайн-платформа по продаже и подбору книг</title>
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    <body>
        @if(session('success'))
        <div id="order-popup" class="popup-overlay">
            <div class="popup">
                <h2>Подтверждение</h2>
                <p>{{ session('success') }}</p>
                <button onclick="closePopup()">Хорошо</button>
            </div>
        </div>
        @endif

        <header class="header">
            <div class="container">
                <a href="/" class="logo">📘 РеалКнига</a>
                <nav>
                    <a href="/catalog" class="nav-link">Каталог</a>

                    @php
                        $cart = session('cart', []);
                        $count = array_sum(array_column($cart, 'quantity'));
                    @endphp

                    <a href="/cart" class="nav-link cart-link">
                        Корзина
                        @if($count > 0)
                            <span class="cart-count">{{ $count }}</span>
                        @endif
                    </a>

                    @guest
                        <a href="{{ route('login.form') }}" class="nav-link">Вход</a>
                        <a href="{{ route('register.form') }}" class="nav-link">Регистрация</a>
                    @else
                        @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                                Панель администратора
                            </a>
                        @endif
                        <a href="{{ route('profile.index') }}" class="nav-link">Профиль</a>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="logout-btn">
                                Выйти
                            </button>
                        </form>
                    @endguest
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="footer">
            <div class="container footer-inner">
                <p>© 2025 РеалКнига — онлайн-платформа по продаже и подбору книг по Беларуси</p>
            </div>
        </footer>

        <script>
        function closePopup() {
            document.getElementById('order-popup').style.display = 'none';
        }
        
        function closeErrorPopup() {
            document.getElementById('error-popup').style.display = 'none';
        }
        
        // Scroll to top button
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.innerHTML = '↑';
        scrollToTopBtn.className = 'scroll-to-top';
        scrollToTopBtn.setAttribute('aria-label', 'Наверх страницы');
        document.body.appendChild(scrollToTopBtn);
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.classList.add('visible');
            } else {
                scrollToTopBtn.classList.remove('visible');
            }
        });
        
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        </script>
        
        <style>
        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 230px;
            width: 70px;
            height: 70px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            z-index: 999;
        }
        
        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .scroll-to-top:hover {
            background: #2563eb;
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .scroll-to-top {
                bottom: 20px;
                right: calc(100vw - 420px);
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
        }
        
        @media (max-width: 480px) {
            .scroll-to-top {
                right: 20px;
                bottom: 80px;
            }
        }
        </style>
    </body>
</html>
