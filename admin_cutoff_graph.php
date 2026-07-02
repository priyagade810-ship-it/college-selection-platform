<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

require_admin(); 

$title = 'College Cutoff Analysis';
$error = null;

try {
    $pdo = db();
    // Descending order – highest cutoff first
    $stmt = $pdo->query("SELECT college_name, cut_off FROM colleges ORDER BY cut_off DESC");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

require __DIR__ . '/header.php';
?>

<style>
:root {
    --chart-grid: #edf2f7;
    --axis-color: #4a5568;
    --primary-blue: #3182ce;
}

.graph-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    padding: 40px;
    margin: 40px auto;
    max-width: 900px;
    font-family: 'Inter', -apple-system, sans-serif;
}

.graph-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 40px;
    text-align: center;
}

/* Main Viewport */
.graph-viewport {
    height: 400px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    padding: 0 10px 0 60px; /* Left padding for Y-axis numbers */
    position: relative;
    border-bottom: 2px solid var(--axis-color);
    border-left: 2px solid var(--axis-color);
    /* Dynamic Grid Lines using CSS Linear Gradient */
    background-image: linear-gradient(var(--chart-grid) 1px, transparent 1px);
    background-size: 100% 40px; /* 400px height / 10 divisions = 40px each */
}

/* Y-Axis Percentage Labels */
.y-marker {
    position: absolute;
    left: -50px;
    width: 45px;
    text-align: right;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--axis-color);
}

.bar-wrapper {
    flex: 1;
    height: 100%;
    display: flex;
    align-items: flex-end;
    position: relative;
}

.graph-bar {
    width: 100%;
    min-width: 25px;
    border-radius: 4px 4px 0 0;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.graph-bar:hover {
    filter: saturate(1.5) brightness(1.1);
    transform: scaleX(1.05);
}

/* Professional Floating Tooltip */
.graph-bar .tooltip-box {
    position: absolute;
    top: -60px;
    left: 50%;
    transform: translateX(-50%);
    background: #1a202c;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: 0.2s ease;
    z-index: 100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.graph-bar .tooltip-box::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid #1a202c;
}

.graph-bar:hover .tooltip-box {
    opacity: 1;
    top: -70px;
}

/* Legend Styling */
.legend-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 50px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.85rem;
    color: #4a5568;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    flex-shrink: 0;
}
</style>

<div class="graph-card">
    <h2 class="graph-title">College Cutoff Performance (%)</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="graph-viewport">
        <!-- Y-Axis Markers (0% to 100%) -->
        <?php for($m=0; $m<=100; $m+=10): ?>
            <span class="y-marker" style="bottom: <?php echo $m; ?>%;"><?php echo $m; ?>%</span>
        <?php endfor; ?>

        <?php
        // Professional Color Palette
        $colors = ['#3182ce', '#38a169', '#d69e2e', '#e53e3e', '#805ad5', '#319795', '#dd6b20', '#718096'];
        
        $i = 0;
        foreach ($colleges as $c):
            $val = (float)$c['cut_off'];
            // Height calculation based on 100% scale
            $height = $val; 
            $color = $colors[$i % count($colors)];
        ?>
            <div class="bar-wrapper">
                <div class="graph-bar" 
                     style="height: <?php echo $height; ?>%; background-color: <?php echo $color; ?>;">
                    
                    <!-- Tooltip inside bar -->
                    <div class="tooltip-box">
                        <strong><?php echo htmlspecialchars($c['college_name']); ?></strong><br>
                        Cutoff: <?php echo $val; ?>%
                    </div>
                </div>
            </div>
        <?php $i++; endforeach; ?>
    </div>

    <!-- Legend to identify colleges by color -->
    <div class="legend-grid">
        <?php $i = 0; foreach ($colleges as $c): ?>
            <div class="legend-item">
                <div class="dot" style="background-color: <?php echo $colors[$i % count($colors)]; ?>;"></div>
                <span><?php echo htmlspecialchars($c['college_name']); ?> (<?php echo $c['cut_off']; ?>%)</span>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
