<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: login.php"); 
    exit;
}

include 'sql/dbconfig.php';

$message = ''; 
$action = isset($_GET['action']) ? $_GET['action'] : '';
$application_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 

if ($action && $application_id) {
    try {
        $new_status = ($action === 'accept') ? 'Accepted' : 'Rejected';
        $sql = "UPDATE applications SET status = :status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $application_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $message = "Application {$new_status} successfully.";
        } else {
            $message = "Error updating application status.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

$applications = [];
try {
    $sql = "SELECT applications.id AS application_id, users.name, jobs.title, applications.status
            FROM applications
            INNER JOIN users ON applications.applicant_id = users.id
            INNER JOIN jobs ON applications.job_id = jobs.id
            ORDER BY applications.application_date DESC";
    $stmt = $conn->query($sql);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Applications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            color: #333;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #0066cc;
            font-weight: bold;
        }

        a:hover {
            color: #005bb5;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        p {
            text-align: center;
            color: green;
            font-size: 16px;
        }

        .back-btn {
            display: block;
            width: 180px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <h1>Applications for Job Posts</h1>
    <a href="dashboard_hr.php" class="back-btn">Back to Dashboard</a>
    <h2>Applicants</h2>
    
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Applicant Name</th>
            <th>Job Title</th>
            <th>Application Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($applications as $application): ?>
            <tr>
                <td><?php echo htmlspecialchars($application['name']); ?></td>
                <td><?php echo htmlspecialchars($application['title']); ?></td>
                <td><?php echo htmlspecialchars($application['status']); ?></td>
                <td>
                    <a href="?action=accept&id=<?php echo $application['application_id']; ?>">Accept</a> | 
                    <a href="?action=reject&id=<?php echo $application['application_id']; ?>">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
