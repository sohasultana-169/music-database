<?php
// Include database connection
include_once 'db.php';
session_start();

// Function to fetch song details by ID
function getSongById($db, $song_id) {
    $query = "SELECT * FROM Songs WHERE song_id = :song_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':song_id', $song_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to log the song into UserListeningHistory table
function logUserListening($db, $user_id, $song_id) {
    $query = "INSERT INTO UserListeningHistory (user_id, song_id, listened_at) VALUES (:user_id, :song_id, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':song_id', $song_id);
    $stmt->execute();
}

// Check if song_id is provided in the URL
if (isset($_GET['id'])) {
    $song_id = $_GET['id'];

    // Fetch the song details
    $database = new Database();
    $db = $database->getConnection();
    $song = getSongById($db, $song_id);

    // If song is found, log it to UserListeningHistory table
    if ($song) {
        $user_id = $_SESSION['user_id']; // Assuming user is logged in and user_id is stored in session
        logUserListening($db, $user_id, $song_id); // Log the song

        // Now prepare the song data to be shown
        $title = htmlspecialchars($song['title']);
        $filePath = htmlspecialchars($song['file_path']);
        $albumCover = htmlspecialchars($song['cover_image']);
        $artist = htmlspecialchars($song['artist_id']);
    } else {
        echo "Song not found.";
        exit;
    }
} else {
    echo "No song selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Play Song - <?php echo $title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Now Playing: <?php echo $title; ?></h1>
    <div class="song-info">
        <img src="path_to_images/<?php echo $albumCover; ?>" alt="Album Cover" width="200">
        <p>Artist: <?php echo $artist; ?></p>
    </div>

    <div class="audio-player">
        <audio controls>
            <source src="<?php echo $filePath; ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
</body>
</html>
