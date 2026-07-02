<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!empty($_SESSION['admin_id'])) {
    redirect('admin_dashboard.php');
}

$title = 'Admin Login';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        try {
            $pdo = db();
            $stmt = $pdo->prepare('SELECT admin_id, password_hash FROM admins WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, (string)$admin['password_hash'])) {
                $_SESSION['admin_id'] = (int)$admin['admin_id'];
                redirect('admin_dashboard.php');
            }

            $error = 'Invalid credentials.';
        } catch (PDOException $e) {
            $error = 'Database error. Run setup.php first.';
        }
    }
}

require __DIR__ . '/header.php';
?>

<style>

/* 🌈 Animated Gradient Background */
body {
    min-height: 100vh;
    background: linear-gradient(-45deg, #667eea, #764ba2, #4facfe, #43e97b);
    background-size: 400% 400%;
    animation: gradientBG 10s ease infinite;
    font-family: 'Segoe UI', sans-serif;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* 💎 Glass Card */
.admin-card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
    border: none;
    padding: 10px;
}

/* ✨ Title */
.admin-title {
    font-size: 28px;
    font-weight: bold;
    color: #fff;
    text-align: center;
    margin-bottom: 20px;
    letter-spacing: 1px;
    animation: glow 2s infinite alternate;
}

@keyframes glow {
    from { text-shadow: 0 0 10px #fff, 0 0 20px #4facfe; }
    to { text-shadow: 0 0 20px #fff, 0 0 40px #43e97b; }
}

/* Labels */
.form-label {
    color: #fff;
    font-weight: 600;
}

/* Inputs */
.form-control {
    border-radius: 10px;
    border: none;
}

.form-control:focus {
    box-shadow: 0 0 10px rgba(255,255,255,0.6);
}

/* 🚀 Button */
.btn-login {
    background: linear-gradient(45deg, #4facfe, #43e97b);
    border: none;
    font-size: 16px;
    padding: 10px;
    border-radius: 10px;
    transition: 0.3s;
}

.btn-login:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.3);
}

/* Animation */
.fade-in {
    animation: fadeIn 0.8s ease-in-out;
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(30px);}
    to {opacity: 1; transform: translateY(0);}
}

</style>

<!-- ================= LOGIN UI ================= -->
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">

<div class="col-md-5 fade-in">

<div class="card admin-card">
<div class="card-body">

<div class="admin-title">
    🔐 Admin Login
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" autocomplete="off">

<div class="mb-3">
    <label class="form-label">Username</label>
    <input class="form-control"
           name="username"
           value="<?= e((string)($_POST['username'] ?? '')) ?>"
           required>
</div>

<div class="mb-3">
    <label class="form-label">Password</label>
    <input class="form-control"
           type="password"
           name="password"
           required>
</div>

<button class="btn btn-login w-100" type="submit">
    🚀 Login
</button>

</form>

</div>
</div>

</div>
</div>

<?php require __DIR__ . '/footer.php'; ?>