<?php

// Include the db.php file to load the Database class
include_once 'db.php';

// Start the session only if it has not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // Start session only if it is not already started
}

// Database connection function
function getDbConnection() {
    // Your database connection logic here
    try {
        $database = new Database();  // Assuming Database class is defined in db.php
        return $database->getConnection();  // Returns the PDO connection
    } catch (Exception $e) {
        echo "Error connecting to the database: " . $e->getMessage();
        exit;
    }
}

// Function to get the songs liked by the user
function getUserLikedSongs($userId) {
    $db = getDbConnection();  // Get the database connection

    // Prepare SQL query to fetch liked songs for the logged-in user
    $stmt = $db->prepare("SELECT song_id FROM UserFavourite WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // Fetch and return the liked song IDs
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Function to add or remove a song from the liked list
function likeUnlikeSong($userId, $songId) {
    $db = getDbConnection();  // Get the database connection

    // Check if the song is already liked
    $stmt = $db->prepare("SELECT COUNT(*) FROM UserFavourite WHERE user_id = :user_id AND song_id = :song_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':song_id', $songId);
    $stmt->execute();
    
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        // Song is already liked, so we remove it (unlike)
        $stmt = $db->prepare("DELETE FROM UserFavourite WHERE user_id = :user_id AND song_id = :song_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':song_id', $songId);
        $stmt->execute();
    } else {
        // Song is not liked, so we add it (like)
        $stmt = $db->prepare("INSERT INTO UserFavourite (user_id, song_id) VALUES (:user_id, :song_id)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':song_id', $songId);
        $stmt->execute();
    }
}

// Function to fetch song details by song_id
function getSongDetails($songId) {
    $db = getDbConnection();  // Get the database connection

    // Prepare SQL query to fetch song details
    $stmt = $db->prepare("SELECT s.song_id, s.title, s.file_path, s.cover_image, a.title AS album_title, ar.name AS artist_name
                          FROM songs s
                          JOIN albums a ON s.album_id = a.album_id
                          JOIN artists ar ON s.artist_id = ar.artist_id
                          WHERE s.song_id = :song_id");
    $stmt->bindParam(':song_id', $songId);
    $stmt->execute();

    // Return the song details as an associative array
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>