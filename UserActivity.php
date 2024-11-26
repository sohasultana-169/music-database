<?php
include 'db.php';

function logUserActivity($user_id, $activity_type, $details, $performance) {
    global $conn;

    $sql = "INSERT INTO UserActivity (user_id, activity_type, details, performance) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $activity_type, $details, $performance);

    if ($stmt->execute()) {
        echo "Activity logged successfully";
    } else {
        echo "Error logging activity: " . $stmt->error;
    }

    $stmt->close();
}

// Example usage
$user_id = 1; // Replace with the actual user ID
$activity_type = "play";
$details = "User played the song 'Song Title'";
$performance = "Good";

logUserActivity($user_id, $activity_type, $details, $performance);
?>
