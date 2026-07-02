<?php
declare(strict_types=1); // ✅ FIRST LINE (IMPORTANT)

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

require_admin();

$pdo = db();
$error = null;

// GET ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    redirect('admin_dashboard.php');
}

// FETCH STUDENT
$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    redirect('admin_dashboard.php');
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = $_POST['name'] ?? '';
    $email  = $_POST['email'] ?? '';
    $marks  = $_POST['marks'] ?? null;
    $stream = $_POST['stream'] ?? null;
    $city   = $_POST['city'] ?? '';

    try {
        $stmt = $pdo->prepare("
            UPDATE students 
            SET name=?, email=?, marks=?, stream=?, city=? 
            WHERE student_id=?
        ");

        $stmt->execute([
            $name,
            $email,
            $marks !== '' ? $marks : null,
            $stream !== '' ? $stream : null,
            $city,
            $id
        ]);

        // 🔥 VALIDATION
        $isValid = true;
        $message = "";

        if (empty($name) || empty($email)) {
            $isValid = false;
            $message = "Your profile is incomplete.";
        } elseif ($marks !== null && $marks < 35) {
            $isValid = false;
            $message = "Marks below eligibility.";
        } elseif (empty($stream)) {
            $isValid = false;
            $message = "Stream missing.";
        } else {
            $message = "Profile updated successfully.";
        }


        redirect('admin_dashboard.php');

    } catch (Exception $e) {
        $error = "Update failed!";
    }
}
?>

<!doctype html>
<html>
<head>
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Student</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">

        <input type="text" name="name" class="form-control mb-2"
            value="<?= e($student['name']) ?>" required>

        <input type="email" name="email" class="form-control mb-2"
            value="<?= e($student['email']) ?>" required>

        <input type="number" step="0.01" name="marks" class="form-control mb-2"
            value="<?= e((string)($student['marks'] ?? '')) ?>">

        <select name="stream" class="form-control mb-2">
            <option value="">Select Stream</option>
            <option value="Science" <?= ($student['stream']=='Science')?'selected':'' ?>>Science</option>
            <option value="Commerce" <?= ($student['stream']=='Commerce')?'selected':'' ?>>Commerce</option>
            <option value="Arts" <?= ($student['stream']=='Arts')?'selected':'' ?>>Arts</option>
        </select>

        <input type="text" name="city" class="form-control mb-3"
            value="<?= e($student['city']) ?>">

        <button class="btn btn-primary">Update</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>

    </form>
</div>

</body>
</html>