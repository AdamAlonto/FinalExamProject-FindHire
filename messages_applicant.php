<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'applicant') {
    header("Location: login.php"); 
    exit;
}

include 'sql/dbconfig.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['message'];

    try {
        $hrQuery = "SELECT id FROM users WHERE role = 'hr' LIMIT 1";
        $hrStmt = $conn->prepare($hrQuery);
        $hrStmt->execute();
        $hrUser = $hrStmt->fetch(PDO::FETCH_ASSOC);

        if ($hrUser) {
            $receiver_id = $hrUser['id'];

            $sql = "INSERT INTO messages (sender_id, receiver_id, message) 
                    VALUES (:sender_id, :receiver_id, :message)";
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(':sender_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
            $stmt->bindParam(':message', $content, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $message = "Message sent successfully!";
            } else {
                $message = "Error: " . $stmt->errorInfo()[2];
            }
        } else {
            $message = "Error: No HR user found in the system.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message to HR</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        textarea {
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            min-height: 150px;
            resize: vertical;
        }

        button {
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            color: green;
            text-align: center;
            margin-top: 20px;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .links {
            text-align: center;
            margin-top: 15px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Send a Message to HR</h1>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <form method="post">
            <textarea name="message" required placeholder="Type your message here"></textarea>
            <button type="submit">Send Message</button>
        </form>

        <div class="links">
            <form action="dashboard_applicant.php" method="GET">
                <button type="submit">Back to Dashboard</button>
            </form>
        </div>
    </div>
</body>
</html>
