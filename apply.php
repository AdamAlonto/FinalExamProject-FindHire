<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    header("Location: login.php");
    exit;
}

include 'sql/dbconfig.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['job_id']) && !empty($_POST['description']) && isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $job_id = $_POST['job_id']; 
        $user_id = $_SESSION['user_id'];
        $description = $_POST['description'];
        $resume = $_FILES['resume']['name'];
        $target_dir = "uploads/";

        $target_file = $target_dir . basename($resume);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($fileType != "pdf" && $fileType != "docx" && $fileType != "doc") {
            $message = "Only PDF, DOCX, and DOC files are allowed.";
        }
        elseif ($_FILES['resume']['size'] > 2000000) {
            $message = "The file is too large. Maximum size is 2MB.";
        }
        elseif ($_FILES['resume']['error'] != UPLOAD_ERR_OK) {
            $message = "Error uploading the resume.";
        }
        elseif (!is_writable($target_dir)) {
            $message = "The upload directory is not writable.";
        }

        else {
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $target_file)) {
                $sql = "INSERT INTO applications (job_id, user_id, description, resume) VALUES (:job_id, :user_id, :description, :resume)";
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':description', $description, PDO::PARAM_STR);
                $stmt->bindParam(':resume', $resume, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $message = "Application submitted successfully.";
                } else {
                    $message = "Error: " . $stmt->errorInfo()[2]; 
                }
            } else {
                $message = "There was an error moving the uploaded file.";
            }
        }
    } else {
        $message = "Please fill in all fields and upload your resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #00bfff;
        }

        .back-button {
            margin-top: 20px;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Apply for a Job</h2>
    <?php if ($message): ?>
        <p style="text-align: center;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <textarea name="description" placeholder="Why are you the best candidate?" required></textarea>
        <input type="file" name="resume" required>
        <button type="submit">Submit Application</button>
    </form>

    <div class="back-button">
        <a href="dashboard_applicant.php"><button>Back to Dashboard</button></a>
    </div>
</body>
</html>
