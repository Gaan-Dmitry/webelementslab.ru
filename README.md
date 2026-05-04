# WebElementsLab.ru — Библиотека готовых HTML, CSS и JS сниппетов

![WebElementsLab Logo](assets/img/logo512.png)

**WebElementsLab** — это полнофункциональная веб-платформа для хранения, обмена и использования готовых сниппетов кода на HTML, CSS и JavaScript. Проект предназначен для веб-разработчиков, которые хотят быстро находить, тестировать и внедрять проверенные UI-компоненты и решения в свои проекты.

## 📋 Оглавление

- [О проекте](#о-проекте)
- [Основные возможности](#основные-возможности)
- [Технологический стек](#технологический-стек)
- [Требования к серверу](#требования-к-серверу)
- [Структура проекта](#структура-проекта)
- [База данных](#база-данных)
- [Установка и настройка](#установка-и-настройка)
- [Архитектура приложения](#архитектура-приложения)
- [Функциональные модули](#функциональные-модули)
- [Система безопасности](#система-безопасности)
- [SEO-оптимизация](#seo-оптимизация)
- [API и обработчики](#api-и-обработчики)
- [Система шаблонов](#система-шаблонов)
- [Работа с файлами](#работа-с-файлами)
- [Лицензия](#лицензия)

---

## 📖 О проекте

WebElementsLab представляет собой современную библиотеку сниппетов с возможностью:
- Просмотра каталога готовых решений
- Поиска и фильтрации по тегам
- Предварительного просмотра кода в изолированном iframe
- Добавления сниппетов в избранное
- Создания и редактирования собственных сниппетов
- Управления профилем пользователя с загрузкой аватара и фона
- Подписки на обновления

Проект разработан с учётом современных стандартов веб-разработки, включает адаптивный дизайн, систему аутентификации, защиту от основных веб-угроз и SEO-оптимизацию.

---

## ✨ Основные возможности

### Для всех пользователей:
- **Просмотр каталога** — бесконечная лента сниппетов с ленивой загрузкой
- **Поиск** — фильтрация по названию и тегам
- **Предпросмотр** — живое превью HTML/CSS/JS кода в sandboxed iframe
- **Просмотр карточки** — детальная страница сниппета с табами для HTML, CSS, JS
- **Копирование кода** — кнопка быстрого копирования с подтверждением
- **Случайный сниппет** — переход к случайному элементу из базы
- **Гайд** — руководство по подключению CSS и JS

### Для зарегистрированных пользователей:
- **Избранное** — сохранение сниппетов в личный список избранного
- **Создание сниппетов** — добавление новых элементов с предпросмотром
- **Редактирование** — изменение только своих сниппетов
- **Профиль** — персональная страница с информацией о пользователе
- **Настройки профиля** — загрузка аватара и фона, обрезка изображений
- **Социальные ссылки** — интеграция с VK, Telegram, GitHub

---

## 🛠 Технологический стек

### Backend:
- **PHP 7.4+** — серверный язык программирования
- **PDO (PHP Data Objects)** — безопасная работа с базой данных
- **MySQL 5.7+** — реляционная база данных
- **Session management** — управление сессиями пользователей

### Frontend:
- **HTML5** — семантическая разметка
- **CSS3** — стилизация с использованием:
  - CSS Custom Properties (переменные)
  - Flexbox для layout
  - Модульная структура (BEM-подобный подход)
  - Адаптивный дизайн
- **Vanilla JavaScript (ES6+)** — клиентская логика без фреймворков
- **Prism.js** — подсветка синтаксиса кода
- **Cropper.js** — обрезка изображений аватара и фона

### Дополнительные технологии:
- **Web Share API** — нативный шеринг на мобильных устройствах
- **Clipboard API** — копирование в буфер обмена
- **LocalStorage** — сохранение темы оформления
- **Sandboxed iframe** — безопасный предпросмотр кода

---

## 📋 Требования к серверу

| Компонент | Минимальная версия | Рекомендуемая версия |
|-----------|-------------------|---------------------|
| PHP | 7.4 | 8.0+ |
| MySQL | 5.7 | 8.0+ |
| Apache/Nginx | Любая современная | С поддержкой mod_rewrite |
| SSL | Рекомендуется | Обязательно для продакшена |

### Необходимые PHP-расширения:
- `pdo_mysql`
- `json`
- `session`
- `fileinfo` (для загрузки файлов)

### Права доступа:
Директория `uploads/` должна иметь права на запись (755 или 775).

---

## 📁 Структура проекта

```
/workspace
├── index.php                     # Главная страница с лентой сниппетов
├── 404.php                       # Страница ошибки 404
├── README.md                     # Документация проекта
├── ТЗ.txt                        # Техническое задание для SEO-специалиста
│
├── assets/                       # Статические ресурсы
│   ├── css/                      # Таблицы стилей
│   │   ├── style.css             # Главный файл стилей (импортирует модули)
│   │   ├── adaptive.css          # Адаптивные стили
│   │   ├── base/                 # Базовые стили
│   │   │   ├── _reset.css        # Сброс стилей браузера
│   │   │   ├── _variables.css    # CSS-переменные
│   │   │   └── _links.css        # Стили ссылок
│   │   ├── components/           # Компоненты UI
│   │   │   ├── _anim.css         # Анимации
│   │   │   ├── _auth.css         # Формы авторизации
│   │   │   ├── _buttons.css      # Кнопки
│   │   │   ├── _cards.css        # Карточки сниппетов
│   │   │   ├── _copy.css         # Кнопки копирования
│   │   │   ├── _forms.css        # Формы
│   │   │   ├── _guide.css        # Страница гайда
│   │   │   ├── _pre.css          # Блоки кода
│   │   │   ├── _spinner.css      # Индикаторы загрузки
│   │   │   ├── _subblock.css     # Подблоки
│   │   │   ├── _tabs.css         # Вкладки
│   │   │   └── _tags.css         # Теги
│   │   ├── layout/               # Layout-стили
│   │   │   ├── _header.css       # Шапка сайта
│   │   │   ├── _footer.css       # Подвал сайта
│   │   │   ├── _main.css         # Основной контент
│   │   │   └── _block-main.css   # Блоки контента
│   │   ├── pages/                # Стили страниц
│   │   │   ├── 404.css           # Страница 404
│   │   │   ├── favorites.css     # Избранное
│   │   │   ├── profile.css       # Профиль
│   │   │   └── settings.css      # Настройки
│   │   └── utils/                # Утилиты
│   │       ├── _flex.css         # Flexbox-утилиты
│   │       ├── _font.css         # Шрифты
│   │       └── _position.css     # Позиционирование
│   │
│   ├── js/                       # JavaScript файлы
│   │   ├── main.js               # Основная логика главной страницы
│   │   ├── snippet.js            # Логика карточки сниппета
│   │   ├── form_validation.js    # Валидация форм
│   │   ├── theme-toggle.js       # Переключение темы
│   │   ├── profile-header.js     # Выпадающее меню профиля
│   │   ├── profile-settings.js   # Настройки профиля (кроппер)
│   │   ├── desktop-only.js       # Desktop-специфичный код
│   │   └── script.js             # (пустой, зарезервирован)
│   │
│   └── img/                      # Изображения
│       ├── logo.svg              # Логотип SVG
│       ├── logo192.png           # Иконка 192x192
│       ├── logo512.png           # Иконка 512x512
│       └── favicon.ico           # Favicon
│
├── handlers/                     # Обработчики AJAX-запросов
│   ├── load_snippets.php         # Загрузка списка сниппетов
│   ├── login_handler.php         # Обработка входа
│   ├── toggle_fav.php            # Добавление/удаление из избранного
│   └── password_reset_handler.php # Сброс пароля
│
├── includes/                     # Подключаемые PHP-файлы
│   └── db.php                    # Подключение к базе данных
│
├── pages/                        # Страницы приложения
│   ├── card.php                  # Карточка сниппета
│   ├── edit_or_create_card.php   # Создание/редактирование сниппета
│   ├── favorites.php             # Избранные сниппеты
│   ├── guide.php                 # Гайд по подключению CSS/JS
│   ├── login.php                 # Страница входа
│   ├── logout.php                # Выход из системы
│   ├── password_reset.php        # Восстановление пароля
│   ├── privacy.php               # Политика конфиденциальности
│   ├── profile.php               # Профиль пользователя
│   ├── random_snippet.php        # Перенаправление на случайный сниппет
│   ├── register.php              # Регистрация
│   ├── settings.php              # Настройки профиля
│   └── check_field.php           # Проверка уникальности полей
│
├── templates/                    # Шаблоны частей страниц
│   ├── header.php                # Шапка сайта
│   └── footer.php                # Подвал сайта
│
└── uploads/                      # Загруженные пользователями файлы
    ├── default-avatar.png        # Аватар по умолчанию
    ├── default-bg.jpg            # Фон по умолчанию
    ├── avatar_*.jpeg/png/webp    # Аватары пользователей
    └── bg_*.jpg                  # Фоны профилей
```

---

## 🗄 База данных

### Таблицы

#### `users` — Пользователи
| Поле | Тип | Описание |
|------|-----|----------|
| id | INT PRIMARY KEY AUTO_INCREMENT | ID пользователя |
| username | VARCHAR | Уникальное имя пользователя |
| email | VARCHAR UNIQUE | Email (логин) |
| password | VARCHAR | Хеш пароля (PASSWORD_DEFAULT) |
| role | VARCHAR | Роль ('user', 'admin') |
| created_at | TIMESTAMP | Дата регистрации |

#### `user_profiles` — Профили пользователей
| Поле | Тип | Описание |
|------|-----|----------|
| id | INT PRIMARY KEY AUTO_INCREMENT | ID записи |
| user_id | INT FOREIGN KEY | Ссылка на users.id |
| avatar | VARCHAR | Путь к аватару |
| bg_img | VARCHAR | Путь к фоновому изображению |
| bio | TEXT | Информация о себе |
| vk | VARCHAR | Username VK |
| tg | VARCHAR | Username Telegram |
| github | VARCHAR | Username GitHub |

#### `snippets` — Сниппеты кода
| Поле | Тип | Описание |
|------|-----|----------|
| id | INT PRIMARY KEY AUTO_INCREMENT | ID сниппета |
| name | VARCHAR(100) | Название |
| description | TEXT | Описание |
| tag | VARCHAR(100) | Теги через запятую |
| html | TEXT | HTML-код |
| css | TEXT | CSS-код |
| js | TEXT | JavaScript-код |
| user_id | INT FOREIGN KEY | Автор сниппета |
| created_at | DATETIME | Дата создания |

#### `favorites` — Избранное
| Поле | Тип | Описание |
|------|-----|----------|
| id | INT PRIMARY KEY AUTO_INCREMENT | ID записи |
| user_id | INT FOREIGN KEY | ID пользователя |
| snippet_id | INT FOREIGN KEY | ID сниппета |
| created_at | TIMESTAMP | Дата добавления |

#### `password_resets` — Сброс пароля
| Поле | Тип | Описание |
|------|-----|----------|
| id | INT PRIMARY KEY AUTO_INCREMENT | ID записи |
| user_id | INT FOREIGN KEY | ID пользователя |
| token | VARCHAR(64) | Уникальный токен |
| expires_at | DATETIME | Срок действия (1 час) |

---

## ⚙️ Установка и настройка

### Шаг 1: Подготовка окружения
```bash
# Скопируйте файлы проекта в директорию веб-сервера
cp -r /workspace/* /var/www/webelementslab/

# Установите права на запись для директории загрузок
chmod -R 755 /var/www/webelementslab/uploads/
chown -R www-data:www-data /var/www/webelementslab/uploads/
```

### Шаг 2: Настройка базы данных
```sql
-- Создание базы данных
CREATE DATABASE webelementslab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Создание таблиц (пример)
USE webelementslab;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    avatar VARCHAR(255),
    bg_img VARCHAR(255),
    bio TEXT,
    vk VARCHAR(100),
    tg VARCHAR(100),
    github VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE snippets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    tag VARCHAR(100),
    html TEXT,
    css TEXT,
    js TEXT,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    snippet_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (snippet_id) REFERENCES snippets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, snippet_id)
);

CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Шаг 3: Конфигурация подключения к БД
Отредактируйте файл `includes/db.php`:
```php
$host = 'localhost';
$dbname = 'your_database_name';
$user = 'your_username';
$password = 'your_password';
```

### Шаг 4: Настройка веб-сервера

#### Для Apache (.htaccess):
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
```

#### Для Nginx:
```nginx
server {
    listen 80;
    server_name webelementslab.ru;
    root /var/www/webelementslab;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
```

---

## 🏗 Архитектура приложения

### MVC-подобная архитектура
Проект использует упрощённую MVC-архитектуру:

- **Model** — работа с данными через PDO в файлах `handlers/` и `pages/`
- **View** — PHP-шаблоны в `pages/`, `templates/`
- **Controller** — бизнес-логика в `handlers/` и внутри страниц

### Маршрутизация
Прямая маршрутизация через файлы:
- `/` → `index.php`
- `/pages/card.php?id=1` → `pages/card.php`
- `/handlers/load_snippets.php` → AJAX endpoint

### Шаблонизация
Используется нативный PHP с включением частей через `require_once`:
```php
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<!-- Контент страницы -->
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
```

---

## 🔧 Функциональные модули

### 1. Система аутентификации

#### Регистрация (`pages/register.php`, `handlers/`)
- Валидация имени пользователя (3-20 символов, буквы и цифры)
- Валидация email (RFC-стандарт)
- Валидация пароля (минимум 6 символов, спецсимволы)
- Проверка уникальности через AJAX (`check_field.php`)
- Хеширование пароля через `password_hash()`
- Согласие с политикой конфиденциальности

#### Вход (`pages/login.php`, `handlers/login_handler.php`)
- Защита от brute-force (5 попыток, блокировка на 30 секунд)
- JSON-ответы для AJAX
- Сохранение сессии при успешном входе
- Перенаправление на предыдущую страницу

#### Выход (`pages/logout.php`)
- Полная очистка сессии
- Удаление cookie сессии
- Перенаправление на главную

#### Сброс пароля (`pages/password_reset.php`)
- Генерация криптографически стойкого токена
- Сохранение токена с временем истечения (1 час)
- Отправка ссылки на email (требуется настройка mail())

### 2. Управление сниппетами

#### Просмотр каталога (`index.php`, `main.js`)
- Бесконечная прокрутка (Infinite Scroll)
- Пагинация через offset/limit (по 10 записей)
- Ленивая загрузка iframe с превью
- Фильтрация по поисковому запросу

#### Карточка сниппета (`pages/card.php`)
- Превью в iframe с sandbox
- Табы для HTML/CSS/JS
- Подсветка синтаксиса через Prism.js
- Кнопка копирования кода
- Добавление в избранное
- Редактирование для автора

#### Создание/Редактирование (`pages/edit_or_create_card.php`)
- Живой предпросмотр изменений
- Валидация обязательных полей (name, html)
- Автоматическое сохранение автора
- Редактирование только своих сниппетов

### 3. Система избранного

#### Добавление/Удаление (`handlers/toggle_fav.php`)
- Проверка авторизации
- Toggle-логика (добавить/удалить)
- JSON-ответ со статусом

#### Страница избранного (`pages/favorites.php`)
- Сортировка по дате добавления
- Удаление прямо из списка
- Share-функциональность

### 4. Профиль пользователя

#### Просмотр профиля (`pages/profile.php`)
- Отображение аватара и фона
- Социальные ссылки (VK, TG, GitHub)
- Биография пользователя
- Кнопки действий для владельца

#### Настройки (`pages/settings.php`)
- Загрузка аватара и фона
- Обрезка через Cropper.js
- Удаление изображений
- Редактирование биографии и соцсетей
- INSERT/UPDATE логика для `user_profiles`

### 5. Темная тема
- Переключение между 'dark' и 'darker'
- Сохранение в LocalStorage
- CSS-переменные для цветов

---

## 🔒 Система безопасности

### Защита данных
1. **Подготовленные выражения PDO** — защита от SQL-инъекций
2. **password_hash()/password_verify()** — безопасное хеширование
3. **htmlspecialchars()** — экранирование вывода (XSS-защита)
4. **Sandboxed iframe** — изоляция выполняемого кода

### Защита сессий
1. **HttpOnly cookies** — недоступность из JS
2. **Регенерация session ID** — при критических действиях
3. **Очистка памяти** — unset() для паролей

### Защита от атак
1. **Rate limiting** — блокировка после 5 неудачных попыток входа
2. **Валидация входных данных** — фильтрация $_GET, $_POST
3. **CORS** — ограничение источников запросов
4. **Referrer Policy** — no-referrer для iframe

### Безопасность файлов
1. **Проверка MIME-типов** — только изображения
2. **Переименование файлов** — avatar_{user_id}_{timestamp}.ext
3. **Ограничение прав** — 755 на uploads/

---

## 🔍 SEO-оптимизация

### Мета-теги
Каждая страница содержит:
- Уникальный `<title>` (50-60 символов)
- `<meta name="description">` (120-160 символов)
- `<meta name="keywords">`
- Open Graph теги для соцсетей
- Twitter Cards
- Canonical URL

### Индексация
- `robots: index, follow` для публичных страниц
- `robots: noindex, nofollow` для служебных (login, register, settings)
- ЧПУ (человеко-понятные URL)

### Микроразметка
Планируется внедрение schema.org:
- Product для карточек сниппетов
- Article/HowTo для гайдов
- Organization для сайта

### Производительность
- Ленивая загрузка изображений (`loading="lazy"`)
- Версионирование CSS/JS через filemtime()
- Минимизация HTTP-запросов

---

## 🌐 API и обработчики

### endpoints

#### GET `/handlers/load_snippets.php`
Загрузка списка сниппетов для бесконечной ленты.

**Параметры:**
- `offset` (int) — смещение для пагинации
- `q` (string) — поисковый запрос

**Ответ:**
```json
[
  {
    "id": 1,
    "name": "Button Component",
    "description": "Стильная кнопка...",
    "tag": "button,ui",
    "tags": ["button", "ui"],
    "primary_tag": "button",
    "preview": "Текст превью HTML...",
    "html": "...",
    "css": "...",
    "js": "...",
    "is_favorite": false,
    "can_favorite": true
  }
]
```

#### POST `/handlers/login_handler.php`
Аутентификация пользователя.

**Тело запроса (form-data):**
- `login` — email
- `password` — пароль

**Ответы:**
```json
// Успех
{"success": true, "username": "JohnDoe"}

// Ошибка валидации
{"success": false, "errors": ["Неверный пароль"]}

// Блокировка
{"success": false, "errors": ["Слишком много попыток."], "blocked": true, "wait": 25}
```

#### POST `/handlers/toggle_fav.php`
Добавление/удаление из избранного.

**Тело запроса (JSON):**
```json
{"id": 42}
```

**Ответы:**
```json
{"status": "added"}
{"status": "removed"}
{"error": "unauthorized"}
```

#### POST `/handlers/password_reset_handler.php`
Запрос на сброс пароля.

**Тело запроса (form-data):**
- `email` — email пользователя

**Ответ:**
```json
{"success": true, "message": "Ссылка отправлена"}
```

#### GET `/pages/check_field.php`
Проверка уникальности поля.

**Параметры:**
- `field` — 'username' или 'email'
- `value` — проверяемое значение

**Ответ:**
```json
{"exists": true}
```

---

## 🎨 Система шаблонов

### Header (`templates/header.php`)
Компоненты шапки:
- Логотип с ссылкой на главную
- Навигация (Главная, Случайный, Создать)
- Меню профиля (выпадающее):
  - Профиль
  - Настройки
  - Создать
  - Избранное
  - Выход
- Кнопки Вход/Регистрация для гостей

### Footer (`templates/footer.php`)
Компоненты подвала:
- Копирайт с динамическим годом
- Ссылка на GitHub
- Политика конфиденциальности
- Переключатель темы (🌑 Ещё темнее)

---

## 📂 Работа с файлами

### Загрузка изображений
Файл: `pages/settings.php`

**Процесс:**
1. Проверка `$_FILES['avatar']['error'] === UPLOAD_ERR_OK`
2. Генерация имени: `avatar_{user_id}_{timestamp}.{ext}`
3. Перемещение: `move_uploaded_file()`
4. Удаление старого файла при замене
5. Сохранение пути в БД

### Обрезка изображений
Файл: `assets/js/profile-settings.js`

**Библиотека:** Cropper.js v1.5.13

**Функционал:**
- Предпросмотр перед обрезкой
- Кроппинг с соотношением сторон
- Экспорт в canvas/blob
- Отправка на сервер через FormData

### Кеширование
Версионирование файлов через timestamp:
```php
<link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(...) ?>">
<img src="<?= $avatar ?>?t=<?= $avatarTime ?>">
```

---

## 📝 Лицензия

MIT License

Copyright (c) 2025 WebElementsLab

Разрешается бесплатное использование, копирование, модификация и распространение программного обеспечения при условии сохранения уведомления об авторских правах.

---

## 👥 Контакты

- **Email:** admin@01000100.ru
- **GitHub:** https://github.com/4gdv5fg1qq
- **Сайт:** https://webelementslab.ru

---

## 📊 Статистика проекта

| Метрика | Значение |
|---------|----------|
| Язык | PHP + Vanilla JS |
| Строк кода (примерно) | ~5000+ |
| Количество страниц | 13 |
| Количество обработчиков | 4 |
| CSS-модулей | 20+ |
| JS-модулей | 8 |
| Таблиц БД | 5 |

---

> **Примечание для курсовой работы:** Данный проект демонстрирует полный цикл разработки современного веб-приложения: от проектирования базы данных до реализации frontend-логики, включая вопросы безопасности, производительности и SEO-оптимизации. Архитектура проекта позволяет масштабировать функционал и служит отличным примером для изучения fullstack-разработки на PHP.
