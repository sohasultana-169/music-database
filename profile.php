<?php
session_start(); // Start the session

// Include the database connection file
include_once 'db.php'; // Ensure this path is correct

// Create a database connection using the Database class from db.php
$database = new Database();
$db = $database->getConnection();

// Check if the database connection was successful
if ($db === null) {
    die("Database connection failed.");
}

// Check if user is logged in
if (isset($_SESSION['username'])) {
    $welcomeMessage = "Welcome " . $_SESSION['username'];
} else {
    $welcomeMessage = "Welcome Guest";
}

// Fetch user profile information or any other data if necessary
$query = "SELECT * FROM Users WHERE username = :username"; // Adjust the query as per your needs

// Prepare the query
$stmt = $db->prepare($query);

// Check if the query preparation was successful
if ($stmt === false) {
    die("Error preparing the query: " . print_r($db->errorInfo(), true));
}

// Bind the username parameter
$stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);

// Execute the query
$stmt->execute();

// Fetch the user data
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data was retrieved
if ($user === false) {
    // Handle the case where no user was found
    die("User not found or invalid session.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/home.css"> <!-- Link to your home.css for navbar -->
    <style>
        /* Base styles from login.php */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background: radial-gradient(circle, #000 60%, #1a1a1a 100%);
            color: #fff;
            margin: 0;
            flex-direction: column;
        }
        .navbar {
            width: 100%;
            background-color: #000;
            padding: 10px 0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .navbar ul li {
            margin: 0 20px;
        }
        .navbar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1em;
            padding: 10px;
            transition: background-color 0.3s;
        }
        .navbar ul li a:hover {
            background-color: #0db9ff;
            border-radius: 5px;
        }
        /* Profile Page Styles */
        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
            margin-top: 70px; /* To make space for the fixed navbar */
        }
        h1 {
            font-size: 2.5em;
            color: #0db9ff;
            margin-bottom: 20px;
            text-shadow: 0px 0px 5px #0db9ff;
        }
        .profile-info {
            text-align: left;
            font-size: 1.1em;
            margin: 20px 0;
        }
        .profile-info p {
            color: #ddd;
            margin-bottom: 10px;
        }
        .profile-info h2 {
            font-size: 1.5em;
            color: #0db9ff;
            margin-bottom: 10px;
        }
        .profile-picture img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid #0db9ff;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
        }
        /* Responsive styling */
        @media (max-width: 400px) {
            .container {
                width: 100%;
                padding: 20px;
            }
            h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </div>

    <!-- Profile Page Content -->
    <div class="container">
        <h1>User Profile</h1>

        <div class="profile-picture">
            <img src="https://static.vecteezy.com/system/resources/previews/019/465/366/original/3d-user-icon-on-transparent-background-free-png.png" alt="User Profile Picture" loading="lazy">
        </div>

        <div class="profile-info">
            <h2>Username:</h2>
            <p><?php echo htmlspecialchars($user['username']); ?></p>

            

            <h2>Created At:</h2>
            <p><?php echo htmlspecialchars($user['created_at']); ?></p>
        </div>
    </div>
</body>
</html>
