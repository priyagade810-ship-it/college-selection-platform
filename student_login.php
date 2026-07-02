<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Redirect if already logged in
if (!empty($_SESSION['student_id'])) {
    redirect('student_profile.php');
}

$title = 'Student Login ';
$error = null;

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        try {
            $pdo = db();
            $stmt = $pdo->prepare('SELECT student_id, password_hash FROM students WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, (string)$user['password_hash'])) {
                $_SESSION['student_id'] = (int)$user['student_id'];
                redirect('student_profile.php');
            }

            $error = 'Invalid credentials.';
        } catch (PDOException $e) {
            $error = 'Database error. Run setup.php first.';
        }
    }
}

require __DIR__ . '/header.php';
?>

<!-- ================= PAGE STYLE ================= -->
<style>
    /* संपूर्ण पेजला सेंटर करणे आणि बॅकग्राउंड सेट करणे */
    body {
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
        background: #0f172a; /* Dark sleek background */
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        position: relative;
    }

    /* --- Floating Animated Circles --- */
    .circle {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));
        backdrop-filter: blur(8px);
        z-index: -1;
        animation: floatAround 20s infinite linear;
    }

    .c1 { width: 80px; height: 80px; top: 10%; left: 15%; animation-duration: 15s; }
    .c2 { width: 120px; height: 120px; bottom: 15%; right: 10%; animation-duration: 25s; opacity: 0.6; }
    .c3 { width: 50px; height: 50px; top: 40%; right: 20%; animation-duration: 18s; }
    .c4 { width: 100px; height: 100px; bottom: 20%; left: 10%; animation-duration: 22s; opacity: 0.4; }
    .c5 { width: 60px; height: 60px; top: 70%; left: 45%; animation-duration: 30s; }

    @keyframes floatAround {
        0% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(40px, 60px) rotate(120deg); }
        66% { transform: translate(-30px, 30px) rotate(240deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    /* Login card - Glass Effect */
    .login-card {
        width: 90%;
        max-width: 400px;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.6);
        padding: 40px;
        text-align: center;
        z-index: 10;
        animation: cardEntry 0.8s cubic-bezier(0.17, 0.67, 0.83, 0.67);
    }

    @keyframes cardEntry {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .login-title {
        font-size: 26px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 35px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    /* Form styling */
    .form-group {
        text-align: left;
        margin-bottom: 22px;
    }

    .form-label {
        color: #e2e8f0;
        font-size: 12px;
        margin-left: 4px;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.8px;
    }

    /* Input Field - Fix for Black Text */
    .form-control {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important; /* माहिती पांढऱ्या रंगात दिसेल */
        border-radius: 12px;
        padding: 12px 16px;
        width: 100%;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.12) !important;
        border-color: #38bdf8 !important;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.4);
        outline: none;
        color: #ffffff !important;
    }

    /* Placeholder Color */
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4) !important;
    }

    /* Buttons */
    .btn-login {
        background: linear-gradient(135deg, #38bdf8, #0ea5e9);
        color: #0f172a;
        font-weight: 700;
        border: none;
        border-radius: 12px;
        padding: 13px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 12px;
        font-size: 16px;
        text-transform: uppercase;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(56, 189, 248, 0.5);
        filter: brightness(1.1);
    }

    .register-link {
        margin-top: 25px;
        display: block;
        color: #94a3b8;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
    }

    .register-link b {
        color: #38bdf8;
    }

    .register-link:hover {
        color: #ffffff;
    }

    .alert {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 14px;
    }
</style>

<!-- --- Background Floating Circles --- -->
<div class="circle c1"></div>
<div class="circle c2"></div>
<div class="circle c3"></div>
<div class="circle c4"></div>
<div class="circle c5"></div>

<!-- ================= LOGIN CARD ================= -->
<div class="login-card">

    <div class="login-title">Student Login</div>

    <?php if ($error): ?>
        <div class="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input class="form-control"
                   type="email"
                   name="email"
                   placeholder="Enter your email"
                   value="<?= e((string)($_POST['email'] ?? '')) ?>"
                   required>
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input class="form-control"
                   type="password"
                   name="password"
                   placeholder="Enter password"
                   required>
        </div>

        <button class="btn-login" type="submit">Log In</button>
        
        <a class="register-link" href="student_register.php">
            New here? <b>Create an Account</b>
        </a>

    </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>
