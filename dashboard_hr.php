<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f2f5;
            color: #333;
        }

        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 40px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .button-container button {
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #00bfff; /* Neon blue on hover */
        }
    </style>
</head>
<body>
    <h2>Welcome to the HR Dashboard</h2>
    <p>Manage job posts, view applications, and communicate with applicants.</p>
    <div class="button-container">
        <a href="manage_jobs.php"><button>Manage Jobs</button></a>
        <a href="view_applications.php"><button>View Applications</button></a>
        <a href="messages_hr.php"><button>Messages</button></a>
        <a href="logout.php"><button>Logout</button></a>
    </div>
</body>
</html>
