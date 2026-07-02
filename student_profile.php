<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Ensure the user is logged in as a student
require_student();

$title = 'Student Profile';
$pdo = db();
$error = null;

/** 
 * 1. FETCH LATEST NOTIFICATIONS
 */
try {
    $notif_stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
    $notifications = $notif_stmt->fetchAll();
} catch (PDOException $e) {
    $notifications = [];
}

/** 
 * 2. FETCH CURRENT STUDENT DATA
 */
$stmt = $pdo->prepare('SELECT * FROM students WHERE student_id = ? LIMIT 1');
$stmt->execute([(int)$_SESSION['student_id']]);
$student = $stmt->fetch();

/** 
 * 3. HANDLE FORM SUBMISSION
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marks = trim((string)($_POST['marks'] ?? ''));
    $stream = (string)($_POST['stream'] ?? '');
    $city = trim((string)($_POST['city'] ?? ''));
    $category = trim((string)($_POST['category'] ?? ''));

    if (!is_numeric($marks) || (float)$marks <= 0 || (float)$marks > 100) {
        $error = 'Marks must be between 0 and 100.';
    }

    if (!$error) {
        $upd = $pdo->prepare(
            'UPDATE students SET marks=?, stream=?, city=?, category=? WHERE student_id=?'
        );
        $upd->execute([
            (float)$marks,
            $stream,
            $city,
            $category === '' ? null : $category,
            (int)$_SESSION['student_id']
        ]);

        redirect('eligible_colleges.php');
    }
}

require __DIR__ . '/header.php';
?>

<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cloudflare.com">

<style>
    :root {
        --primary: #6366f1;
        --secondary: #a855f7;
        --bg-dark: #0f172a;
        --glass: rgba(255, 255, 255, 0.05);
        --text-white: #f8fafc;
    }

    body {
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
        color: var(--text-white);
        margin: 0;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    /* --- Background Floating Circles --- */
    .bg-circle {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        z-index: -1;
        opacity: 0.4;
        animation: move 20s infinite alternate;
    }
    .c1 { width: 300px; height: 300px; background: var(--primary); top: -50px; left: -50px; }
    .c2 { width: 250px; height: 250px; background: var(--secondary); bottom: -50px; right: -50px; animation-delay: -5s; }

    @keyframes move {
        from { transform: translate(0,0); }
        to { transform: translate(50px, 100px); }
    }

    /* --- NEWS TICKER --- */
    .ticker-wrap {
        width: 100%;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding: 10px 0;
        margin-bottom: 30px;
    }

    .ticker-container {
        display: flex;
        max-width: 1200px;
        margin: 0 auto;
        overflow: hidden;
    }

    .ticker-label {
        background: var(--primary);
        padding: 5px 15px;
        font-weight: bold;
        font-size: 12px;
        text-transform: uppercase;
        border-radius: 5px;
        margin-right: 15px;
        z-index: 2;
    }

    .ticker-content {
        white-space: nowrap;
        animation: ticker-scroll 30s linear infinite;
    }

    @keyframes ticker-scroll {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }

    .ticker-item {
        display: inline-block;
        margin-right: 50px;
        font-size: 14px;
        color: #cbd5e1;
    }

    /* --- PROFILE CARD (Centered) --- */
    .main-container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .profile-card {
        background: var(--glass);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        padding: 40px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .welcome-msg {
        font-size: 28px;
        font-weight: 700;
        background: linear-gradient(to right, #818cf8, #c084fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    /* --- FORM ELEMENTS --- */
    .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #94a3b8;
        margin-bottom: 8px;
        display: block;
    }

    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 15px;
        padding: 14px;
        margin-bottom: 20px;
        transition: 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary) !important;
        background: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
    }

    .form-select option { background: #1e293b; color: #fff; }

    /* --- BUTTON --- */
    .btn-submit {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        padding: 16px;
        border-radius: 15px;
        width: 100%;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: 0.4s;
        margin-top: 10px;
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(168, 85, 247, 0.4);
        filter: brightness(1.1);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #fca5a5;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<!-- Background Circles -->
<div class="bg-circle c1"></div>
<div class="bg-circle c2"></div>

<!-- SCROLLING UPDATES -->
<div class="ticker-wrap">
    <div class="ticker-container">
        <div class="ticker-label">Updates</div>
        <div class="ticker-content">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $n): ?>
                    <span class="ticker-item">
                        <i class="fas fa-bullhorn text-warning me-2"></i> 
                        <?php echo htmlspecialchars($n['message']); ?> 
                        <small class="ms-2 opacity-50"><?php echo date('h:i A', strtotime($n['created_at'])); ?></small>
                    </span>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="ticker-item">Welcome! Update your marks to see the best matching colleges for your career.</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="profile-card">
        <div class="text-center mb-5">
            <div class="mb-3">
                <div style="width: 80px; height: 80px; background: var(--glass); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-user-graduate fa-2x" style="color: var(--primary);"></i>
                </div>
            </div>
            <h2 class="welcome-msg">Welcome, <?php echo htmlspecialchars($student['name'] ?? 'Student'); ?>!</h2>
            <p style="color: #94a3b8; font-size: 14px;">Please complete your academic profile</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-circle-exclamation me-2"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-percent me-2"></i>10th Aggregate Marks</label>
                    <input type="text" class="form-control" name="marks" placeholder="e.g. 85.50" 
                           value="<?= e((string)($student['marks'] ?? '')) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-graduation-cap me-2"></i>Select Stream</label>
                    <select class="form-select" name="stream" required>
                        <?php foreach (['Science','Commerce','Arts'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($student['stream'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-city me-2"></i>Preferred City</label>
                    <select class="form-select" name="city" required>
                        <option value="Pune" <?= ($student['city'] ?? '') === 'Pune' ? 'selected' : '' ?>>Pune</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label"><i class="fas fa-users me-2"></i>Category</label>
                    <select class="form-select" name="category">
                        <option value="OPEN" <?= ($student['category'] ?? '') === 'OPEN' ? 'selected' : '' ?>>OPEN</option>
                        <option value="OBC" <?= ($student['category'] ?? '') === 'OBC' ? 'selected' : '' ?>>OBC</option>
                        <option value="ST" <?= ($student['category'] ?? '') === 'ST' ? 'selected' : '' ?>>ST</option>
                        <option value="NT" <?= ($student['category'] ?? '') === 'NT' ? 'selected' : '' ?>>NT</option>
                         <option value="OTHER" <?= ($student['category'] ?? '') === 'OTHER' ? 'selected' : '' ?>>OTHER</option>

                    </select>
                </div>
            </div>

            <button class="btn-submit" type="submit">
                Find Eligible Colleges <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
