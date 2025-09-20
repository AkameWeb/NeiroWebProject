NeuroMusic - Умный поиск музыки через нейросеть 🎵
(https://img.shields.io/badge/PHP-8.2%252B-777BB4?logo=php)
(https://img.shields.io/badge/OpenAI-GPT-412991?logo=openai)
(https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)
(https://img.shields.io/badge/License-MIT-green)

NeuroMusic - это современное веб-приложение для интеллектуального поиска музыки с использованием искусственного интеллекта. Просто опишите песню своими словами, и нейросеть найдет подходящие треки!

✨ Возможности
🎯 Умный поиск - Нейросеть понимает естественный язык

🔍 Глубокий анализ - Учитывает жанр, настроение и эпоху

💡 Персональные рекомендации - Предлагает треки по вашим предпочтениям

🎧 Мгновенное прослушивание - Слушайте треки прямо на сайте

🔐 Система авторизации - Регистрация и вход с запоминанием сессии

🚀 Быстрый старт
Предварительные требования
Docker Desktop для macOS или Windows

OpenAI API ключ (получите на platform.openai.com)

Установка за 3 шага
Клонируйте репозиторий

bash
git clone https://github.com/yourusername/neuromusic.git
cd neuromusic
Настройте окружение

bash
# Скопируйте и отредактируйте .env файл
cp .env.example .env
# Добавьте ваш OpenAI API ключ в .env файл
Запустите приложение

bash
# Соберите и запустите контейнеры
docker-compose up -d

# Откройте в браузере
open http://localhost:8080
📦 Docker компоновка
Приложение состоит из трех контейнеров:

neuromusic-app - PHP Apache с приложением

neuromusic-mysql - MySQL база данных

neuromusic-phpmyadmin - Веб-интерфейс для управления БД

⚙️ Конфигурация
Переменные окружения
Создайте файл .env на основе .env.example:

env
OPENAI_API_KEY=your-openai-api-key-here
DB_HOST=mysql
DB_NAME=neuromusic
DB_USER=neuromusic_user
DB_PASSWORD=neuromusic_password
PHP_MEMORY_LIMIT=512M
APP_ENV=development
Порты по умолчанию
Приложение: http://localhost:8080

PHPMyAdmin: http://localhost:8081

MySQL: localhost:3307

🎯 Использование
Зарегистрируйтесь или войдите в систему

Опишите желаемую музыку в поисковой строке:

"энергичный рок для тренировки"

"грустная баллада о любви"

"джаз с саксофоном из 60-х"

"что-то похожее на The Beatles"

Получите результаты - нейросеть проанализирует запрос и найдет подходящие треки

Слушайте треки - прямо в браузере

🛠️ Разработка
Структура проекта
text
neuromusic/
├── src/                 # Исходный код PHP
├── docker/              # Docker конфигурации
├── uploads/            # Загружаемые файлы
├── logs/               # Логи приложения
├── docker-compose.yml  # Docker компоновка
├── Dockerfile          # Образ приложения
└── README.md          # Документация
Команды разработки
bash
# Запуск в режиме разработки
docker-compose up -d

# Просмотр логов
docker-compose logs -f neuromusic-app

# Остановка приложения
docker-compose down

# Пересборка образов
docker-compose build --no-cache

# Запуск терминала в контейнере
docker-compose exec neuromusic-app bash
🔧 Troubleshooting
common issues
Docker не запущен

bash
# Откройте Docker Desktop и дождитесь запуска
Порт уже занят

bash
# Измените порты в docker-compose.yml
ports:
  - "8081:80"  # вместо 8080
Нет доступа к файлам

bash
# Настройте File Sharing в Docker Desktop
# Settings → Resources → File Sharing
Неверный OpenAI API ключ

bash
# Проверьте ключ в .env файле
# Получите новый на platform.openai.com
🤝 Contributing
Мы приветствуем вклад в развитие проекта!

Форкните репозиторий

Создайте feature branch: git checkout -b feature/amazing-feature

Сделайте коммит: git commit -m 'Add amazing feature'

Запушите ветку: git push origin feature/amazing-feature

Откройте Pull Request

📝 Лицензия
Этот проект распространяется под лицензией MIT. Смотрите файл LICENSE для подробностей.

Дальнейщее развитие программы.
В данный момент разработан фронтент и часть функционала. В будущем планируется привязка к аккаунту ВК,
скачивание музыки и отпределённых её фрагментов. С частичным редактированием аудиодорожки.
