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
$job_id = $_GET['job_id'];

$stmt = $conn->prepare("SELECT jp.JobID AS JobID, jp.Title as Title, jp.JobType as JobType, jp.Description as Description, jp.Skills as Skills, jp.salary AS salary, COUNT(at.JobSeekerID) AS number_of_applicants, COUNT(CASE WHEN at.Status = 'Pending' THEN 1 END) AS Pending, COUNT(CASE WHEN at.Status = 'Accepted' THEN 1 END) AS Accepted, COUNT(CASE WHEN at.Status = 'Rejected' THEN 1 END) AS Rejected FROM jobPost jp LEFT JOIN application at ON jp.JobID = at.JobID and jp.RecruiterID=:rec_uid and jp.JobID=:job_id GROUP BY jp.JobID");
$stmt->execute([
    'rec_uid' => $_SESSION['uid'],
    'job_id'=> $job_id
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt1 = $conn->prepare("select at.ApplyID as ApplyID, at.Status as Status, at.CoverLetter as CoverLetter, js.name as name, at.Status as Status, js.email as email, js.Phone as Phone, js.Education as Education, js.Skills as Skills, js.Experience as Experience from application at inner join JobSeeker js on js.UID=at.JobSeekerID and at.JobID=:job_id");
$stmt1->execute([
    'job_id'=> $job_id
]);
$data1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);



?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data["Title"] ?></title>
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
            font-size: 20px;
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
            display: block;
            flex-direction: column;
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
            display: flex;
            justify-content: center;
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
        table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px auto;
        background: linear-gradient(to bottom, #abcadea5, #ddf5f9);
      }
      th,
      td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
      }
      th {
        background-color: #f4f4f4;
        font-weight: bold;
      }
      select {
        font-size: 17px;
        width: 80%;
        text-align: center;
      }
      .newbtns {
        display: flex;
        flex-direction: row;
        margin-left: 30%;
      }
      .scrollable-section {
    max-height: 200px; /* Set the desired height for the scrollable area */
    overflow-y: auto; /* Enable vertical scrolling */
    padding: 10px;
    border: 1px solid #ddd; /* Optional: Add a border for a better visual distinction */
    border-radius: 5px; /* Optional: Rounded corners */
    background-color: #f9f9f9; /* Optional: Light background color */
    text-align: left; /* Align text to the left for readability */
}
    .section{
        display: block;
        justify-content: left;
    }
     </style>
</head>
<body>
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
            <h1><?php echo $data["Title"] ?></h1>
            <p>Job Type: <?php echo $data["JobType"] ?></p>
            <p>Skill Set: <?php echo $data["Skills"] ?></p>
            <p>Salary: <?php echo $data["salary"] ?></p>
            <p style="color:blue">Pending: <?php echo $data["Pending"] ?></p>
            <p style="color:green">Accepted: <?php echo $data["Accepted"] ?></p>
            <p style="color:red">Rejected: <?php echo $data["Rejected"] ?></p>
        </div>
        <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Email</th>
                <th>Details & Cover Letter</th>
              </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($data1); $i++) { ?>
                    <tr>
                        <td><?php echo $data1[$i]["name"] ?></td>
                        <td>
                            <select class="interviewdetails" name="status" id="status">
                                <option value="<?php echo $data1[$i]["Status"] ?>_<?php echo $data1[$i]["ApplyID"] ?>"><?php echo $data1[$i]["Status"] ?></option>
                                <option value="Pending_<?php echo $data1[$i]["ApplyID"] ?>">Pending</option>
                                <option value="Accepted_<?php echo $data1[$i]["ApplyID"] ?>">Accepted</option>
                                <option value="Rejected_<?php echo $data1[$i]["ApplyID"] ?>">Rejected</option>
                            </select>
                        </td>
                        <td><?php echo $data1[$i]["email"] ?></td>
                        <td>
                            <div class="btns">
                            <a href="#" class="description">View</a>
                            </div>
                            
                            <div id="description-modal" class="modal">
                                <div class="modal-content">
                                    <span class="close-btn" id="close-modal">&times;</span>
                                    <div class="section">
                                        <p style="color:#2a485c; font-size:35px"><b>Name: </b><?php echo $data1[$i]["name"] ?></p>
                                        <p><b>Phone: </b><?php echo $data1[$i]["Phone"] ?></p>
                                        <p><b>Email: </b><?php echo $data1[$i]["email"] ?></p>
                                        <p><b>Education: </b><?php echo $data1[$i]["Education"] ?></p>
                                        <p id="job-description-text"><b>Skill Set: </b> <?php echo $data1[$i]["Skills"] ?></p>
                                        <p id="job-description-text"><b>Experience: </b><?php echo $data1[$i]["Experience"] ?></p>
                                    </div>
                                    <h1>Cover Letter</h1>
                                    <hr>
                                    <div class="scrollable-section">
                                        <p><?php echo $data1[$i]["CoverLetter"] ?></p>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
      const interviewselect = document.querySelectorAll(".interviewdetails");
      interviewselect.forEach((btn) => {
        btn.addEventListener("change", () => {
          const lis = btn.value.split("_");
          const status = lis[0];
          const app_id = lis[1];
          console.log(lis);
          fetch(
            `http://localhost/ajob/updateStatus.php?app_id=${app_id}&status=${status}`
          )
            .then((res) => console.log(res))
            .catch((err) => console.log(err));
        });
      });
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
