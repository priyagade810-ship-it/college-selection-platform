<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

require_student();

$title = 'Eligible Colleges';
$pdo = db();

// Fetch student info
$stmt = $pdo->prepare('SELECT name, marks, stream, city, category, student_id FROM students WHERE student_id=? LIMIT 1');
$stmt->execute([(int)$_SESSION['student_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Eligible colleges logic
$eligible = [];
if ($student && isset($student['marks'], $student['stream'], $student['city'])) {
    $marks = (float)$student['marks'];
    $lower_limit = $marks - 2.0;
    $category = $student['category'] ?? '';

    $sql = "SELECT college_id, college_name, address, city, cut_off, seats, category, website_url 
            FROM colleges 
            WHERE stream = :stream AND city = :city AND seats > 0 AND cut_off <= :marks AND cut_off >= :lower_limit";

    if ($category === '' || $category === 'Open' || $category === null) {
        $sql .= " AND (category IS NULL OR category = '' OR category = 'Open')";
        $params = ['stream'=>$student['stream'],'city'=>$student['city'],'marks'=>$marks,'lower_limit'=>$lower_limit];
    } else {
        $sql .= " AND (category IS NULL OR category = '' OR category = 'Open' OR category = :cat)";
        $params = ['stream'=>$student['stream'],'city'=>$student['city'],'marks'=>$marks,'lower_limit'=>$lower_limit,'cat'=>$category];
    }

    $q = $pdo->prepare($sql." ORDER BY cut_off DESC");
    $q->execute($params);
    $eligible = $q->fetchAll(PDO::FETCH_ASSOC);
}

require __DIR__ . '/header.php';
?>

<link rel="stylesheet" href="https://cloudflare.com">

<style>

body{
    background:#071120;
    color:#e2e8f0;
    font-family:'Segoe UI',sans-serif;
}

/* MAIN CONTAINER */

.container{
    max-width:1400px;
}

/* SIDEBAR */

.sidebar{
    background:#0f172a;
    border:1px solid #1e293b;
    border-radius:18px;
    padding:22px;
    position:sticky;
    top:20px;
}

.avatar-icon{
    width:70px;
    height:70px;
    border-radius:18px;
    margin:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    font-size:28px;
    color:white;
}

.sidebar h4{
    font-size:24px;
    text-align:center;
    margin-top:14px;
    margin-bottom:18px;
    font-weight:600;
}

.stat-pill{
    background:#111c31;
    border:1px solid #1f2b46;
    border-radius:12px;
    padding:11px 14px;
    margin-bottom:10px;
    display:flex;
    justify-content:space-between;
    font-size:14px;
}

.recommend-box{
    margin-top:18px;
    background:#111c31;
    border:1px dashed #4f46e5;
    border-radius:16px;
    padding:18px;
    text-align:center;
}

.recommend-box h2{
    margin:0;
    font-size:30px;
    font-weight:700;
}

.sidebar-btn{
    height:44px;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    border:1px solid #334155 !important;
}

/* TITLE */

.page-title{
    font-size:28px;
    font-weight:700;
    margin-bottom:18px;
    color:#fff;
}

/* CARD */

.college-card{
    background:#0f172a;
    border:1px solid #1e293b;
    border-radius:18px;
    padding:18px;
    margin-bottom:18px;
    transition:0.25s;
}

.college-card:hover{
    border-color:#4f46e5;
    transform:translateY(-2px);
}

/* TOP */

.top-section{
    display:flex;
    justify-content:space-between;
    gap:20px;
}

.college-name{
    font-size:22px;
    font-weight:700;
    margin-bottom:4px;
    color:white;
}

.college-location{
    font-size:14px;
    color:#94a3b8;
    margin-bottom:12px;
}

/* BUTTONS */

.btn-trend-link{
    background:#111c31;
    border:1px solid #334155;
    color:#cbd5e1;
    text-decoration:none;
    padding:8px 14px;
    border-radius:10px;
    font-size:13px;
    display:inline-block;
    transition:0.3s;
}

.btn-trend-link:hover{
    background:#4f46e5;
    color:white;
}

.action-buttons{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:14px;
}

.btn-visit{
    border:none;
    border-radius:10px;
    padding:9px 16px;
    font-size:13px;
    font-weight:600;
    text-decoration:none;
}

.website-btn{
    background:white;
    color:#111827;
}

.website-btn:hover{
    background:#4f46e5;
    color:white;
}

.map-btn{
    background:#ef4444;
    color:white;
}

.map-btn:hover{
    background:#dc2626;
    color:white;
}

/* CUTOFF */

.cutoff-label{
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    padding:8px 14px;
    border-radius:10px;
    font-size:13px;
    font-weight:700;
    color:white;
}

/* BOOKMARK */

.btn-bookmark{
    background:none;
    border:none;
    font-size:22px;
    cursor:pointer;
}

/* INFO BOX */

.info-wrapper{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
    margin-top:18px;
}

.info-box{
    background:#111c31;
    border:1px solid #1f2b46;
    border-radius:14px;
    padding:16px;
    text-align:center;
}

.info-title{
    color:#94a3b8;
    font-size:12px;
    margin-bottom:5px;
}

.info-value{
    color:white;
    font-size:20px;
    font-weight:700;
}

/* RATING */

.rating-box{
    background:#111c31;
    border:1px solid #1f2b46;
    border-radius:14px;
    padding:14px;
    margin-top:14px;
}

.review-btn{
    border-radius:8px;
    font-size:12px;
}

/* FORM */

.form-select,
.form-control{
    background:#0f172a !important;
    border:1px solid #334155 !important;
    color:white !important;
    border-radius:10px !important;
    font-size:13px;
}

.submit-review-btn{
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    border:none;
    height:42px;
    border-radius:10px;
    color:white;
    font-weight:600;
}

/* BOTTOM */

.bottom-bar{
    border-top:1px solid #1f2b46;
    margin-top:18px;
    padding-top:14px;
}

.admission-open{
    color:#60a5fa;
    font-size:13px;
    font-weight:600;
}

/* MOBILE */

@media(max-width:992px){

    .top-section{
        flex-direction:column;
    }

    .info-wrapper{
        grid-template-columns:1fr;
    }

    .bottom-bar{
        flex-direction:column;
        gap:14px;
        align-items:flex-start !important;
    }

}

</style>
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar">
                <div class="avatar-icon"><i class="fas fa-user-graduate"></i></div>
                <h4><?= htmlspecialchars($student['name'] ?? 'Student') ?></h4>
                <div class="stat-pill"><span>🎯 Score</span><b><?= $student['marks'] ?>%</b></div>
                <div class="stat-pill"><span>📚 Stream</span><b><?= $student['stream'] ?></b></div>
                <div class="stat-pill"><span>📍 City</span><b><?= $student['city'] ?></b></div>
                <div class="mt-4 p-3 rounded-4" style="background: rgba(99,102,241,0.1); border:1px dashed #6366f1;">
                    <h2><?= count($eligible) ?></h2>
                    <small>Recommended Colleges</small>
                </div>
                <a href="student_profile.php" class="btn btn-outline-light w-100 mt-4">Update Profile</a>
                <a href="my_bookmarks.php" class="btn btn-outline-light w-100 mt-2">My Bookmarks</a>
            </div>
        </div>

        <!-- Colleges -->
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4 text-white"><i class="fas fa-building-columns me-2 text-primary"></i> Recommended Colleges</h5>
            <?php if(empty($eligible)): ?>
                <div class="college-card text-center py-5">
                    <h5>No Matches Found</h5>
                </div>
            <?php else: ?>
                <?php foreach($eligible as $c):
                    $bookmarked = is_bookmarked($student['student_id'], $c['college_id']);
                ?>
                <div class="college-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5><?= htmlspecialchars($c['college_name']) ?></h5>
                            <p><?= htmlspecialchars($c['address'] ?? $c['city']) ?></p>
                            <a href="college_trend.php?id=<?= $c['college_id'] ?>" class="btn-trend-link">View Cut-off Trend</a>
                        </div>
                        <!-- =================== Rating & Review System Start =================== -->
<div class="mt-3 p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
    
    <?php
    // १. सरासरी रेटिंग आणि एकूण रिव्ह्यूची संख्या काढणे
    $stmt_rate = $pdo->prepare("SELECT AVG(rating) as avg_r, COUNT(*) as total_r FROM college_reviews WHERE college_id = :cid AND status = 'approved'");
    $stmt_rate->execute([':cid' => $c['college_id']]);
    $review_summary = $stmt_rate->fetch();
    $avg_rating = round((float)$review_summary['avg_r'], 1);
    ?>

    <!-- रेटिंग डिस्प्ले आणि कंट्रोल्स -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <span style="color: #f1c40f; font-weight: bold; font-size: 14px;">
                <?= ($avg_rating > 0) ? $avg_rating . " ⭐" : "No Ratings Yet" ?>
            </span>
            <?php if ($review_summary['total_r'] > 0): ?>
                <button class="btn btn-link btn-sm text-decoration-none p-0 ms-2" onclick="toggleSection('allReviews_<?= $c['college_id'] ?>')" style="font-size: 11px; color: #a855f7;">
                    Read <?= $review_summary['total_r'] ?> Reviews
                </button>
            <?php endif; ?>
        </div>
        <button class="btn btn-sm btn-outline-primary py-0" onclick="toggleSection('reviewForm_<?= $c['college_id'] ?>')" style="font-size: 11px; border-radius: 8px;">Write Review</button>
    </div>

    <!-- विभाग १: आधीचे रिव्ह्यू वाचण्यासाठी (Read Reviews) -->
    <div id="allReviews_<?= $c['college_id'] ?>" style="display: none; background: rgba(0,0,0,0.2); border-radius: 12px; padding: 10px; margin-top: 10px; max-height: 180px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.05);">
        <?php
        $stmt_all = $pdo->prepare("SELECT user_name, rating, comment, created_at FROM college_reviews WHERE college_id = :cid AND status = 'approved' ORDER BY created_at DESC");
        $stmt_all->execute([':cid' => $c['college_id']]);
        $all_reviews = $stmt_all->fetchAll();

        if (count($all_reviews) > 0):
            foreach ($all_reviews as $rev):
        ?>
            <div style="border-bottom: 1px solid rgba(255,255,255,0.05); padding: 8px 0;">
                <div class="d-flex justify-content-between" style="font-size: 12px;">
                    <b style="color: #6366f1;"><?= htmlspecialchars($rev['user_name']) ?></b>
                    <span style="color: #f1c40f;"><?= $rev['rating'] ?> ⭐</span>
                </div>
                <p style="font-size: 12px; margin: 4px 0; color: #cbd5e1; line-height: 1.4;"><?= htmlspecialchars($rev['comment']) ?></p>
                <small style="font-size: 10px; color: #64748b;"><?= date('d M Y', strtotime($rev['created_at'])) ?></small>
            </div>
        <?php 
            endforeach; 
        else:
            echo "<small style='color:#64748b;'>No reviews yet.</small>";
        endif;
        ?>
    </div>

    <!-- विभाग २: नवीन रिव्ह्यू लिहिण्यासाठी (Write Review Form) -->
    <div id="reviewForm_<?= $c['college_id'] ?>" style="display: none;" class="mt-3">
        <form action="submit_review.php" method="POST">
            <input type="hidden" name="college_id" value="<?= $c['college_id'] ?>">
            <input type="hidden" name="user_name" value="<?= htmlspecialchars($student['name']) ?>">
            
            <label style="font-size: 11px; color: #94a3b8; margin-bottom: 5px;">Your Rating:</label>
            <select name="rating" class="form-select form-select-sm mb-2" style="background: #1e293b; color: white; border: 1px solid #334155; border-radius: 8px;" required>
                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                <option value="4">⭐⭐⭐⭐ (Good)</option>
                <option value="3">⭐⭐⭐ (Average)</option>
                <option value="2">⭐⭐ (Poor)</option>
                <option value="1">⭐ (Very Bad)</option>
            </select>
            
            <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Tell others about your experience..." style="background: #1e293b; color: white; border: 1px solid #334155; border-radius: 8px;" required></textarea>
            
            <button type="submit" name="submit_review" class="btn btn-primary btn-sm w-100" style="background: linear-gradient(135deg, #6366f1, #a855f7); border: none; font-weight: bold; border-radius: 8px;">Submit Review</button>
        </form>
    </div>
</div>
<!-- =================== Rating & Review System End =================== -->



                        <div class="d-flex gap-2 align-items-center">
                            <span class="cutoff-label"><?= $c['cut_off'] ?>%</span>
                            <button class="btn-bookmark" data-college="<?= $c['college_id'] ?>"><?= $bookmarked ? '💜':'🤍' ?></button>
                        </div>
                    </div>
                    <div class="d-flex gap-3 my-4">
                        <div class="info-box">MARKS<br><b><?= $c['cut_off'] ?>%</b></div>
                        <div class="info-box">SEATS<br><b><?= $c['seats'] ?></b></div>
                        <div class="info-box">CATEGORY<br><b><?= $c['category'] ?? 'Open' ?></b></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <small class="text-primary fw-bold"><i class="fas fa-check-circle me-1"></i> Admission Open 2024-25</small>
                        <!-- Google Map Button -->
        <a href="https://google.com<?= urlencode($c['college_name'] . ' ' . $c['city']) ?>" 
           target="_blank" 
           class="btn-visit" 
           style="background: #ea4335; color: white; display: flex; align-items: center; gap: 5px;">
           <i class="fas fa-map-marker-alt"></i> Map
        </a>

                        <?php if(!empty($c['website_url'])): ?>
                            <a href="<?= htmlspecialchars($c['website_url']) ?>" target="_blank" class="btn-visit text-decoration-none">Visit Website</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-bookmark').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        const collegeId = btn.dataset.college;
        fetch('bookmark_action.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({college_id:collegeId})
        }).then(res=>res.json()).then(data=>{
            if(data.status==='added') btn.textContent='💜';
            else if(data.status==='removed') btn.textContent='🤍';
        });
    });
});
function toggleReviewForm(id) {
    const form = document.getElementById('reviewForm_' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
function showAllReviews(id) {
    const reviewBox = document.getElementById('allReviews_' + id);
    if (reviewBox.style.display === 'none') {
        reviewBox.style.display = 'block';
    } else {
        reviewBox.style.display = 'none';
    }
}
function toggleSection(id) {
    const element = document.getElementById(id);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

</script>

<?php require __DIR__ . '/footer.php'; ?> 