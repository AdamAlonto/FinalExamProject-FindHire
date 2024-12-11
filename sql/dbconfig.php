<?php
$host = 'localhost';
$dbname = 'job_application_system';  // Replace with your actual DB name
$username = 'root';  // Default for XAMPP MySQL
$password = '';  // Default for XAMPP MySQL

try {
    // Establish PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
