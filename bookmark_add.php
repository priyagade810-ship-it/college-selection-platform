<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_student();

if(!isset($_GET['college_id'])) {
    redirect('student_eligible.php');
}

$college_id = (int)$_GET['college_id'];
$student_id = (int)$_SESSION['student_id'];

$pdo = db();
$stmt = $pdo->prepare("INSERT IGNORE INTO bookmarks (student_id, college_id) VALUES (?, ?)");
$stmt->execute([$student_id, $college_id]);

redirect('student_eligible.php');