<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['type'] != 'job_seeker') {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];
$stmt = $conn->prepare("SELECT * FROM jobseeker WHERE UID = :UID");
$stmt->execute(['UID' => $uid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);



$stmt1 = $conn->prepare("SELECT * FROM user WHERE UID = :UID");
$stmt1->execute(['UID' => $uid]);
$userMain = $stmt1->fetch(PDO::FETCH_ASSOC);

$job_id = $_GET['job_id'];


$stmt = $conn->prepare("SELECT * FROM jobpost WHERE JobID=:job_id");
$stmt->execute(['job_id' => $job_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$status = "Pending";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->beginTransaction();
    $stmt2 = $conn->prepare(
    "INSERT INTO application (JobID, JobSeekerID, CoverLetter, Status) 
                    VALUES (:job_id, :seeker_id, :coverletter, :status)");
    $stmt2->execute([
            'job_id' => $job_id,
            'seeker_id' => $_SESSION['uid'],
            'coverletter' => $_POST['coverletter'],
            'status' => $status
    ]);
    $conn->commit();
    header("Location: dashboard.php");
    exit();

}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Page</title>
    <link rel="stylesheet" href="./css/index.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            gap: 15px;
        }

        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar .nav-links a:hover {
            background-color: #2980b9;
        }

        .container {
            text-align: center;
            padding: 20px;
            margin: 50px auto;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 50%;
        }

        .container h1 {
            font-size: 24px;
            color: #333;
        }
        .contaner a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="logo">AJOB</div>
        <div class="nav-links">
            <a href="dashboard.php">Home</a>    
            <a href="profile.php">View profile</a>    
            <?php if ($type == 'job_seeker'): ?>
                <a href="jobs_applied.php">Jobs Applied</a>
            <?php elseif ($type == 'recruiter'): ?>
                <a href="#">Job Posts</a>
                <a href="addPost.php">Add Posts</a>
            <?php endif; ?>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="container">
        <h1>Application Form:</h1>

    <form method="post" action="apply.php?job_id=<?php echo $job_id ?>">
            <div>
                <label>Title:</label>
                <input type="text" name="title" disabled value="<?php echo $data['Title'] ?>" required>
                <label>Job Type:</label>
                <input type="text" name="jobtype" disabled value="<?php echo $data['JobType'] ?>" required>
                <label for="salary">Salary:</label>
                <input type="number" id="salary" name="salary" disabled value="<?php echo $data['salary'] ?>" step="0.01" required>
                <label>Cover Letter:</label>
                <textarea name="coverletter"></textarea>
            </div>
            <button type="submit">Apply</button>
        </form>
        
    </div>
    </div>
</body>
</html>
