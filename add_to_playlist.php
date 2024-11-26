<?php
session_start();
include('db.php');  // Include your database connection file

if (isset($_POST['add_to_playlist'])) {
    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "You need to log in to add to a playlist.";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $playlist_name = $_POST['playlist_name'];
    $song_id = $_POST['song_id'];

    // Insert the new playlist into the Playlists table
    try {
        $query = "INSERT INTO Playlists (user_id, name) VALUES (:user_id, :playlist_name)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':playlist_name', $playlist_name);
        $stmt->execute();

        // Get the ID of the newly created playlist
        $playlist_id = $conn->lastInsertId();

        // Now add the song to the newly created playlist
        $insertSongQuery = "INSERT INTO PlaylistSongs (playlist_id, song_id) VALUES (:playlist_id, :song_id)";
        $stmt = $conn->prepare($insertSongQuery);
        $stmt->bindParam(':playlist_id', $playlist_id);
        $stmt->bindParam(':song_id', $song_id);
        $stmt->execute();

        echo "Playlist created and song added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
