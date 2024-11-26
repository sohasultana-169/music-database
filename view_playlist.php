<?php
include_once 'db.php';
include_once 'functions.php';

// Check if 'id' is set in $_GET and assign it to $playlistId
if (isset($_GET['id'])) {
    $playlistId = $_GET['id'];
} else {
    die("Playlist ID not provided.");
}

// Fetch playlist details
$playlistQuery = "SELECT * FROM Playlists WHERE id = :playlistId";
$stmt = $conn->prepare($playlistQuery);
if ($stmt) {
    $stmt->bindParam(':playlistId', $playlistId);
    $stmt->execute();
    $playlist = $stmt->fetch();
} else {
    die("Failed to prepare statement for playlist details.");
}

// Fetch songs in the playlist
$songsQuery = "
    SELECT Songs.id, Songs.title 
    FROM playlist_songs 
    JOIN Songs ON playlist_songs.song_id = Songs.id 
    WHERE playlist_songs.playlist_id = :playlistId";
$stmt = $conn->prepare($songsQuery);
if ($stmt) {
    $stmt->bindParam(':playlistId', $playlistId);
    $stmt->execute();
    $songs = $stmt->fetchAll();
} else {
    die("Failed to prepare statement for fetching songs.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Playlist - <?php echo htmlspecialchars($playlist['playlist_name']); ?></title>
</head>
<body>
    <h2>Playlist: <?php echo htmlspecialchars($playlist['playlist_name']); ?></h2>
    <ul>
        <?php foreach ($songs as $song): ?>
            <li><?php echo htmlspecialchars($song['title']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
