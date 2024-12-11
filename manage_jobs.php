<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hr') {
    header("Location: login.php"); 
    exit;
}

include 'sql/dbconfig.php';

if (isset($_POST['post_job'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    try {
        $sql = "INSERT INTO jobs (title, description, created_by) VALUES (:title, :description, :created_by)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':title' => $title, ':description' => $description, ':created_by' => $_SESSION['user_id']]);
        header("Location: manage_jobs.php"); 
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

if (isset($_GET['edit_id'])) {
    $job_id = $_GET['edit_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_job'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];

        try {
            $sql = "UPDATE jobs SET title = :title, description = :description WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':title' => $title, ':description' => $description, ':id' => $job_id]);
            header("Location: manage_jobs.php"); 
            exit;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    try {
        $sql = "SELECT * FROM jobs WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $job_id]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

if (isset($_GET['delete_id'])) {
    $job_id = $_GET['delete_id'];

    try {
        $sql = "DELETE FROM jobs WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $job_id]);
        header("Location: manage_jobs.php"); 
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

$jobs = [];
try {
    $sql = "SELECT * FROM jobs ORDER BY created_by DESC";
    $stmt = $conn->query($sql);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Job Posts</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .button-container a {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .button-container a:hover {
            background-color: #00bfff;
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

        form input, form textarea {
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
        }

        form input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 15px;
            font-size: 1.2em;
        }

        form input[type="submit"]:hover {
            background-color: #00bfff;
        }
    </style>
</head>
<body>
    <h1>Manage Job Posts</h1>
    <a href="dashboard_hr.php">Back to Dashboard</a>

    <h2>Existing Job Posts</h2>
    <table>
        <tr>
            <th>Job Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($jobs as $job): ?>
            <tr>
                <td><?php echo htmlspecialchars($job['title']); ?></td>
                <td><?php echo htmlspecialchars($job['description']); ?></td>
                <td>
                    <a href="manage_jobs.php?edit_id=<?php echo $job['id']; ?>">Edit</a> | 
                    <a href="manage_jobs.php?delete_id=<?php echo $job['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Post New Job</h2>
    <form method="POST" action="manage_jobs.php">
        <label for="title">Job Title:</label>
        <input type="text" name="title" required><br><br>
        <label for="description">Job Description:</label>
        <textarea name="description" required></textarea><br><br>
        <input type="submit" name="post_job" value="Post Job">
    </form>

    <?php if (isset($_GET['edit_id'])): ?>
        <h2>Edit Job</h2>
        <form method="POST" action="manage_jobs.php?edit_id=<?php echo $job['id']; ?>">
            <label for="title">Job Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required><br><br>
            <label for="description">Job Description:</label>
            <textarea name="description" required><?php echo htmlspecialchars($job['description']); ?></textarea><br><br>
            <input type="submit" name="edit_job" value="Update Job">
        </form>
    <?php endif; ?>

</body>
</html>
