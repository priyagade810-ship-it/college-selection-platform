<?php 
declare(strict_types=1); 
require_once __DIR__ . '/db.php'; 

$pdo = db(); // PDO कनेक्शन मिळवा 

// १. रिव्ह्यू Approve करण्यासाठी लॉजिक 
if (isset($_GET['approve'])) { 
    $id = (int)$_GET['approve']; 
    try { 
        $stmt = $pdo->prepare("UPDATE college_reviews SET status = 'approved' WHERE review_id = :id"); 
        $stmt->execute([':id' => $id]); 
        header("Location: admin_manage_reviews.php?msg=Approved"); 
        exit; 
    } catch (PDOException $e) { 
        die("Error: " . $e->getMessage()); 
    } 
} 

// २. रिव्ह्यू Delete करण्यासाठी लॉजिक 
if (isset($_GET['delete'])) { 
    $id = (int)$_GET['delete']; 
    try { 
        $stmt = $pdo->prepare("DELETE FROM college_reviews WHERE review_id = :id"); 
        $stmt->execute([':id' => $id]); 
        header("Location: admin_manage_reviews.php?msg=Deleted"); 
        exit; 
    } catch (PDOException $e) { 
        die("Error: " . $e->getMessage()); 
    } 
} 

// ३. सर्व रिव्ह्यूची यादी मिळवणे (JOIN वापरून कॉलेजचे नाव काढणे)
try { 
    $sql = "SELECT r.*, c.college_name 
            FROM college_reviews r 
            JOIN colleges c ON r.college_id = c.college_id 
            ORDER BY r.status DESC, r.created_at DESC"; 
    $stmt = $pdo->query($sql); 
    $reviews = $stmt->fetchAll(); 
} catch (PDOException $e) { 
    die("Error: " . $e->getMessage()); 
} 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>Manage Reviews - Admin</title> 
    <style> 
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: Arial, sans-serif; } 
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; } 
        th { background-color: #6c5ce7; color: white; } 
        tr:nth-child(even) { background-color: #f2f2f2; } 
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; font-weight: bold; font-size: 12px; } 
        .approve-btn { background-color: #2ecc71; color: white; } 
        .delete-btn { background-color: #e74c3c; color: white; } 
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .pending { background: #ffeaa7; color: #d63031; }
        .approved { background: #55efc4; color: #00b894; }
    </style> 
</head> 
<body> 

    <h2>Student Reviews Management</h2> 

    <?php if (isset($_GET['msg'])): ?> 
        <p style="color: green; font-weight: bold;">Action Completed: <?php echo htmlspecialchars($_GET['msg']); ?></p> 
    <?php endif; ?> 

    <table> 
        <thead> 
            <tr> 
                <th>Student Name</th> 
                <th>College Name</th> 
                <th>Rating</th> 
                <th>Review</th> 
                <th>Status</th> 
                <th>Action</th> 
            </tr> 
        </thead> 
        <tbody> 
            <?php if (count($reviews) > 0): ?> 
                <?php foreach ($reviews as $review): ?> 
                <tr> 
                    <td><?php echo htmlspecialchars($review['user_name']); ?></td> 
                    <td><b><?php echo htmlspecialchars($review['college_name']); ?></b></td> 
                    <td><?php echo $review['rating']; ?> ⭐</td> 
                    <td><?php echo htmlspecialchars($review['comment']); ?></td> 
                    <td>
                        <?php if($review['status'] === 'pending'): ?>
                            <span class="status-badge pending">Pending</span>
                        <?php else: ?>
                            <span class="status-badge approved">Approved ✅</span>
                        <?php endif; ?>
                    </td> 
                    <td> 
                        <?php if($review['status'] === 'pending'): ?>
                            <a href="?approve=<?php echo $review['review_id']; ?>" class="btn approve-btn" onclick="return confirm('Do you want to approve this review?')">Approve</a> 
                        <?php endif; ?>
                        <a href="?delete=<?php echo $review['review_id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a> 
                    </td> 
                </tr> 
                <?php endforeach; ?> 
            <?php else: ?> 
                <tr> 
                    <td colspan="6" style="text-align: center;">No reviews found.</td> 
                </tr> 
            <?php endif; ?> 
        </tbody> 
    </table> 
    
    <br>
    <a href="admin_dashboard.php" style="text-decoration: none; color: #6c5ce7; font-weight: bold;">← Back to Dashboard</a>

</body> 
</html>
