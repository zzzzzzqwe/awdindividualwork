<?php
require_once 'check_auth.php';
if ($_SESSION['role'] !== 'admin') {
    echo "Доступ запрещён";
    exit;
}
