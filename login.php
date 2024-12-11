<?php
session_start();
include 'sql/dbconfig.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; 

    $sql = "SELECT * FROM users WHERE email = :email AND role = :role";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['role'];

            if ($role === 'applicant') {
                header("Location: dashboard_applicant.php");
                exit;
            } elseif ($role === 'hr') {
                header("Location: dashboard_hr.php");
                exit;
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No user found with that email and role.";
    }
}

if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, select {
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
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

        p {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>

            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="applicant">Applicant</option>
                <option value="hr">HR</option>
            </select><br>

            <button type="submit">Login</button>
        </form>

        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <div class="links">
            <a href="register.php">Register</a>
            <a href="index.php">Go back</a>
        </div>
    </div>
</body>
</html>
