<?php
session_start();
include_once 'functions.php';
include_once 'db.php';

// Initialize the connection
$database = new Database();
$db = $database->getConnection();  // PDO connection

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $songId = $_POST['song_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($songId && $action) {
        // Handle like/unlike actions
        if ($action === 'like') {
            $result = likeSong($userId, $songId); // Like the song
        } elseif ($action === 'unlike') {
            $result = unlikeSong($userId, $songId); // Unlike the song
        }

        // Redirect back to home page after action
        header("Location: home.php");
        exit();
    }
} else {
    echo "Please log in to perform this action.";
    exit();
}
?>