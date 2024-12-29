<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
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
$stmt = $conn->prepare("select at.JobID, jp.Title as Title, jp.JobType as JobType, jp.Salary as Salary,jp.Skills as Skills, jp.Description as Description, at.Status as Status from application at inner join jobpost jp on at.JobID = jp.JobID and at.JobSeekerID=:seeker_id");
$stmt->execute([
    'seeker_id' => $_SESSION['uid']
]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs Applied</title>
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
            width: 70%;
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
            gap: 50px;
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
        .btns .description1{
            background-color:rgb(199, 225, 232);
            transition: 0.3s background-color color;
        }
        .btns .description1:hover{
            background-color:rgb(183, 208, 216);

        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Overlay effect */
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 50%;
            max-width: 600px;
            text-align: center;
            position: relative;
        }

        .modal-content h2 {
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #333;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: #888;
        }

        .close-btn:hover {
            color: #333;
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
                        <p>Job Type: <?php echo $data[$i]["JobType"] ?></p>
                        <p>Skill Set: <?php echo $data[$i]["Skills"] ?></p>
                        <p>Salary: <?php echo $data[$i]["Salary"] ?></p>
                    </div>
                        <div class="btns">
                        <div class="status">
                            <?php if($data[$i]["Status"]=="Pending"){ ?>
                                <h1 style="color:blue">Status: <?php echo $data[$i]["Status"] ?></h1>
                            <?php }else if($data[$i]["Status"]=="Rejected"){ ?>
                                <h1 style="color:red">Status: <?php echo $data[$i]["Status"] ?></h1>
                            <?php }else if($data[$i]["Status"]=="Accepted"){ ?>
                                <h1 style="color:green">Status: <?php echo $data[$i]["Status"] ?></h1>
                            <?php } ?>
                        </div>
                            <a href="#" class="description">Description</a>
                        </div>
                        <div id="description-modal" class="modal">
                        <div class="modal-content">
                            <span class="close-btn" id="close-modal">&times;</span>
                            <h2><?php echo $data[$i]["Title"] ?></h2>
                            <p>Job Type: <?php echo $data[$i]["JobType"] ?></p>
                            <p>Skill Set: <?php echo $data[$i]["Skills"] ?></p>
                            <p>Salary: <?php echo $data[$i]["Salary"] ?></p>
                            <p id="job-description-text">Job Description: <?php echo $data[$i]["Description"] ?></p>
                        </div>
                        </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
        function showalert() {
            alert("You must sign in as a job seeker!")
        }
        // Modal references
        const descriptionModal = document.getElementById("description-modal");
        const closeModalBtn = document.getElementById("close-modal");
        const descriptionButtons = document.querySelectorAll(".description");
        const jobDescriptionText = document.getElementById("job-description-text");

        // Function to open the modal
        function openModal(description) {
            descriptionModal.style.display = "flex";
        }

        // Function to close the modal
        function closeModal() {
            descriptionModal.style.display = "none";
        }

        // Attach event listeners to all description buttons
        descriptionButtons.forEach((button, index) => {
            button.addEventListener("click", (event) => {
                event.preventDefault(); // Prevent default action
                const jobDescription = `This is the detailed description for job ${index + 1}.`; // Example description; replace as needed
                openModal(jobDescription);
            });
        });

        // Close modal on clicking the close button
        closeModalBtn.addEventListener("click", closeModal);

        // Close modal when clicking outside the modal content
        window.addEventListener("click", (event) => {
            if (event.target === descriptionModal) {
                closeModal();
            }
        });

    </script>
</body>
</html>
