<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

require_admin();

$title = 'Admin Dashboard';
$error = null;
$success = null;

$colleges = [];
$students = [];

try {
    $pdo = db();

    // --- 1. Quick Cut-off Update & Notification Logic ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cutoff'])) {
        $cid = (int)$_POST['college_id'];
        $new_cutoff = (float)$_POST['new_cutoff'];
        $c_name = $_POST['college_name'];

        $stmt = $pdo->prepare("UPDATE colleges SET cut_off = ? WHERE college_id = ?");
        $stmt->execute([$new_cutoff, $cid]);

        // Notification message
        $notif_msg = "Important Update: $c_name cutoff changed to $new_cutoff%.";
        $notif_stmt = $pdo->prepare("INSERT INTO notifications (message, created_at) VALUES (?, NOW())");
        $notif_stmt->execute([$notif_msg]);

        $success = "$c_name cutoff updated successfully!";
    }

    // --- 2. Get search & filter values ---
    $search = trim($_GET['search'] ?? '');
    $f_city = trim($_GET['f_city'] ?? '');
    $f_stream = trim($_GET['f_stream'] ?? '');

    $sql = "SELECT * FROM colleges WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND college_name LIKE ?";
        $params[] = "%$search%";
    }
    if ($f_city !== '') {
        $sql .= " AND city = ?";
        $params[] = $f_city;
    }
    if ($f_stream !== '') {
        $sql .= " AND stream = ?";
        $params[] = $f_stream;
    }

    $sql .= " ORDER BY city ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get students
    $students = $pdo->query('SELECT * FROM students ORDER BY student_id DESC')->fetchAll(PDO::FETCH_ASSOC);
// Get Pending Reviews count
$pending_reviews_stmt = $pdo->query("SELECT COUNT(*) FROM college_reviews WHERE status = 'pending'");
$pending_count = $pending_reviews_stmt->fetchColumn();

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

require __DIR__ . '/header.php';
?>

<style>
body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
.container { margin-top: 30px; }
.dashboard-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
.btn-box { margin: 10px; padding: 15px 30px; border-radius: 8px; font-weight: bold; text-decoration: none; display: inline-block; color: white; transition: 0.3s; border: none; cursor: pointer; }
.btn-orange { background: #ff7b54; }
.btn-blue { background: #4facfe; }
.btn-green { background: #43e97b; }
.view-div { display: none; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.table th, .table td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
.table th { background: #343a40; color: white; white-space: nowrap; }
.btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 4px; text-decoration: none; color: white; margin-right: 5px; display: inline-block; }
.btn-edit { background: #007bff; }
.btn-delete { background: #dc3545; }

/* Fixed width for Action column to keep buttons side-by-side */
.col-actions { width: 130px; text-align: center; }
.col-update { width: 160px; }

.stat-card { border-radius: 10px; padding: 20px; color: white; margin-bottom: 20px; text-align: center; }
.bg-info-custom { background: #17a2b8; }
.bg-success-custom { background: #28a745; }
.filter-section { background: #f1f3f5; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
.quick-update-input { width: 70px !important; }
</style>

<div class="container">

<?php if ($success): ?> 
    <div class="alert alert-success"><?php echo $success; ?></div> 
<?php endif; ?>

<?php if ($error): ?> 
    <div class="alert alert-danger"><?php echo $error; ?></div> 
<?php endif; ?>

<!-- Stats -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="stat-card bg-info-custom">
            <h4>Total Colleges: <?php echo count($colleges); ?></h4>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card bg-success-custom">
            <h4>Registered Students: <?php echo count($students); ?></h4>
        </div>
    </div>
    <div class="col-md-6"> <!-- आधीचे col-md-6 बदलून col-md-4 करा म्हणजे ३ कार्ड्स एका लाईनमध्ये बसतील -->
    <div class="stat-card" style="background: #e74c3c;">
        <h4>Pending Reviews: <?php echo $pending_count; ?></h4>
    </div>
</div>

</div>

<div id="mainMenu" class="dashboard-card">
    <h2>Admin Dashboard</h2>
    <a href="admin_college_create.php" class="btn-box btn-orange">Add College</a>
    <button class="btn-box btn-blue" onclick="showSection('collegesSection')">View Colleges</button>
    <button class="btn-box btn-green" onclick="showSection('studentsSection')">View Students</button>
    <a href="admin_cutoff_graph.php" class="btn-box btn-green">View Cutoff Graph</a>
    <a href="admin_manage_reviews.php" class="btn-box" style="background: #6c5ce7;">Manage Reviews</a>
</div>

<!-- COLLEGES -->
<div id="collegesSection" class="view-div">
    <button class="btn btn-dark btn-sm mb-3" onclick="showSection('mainMenu')">Back</button>
    <h3>Colleges & Cut-off Management</h3>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>City</th>
                <th>Address</th>
                <th>Stream</th>
                <th>Cut-off</th>
                <th class="col-update">Cut-off Update</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($colleges as $c): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['college_name']); ?></td>
                <td><?php echo htmlspecialchars($c['category'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($c['city']); ?></td>
                <td><?php echo htmlspecialchars($c['address'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($c['stream']); ?></td>
                <td><?php echo $c['cut_off']; ?>%</td>
                <td class="col-update">
                    <form method="POST" class="d-flex gap-1">
                        <input type="hidden" name="college_id" value="<?php echo $c['college_id']; ?>">
                        <input type="hidden" name="college_name" value="<?php echo htmlspecialchars($c['college_name']); ?>">
                        <input type="number" step="0.01" name="new_cutoff" class="form-control form-control-sm quick-update-input" placeholder="%" required>
                        <button type="submit" name="update_cutoff" class="btn btn-success btn-sm">Save</button>
                    </form>
                </td>
                <td class="col-actions">
                    <div style="display: flex; gap: 5px;">
                        <a href="admin_college_edit.php?id=<?php echo $c['college_id']; ?>" class="btn-sm btn-edit">Edit</a>
                        <a href="admin_college_delete.php?id=<?php echo $c['college_id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- STUDENTS -->
<div id="studentsSection" class="view-div">
    <button class="btn btn-dark btn-sm mb-3" onclick="showSection('mainMenu')">Back</button>
    <h3>Registered Students</h3>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Marks</th>
                <th>Stream</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?php echo $s['student_id']; ?></td>
                <td><?php echo htmlspecialchars($s['name']); ?></td>
                <td><?php echo htmlspecialchars($s['email']); ?></td>
                <td><?php echo $s['marks'] ?? 0; ?>%</td>
                <td><?php echo htmlspecialchars($s['stream'] ?? '-'); ?></td>
                <td>
                    <a href="admin_student_edit.php?id=<?php echo $s['student_id']; ?>" class="btn-sm btn-edit">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<script>
function showSection(id) {
    document.querySelectorAll('.view-div').forEach(div => div.style.display = 'none');
    document.getElementById('mainMenu').style.display = 'none';

    if (id === 'mainMenu') {
        document.getElementById('mainMenu').style.display = 'block';
    } else {
        document.getElementById(id).style.display = 'block';
    }
    }
</script>

<?php require __DIR__ . '/footer.php'; ?>
