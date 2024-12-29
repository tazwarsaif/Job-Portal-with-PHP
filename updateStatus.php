<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];
$stmt = $conn->prepare("SELECT * FROM recruiter WHERE UID = :UID");
$stmt->execute(['UID' => $uid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);



$stmt1 = $conn->prepare("SELECT * FROM user WHERE UID = :UID");
$stmt1->execute(['UID' => $uid]);
$userMain = $stmt1->fetch(PDO::FETCH_ASSOC);

$app_id = $_GET['app_id'];
$status = $_GET['status'];

echo $status;
$conn->beginTransaction();
$stmt = $conn->prepare("UPDATE application SET Status=:status where ApplyID=:apply_id");
$stmt->execute(['status'=>$status,'apply_id' => $app_id]);
$conn->commit();
exit();
?>