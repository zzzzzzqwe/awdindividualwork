<?php
/**
 * Конфигурация подключения к базе данных PostgreSQL.
 *
 * Возвращает экземпляр PDO с установленными настройками подключения
 * и режимом обработки ошибок через исключения.
 *
 * PHP version 8.4.4
 * 
 * @author Dmitrii
 * @author Stanislav
 */

 /**
 * Создаёт подключение к базе данных PostgreSQL.
 *
 * @return PDO Объект подключения к базе
 */
function getPDO(): PDO {
    return new PDO("pgsql:host=localhost;port=5432;dbname=awdli", "postgres", "password", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
