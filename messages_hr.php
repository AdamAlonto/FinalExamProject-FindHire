<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: login.php"); 
    exit;
}

include 'sql/dbconfig.php';

$messages = [];
try {
    $sql = "SELECT messages.*, users.name AS sender_name
            FROM messages
            INNER JOIN users ON messages.sender_id = users.id
            WHERE messages.receiver_id = :hr_id
            ORDER BY messages.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':hr_id', $_SESSION['user_id'], PDO::PARAM_INT); 
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (isset($_POST['reply_message'], $_POST['applicant_id'], $_POST['message_id'])) {
    $reply_message = $_POST['reply_message'];
    $applicant_id = $_POST['applicant_id'];
    $message_id = $_POST['message_id'];

    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, reply_to)
                VALUES (:sender_id, :receiver_id, :message, :reply_to)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':sender_id', $_SESSION['user_id'], PDO::PARAM_INT); 
        $stmt->bindParam(':receiver_id', $applicant_id, PDO::PARAM_INT); 
        $stmt->bindParam(':message', $reply_message, PDO::PARAM_STR);
        $stmt->bindParam(':reply_to', $message_id, PDO::PARAM_INT); 
        $stmt->execute();

        $message = "Reply sent successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>HR Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f2f5;
            color: #333;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-size: 1.2em;
            margin-bottom: 20px;
            display: inline-block;
            text-align: center;
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        li strong {
            color: #007bff;
        }

        form {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }

        form textarea {
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
            min-height: 100px;
        }

        form input[type="hidden"] {
            display: none;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 15px;
            font-size: 1.2em;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #00bfff;
        }

        p {
            text-align: center;
            color: green;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Messages from Applicants</h1>
    <a href="dashboard_hr.php">Back to Dashboard</a>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <ul>
        <?php foreach ($messages as $message): ?>
            <li>
                <strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong>
                <?php echo htmlspecialchars($message['message']); ?>
                <br><small>Received on: <?php echo $message['created_at']; ?></small>
                <br>
                <form method="POST" action="">
                    <textarea name="reply_message" required placeholder="Write your reply here..."></textarea>
                    <input type="hidden" name="applicant_id" value="<?php echo $message['sender_id']; ?>">
                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                    <button type="submit">Send Reply</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
