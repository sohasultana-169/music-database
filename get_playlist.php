<?php
include 'db.php';
$stmt = $conn->query("SELECT id, playlist_name FROM Playlists");
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($playlists);
?>
