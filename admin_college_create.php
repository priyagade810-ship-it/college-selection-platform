<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_admin();

$title = 'Add College';
$error = null;

// 'website_url' नवीन फील्ड ॲरेमध्ये जोडले आहे
$college = [
    'college_name' => '',
    'stream' => '',
    'cut_off' => '',
    'city' => '',
    'address' => '',
    'website_url' => '', // ✨ New field
    'category' => '',
    'seats' => '0'
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $college['college_name'] = trim((string)($_POST['college_name'] ?? ''));
    $college['stream'] = (string)($_POST['stream'] ?? '');
    $college['cut_off'] = trim((string)($_POST['cut_off'] ?? ''));
    $college['city'] = trim((string)($_POST['city'] ?? ''));
    $college['address'] = trim((string)($_POST['address'] ?? ''));
    $college['website_url'] = trim((string)($_POST['website_url'] ?? '')); // ✨ New data fetch
    $college['category'] = trim((string)($_POST['category'] ?? ''));
    $college['seats'] = trim((string)($_POST['seats'] ?? '0'));

    $allowedStreams = ['Science', 'Commerce', 'Arts'];

    // Validation Logic
    if($college['college_name'] === '' || $college['city'] === '' || $college['cut_off'] === ''){
        $error = 'College name, cut-off, and city are required.';
    } elseif(!in_array($college['stream'], $allowedStreams, true)){
        $error = 'Invalid stream.';
    } elseif(!is_numeric($college['cut_off']) || (float)$college['cut_off'] < 0 || (float)$college['cut_off'] > 100){
        $error = 'Cut-off must be between 0 and 100.';
    } elseif(!ctype_digit($college['seats']) || (int)$college['seats'] < 0){
        $error = 'Seats must be a non-negative integer.';
    } else {
        try {
            $pdo = db();
            // ✨ SQL Query अपडेट केली आहे (website_url कॉलमसह)
            $stmt = $pdo->prepare('INSERT INTO colleges (college_name, stream, cut_off, city, address, website_url, category, seats) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $college['college_name'],
                $college['stream'],
                (float)$college['cut_off'],
                $college['city'],
                $college['address'] === '' ? null : $college['address'],
                $college['website_url'] === '' ? null : $college['website_url'], // ✨ Save URL
                $college['category'] === '' ? null : $college['category'],
                (int)$college['seats'],
            ]);
            
            flash_set('success', 'College added successfully!');
            redirect('admin_dashboard.php');
        } catch(PDOException $e) {
            $error = 'Database error. Make sure "website_url" column exists in your table.';
        }
    }
}

require __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">🚀 Add New College</h1>
            <a class="btn btn-outline-secondary btn-sm" href="admin_dashboard.php">Back to Dashboard</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger shadow-sm border-0"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="post">
                    <!-- College Name -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">College Name</label>
                        <input class="form-control" name="college_name" value="<?= e($college['college_name']) ?>" placeholder="e.g. Fergusson College" required>
                    </div>

                    <div class="row g-3">
                        <!-- Stream -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Stream</label>
                            <select class="form-select" name="stream" required>
                                <?php foreach(['Science','Commerce','Arts'] as $s): ?>
                                    <option value="<?= e($s) ?>" <?= $college['stream'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Cut-off -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cut-off (%)</label>
                            <input class="form-control" name="cut_off" value="<?= e($college['cut_off']) ?>" placeholder="e.g. 85.50" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <!-- City -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">City</label>
                            <input class="form-control" name="city" value="<?= e($college['city']) ?>" placeholder="e.g. Pune" required>
                        </div>
                        <!-- Seats -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Available Seats</label>
                            <input class="form-control" name="seats" value="<?= e($college['seats']) ?>" required>
                        </div>
                    </div>

                    <!-- 🌐 ✨ Website URL Field (New Addition) -->
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold text-primary">Official Website Link (Optional)</label>
                        <input type="url" class="form-control" name="website_url" value="<?= e($college['website_url']) ?>" placeholder="https://example.edu">
                        <small class="text-muted">Example: https://fergusson.edu</small>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Full college location details"><?= e($college['address']) ?></textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Category (optional)</label>
                        <input class="form-control" name="category" value="<?= e($college['category']) ?>" placeholder="e.g. Open / OBC / SC">
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary py-2 fw-bold" type="submit">
                            Save College Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
