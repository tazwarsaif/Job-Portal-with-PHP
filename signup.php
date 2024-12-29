<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $type = $_POST['type']; 

    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM User WHERE Username = :username");
        $stmt->execute(['username' => $username]);
        $userExists = $stmt->fetchColumn();

        if ($userExists > 0) {
            echo "<script>alert('Username already exists. Please choose another one.');</script>";
            $conn->rollBack();
        } else {
            $stmt = $conn->prepare("INSERT INTO User (Username, Password, Type) VALUES (:username, :password, :type)");
            $stmt->execute(['username' => $username, 'password' => $password, 'type' => $type]);
            $uid = $conn->lastInsertId();

            if ($type == 'job_seeker') {
                $stmt = $conn->prepare(
                    "INSERT INTO JobSeeker (UID, Name, Phone, Email, Education, Skills, Experience) 
                    VALUES (:uid, :name, :phone, :email, :education, :skills, :experience)"
                );
                $stmt->execute([
                    'uid' => $uid,
                    'name' => $_POST['name'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'education' => $_POST['education'],
                    'skills' => $_POST['skills'],
                    'experience' => $_POST['experience']
                ]);
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO Recruiter (UID, Name, Phone, Organization, Email) 
                    VALUES (:uid, :name, :phone, :organization, :email)"
                );
                $stmt->execute([
                    'uid' => $uid,
                    'name' => $_POST['name'],
                    'phone' => $_POST['phone'],
                    'organization' => $_POST['organization'],
                    'email' => $_POST['email']
                ]);
            }

            $conn->commit();
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        die("Error during signup: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="logo">Sign Up</div>
        <div class="nav-links">
            <a href="jobs_applied.php">Jobs Applied</a>
            <a href="login.php">Log In</a>
        </div>
    </div>

    <!-- Signup Form -->
    <div class="container">
        <h1>Create an Account</h1>
        <form method="post" action="signup.php">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Type:</label>
            <select id="userType" name="type" required onchange="toggleFields()">
                <option value="type">Select type</option>
                <option value="job_seeker">Job Seeker</option>
                <option value="recruiter">Recruiter</option>
            </select>

            <!-- Common Fields -->
            <div>
                <label>Name:</label>
                <input type="text" name="name" required>
                <label>Phone:</label>
                <input type="text" name="phone" required>
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <!-- Job Seeker-Specific Fields -->
            <div id="jobSeekerFields">
                <label>Education:</label>
                <textarea name="education"></textarea>
                <label>Skills:</label>
                <textarea name="skills"></textarea>
                <label>Experience:</label>
                <textarea name="experience"></textarea>
            </div>

            <!-- Recruiter-Specific Fields -->
            <div id="recruiterFields">
                <label>Organization:</label>
                <input type="text" name="organization">
            </div>

            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Log In</a></p>
        
    </div>

    <script>
    function toggleFields() {
        const userType = document.getElementById('userType').value;
        const jobSeekerFields = document.getElementById('jobSeekerFields');
        const recruiterFields = document.getElementById('recruiterFields');

        if (userType === 'job_seeker') {
            jobSeekerFields.style.display = 'block';
            recruiterFields.style.display = 'none';
        } else if(userType === 'recruiter') {
            jobSeekerFields.style.display = 'none';
            recruiterFields.style.display = 'block';
        } else {
            jobSeekerFields.style.display = 'none';
            recruiterFields.style.display = 'none';
        }
    }
    </script>
</body>
</html>
