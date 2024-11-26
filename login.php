<?php
session_start();
include_once 'db.php';  // Using include_once to avoid multiple declarations

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SQL query
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);

    // Check if the query was prepared successfully
    if ($stmt === false) {
        die("Error preparing the SQL statement.");
    }

    // Bind the parameter and execute the query
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and password is correct
    if ($user && password_verify($password, $user['pwd'])) {
        // Store user info in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];  // Store username
        $_SESSION['role'] = ($user['is_admin'] == 1) ? 'admin' : 'user';

        // Redirect user based on role
        if ($_SESSION['role'] == 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: home.php");
            exit();
        }
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
        .error {
            color: red;
            margin-top: 10px;
        }
        .signup-link {
            margin-top: 20px;
        }
        .signup-link a {
            color: #0db9ff;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
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
        <h2>Login Form</h2>

        <!-- PHP to handle error messages -->
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" id="username" name="username" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Enter Password" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>
</body>
</html>
