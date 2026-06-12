@extends('layouts.app')

@section('title', 'Пользователи - Админ панель')

@section('content')
<div class="admin-dashboard">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Пользователи</h1>
            <p class="admin-subtitle">Управление аккаунтами пользователей</p>
        </div>
        <div class="admin-actions">
            <a href="/admin" class="btn-secondary">
                <span class="btn-icon">←</span>
                Назад к панели
            </a>
        </div>
    </div>

    <div class="admin-content">
        <!-- Статистика пользователей -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <div class="stat-number">{{ \App\Models\User::count() }}</div>
                    <div class="stat-label">Всего пользователей</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📧</div>
                <div class="stat-content">
                    <div class="stat-number">{{ \App\Models\User::where('email_verified_at', '!=', null)->count() }}</div>
                    <div class="stat-label">Подтвердили email</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <div class="stat-number">{{ \App\Models\User::where('created_at', '>=', now()->subDays(30))->count() }}</div>
                    <div class="stat-label">Новые за 30 дней</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-content">
                    <div class="stat-number">{{ \App\Models\Order::count() }}</div>
                    <div class="stat-label">Всего заказов</div>
                </div>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="filters-container">
            <div class="filter-group">
                <label class="filter-label">Поиск:</label>
                <input type="text" id="userSearch" placeholder="Поиск по имени или email..." class="search-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">Роль:</label>
                <select id="roleFilter" class="filter-select">
                    <option value="">Все роли</option>
                    <option value="user">Пользователи</option>
                    <option value="operator">Операторы</option>
                    <option value="admin">Администраторы</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Дата регистрации:</label>
                <select id="dateFilter" class="filter-select">
                    <option value="">Все даты</option>
                    <option value="today">Сегодня</option>
                    <option value="week">За неделю</option>
                    <option value="month">За месяц</option>
                    <option value="year">За год</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Сортировка:</label>
                <select id="sortFilter" class="filter-select">
                    <option value="default">По умолчанию</option>
                    <option value="name-asc">По имени (А-Я)</option>
                    <option value="name-desc">По имени (Я-А)</option>
                    <option value="email-asc">По email (А-Я)</option>
                    <option value="email-desc">По email (Я-А)</option>
                    <option value="created-desc">По дате (новые)</option>
                    <option value="created-asc">По дате (старые)</option>
                    <option value="orders-desc">По заказам (много)</option>
                    <option value="orders-asc">По заказам (мало)</option>
                </select>
            </div>
        </div>

        <!-- Таблица пользователей -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Регистрация</th>
                        <th>Заказы</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @foreach($users as $user)
                    <tr class="user-row" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-role="{{ $user->role }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}" data-date="{{ $user->created_at->format('Y-m-d') }}">
                        <td>
                            <span class="user-id">#{{ $user->id }}</span>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="user-email">{{ $user->email }}</span>
                        </td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">
                                @if($user->role === 'admin')
                                    Администратор
                                @elseif($user->role === 'operator')
                                    Оператор
                                @else
                                    Пользователь
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date-main">{{ $user->created_at->format('d.m.Y') }}</div>
                                <div class="date-time">{{ $user->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="order-count">{{ $user->orders->count() }}</span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <button class="btn-view-small" onclick="viewUser({{ $user->id }})">
                                    <span>👁️</span>
                                </button>
                                @if(auth()->user()->isAdmin())
                                    <button class="btn-edit-small" onclick="editUser({{ $user->id }})">
                                        <span>✏️</span>
                                    </button>
                                    @if(!$user->is_admin)
                                    <button class="btn-delete-small" onclick="showDeleteModal({{ $user->id }})">
                                        <span>🚫️</span>
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($users->isEmpty())
            <div class="no-data">
                <div class="no-data-icon">👥</div>
                <h3>Пользователи не найдены</h3>
                <p>Пока нет зарегистрированных пользователей</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Модальное окно просмотра пользователя -->
<div id="userModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3>Информация о пользователе</h3>
            <button class="modal-close" onclick="closeUserModal()">×</button>
        </div>
        <div class="modal-body" id="userModalBody">
            <!-- Содержимое загружается через JavaScript -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeUserModal()">Закрыть</button>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования пользователя -->
<div id="editUserModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3>Редактировать пользователя</h3>
            <button class="modal-close" onclick="closeEditUserModal()">×</button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                @csrf
                <input type="hidden" name="user_id" id="editUserId">
                <div class="form-group">
                    <label class="form-label">Имя</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Роль</label>
                    <select name="role" class="form-input">
                        <option value="user">Пользователь</option>
                        <option value="operator">Оператор</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="email_verified" class="checkbox">
                        <span class="checkbox-text">Email подтвержден</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeEditUserModal()">Отмена</button>
            <button type="button" class="btn-primary" onclick="saveUser()">Сохранить</button>
        </div>
    </div>
</div>

<script>
// Проверка загрузки JavaScript
console.log('Users page JavaScript loaded');

// Проверка кликов по кнопкам
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - users page');
    
    // Проверяем наличие кнопок
    const viewButtons = document.querySelectorAll('.btn-view-small');
    const editButtons = document.querySelectorAll('.btn-edit-small');
    const deleteButtons = document.querySelectorAll('.btn-delete-small');
    
    console.log('Found view buttons:', viewButtons.length);
    console.log('Found edit buttons:', editButtons.length);
    console.log('Found delete buttons:', deleteButtons.length);
    
    // Добавляем обработчики кликов для всех кнопок
    viewButtons.forEach(btn => {
        console.log('Setting up view button:', btn);
        btn.addEventListener('click', function(e) {
            console.log('View button clicked!', e);
            e.preventDefault();
            e.stopPropagation();
            const userId = this.getAttribute('onclick').match(/viewUser\((\d+)\)/)[1];
            viewUser(userId);
        });
    });
    
    editButtons.forEach(btn => {
        console.log('Setting up edit button:', btn);
        btn.addEventListener('click', function(e) {
            console.log('Edit button clicked!', e);
            e.preventDefault();
            e.stopPropagation();
            const userId = this.getAttribute('onclick').match(/editUser\((\d+)\)/)[1];
            editUser(userId);
        });
    });
    
    deleteButtons.forEach(btn => {
        console.log('Setting up delete button:', btn);
        btn.addEventListener('click', function(e) {
            console.log('Delete button clicked!', e);
            e.preventDefault();
            e.stopPropagation();
            const userId = this.getAttribute('onclick').match(/showDeleteModal\((\d+)\)/)[1];
            showDeleteModal(userId);
        });
    });
});

