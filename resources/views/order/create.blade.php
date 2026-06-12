@extends('layouts.app')

@section('content')
<div class="checkout-wrapper">
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Оформление заказа</h1>
            <p>Заполните форму для завершения покупки</p>
            @if(Auth::check() && (Auth::user()->name || Auth::user()->phone || Auth::user()->address || Auth::user()->region))
                <div class="profile-info-notice">
                    <span class="notice-icon">ℹ️</span>
                    <span class="notice-text">Данные из вашего профиля автоматически подставлены в форму. Вы можете их изменить при необходимости.</span>
                </div>
            @endif
        </div>
        
        <form method="POST" action="/order" class="checkout-form">
            @csrf
            
            <div class="form-grid">
                <div class="form-section">
                    <h2>Данные получателя</h2>
                    
                    <div class="form-group">
                        <label class="form-label">ФИО получателя</label>
                        <input type="text" name="name" placeholder="Иванов Иван Иванович" class="form-input" 
                               value="{{ Auth::check() ? old('name', Auth::user()->name) : old('name') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" placeholder="example@email.com" class="form-input" 
                               value="{{ Auth::check() ? old('email', Auth::user()->email) : old('email') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Телефон</label>
                        <input type="tel" name="phone" placeholder="+375 (XX) XXX-XX-XX" class="form-input" 
                               value="{{ Auth::check() ? old('phone', Auth::user()->phone) : old('phone') }}" required>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Способ доставки</h2>
                    
                    <div class="form-group">
                        <label class="form-label">Выберите способ доставки</label>
                        <div class="delivery-options">
                            <div class="delivery-option">
                                <input type="radio" name="delivery_type" id="belarusian_post" value="belarusian_post" 
                                       {{ old('delivery_type', 'belarusian_post') === 'belarusian_post' ? 'checked' : '' }} required>
                                <label for="belarusian_post" class="delivery-label">
                                    <div class="delivery-info">
                                        <div class="delivery-title">🏤 Белпочта</div>
                                        <div class="delivery-desc">Доставка на домашний адрес</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="delivery-option">
                                <input type="radio" name="delivery_type" id="euro_post" value="euro_post" 
                                       {{ old('delivery_type') === 'euro_post' ? 'checked' : '' }}>
                                <label for="euro_post" class="delivery-label">
                                    <div class="delivery-info">
                                        <div class="delivery-title">📦 Европочта</div>
                                        <div class="delivery-desc">Доставка в пункт выдачи</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Способ оплаты</h2>
                    
                    <div class="form-group">
                        <label class="form-label">Способ оплаты</label>
                        <div class="payment-single">
                            <div class="payment-info">
                                <div class="payment-title">💵 Наложенный платеж</div>
                                <div class="payment-desc">Оплата при получении заказа</div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_type" value="cash_on_delivery">
                    </div>
                </div>
                
                <div class="form-section" id="address-section">
                    <h2>Адрес доставки</h2>
                    
                    <!-- Адрес для Белпочты -->
                    <div id="belarusian_post_fields" class="delivery-fields" 
                         style="{{ old('delivery_type', 'belarusian_post') === 'belarusian_post' ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group">
                            <label class="form-label">Адрес доставки</label>
                            <input type="text" name="address" placeholder="Город, улица, дом, квартира" class="form-input" 
                                   value="{{ Auth::check() ? old('address', Auth::user()->address) : old('address') }}">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Регион доставки</label>
                            <div class="select-wrapper">
                                <select name="region" class="form-select">
                                    <option value="" disabled selected>Выберите регион</option>
                                    <option value="Минск" {{ Auth::check() && Auth::user()->region === 'Минск' ? 'selected' : '' }}>Минск</option>
                                    <option value="Брестская область" {{ Auth::check() && Auth::user()->region === 'Брестская область' ? 'selected' : '' }}>Брестская область</option>
                                    <option value="Витебская область" {{ Auth::check() && Auth::user()->region === 'Витебская область' ? 'selected' : '' }}>Витебская область</option>
                                    <option value="Гомельская область" {{ Auth::check() && Auth::user()->region === 'Гомельская область' ? 'selected' : '' }}>Гомельская область</option>
                                    <option value="Гродненская область" {{ Auth::check() && Auth::user()->region === 'Гродненская область' ? 'selected' : '' }}>Гродненская область</option>
                                    <option value="Могилёвская область" {{ Auth::check() && Auth::user()->region === 'Могилёвская область' ? 'selected' : '' }}>Могилёвская область</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Адрес для Европочты -->
                    <div id="euro_post_fields" class="delivery-fields" 
                         style="{{ old('delivery_type') === 'euro_post' ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group">
                            <label class="form-label">Адрес пункта выдачи Европочты</label>
                            <input type="text" name="euro_post_address" placeholder="Например: г. Минск, ул. Купревича 1, к. 1, п. 123" class="form-input" 
                                   value="{{ old('euro_post_address') }}">
                            <small class="form-hint">Укажите полный адрес пункта выдачи Европочты</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="checkout-summary">
                <div class="summary-box">
                    <div class="summary-title" style="color: white;">Итого к оплате:</div>
                    <div class="summary-amount">
                        @php
                            $cart = session('cart', []);
                            $total = 0;
                            foreach($cart as $item) {
                                $total += $item['price'] * $item['quantity'];
                            }
                        @endphp
                        {{ number_format($total, 2, ',', ' ') }} BYN
                    </div>
                    <button type="submit" class="btn-submit">Подтвердить заказ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
    const belarusianPostFields = document.getElementById('belarusian_post_fields');
    const euroPostFields = document.getElementById('euro_post_fields');
    const addressInputs = belarusianPostFields.querySelectorAll('input, select');
    const euroPostInputs = euroPostFields.querySelectorAll('input');

    function toggleDeliveryFields() {
        const selectedDelivery = document.querySelector('input[name="delivery_type"]:checked').value;
        
        if (selectedDelivery === 'belarusian_post') {
            belarusianPostFields.style.display = 'block';
            euroPostFields.style.display = 'none';
            
            // Делаем поля Белпочты обязательными
            addressInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
            
            // Убираем обязательность с полей Европочты
            euroPostInputs.forEach(input => {
                input.removeAttribute('required');
            });
        } else if (selectedDelivery === 'euro_post') {
            belarusianPostFields.style.display = 'none';
            euroPostFields.style.display = 'block';
            
            // Убираем обязательность с полей Белпочты
            addressInputs.forEach(input => {
                input.removeAttribute('required');
            });
            
            // Делаем поля Европочты обязательными
            euroPostInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
        }
    }

    // Обработчик изменения способа доставки
    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', toggleDeliveryFields);
    });

    // Инициализация при загрузке
    toggleDeliveryFields();
});
</script>
@endsection