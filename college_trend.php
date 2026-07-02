<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

require_student();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = db();

$stmt = $pdo->prepare("SELECT college_name, cut_off, cutoff_2022, cutoff_2023 FROM colleges WHERE college_id = ?");
$stmt->execute([$id]);
$college = $stmt->fetch();

if (!$college) { die("College not found."); }

require __DIR__ . '/header.php';

$c22 = (float)($college['cutoff_2022'] ?: 0);
$c23 = (float)($college['cutoff_2023'] ?: 0);
$c24 = (float)($college['cut_off'] ?: 0);
?>

<style>
    body { background: #080a12; color: #fff; font-family: 'Poppins', sans-serif; overflow-x: hidden; }
    
    .analysis-container { max-width: 900px; margin: 80px auto; padding: 0 20px; }

    .main-title { font-size: 3rem; font-weight: 900; text-align: center; margin-bottom: 10px; background: linear-gradient(to right, #00dbde, #fc00ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .sub-title { text-align: center; color: #636e72; letter-spacing: 3px; font-size: 12px; text-transform: uppercase; margin-bottom: 60px; }

    /* --- MODERN TIMELINE DESIGN --- */
    .timeline { position: relative; max-width: 700px; margin: 0 auto; }
    .timeline::after { content: ''; position: absolute; width: 4px; background: rgba(255,255,255,0.05); top: 0; bottom: 0; left: 50%; margin-left: -2px; border-radius: 10px; }

    .t-container { padding: 10px 40px; position: relative; background-color: inherit; width: 50%; box-sizing: border-box; }
    .t-container::after { content: ''; position: absolute; width: 20px; height: 20px; right: -10px; background-color: #1e272e; border: 4px solid #00dbde; top: 20px; border-radius: 50%; z-index: 1; box-shadow: 0 0 15px #00dbde; }

    .left { left: 0; }
    .right { left: 50%; }
    .right::after { left: -10px; border-color: #fc00ff; box-shadow: 0 0 15px #fc00ff; }

    .glass-card { padding: 30px; background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); position: relative; border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.1); transition: 0.4s ease; }
    .glass-card:hover { transform: scale(1.05); background: rgba(255, 255, 255, 0.07); border-color: rgba(255,255,255,0.2); }

    .year-text { font-size: 14px; font-weight: 700; color: #00dbde; margin-bottom: 10px; display: block; }
    .right .year-text { color: #fc00ff; }

    .percent-text { font-size: 3.5rem; font-weight: 900; line-height: 1; margin: 10px 0; font-family: 'Inter', sans-serif; }
    .trend-indicator { font-size: 12px; font-weight: 600; padding: 5px 15px; border-radius: 50px; background: rgba(255,255,255,0.05); color: #95afc0; }

    .current-label { position: absolute; top: -15px; right: 20px; background: #fc00ff; color: #fff; font-size: 10px; padding: 4px 12px; border-radius: 10px; font-weight: 800; box-shadow: 0 5px 15px rgba(252, 0, 255, 0.4); }

    .footer-btn { text-align: center; margin-top: 80px; }
    .btn-back { color: #fff; text-decoration: none; font-weight: 600; font-size: 14px; opacity: 0.5; transition: 0.3s; }
    .btn-back:hover { opacity: 1; letter-spacing: 1px; }

    @media screen and (max-width: 600px) {
        .timeline::after { left: 31px; }
        .t-container { width: 100%; padding-left: 70px; padding-right: 25px; }
        .t-container::after { left: 21px; }
        .right { left: 0%; }
    }
</style>

<div class="analysis-container">
    <h1 class="main-title"><?= htmlspecialchars($college['college_name']) ?></h1>
    <p class="sub-title">Visualizing Admission Journey</p>

    <div class="timeline">
        <!-- Year 2022 -->
        <div class="t-container left">
            <div class="glass-card">
                <span class="year-text">SESSION 2022</span>
                <div class="percent-text"><?= $c22 ?><span style="font-size: 20px">%</span></div>
                <div class="trend-indicator">Base Record</div>
            </div>
        </div>

        <!-- Year 2023 -->
        <div class="t-container right">
            <div class="glass-card">
                <span class="year-text">SESSION 2023</span>
                <div class="percent-text"><?= $c23 ?><span style="font-size: 20px">%</span></div>
                <div class="trend-indicator">Intermediate Growth</div>
            </div>
        </div>

        <!-- Year 2024 -->
        <div class="t-container left" style="margin-top: 30px;">
            <div class="glass-card" style="border-color: rgba(252, 0, 255, 0.3);">
                <span class="current-label">LATEST</span>
                <span class="year-text" style="color: #fc00ff;">SESSION 2024</span>
                <div class="percent-text" style="text-shadow: 0 0 20px rgba(252, 0, 255, 0.2);"><?= $c24 ?><span style="font-size: 20px">%</span></div>
                <div class="trend-indicator">Admission Threshold</div>
            </div>
        </div>
    </div>

    <div class="footer-btn">
        <a href="javascript:window.close()" class="btn-back">← CLOSE ANALYSIS</a>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
