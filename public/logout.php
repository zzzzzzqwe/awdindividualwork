<?php
/**
 * Выход из системы.
 *
 * Завершает сессию пользователя, удаляет все данные авторизации
 * и перенаправляет обратно на страницу входа.
 *
 * PHP version 8.4.4
 * 
 * @author Dmitrii
 * @author Stanislav
 */
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