// Поиск и фильтрация
document.getElementById('userSearch').addEventListener('input', filterAndSortUsers);
document.getElementById('roleFilter').addEventListener('change', filterAndSortUsers);
document.getElementById('dateFilter').addEventListener('change', filterAndSortUsers);
document.getElementById('sortFilter').addEventListener('change', filterAndSortUsers);

function filterAndSortUsers() {
    const searchTerm = document.getElementById('userSearch').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;
    const tbody = document.getElementById('usersTableBody');
    const rows = Array.from(tbody.getElementsByTagName('tr'));
    
    // Фильтрация
    const filteredRows = rows.filter(row => {
        const id = row.querySelector('.user-id')?.textContent || '';
        const name = row.querySelector('.user-name')?.textContent || '';
        const email = row.querySelector('.user-email')?.textContent || '';
        const role = row.dataset.role || '';
        
        const matchesSearch = !searchTerm || id.includes(searchTerm) || name.includes(searchTerm) || email.includes(searchTerm);
        const matchesRole = !roleFilter || role === roleFilter;
        
        let matchesDate = true;
        if (dateFilter) {
            const userDate = new Date(row.dataset.date);
            const today = new Date();
            
            switch(dateFilter) {
                case 'today':
                    matchesDate = userDate.toDateString() === today.toDateString();
                    break;
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matchesDate = userDate >= weekAgo;
                    break;
                case 'month':
                    const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matchesDate = userDate >= monthAgo;
                    break;
                case 'year':
                    const yearAgo = new Date(today.getTime() - 365 * 24 * 60 * 60 * 1000);
                    matchesDate = userDate >= yearAgo;
                    break;
            }
        }
        
        return matchesSearch && matchesRole && matchesDate;
    });
    
    // Сортировка
    filteredRows.sort((a, b) => {
        switch(sortFilter) {
            case 'name-asc':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'name-desc':
                return b.dataset.name.localeCompare(a.dataset.name);
            case 'email-asc':
                return a.dataset.email.localeCompare(b.dataset.email);
            case 'email-desc':
                return b.dataset.email.localeCompare(a.dataset.email);
            case 'created-desc':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'created-asc':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'orders-desc':
                const ordersA = parseInt(a.querySelector('.order-count').textContent);
                const ordersB = parseInt(b.querySelector('.order-count').textContent);
                return ordersB - ordersA;
            case 'orders-asc':
                const ordersA2 = parseInt(a.querySelector('.order-count').textContent);
                const ordersB2 = parseInt(b.querySelector('.order-count').textContent);
                return ordersA2 - ordersB2;
            default:
                return 0;
        }
    });
    
    // Обновление отображения
    rows.forEach(row => {
        row.style.display = 'none';
    });
    
    filteredRows.forEach(row => {
        row.style.display = '';
    });
    
    // Перестановка отсортированных строк
    filteredRows.forEach(row => {
        tbody.appendChild(row);
    });
}

