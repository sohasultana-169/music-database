<?php
$data = json_decode(file_get_contents('php://input'), true);
$playlistName = $data['name'];

// SQL to insert the new playlist into the database
// Example query
$sql = "INSERT INTO playlists (name) VALUES ('$playlistName')";
// Execute the query and respond with JSON
?>
