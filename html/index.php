<?php
require("../php/header.php");
?>
<body>
    <header>
        <div class="header-top">
            <div class="auth-container">
                <div class="auth-button" id="authButton">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Войти
                </div>
                
                <div class="user-menu" id="userMenu">
                    <img src="" alt="Аватар" class="user-avatar" id="userAvatar">
                    <span class="user-name" id="userName">Пользователь</span>
                    <button class="logout-btn" id="logoutButton">Выйти</button>
                </div>

                <div class="auth-dropdown" id="authDropdown">
                    <div class="auth-tabs">
                        <div class="auth-tab active" data-tab="login">Вход</div>
                        <div class="auth-tab" data-tab="register">Регистрация</div>
                    </div>

                    <div class="auth-form active" data-form="login">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <input type="email" id="login-email" placeholder="Ваш email">
                        </div>
                        <div class="form-group">
                            <label for="login-password">Пароль</label>
                            <input type="password" id="login-password" placeholder="Ваш пароль">
                        </div>
                        <div class="form-group remember-me">
                            <input type="checkbox" id="remember-me" checked>
                            <label for="remember-me">Запомнить меня</label>
                        </div>
                        <button class="auth-submit" id="loginSubmit">Войти</button>
                    </div>

                    <div class="auth-form" data-form="register">
                        <div class="form-group">
                            <label for="register-name">Имя</label>
                            <input type="text" id="register-name" placeholder="Ваше имя">
                        </div>
                        <div class="form-group">
                            <label for="register-email">Email</label>
                            <input type="email" id="register-email" placeholder="Ваш email">
                        </div>
                        <div class="form-group">
                            <label for="register-password">Пароль</label>
                            <input type="password" id="register-password" placeholder="Придумайте пароль">
                        </div>
                        <div class="form-group">
                            <label for="register-password-confirm">Подтвердите пароль</label>
                            <input type="password" id="register-password-confirm" placeholder="Повторите пароль">
                        </div>
                        <div class="form-group remember-me">
                            <input type="checkbox" id="remember-register" checked>
                            <label for="remember-register">Запомнить меня</label>
                        </div>
                        <button class="auth-submit" id="registerSubmit">Зарегистрироваться</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="header-content">
            <h1>Умный поиск музыки</h1>
            <p>Опишите песню своими словами, и нейросеть найдет её для вас</p>
            
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Введите название песни.." id="searchInput">
                <button class="search-button" id="searchButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

      <main class="container">
        <div class="results-container">
            <div class="results-header">
                <div class="results-count" id="resultsCount">Войдите, чтобы начать поиск</div>
                <div class="filters">
                    <button class="filter-btn active" data-filter="all">Все</button>
                    <button class="filter-btn" data-filter="similar">Похожие</button>
                    <button class="filter-btn" data-filter="popular">Популярные</button>
                    <button class="filter-btn" data-filter="new">Новые</button>
                </div>
            </div>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Нейросеть анализирует ваш запрос...</p>
            </div>

            <div class="results-grid" id="resultsGrid">
                <div class="welcome-message">
                    <h2>🎵 Добро пожаловать в NeuroMusic!</h2>
                    <p>Используйте силу искусственного интеллекта для поиска музыки. Просто опишите что вы ищете, а нейросеть поймет и найдет подходящие треки.</p>
                    
                    <div class="examples">
                        <h3>Примеры запросов:</h3>
                        <ul>
                            <li>⚡ "энергичный рок для тренировки"</li>
                            <li>😢 "грустная баллада о любви"</li>
                            <li>🎷 "джаз с саксофоном из 60-х"</li>
                            <li>🎸 "что-то похожее на The Beatles"</li>
                        </ul>
                    </div>
                    
                    <div class="features">
                        <h3>Возможности:</h3>
                        <div class="feature-grid">
                            <div class="feature">
                                <strong>🎯 Умный поиск</strong>
                                <p>Нейросеть понимает естественный язык</p>
                            </div>
                            <div class="feature">
                                <strong>🔍 Глубокий анализ</strong>
                                <p>Учитывает жанр, настроение и эпоху</p>
                            </div>
                            <div class="feature">
                                <strong>💡 Персональные рекомендации</strong>
                                <p>Предлагает треки по вашим предпочтениям</p>
                            </div>
                            <div class="feature">
                                <strong>🎧 Мгновенное прослушивание</strong>
                                <p>Слушайте треки прямо на сайте</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Audio Player (hidden until needed) -->
    <audio id="audioPlayer" hidden></audio>
    
    <script src="../script/script.js"></script>
<?php
require("../php/footer.php");
?>