<?php

declare(strict_types=1);

// SAFE ESCAPE FUNCTION (NULL SAFE)
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// REDIRECT
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

// REQUIRE STUDENT LOGIN
function require_student(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['student_id'])) {
        redirect('student_login.php');
    }
}

// REQUIRE ADMIN LOGIN
function require_admin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['admin_id'])) {
        redirect('admin_login.php');
    }
}

// FLASH MESSAGE SET
function flash_set(string $key, string $message): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['_flash'][$key] = $message;
}

// FLASH MESSAGE GET
function flash_get(string $key): ?string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

// ==================== BOOKMARK FUNCTIONS ====================

// Add Bookmark
function add_bookmark(int $student_id, int $college_id): bool
{
    $pdo = db();
    $stmt = $pdo->prepare("INSERT IGNORE INTO bookmarks (student_id, college_id) VALUES (:student_id, :college_id)");
    return $stmt->execute(['student_id' => $student_id, 'college_id' => $college_id]);
}

// Remove Bookmark
function remove_bookmark(int $student_id, int $college_id): bool
{
    $pdo = db();
    $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE student_id = :student_id AND college_id = :college_id");
    return $stmt->execute(['student_id' => $student_id, 'college_id' => $college_id]);
}

// Check if Bookmarked
function is_bookmarked(int $student_id, int $college_id): bool
{
    $pdo = db();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookmarks WHERE student_id = :student_id AND college_id = :college_id");
    $stmt->execute(['student_id' => $student_id, 'college_id' => $college_id]);
    return $stmt->fetchColumn() > 0;
}

// Get all Bookmarked Colleges for a Student
function get_bookmarked_colleges(int $student_id): array
{
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT c.* FROM colleges c
        JOIN bookmarks b ON c.college_id = b.college_id
        WHERE b.student_id = :student_id
        ORDER BY c.college_name
    ");
    $stmt->execute(['student_id' => $student_id]);
    return $stmt->fetchAll();
}