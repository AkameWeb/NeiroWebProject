// ===== COOKIE MANAGER =====
class CookieManager {
    static setCookie(name, value, days = 30) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    static getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    static deleteCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }
}

// ===== AUTH MANAGER =====
class AuthManager {
    constructor() {
        this.isLoggedIn = false;
        this.authButton = document.getElementById('authButton');
        this.userMenu = document.getElementById('userMenu');
        this.userName = document.getElementById('userName');
        this.userAvatar = document.getElementById('userAvatar');
        this.authDropdown = document.getElementById('authDropdown');
        this.resultsCount = document.getElementById('resultsCount');
        this.resultsGrid = document.getElementById('resultsGrid');
        this.welcomeMessage = document.querySelector('.welcome-message');
        
        this.init();
    }

    async init() {
        // Проверяем авторизацию при загрузке
        await this.checkAuth();
    }

    async checkAuth() {
        try {
            const response = await fetch('../php/check_auth.php', {
                method: 'GET',
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.simulateLogin(data.user.username);
            }
        } catch (error) {
            console.error('Auth check error:', error);
        }
    }

    simulateLogin(username) {
        this.isLoggedIn = true;
        
        // Обновляем UI
        this.authButton.style.display = 'none';
        this.userMenu.classList.add('active');
        this.userName.textContent = username;
        this.userAvatar.src = 'https://source.unsplash.com/random/100x100/?person';
        this.authDropdown.classList.remove('active');
        
        // Обновляем сообщение и скрываем приветствие
        this.resultsCount.textContent = 'Введите запрос для поиска музыки';
        if (this.welcomeMessage) {
            this.welcomeMessage.style.display = 'none';
        }
        
        // Показываем пустую сетку результатов
        this.resultsGrid.innerHTML = '';
    }

    async handleLogin() {
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const rememberMe = document.getElementById('remember-me').checked;
        
        if (!email || !password) {
            alert('Пожалуйста, заполните все поля');
            return;
        }

        try {
            const response = await fetch('../php/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: rememberMe
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.simulateLogin(data.user.username);
                // Очищаем форму
                document.getElementById('login-email').value = '';
                document.getElementById('login-password').value = '';
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('Ошибка при авторизации');
        }
    }

    async handleRegister() {
        const name = document.getElementById('register-name').value;
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const passwordConfirm = document.getElementById('register-password-confirm').value;
        const rememberMe = document.getElementById('remember-register').checked;
        
        if (!name || !email || !password || !passwordConfirm) {
            alert('Пожалуйста, заполните все поля');
            return;
        }
        
        if (password !== passwordConfirm) {
            alert('Пароли не совпадают');
            return;
        }
        
        if (password.length < 6) {
            alert('Пароль должен содержать минимум 6 символов');
            return;
        }

        try {
            const response = await fetch('../php/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    password: password,
                    passwordConfirm: passwordConfirm,
                    remember: rememberMe
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.simulateLogin(data.user.username);
                // Очищаем форму
                document.getElementById('register-name').value = '';
                document.getElementById('register-email').value = '';
                document.getElementById('register-password').value = '';
                document.getElementById('register-password-confirm').value = '';
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Register error:', error);
            alert('Ошибка при регистрации');
        }
    }

    async handleLogout() {
        try {
            const response = await fetch('../php/logout.php', {
                method: 'POST',
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.simulateLogout();
            }
        } catch (error) {
            console.error('Logout error:', error);
            this.simulateLogout(); // Все равно разлогиниваем пользователя
        }
    }

    simulateLogout() {
        this.isLoggedIn = false;
        
        // Обновляем UI
        this.authButton.style.display = 'flex';
        this.userMenu.classList.remove('active');
        this.authDropdown.classList.remove('active');
        
        // Показываем приветственное сообщение
        if (this.welcomeMessage) {
            this.welcomeMessage.style.display = 'block';
        } else {
            // Создаем приветственное сообщение если его нет
            this.resultsGrid.innerHTML = `
                <div class="welcome-message">
                    <h2>Добро пожаловать!</h2>
                    <p>Авторизуйтесь, чтобы начать поиск музыки с помощью нейросети</p>
                </div>
            `;
        }
        this.resultsCount.textContent = 'Войдите, чтобы начать поиск';
    }
}

// ===== SEARCH MANAGER =====
class SearchManager {
    constructor() {
        this.resultsGrid = document.getElementById('resultsGrid');
        this.resultsCount = document.getElementById('resultsCount');
        this.loading = document.getElementById('loading');
    }

    async performSearch(query) {
        if (!query.trim()) return;

        this.showLoading();
        
        try {
            const response = await fetch('../php/search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ query: query })
            });

            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.results, data.count);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showError('Ошибка при поиске');
        } finally {
            this.hideLoading();
        }
    }

    displayResults(results, count) {
        this.resultsCount.textContent = `Найдено: ${count} треков`;
        
        if (results.length === 0) {
            this.resultsGrid.innerHTML = `
                <div class="welcome-message">
                    <h2>Ничего не найдено</h2>
                    <p>Попробуйте изменить запрос или использовать другие ключевые слова</p>
                </div>
            `;
            return;
        }

        this.resultsGrid.innerHTML = results.map(track => `
            <div class="track-card" data-id="${track.id}">
                <img src="${track.image}" alt="${track.title}" class="track-image" onerror="this.src='https://source.unsplash.com/random/300x300/?music'">
                <div class="track-info">
                    <div class="track-title">${this.highlightQuery(track.title, track._query)}</div>
                    <div class="track-artist">${this.highlightQuery(track.artist, track._query)}</div>
                    <div class="track-genre">${track.genre}</div>
                    <div class="track-description">${track.description}</div>
                    <div class="track-actions">
                        <span class="duration">${track.duration}</span>
                        <button class="play-btn" onclick="musicPlayer.playTrack(${JSON.stringify(track).replace(/"/g, '&quot;')})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    highlightQuery(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    showLoading() {
        this.loading.classList.add('active');
        this.resultsGrid.innerHTML = '';
    }

    hideLoading() {
        this.loading.classList.remove('active');
    }

    showError(message) {
        this.resultsGrid.innerHTML = `
            <div class="welcome-message">
                <h2>Ошибка поиска</h2>
                <p>${message}</p>
            </div>
        `;
    }
}

// ===== MUSIC PLAYER =====
class MusicPlayer {
    constructor() {
        this.audioPlayer = new Audio();
        this.currentTrack = null;
        this.isPlaying = false;
    }

    playTrack(track) {
        if (this.currentTrack?.id === track.id && this.isPlaying) {
            this.pause();
            return;
        }

        this.currentTrack = track;
        this.audioPlayer.src = track.audio;
        this.audioPlayer.play();
        this.isPlaying = true;
        
        // Показываем уведомление о воспроизведении
        this.showPlayNotification(track);
    }

    pause() {
        this.audioPlayer.pause();
        this.isPlaying = false;
    }

    showPlayNotification(track) {
        // Создаем простое уведомление
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1000;
            max-width: 300px;
        `;
        notification.innerHTML = `
            <strong>🎵 Сейчас играет:</strong><br>
            ${track.title} - ${track.artist}
        `;
        
        document.body.appendChild(notification);
        
        // Автоматически скрываем через 3 секунды
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// ===== GLOBAL VARIABLES =====
let authManager;
let searchManager;
let musicPlayer;

// ===== MAIN INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация менеджеров
    authManager = new AuthManager();
    searchManager = new SearchManager();
    musicPlayer = new MusicPlayer();

    // Получаем элементы DOM
    const authButton = document.getElementById('authButton');
    const authDropdown = document.getElementById('authDropdown');
    const userMenu = document.getElementById('userMenu');
    const authTabs = document.querySelectorAll('.auth-tab');
    const authForms = document.querySelectorAll('.auth-form');
    const loginSubmit = document.getElementById('loginSubmit');
    const registerSubmit = document.getElementById('registerSubmit');
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');
    const logoutButton = document.getElementById('logoutButton');

    // Обработчики авторизации
    authButton.addEventListener('click', function(e) {
        e.stopPropagation();
        authDropdown.classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!authDropdown.contains(e.target) && 
            !authButton.contains(e.target) && 
            !userMenu.contains(e.target)) {
            authDropdown.classList.remove('active');
        }
    });

    authTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Активируем вкладку
            authTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Показываем соответствующую форму
            authForms.forEach(form => {
                form.classList.remove('active');
                if (form.dataset.form === tabName) {
                    form.classList.add('active');
                }
            });
        });
    });

    loginSubmit.addEventListener('click', function(e) {
        e.preventDefault();
        authManager.handleLogin();
    });

    registerSubmit.addEventListener('click', function(e) {
        e.preventDefault();
        authManager.handleRegister();
    });

    logoutButton.addEventListener('click', function(e) {
        e.preventDefault();
        authManager.handleLogout();
    });

    // Обработчики поиска
    searchButton.addEventListener('click', function() {
        if (authManager.isLoggedIn) {
            searchManager.performSearch(searchInput.value.trim());
        } else {
            authDropdown.classList.add('active');
        }
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && authManager.isLoggedIn) {
            searchManager.performSearch(searchInput.value.trim());
        }
    });
});