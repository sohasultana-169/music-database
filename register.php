<?php
session_start();
include 'db.php';  // Ensure this file contains your database connection logic

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Get the selected role (user/admin)

    // Check if the passwords match
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit();
    }

    // Check if username already exists
    $sql = "SELECT COUNT(*) FROM Users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $existingUserCount = $stmt->fetchColumn();

    if ($existingUserCount > 0) {
        echo "Error: Username already taken.";
        exit();
    }

    // Hash the password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Map role to integer (1 for admin, 0 for user)
    $role = ($role == 'admin') ? 1 : 0;

    // Prepare the SQL query
    $sql = "INSERT INTO Users (username, pwd, is_admin) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql); // Use $conn here, not $pdo

    // Bind parameters
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->bindParam(2, $password_hashed, PDO::PARAM_STR);
    $stmt->bindParam(3, $role, PDO::PARAM_INT);

    // Execute the query and handle the redirection after registration
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->lastInsertId(); // Get the ID of the newly inserted user
        $_SESSION['role'] = $role; // Store the user's role in the session

        if ($role == 1) {
            // Redirect admins to the admin dashboard
            header("Location: admin_dashboard.php");
        } else {
            // Redirect regular users to the home page
            header("Location: home.php");
        }
        exit(); // Ensure no further code is executed after the redirect
    } else {
        // Display error message if execution fails
        echo "Error: " . $stmt->errorInfo()[2]; // Provide more details if an error occurs
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicDB - Register</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background: radial-gradient(circle, #000 60%, #1a1a1a 100%);
            color: #fff;
            margin: 0;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
        }
        h2 {
            font-size: 2em;
            color: #0db9ff;
            margin-bottom: 20px;
            text-shadow: 0px 0px 5px #0db9ff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            outline: none;
            font-size: 1em;
            color: #fff;
            background: #222;
            box-shadow: 0 0 5px #0db9ff;
        }
        select {
            width: 100%;
            padding: 12px;
            background: #222;
            color: #fff;
            border: none;
            border-radius: 5px;
            box-shadow: 0 0 5px #0db9ff;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            font-weight: bold;
            color: #fff;
            background: #0db9ff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #006bb3;
        }
        /* Responsive styling */
        @media (max-width: 400px) {
            .container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <input type="text" name="email_id" placeholder="Enter emailID" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
           
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
