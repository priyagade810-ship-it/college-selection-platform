<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Check if user has admin privileges
require_admin();

$pdo = db();
$error = null;
$success = null;

// Get the College ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/**
 * 1. HANDLE UPDATE REQUEST
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['college_name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $stream = trim($_POST['stream'] ?? '');
    $cut_off = (float)($_POST['cut_off'] ?? 0);
    $cutoff_2022 = (float)($_POST['cutoff_2022'] ?? 0); // ✨ New Field
    $cutoff_2023 = (float)($_POST['cutoff_2023'] ?? 0); // ✨ New Field
    $address = trim($_POST['address']);
    $category = trim($_POST['category']);
    $website_url = trim($_POST['website_url'] ?? '');

    if ($name === '' || $city === '' || $stream === '') {
        $error = 'All mandatory fields are required.';
    } else {
        try {
            /** 
             * Updated query to include cutoff_2022 and cutoff_2023
             */
            $stmt = $pdo->prepare('UPDATE colleges SET college_name = ?, city = ?, stream = ?, cut_off = ?, cutoff_2022 = ?, cutoff_2023 = ?, address = ?, category = ?, website_url = ? WHERE college_id = ?');
            $stmt->execute([$name, $city, $stream, $cut_off, $cutoff_2022, $cutoff_2023, $address, $category, $website_url === '' ? null : $website_url, $id]);
            
            $success = 'College information updated successfully!';
        } catch (PDOException $e) {
            $error = 'Update failed: ' . $e->getMessage();
        }
    }
}

/**
 * 2. FETCH CURRENT COLLEGE DATA
 */
try {
    $stmt = $pdo->prepare('SELECT * FROM colleges WHERE college_id = ?');
    $stmt->execute([$id]);
    $college = $stmt->fetch();

    if (!$college) {
        die('College not found.');
    }
} catch (PDOException $e) {
    die('Database Error: ' . $e->getMessage());
}

require __DIR__ . '/header.php';
?>

<div class="container mt-5">
    <div class="card shadow border-0" style="border-radius: 15px;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
            <h4 class="mb-0">Edit College Details</h4>
            <a href="admin_dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Dashboard</a>
        </div>
        <div class="card-body p-4">
            
            <?php if ($error): ?>
                <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success shadow-sm border-0"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">College Name</label>
                    <input type="text" name="college_name" class="form-control" value="<?php echo htmlspecialchars($college['college_name']); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($college['city']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Stream</label>
                        <input type="text" name="stream" class="form-control" value="<?php echo htmlspecialchars($college['stream']); ?>" required>
                    </div>
                </div>

                <!-- 📊 Trend Analysis Fields (New) -->
                <div class="row bg-light p-3 rounded mb-3 border">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-primary">Current Cut-off (2024) %</label>
                        <input type="number" step="0.01" name="cut_off" class="form-control border-primary" value="<?php echo htmlspecialchars((string)$college['cut_off']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-muted">Cut-off 2023 %</label>
                        <input type="number" step="0.01" name="cutoff_2023" class="form-control" value="<?php echo htmlspecialchars((string)($college['cutoff_2023'] ?? 0)); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold text-muted">Cut-off 2022 %</label>
                        <input type="number" step="0.01" name="cutoff_2022" class="form-control" value="<?php echo htmlspecialchars((string)($college['cutoff_2022'] ?? 0)); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" class="form-control" required>
                            <option value="OPEN" <?php echo ($college['category'] == 'OPEN') ? 'selected' : ''; ?>>OPEN</option>
                            <option value="OBC" <?php echo ($college['category'] == 'OBC') ? 'selected' : ''; ?>>OBC</option>
                            <option value="ST" <?php echo ($college['category'] == 'ST') ? 'selected' : ''; ?>>ST</option>
                            <option value="NT" <?php echo ($college['category'] == 'NT') ? 'selected' : ''; ?>>NT</option>
                            <option value="OTHER" <?php echo ($college['category'] == 'OTHER') ? 'selected' : ''; ?>>OTHER</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-primary">Official Website Link</label>
                        <input type="url" name="website_url" class="form-control" value="<?php echo htmlspecialchars($college['website_url'] ?? ''); ?>" placeholder="https://example.edu">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Enter full college address..." required><?php echo htmlspecialchars($college['address'] ?? ''); ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        Update College Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/header.php'; ?>
