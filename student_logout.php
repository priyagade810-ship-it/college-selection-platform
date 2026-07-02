<?php

declare(strict_types=1);

 require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

unset($_SESSION['student_id']);
redirect('index.php');
