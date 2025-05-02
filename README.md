# awdindividualwork

## requirements:

### Веб-приложение должно включать следующие компоненты и функциональные возможности:

- ~~Аутентификация пользователей~~ - user/admin implemented
- ~~Реализуйте механизм регистрации и входа в систему.~~
- ~~После успешной аутентификации пользователю предоставляется доступ к защищённым разделам сайта.~~ - admin user gets to CRUD content and modify (give admin permission, take away admin permission, delete) users in database. might need to add create functionality for admins-users 
- ~~Данные аутентификации (например, логин и пароль) должны храниться безопасным образом с использованием хеширования в базе данных.~~ - password_hash(), password_verify() implemented, no raw passwords stored in database

- Доп. Задание. Реализуйте механизм восстановления пароля (например, через электронную почту).

### Общедоступный компонент