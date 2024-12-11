<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .button-container a {
            width: 48%;
            text-decoration: none;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Job Application System</h1>
        <div class="button-container">
            <a href="login.php"><button>Login Here</button></a>
            <a href="register.php"><button>Register Here</button></a>
        </div>
    </div>
</body>
</html>
