<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_admin();

$id=isset($_GET['id'])?(int)$_GET['id']:0;

if($id>0){
    try{
        $pdo=db();
        $stmt=$pdo->prepare('DELETE FROM colleges WHERE id=?');
        $stmt->execute([$id]);
    }catch(PDOException $e){}
}

redirect('admin_dashboard.php');