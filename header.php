<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$title = $title ?? 'College Selection platform';

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Colleges Selection Platform</a>
        <div class="d-flex gap-2">
            <?php if (!empty($_SESSION['student_id'])): ?>
                <a class="btn btn-outline-light btn-sm" href="student_profile.php">Student</a>
                <a class="btn btn-outline-warning btn-sm" href="student_logout.php">Logout</a>
            <?php elseif (!empty($_SESSION['admin_id'])): ?>
                <a class="btn btn-outline-light btn-sm" href="admin_dashboard.php">Admin</a>
                <a class="btn btn-outline-warning btn-sm" href="admin_logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="student_login.php">Student Login</a>
                <a class="btn btn-outline-light btn-sm" href="admin_login.php">Admin Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container py-4">