function viewUser(userId) {
    console.log('viewUser called with userId:', userId);
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const modalBody = document.getElementById('userModalBody');
                modalBody.innerHTML = `
                    <div class="user-details">
                        <div class="detail-row">
                            <strong>ID:</strong> #${data.user.id}
                        </div>
                        <div class="detail-row">
                            <strong>Имя:</strong> ${data.user.name}
                        </div>
                        <div class="detail-row">
                            <strong>Email:</strong> ${data.user.email}
                        </div>
                        <div class="detail-row">
                            <strong>Роль:</strong> ${data.user.role === 'admin' ? 'Администратор' : (data.user.role === 'operator' ? 'Оператор' : 'Пользователь')}
                        </div>
                        <div class="detail-row">
                            <strong>Регистрация:</strong> ${data.user.created_at}
                        </div>
                        <div class="detail-row">
                            <strong>Заказов:</strong> ${data.orders_count}
                        </div>
                        <div class="detail-row">
                            <strong>Общая сумма:</strong> ${data.total_spent} BYN
                        </div>
                    </div>
                `;
                
                const modal = document.getElementById('userModal');
                modal.style.display = 'flex';
                modal.style.zIndex = '9999';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100%';
                modal.style.height = '100%';
                modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                
                // Исправляем стили для внутреннего .modal элемента
                const modalInner = modal.querySelector('.modal');
                if (modalInner) {
                    modalInner.style.display = 'block';
                    modalInner.style.position = 'relative';
                    modalInner.style.zIndex = '10000';
                    modalInner.style.backgroundColor = 'white';
                    modalInner.style.padding = '20px';
                    modalInner.style.borderRadius = '8px';
                    modalInner.style.maxWidth = '500px';
                    modalInner.style.width = '90%';
                    modalInner.style.maxHeight = '90vh';
                    modalInner.style.overflowY = 'auto';
                }
                
                console.log('Modal should be visible now');
            } else {
                console.error('API returned error:', data);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

function editUser(userId) {
    console.log('editUser called with userId:', userId);
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Edit user response data:', data);
            if (data.success) {
                document.getElementById('editUserId').value = userId;
                document.querySelector('input[name="name"]').value = data.user.name;
                document.querySelector('input[name="email"]').value = data.user.email;
                document.querySelector('select[name="role"]').value = data.user.role || 'user';
                document.querySelector('input[name="email_verified"]').checked = data.user.email_verified_at !== null;
                
                const modal = document.getElementById('editUserModal');
                console.log('Modal element:', modal);
                console.log('Modal current display:', modal.style.display);
                
                modal.style.display = 'flex';
                modal.style.zIndex = '9999';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100%';
                modal.style.height = '100%';
                modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                
                // Исправляем стили для внутреннего .modal элемента
                const modalInner = modal.querySelector('.modal');
                if (modalInner) {
                    console.log('Found inner modal:', modalInner);
                    modalInner.style.display = 'block';
                    modalInner.style.position = 'relative';
                    modalInner.style.zIndex = '10000';
                    modalInner.style.backgroundColor = 'white';
                    modalInner.style.padding = '20px';
                    modalInner.style.borderRadius = '8px';
                    modalInner.style.maxWidth = '500px';
                    modalInner.style.width = '90%';
                    modalInner.style.maxHeight = '90vh';
                    modalInner.style.overflowY = 'auto';
                    console.log('Inner modal styles applied');
                }
                
                console.log('Edit modal should be visible now');
                console.log('Modal display after setting:', modal.style.display);
            } else {
                console.error('Edit API returned error:', data);
            }
        })
        .catch(error => {
            console.error('Edit fetch error:', error);
        });
}

