<?php
require 'db.php'; // तुमची फाईल

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $pdo = db(); // PDO कनेक्शन मिळवण्यासाठी

    $college_id = (int)$_POST['college_id'];
    $user_name = $_POST['user_name'];
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO college_reviews (college_id, user_name, rating, comment, status) 
            VALUES (:college_id, :user_name, :rating, :comment, 'pending')";
    
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':college_id' => $college_id,
            ':user_name' => $user_name,
            ':rating' => $rating,
            ':comment' => $comment
        ]);
        echo "<script>alert('Review Submitted! Wait for Admin approval.'); window.location.href='eligible_colleges.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
