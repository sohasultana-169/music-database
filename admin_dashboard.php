<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access Denied: Only admins can access this page.";
    exit();
}


<h1>Admin Dashboard</h1>
<p>Welcome, Admin!</p>
<ul>
    <li><a href="add_song.php">Add Song</a></li>
    <li><a href="delete_song.php">Delete Song</a></li>
    <li><a href="view_activity.php">View User Activity</a></li>
</ul>
<a href="logout.php">Logout</a>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<div class="container">
    <h2>Admin Dashboard</h2>

    <!-- Add Song Section -->
    <section>
        <h2>Add Song</h2>
        <form action="add_song.php" method="POST">
            <input type="text" name="title" placeholder="Song Title" required><br>
            <input type="text" name="artist_id" placeholder="Artist ID" required><br>
            <input type="text" name="album_id" placeholder="Album ID" required><br>
            <input type="time" name="duration" placeholder="Duration" required><br>
            <input type="text" name="file_path" placeholder="File Path" required><br>
            <input type="text" name="cover_image" placeholder="Cover Image Path"><br>
            <button type="submit">Add Song</button>
        </form>
    </section>

    <!-- Delete Song Section -->
    <section>
        <h2>Delete Song</h2>
        <?php
        $sql = "SELECT * FROM Songs";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Song ID</th>
                        <th>Title</th>
                        <th>Delete</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['song_id'] . "</td>
                        <td>" . $row['title'] . "</td>
                        <td><a href='delete_song.php?id=" . $row['song_id'] . "'>Delete</a></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No songs found.</p>";
        }
        ?>
    </section>

    <!-- User Activity Section -->
    <section>
        <h2>User Activity Log</h2>
        <?php
        $sql = "SELECT u.username, a.activity_type, a.activity_time, a.details, a.performance
                FROM UserActivity a
                JOIN Users u ON a.user_id = u.user_id
                ORDER BY a.activity_time DESC";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='activity-table'>
                    <tr>
                        <th>Username</th>
                        <th>Activity Type</th>
                        <th>Activity Time</th>
                        <th>Details</th>
                        <th>Performance</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['username'] . "</td>
                        <td>" . $row['activity_type'] . "</td>
                        <td>" . $row['activity_time'] . "</td>
                        <td>" . $row['details'] . "</td>
                        <td>" . $row['performance'] . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No user activity recorded.</p>";
        }
        ?>
    </section>
</div>
</body>
</html>
