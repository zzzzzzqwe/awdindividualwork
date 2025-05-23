# awdindividualwork

## Requirements:

### Веб-приложение должно включать следующие компоненты и функциональные возможности:


todo:
- double check validation for register
- create from admin panel with used email
- create new user with user username


1. Аутентификация пользователей::

- ~~Реализуйте механизм регистрации и входа в систему.~~ user/admin implemented
- ~~После успешной аутентификации пользователю предоставляется доступ к защищённым разделам сайта.~~ - admin user gets to CRUD content and modify (give admin permission, take away admin permission, delete) users in database. might need to add create functionality for admins-users 
- ~~Данные аутентификации (например, логин и пароль) должны храниться безопасным образом с использованием хеширования в базе данных.~~ - password_hash(), password_verify() implemented, no raw passwords stored in database
- Доп. Задание. Реализуйте механизм восстановления пароля (например, через электронную почту).

2. Общедоступный компонент

- ~~Раздел приложения, доступный всем пользователям без необходимости авторизации.~~
- ~~Содержит минимум 2–3 элемента контента, которые формируются динамически с использованием серверных скриптов.~~
- ~~Данные для отображения должны извлекаться из базы данных.~~

3. Формы взаимодействия с пользователем

В приложении необходимо реализовать как минимум две формы:

- ~~Форма создания ресурса~~
- ~~Содержит не менее 5 полей различных типов (текстовые поля, выпадающие списки, переключатели и др.).~~

Обязательные требования:
- ~~Проверка данных как на стороне клиента, так и на стороне сервера~~: 

Browser validation (basic) - every field is required: <input type="text" name="title" required>

Server validation (more complex):  
```php
if (strlen($title) < 5 || strlen($title) > 100) {
    $errors[] = 'Заголовок должен быть от 5 до 100 символов.';
}

if (strlen($body) < 10) {
    $errors[] = 'Содержимое должно быть не короче 10 символов.';
}
// and so on
```

- ~~Обработка ошибок и отображение понятных сообщений пользователю.~~

4. Защищённый компонент (только для авторизованных пользователей):
- ~~Доступен исключительно после входа в систему.~~ - check_auth.php implemented
- ~~Для этого компонента необходимо реализовать роль пользователя «администратор».~~ - implemented
- ~~Администратор должен иметь доступ к 3–7 дополнительным функциям, включая~~:
- ~~Создание новых учётных записей с ролью администратора;~~
- ~~Управление данными в базе данных (просмотр, добавление, редактирование и удаление записей).~~ done

5. Требования к безопасности

- ~~Валидируйте все данные, введенные в формы, чтобы предотвратить внедрение вредоносного кода.~~
- ~~Доступ к закрытым частям приложения должен быть защищён аутентификацией (например, пароль). Дополнительно можно использовать CAPTCHA.~~
- ~~Пароли должны храниться в базе данных с использованием безопасных хэш-функций.~~
- ~~Используйте сессии и переменные сессии (или токены) для управления доступом, чтобы предотвратить обход аутентификации.~~