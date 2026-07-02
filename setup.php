<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

$dsn = sprintf('mysql:host=%s;charset=utf8mb4', $config['db_host']);
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$dbName = $config['db_name'];

$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$dbName`");

// ✅ STUDENTS TABLE
$pdo->exec("
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    marks DECIMAL(5,2) NULL,
    stream ENUM('Science','Commerce','Arts') NULL,
    category VARCHAR(50) NULL,
    city VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");

// ✅ COLLEGES TABLE
$pdo->exec("
CREATE TABLE IF NOT EXISTS colleges (
    college_id INT AUTO_INCREMENT PRIMARY KEY,
    college_name VARCHAR(150) NOT NULL,
    address TEXT NULL,
    website_url VARCHAR(255) NULL,
    stream ENUM('Science','Commerce','Arts') NOT NULL,
    cut_off DECIMAL(5,2) NOT NULL,
    city VARCHAR(100) NOT NULL,
    category VARCHAR(50) NULL,
    seats INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");

// मॅन्युअली नवीन कॉलम्स ॲड करणे
try {
    $pdo->exec("ALTER TABLE colleges ADD COLUMN cutoff_2022 DECIMAL(5,2) DEFAULT 0 AFTER cut_off");
} catch (Exception $e) { }
try {
    $pdo->exec("ALTER TABLE colleges ADD COLUMN cutoff_2023 DECIMAL(5,2) DEFAULT 0 AFTER cutoff_2022");
} catch (Exception $e) { }

// ✅ ADMINS TABLE
$pdo->exec("
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");

// ✅ NOTIFICATIONS TABLE
$pdo->exec("
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
");

// ✅ BOOKMARKS TABLE
$pdo->exec("
CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    college_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(student_id, college_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (college_id) REFERENCES colleges(college_id) ON DELETE CASCADE
) ENGINE=InnoDB
");

// ✅ DEFAULT ADMIN
$checkAdmin = $pdo->prepare("SELECT admin_id FROM admins WHERE username = ?");
$checkAdmin->execute(['admin']);
if (!$checkAdmin->fetch()) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
    $stmt->execute(['admin', $hash]);
}

// ✅ SAMPLE COLLEGES
$count = $pdo->query("SELECT COUNT(*) FROM colleges")->fetchColumn();
if ($count == 0) {
    $stmt = $pdo->prepare("
        INSERT INTO colleges 
        (college_name, address, website_url, stream, cut_off, cutoff_2022, cutoff_2023, city, category, seats)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute(['Fergusson College', 'FC Road, Pune', 'https://fergusson.edu', 'Science', 92.5, 88.5, 90.0, 'Pune', 'Open', 120]);
    $stmt->execute(['SP College', 'Tilak Road, Pune', 'https://spcollegepune.ac.in', 'Commerce', 88.0, 84.0, 86.5, 'Pune', 'Open', 100]);
}

// ✅ SAMPLE NOTIFICATIONS
$countNotif = $pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
if ($countNotif == 0) {
    $stmt = $pdo->prepare("INSERT INTO notifications (message) VALUES (?)");
    $stmt->execute(['Welcome to Edu Guide!']);
    $stmt->execute(['Admission process started.']);
}

echo "✅ Setup Completed Successfully - Data is Safe!";