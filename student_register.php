<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$title = 'Student Registration';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $pdo = db();
            $stmt = $pdo->prepare(
                'INSERT INTO students (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $_SESSION['student_id'] = (int)$pdo->lastInsertId();
            redirect('student_profile.php');
        } catch (PDOException $e) {
            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                $error = 'Email is already registered.';
            } else {
                $error = 'Database error. Please run setup.php first.';
            }
        }
    }
}

require __DIR__ . '/header.php';
?>

<!-- FULL SCREEN BACKGROUND -->
<div class="register-page d-flex justify-content-center align-items-center">

    <!-- GLASS INFO BOX -->
    <div class="register-box text-center">

        <!-- AI STUDENT IMAGE -->
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png"
             class="register-logo mb-3"
             alt="Student">

        <!-- COLORFUL TITLE -->
        <h1 class="register-title mb-2">
            Student Registration
        </h1>

        <p class="text-muted mb-4">
            Create your account to explore eligible colleges using AI assistance
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">

            <div class="mb-3 text-start">
                <label class="form-label fw-semibold">Full Name</label>
                <input class="form-control"
                       name="name"
                       required
                       value="<?= e((string)($_POST['name'] ?? '')) ?>">
            </div>

            <div class="mb-3 text-start">
                <label class="form-label fw-semibold">Email Address</label>
                <input class="form-control"
                       type="email"
                       name="email"
                       required
                       value="<?= e((string)($_POST['email'] ?? '')) ?>">
            </div>

            <div class="mb-3 text-start">
                <label class="form-label fw-semibold">Password</label>
                <input class="form-control"
                       type="password"
                       name="password"
                       required>
            </div>

            <button class="btn btn-primary w-100 mb-2" type="submit">
                Create Account
            </button>

            <a class="btn btn-outline-dark w-100" href="student_login.php">
                Already have an account? Login
            </a>
        </form>
    </div>
</div>

<!-- STYLES -->
<style>
/* Full page background */
.register-page{
    min-height: 100vh;
    background: url('https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1800&q=80')
                center / cover no-repeat;
    padding: 20px;
}

/* Glass box */
.register-box{
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 40px 35px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

/* Logo */
.register-logo{
    width: 90px;
    height: 90px;
}

/* Colorful title */
.register-title{
    font-size: 2.2rem;
    font-weight: 700;
    background: linear-gradient(90deg, #0d6efd, #20c997, #6610f2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Inputs */
.form-control{
    border-radius: 10px;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
