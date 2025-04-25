<?php
function getPDO(): PDO {
    return new PDO("pgsql:host=localhost;port=5432;dbname=awdli", "postgres", "password", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
