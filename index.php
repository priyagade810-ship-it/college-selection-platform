<?php
declare(strict_types=1);
$title = 'Home';
require __DIR__ . '/header.php';
?>

<div class="container-fluid p-0">
    <div class="row g-0" style="height:100vh;">

        <!-- SIDEBAR -->
        <div class="col-md-2 d-flex flex-column sidebar">

            <div class="sidebar-box active" data-target="home">
                <img src="assets/images/home.png" class="sidebar-img">
                <span>Home</span>
            </div>

            <div class="sidebar-box" data-target="student">
                <img src="assets/images/student.png" class="sidebar-img">
                <span>Student</span>
            </div>

            <div class="sidebar-box" data-target="admin">
                <img src="assets/images/admin.png" class="sidebar-img">
                <span>Admin</span>
            </div>

            <div class="sidebar-box" data-target="aboutinfo">
                <img src="assets/images/admin.png" class="sidebar-img">
                <span>About</span>
            </div>

        </div>

        <!-- CONTENT -->
        <div class="col-md-10 position-relative p-0">

            <!-- HOME -->
            <div class="section active-section" id="home"
                 style="background:url('https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">

                <div class="overlay"></div>

                <div class="content-box text-center">
                    <h1 class="welcome-effect">WELCOME</h1>
                    <h2 class="main-heading">College Selection Platform</h2>
                    <p class="tagline">
                        Smart platform to find best colleges based on marks, stream & city
                    </p>
                </div>
            </div>

            <!-- STUDENT -->
            <div class="section d-none" id="student"
                 style="background:url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">

                <div class="overlay"></div>

                <div class="content-box text-center">
                    <h2 class="section-heading text-success">Student Section</h2>
                    <p>Register, Login & View Eligible Colleges</p>

                    <div class="mt-3">
                        <a href="student_register.php" class="btn btn-primary btn-lg custom-btn">Register</a>
                        <a href="student_login.php" class="btn btn-success btn-lg custom-btn">Login</a>
                    </div>
                </div>
            </div>

            <!-- ADMIN -->
            <div class="section d-none" id="admin"
                 style="background:url('https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">

                <div class="overlay"></div>

                <div class="content-box text-center">
                    <h2 class="section-heading text-warning">Admin Section</h2>
                    <p>Manage Colleges, Cut-offs & Seats</p>

                    <a href="admin_login.php" class="btn btn-warning btn-lg custom-btn mt-3">
                        Admin Login
                    </a>
                </div>
            </div>

            <!-- ABOUT -->
            <div class="section d-none" id="aboutinfo"
                 style="background:url('https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;">

                <div class="overlay"></div>

                <div class="content-box text-center container text-white">

                    <h2 class="about-heading mb-4">
                        About Platform
                    </h2>

                    <div class="about-box">
                        <p>
                            College Selection Platform helps students find best colleges based on marks and preferences.
                        </p>
                        <p>
                            It provides transparent and easy college selection system using filters like stream, city & category.
                        </p>
                        <p>
                            Our goal is to simplify admission process and help students make better decisions.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>

/* SIDEBAR */
.sidebar{
    background: linear-gradient(180deg,#0f172a,#1e293b);
}
.sidebar-box{
    flex:1;
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    cursor:pointer;
    transition:0.3s;
}
.sidebar-box:hover{
    background:#4facfe;
    transform:scale(1.05);
}
.sidebar-box.active{
    background:#2563eb;
}
.sidebar-img{
    width:50px;
    margin-bottom:8px;
}

/* SECTION */
.section{
    position:absolute;
    width:100%;
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
}
.overlay{
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
}
.content-box{
    position:relative;
    color:#fff;
    z-index:10;
}

/* WELCOME EFFECT */
.welcome-effect{
    font-size:80px;
    font-weight:900;
    background:linear-gradient(90deg,#00c6ff,#0072ff,#00ff88);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    animation: fadeIn 1.5s ease;
}

.main-heading{
    font-size:45px;
    font-weight:800;
}
.tagline{
    font-size:20px;
    color:#ddd;
}

/* HEADINGS */
.section-heading{
    font-size:40px;
    font-weight:700;
}

/* ABOUT */
.about-box{
    background:rgba(255,255,255,0.15);
    padding:30px;
    border-radius:15px;
    backdrop-filter:blur(10px);
    transition:0.3s;
}
.about-box:hover{
    transform:scale(1.05);
}

/* BUTTONS */
.custom-btn{
    border-radius:30px;
    padding:10px 25px;
    transition:0.3s;
}
.custom-btn:hover{
    transform:scale(1.1);
}

/* TOP BUTTONS */
.top-buttons{
    position:absolute;
    top:15px;
    right:20px;
    z-index:20;
}

/* ANIMATION */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}

.d-none{ display:none; }
.active-section{ display:flex; }

</style>

<script>
const boxes=document.querySelectorAll('.sidebar-box');
const sections=document.querySelectorAll('.section');

boxes.forEach(box=>{
    box.onclick=()=>{
        boxes.forEach(b=>b.classList.remove('active'));
        box.classList.add('active');

        sections.forEach(s=>{
            s.classList.add('d-none');
            s.classList.remove('active-section');
        });

        const target=document.getElementById(box.dataset.target);
        target.classList.remove('d-none');
        target.classList.add('active-section');
    }
});
</script>

<?php require __DIR__ . '/footer.php'; ?>