<?php
declare(strict_types=1);
require_once 'db.php';
require_once 'functions.php';
session_start();

$student_id = $_SESSION['student_id'] ?? 0;
if (!$student_id) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$college_id = (int)($input['college_id'] ?? 0);

if(!$college_id) {
    echo json_encode(['status'=>'error','message'=>'Invalid college id']);
    exit;
}

if(is_bookmarked($student_id, $college_id)) {
    remove_bookmark($student_id, $college_id);
    echo json_encode(['status' => 'removed']);
} else {
    add_bookmark($student_id, $college_id);
    echo json_encode(['status' => 'added']);
}