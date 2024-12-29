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

$job_id = $_GET['job_id'];


$stmt = $conn->prepare("delete from jobpost where JobID=:job_id");
$stmt->execute(['job_id' => $job_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
header("Location: job_posts.php");
exit();
?>