<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hr') {
    header("Location: login_hr.php");
    exit;
}

include 'sql/dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    $sql = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $application_id);

    if ($stmt->execute()) {
        header("Location: view_applications.php?job_id=" . $_GET['job_id']);
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
