<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['type'] != 'recruiter') {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];

$stmt = $conn->prepare("SELECT * FROM User WHERE UID = :UID");
$stmt->execute(['UID' => $uid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = 'Unknown User';
if ($user) {
    $username = $user['Username'];
} else {
    $username = 'Unknown User';
}

//fetching posts
$stmt = $conn->prepare("SELECT jp.JobID AS JobID, jp.Title as Title, jp.JobType as JobType, jp.Description as Description, jp.Skills as Skills, jp.salary AS salary, COUNT(at.JobSeekerID) AS number_of_applicants, COUNT(CASE WHEN at.Status = 'Pending' THEN 1 END) AS Pending, COUNT(CASE WHEN at.Status = 'Accepted' THEN 1 END) AS Accepted, COUNT(CASE WHEN at.Status = 'Rejected' THEN 1 END) AS Rejected FROM jobPost jp LEFT JOIN application at ON jp.JobID = at.JobID and jp.RecruiterID=:rec_uid GROUP BY jp.JobID");
$stmt->execute([
    'rec_uid' => $_SESSION['uid']
]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Jobs</title>
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
        .jobcontainer{
            display: flex;
            flex-direction: row;
            justify-content:space-between;
            padding: 15px;
            transition: 0.4s background-color;
        }
        .jobcontainer:hover{
            background-color:rgb(233, 233, 233);
            cursor: pointer;
        }
        .jobs{
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .titleandall{
            display: flex;
            gap: 10px;
            justify-content: left;
            margin-left: 10px;
        }
        .titleandall h1{
            color: #474a4a;
        }
        .titleandall p{
            color: #828787;
            margin-top: 25px;
        }
        .btns{
            margin-top: 15px;
            display: flex;
            gap: 20px;
        }
        .btns a{
            font-size: 20px;
            border: 1px solid;
            text-decoration: none;
            color: black;
            border-radius: 5px;
            padding: 10px;
            font-weight: 600;
        }
        .btns .apply{
            background-color:rgb(184, 253, 187);
            transition: 0.3s background-color color;
        }
        .btns .apply:hover{
            background-color:rgb(175, 233, 191);
            color: rgb(21, 21, 21);
        }
        .btns .description{
            background-color:rgb(199, 225, 232);
            transition: 0.3s background-color color;
        }
        .btns .description:hover{
            background-color:rgb(183, 208, 216);

        }
        .btns .delete{
            background-color:rgb(237, 172, 167);
            transition: 0.3s background-color color;
        }
        .btns .delete:hover{
            background-color:rgba(238, 130, 120, 0.97);

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
                <a href="job_posts.php">My Jobs</a>
                <a href="addPost.php">Add Posts</a>
            <?php endif; ?>
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <div class="container">
        <div class="jobs">
            <?php for ($i = 0; $i < count($data); $i++) { ?>
                <div class="jobcontainer">
                    <div class="titleandall">
                        <h1><?php echo $data[$i]["Title"] ?></h1>
                        <p style="color:blue">( Pending: <?php echo $data[$i]["Pending"] ?></p>
                        <p style="color:green">Accepted: <?php echo $data[$i]["Accepted"] ?></p>
                        <p style="color:red">Rejected: <?php echo $data[$i]["Rejected"] ?> )</p>
                    </div>
                        <div class="btns">
                            <a href="editJob.php?job_id=<?php echo $data[$i]["JobID"] ?>" class="apply" >Edit</a>
                            <a href="jobDetailed.php?job_id=<?php echo $data[$i]["JobID"] ?>" class="description" >Detailed View</a>
                            <a href="deleteJob.php?job_id=<?php echo $data[$i]["JobID"] ?>" class="delete" >Delete</a>
                        </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        function showalert() {
            alert("You must sign in as a job seeker!")
        }
    </script>
</body>
</html>
