# Индивидуальная работа. Разработка новостного веб-приложения с аутентификацией

## Студенты
**Gachayev Dmitrii I2302**  
**Maximenco Stanislav I2302**

**Выполнено 03.05.2025**  

## Цель работы
Целью работы является разработка защищённого веб-приложения с аутентификацией, взаимодействием с базой данных, управлением пользовательскими ролями и возможностью взаимодействия с контентом через формы.

## Запуск и установка
1. Для работы проекта необходимо установить `PostgreSQL`, создать базу данных, например, `adwli` и повторить архитектуру базы данных:
```sql
CREATE TABLE IF NOT EXISTS users (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    password_hash text NOT NULL,
    role character varying(20) DEFAULT 'user'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS content (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    body text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    category character varying(100),
    is_public boolean DEFAULT true,
    author character varying(100)
);
```
2. Настроить подключение к базе данных, изменив конфигурационный файл `config/config.php`:
```php
<?php
function getPDO(): PDO {
    return new PDO("pgsql:host=localhost;port=YOUR_PORT;dbname=YOUR_DATABASE_NAME", "YOUR_LOGING", "YOUR_PASSWORD", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
```
3. Открыть командную строку в папке проекта и выполнить команду:
```bash
php -S localhost:8000 -t public
```

4. Перейти на `localhost:8000/register.php`