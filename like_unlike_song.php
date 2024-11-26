<?php
// Include the necessary files
include_once 'functions.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and request contains necessary parameters
if (isset($_SESSION['user_id']) && isset($_POST['song_id'])) {
    $userId = $_SESSION['user_id']; // Get the logged-in user's ID
    $songId = $_POST['song_id'];    // Get the song ID from the request

    // Call the like/unlike function
    likeUnlikeSong($userId, $songId);

    // Redirect back to the previous page or homepage
    header("Location: home.php");
    exit;
} else {
    echo "Error: Missing user ID or song ID.";
}
?>