function closeEditUserModal() {
    const modal = document.getElementById('editUserModal');
    modal.style.display = 'none';
    console.log('Edit modal closed');
}

function closeUserModal() {
    const modal = document.getElementById('userModal');
    modal.style.display = 'none';
    console.log('View modal closed');
}

function saveUser() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    
    fetch(`/admin/users/${formData.get('user_id')}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditUserModal();
            location.reload();
        } else {
            alert('Ошибка при обновлении пользователя');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при обновлении пользователя');
    });
}

function toggleUserStatus(userId) {
    if (confirm('Вы уверены, что хотите изменить статус этого пользователя?')) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка при изменении статуса');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при изменении статуса');
        });
    }
}

function deleteUser(userId) {
    fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Пользователь успешно удален', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Ошибка при удалении пользователя: ' + (data.message || 'Неизвестная ошибка'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при удалении пользователя');
    });
}

// Функция для показа уведомлений
function showNotification(message, type = 'info') {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">
                ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
            </span>
            <span class="notification-message">${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    // Добавляем на страницу
    document.body.appendChild(notification);
    
    // Показываем с анимацией
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Автоматически скрываем через 3 секунды
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Модальное окно удаления
function showDeleteModal(userId) {
    console.log('showDeleteModal called with userId:', userId);
    const modal = document.getElementById('deleteModal');
    const userName = document.querySelector(`tr[data-id="${userId}"] .user-name`)?.textContent || 'Пользователь';
    
    console.log('Found userName:', userName);
    console.log('Modal element:', modal);
    
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteUserId').value = userId;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    console.log('Modal display set to flex');
    
    // Проверка, что модальное окно действительно есть в DOM
    if (!modal) {
        console.error('Modal element not found!');
        alert('Ошибка: модальное окно не найдено');
        return;
    }
    
    // Принудительно показываем модальное окно
    setTimeout(() => {
        console.log('Modal current display:', window.getComputedStyle(modal).display);
        console.log('Modal current visibility:', window.getComputedStyle(modal).visibility);
    }, 100);
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function confirmDelete() {
    const userId = document.getElementById('deleteUserId').value;
    hideDeleteModal();
    deleteUser(userId);
}

// Обработчики для модального окна
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    
    // Закрытие по клику на фон
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            hideDeleteModal();
        }
    });
    
    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal.style.display === 'flex') {
            hideDeleteModal();
        }
    });
});
</script>

<!-- Модальное окно удаления -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Подтверждение удаления</h3>
            <button class="modal-close" onclick="hideDeleteModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-icon">🚫️</div>
            <p>Вы уверены, что хотите удалить пользователя <strong id="deleteUserName"></strong>?</p>
            <p class="modal-warning">Это действие нельзя будет отменить.</p>
            <input type="hidden" id="deleteUserId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideDeleteModal()">Отмена</button>
            <button class="btn btn-danger" onclick="confirmDelete()">Удалить</button>
        </div>
    </div>
</div>

<style>
/* Модальное окно */
.modal-overlay {
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

.modal-overlay[style*="flex"] {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 90%;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-overlay[style*="flex"] .modal-content {
    transform: scale(1);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.modal-body {
    padding: 30px 20px;
    text-align: center;
}

.modal-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.modal-body p {
    margin: 0 0 10px 0;
    color: #374151;
    font-size: 16px;
}

.modal-warning {
    color: #dc2626 !important;
    font-weight: 500;
    font-size: 14px !important;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

/* Уведомления */
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

@media (max-width: 768px) {
    .notification {
        right: 10px;
        left: 10px;
        min-width: auto;
        max-width: none;
    }
}
</style>
@endsection
