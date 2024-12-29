<?php
require 'db.php';
session_start();

if (!isset($_SESSION['uid']) || !isset($_SESSION['type'])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];
$type = $_SESSION['type'];
if($type=='recruiter'){
    $stmt = $conn->prepare("SELECT * FROM recruiter WHERE UID = :UID");
    $stmt->execute(['UID' => $uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
    $stmt = $conn->prepare("SELECT * FROM jobseeker WHERE UID = :UID");
    $stmt->execute(['UID' => $uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt1 = $conn->prepare("SELECT * FROM user WHERE UID = :UID");
$stmt1->execute(['UID' => $uid]);
$userMain = $stmt1->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->beginTransaction();
    if($type=='recruiter'){
        $stmt2 = $conn->prepare(
            "UPDATE recruiter SET Name = :name, Phone = :phone, Email =:email, Organization=:organization WHERE UID=:uid"
        );
        $stmt2->execute([
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'organization' => $_POST['organization'],
            'email' => $_POST['email'],
            'uid' => $uid
        ]);
        $conn->commit();
        header("Location: dashboard.php");
        exit();
    }else{
        $stmt2 = $conn->prepare(
            "UPDATE jobseeker SET Name = :name, Phone = :phone, Email =:email, Education=:education, Skills=:skills, Experience = :experience WHERE UID=:uid"
        );
        $stmt2->execute([
            'name' => $_POST['name'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
            'education' => $_POST['education'],
            'skills' => $_POST['skills'],
            'experience' => $_POST['experience'],
            'uid' => $uid
        ]);
        $conn->commit();
        header("Location: dashboard.php");
        exit();
    }
        

}


?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
        <h1>View and Edit Profile:</h1>
    <form method="post" action="profile.php">
            <label>Username:</label>
            <input type="text" name="username" disabled required value="<?php echo $userMain['Username'] ?>">

            <label>Type:</label>
            <input type="text" disabled value="<?php echo $type?>">

            <div>
                <label>Name:</label>
                <input type="text" name="name" required value="<?php echo $user['Name'] ?>">
                <label>Phone:</label>
                <input type="text" name="phone" required value="<?php echo $user['Phone'] ?>">
                <label>Email:</label>
                <input type="email" name="email" required value="<?php echo $user['Email'] ?>">
            <?php if ($type == 'recruiter'): ?>
                <label>Organization:</label>
                <input type="text" name="organization" value="<?php echo $user['Organization'] ?>">
            <?php elseif ($type == 'job_seeker'): ?>
                <label>Education:</label>
                <textarea name="education"><?php echo $user['Education'] ?></textarea>
                <label>Skills:</label>
                <textarea name="skills"><?php echo $user['Skills'] ?></textarea>
                <label>Experience:</label>
                <textarea name="experience"><?php echo $user['Experience'] ?></textarea>
            <?php endif; ?>
            </div>
            

            <button type="submit">Save Changes</button>
        </form>
        
    </div>
    </div>
</body>
</html>
