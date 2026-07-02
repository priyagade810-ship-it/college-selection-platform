<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

require_student();

$title = 'My Bookmarks';
$pdo = db();

// Student info
$stmt = $pdo->prepare('SELECT name, student_id FROM students WHERE student_id=? LIMIT 1');
$stmt->execute([(int)$_SESSION['student_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch bookmarked colleges
$sql = "SELECT c.* FROM colleges c 
        JOIN bookmarks b ON c.college_id = b.college_id 
        WHERE b.student_id = :sid";
$q = $pdo->prepare($sql);
$q->execute(['sid'=>$student['student_id']]);
$bookmarks = $q->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/header.php';
?>

<style>
/* Professional & modern style for bookmarks */
body { background-color: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; }
.college-card { 
    background: rgba(255, 255, 255, 0.05); 
    backdrop-filter: blur(15px);
    border-radius: 24px; border:1px solid rgba(255,255,255,0.1);
    padding: 25px; margin-bottom: 20px; transition:0.3s ease;
}
.college-card:hover { transform: translateY(-5px); border-color:#6366f1; background: rgba(255,255,255,0.08);}
.cutoff-label {
    background: linear-gradient(135deg, #6366f1, #a855f7); color: white; padding:8px 16px; border-radius:12px;
    font-weight:800; font-size:14px; box-shadow:0 4px 12px rgba(99,102,241,0.3);
}
.info-box { 
    background: rgba(0,0,0,0.2); padding:15px; border-radius:15px; text-align:center; flex:1; 
    border: 1px solid rgba(255,255,255,0.05); margin-right:10px;
}
.btn-visit { 
    background: #fff; color: #0f172a; border-radius:12px; padding:10px 20px; font-weight:700; 
    text-decoration:none; font-size:13px; transition:0.3s; display:inline-block;
}
.btn-visit:hover { background:#6366f1; color:white; }
.sidebar { 
    background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);
    border-radius: 24px; padding: 30px; position: sticky; top:20px;
    border:1px solid rgba(255,255,255,0.1); text-align:center;
}
.avatar-icon { 
    width: 80px; height: 80px; background: linear-gradient(135deg,#6366f1,#a855f7); 
    border-radius:20px; display:flex; align-items:center; justify-content:center; 
    margin:0 auto 20px; color:white; font-size:35px;
}
.stat-pill { 
    background: rgba(255, 255, 255, 0.03); padding:12px 15px; border-radius:12px; 
    display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; 
    border:1px solid rgba(255,255,255,0.05);
}
</style>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar">
                <div class="avatar-icon"><i class="fas fa-user-graduate"></i></div>
                <h4><?= htmlspecialchars($student['name'] ?? 'Student') ?></h4>
                <div class="stat-pill"><span>💜 Bookmarked Colleges</span><b><?= count($bookmarks) ?></b></div>
                <a href="student_profile.php" class="btn btn-outline-light w-100 mt-4">Update Profile</a>
                <a href="eligible_colleges.php" class="btn btn-outline-light w-100 mt-2">View Eligible Colleges</a>
            </div>
        </div>

        <!-- Bookmarked Colleges -->
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4 text-white"><i class="fas fa-heart me-2 text-pink"></i> My Bookmarked Colleges</h5>

            <?php if(empty($bookmarks)): ?>
                <div class="college-card text-center py-5">
                    <h5>No Bookmarks Yet</h5>
                </div>
            <?php else: ?>
                <?php foreach($bookmarks as $c): ?>
                    <div class="college-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5><?= htmlspecialchars($c['college_name']) ?></h5>
                                <p><?= htmlspecialchars($c['address'] ?? $c['city']) ?></p>
                            </div>
                            <span class="cutoff-label"><?= $c['cut_off'] ?>%</span>
                        </div>
                        <div class="d-flex gap-3 my-3">
                            <div class="info-box">Seats<br><b><?= $c['seats'] ?></b></div>
                            <div class="info-box">Category<br><b><?= $c['category'] ?? 'Open' ?></b></div>
                        </div>
                        <?php if(!empty($c['website_url'])): ?>
                            <a href="<?= htmlspecialchars($c['website_url']) ?>" target="_blank" class="btn-visit">Visit Website</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>