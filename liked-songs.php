<?php
session_start(); // Start the session

// Include the functions.php file to access the functions
include_once 'functions.php';

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's ID from the session
$userId = $_SESSION['user_id'];

// Fetch liked songs for the logged-in user
try {
    // Fetch the liked songs using the function from functions.php
    $likedSongs = getUserLikedSongs($userId);
} catch (Exception $e) {
    // Handle error if the database query fails
    echo "Error: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Liked Songs</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
        </ul>
    </nav>

    <div class="container">
        <h2>Your Liked Songs</h2>

        <?php if (!empty($likedSongs)): ?>
            <ul>
                <?php foreach ($likedSongs as $songId): ?>
                    <!-- Fetch song details for each liked song -->
                    <?php
                        $db = getDbConnection(); // Make sure you have a valid DB connection
                        $stmt = $db->prepare("SELECT s.song_id, s.title, s.file_path, s.cover_image, a.title AS album_title, ar.name AS artist_name 
                                              FROM songs s
                                              JOIN albums a ON s.album_id = a.album_id
                                              JOIN artists ar ON s.artist_id = ar.artist_id
                                              WHERE s.song_id = :song_id");
                        $stmt->bindParam(':song_id', $songId);
                        $stmt->execute();
                        $song = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <li class="song">
                        <div class="song-cover">
                            <img src="images/<?php echo htmlspecialchars($song['cover_image']); ?>" alt="<?php echo htmlspecialchars($song['title']); ?> cover">
                        </div>
                        <div class="song-details">
                            <h4><?php echo htmlspecialchars($song['title']); ?></h4>
                            <p>Artist: <?php echo htmlspecialchars($song['artist_name']); ?></p>
                            <p>Album: <?php echo htmlspecialchars($song['album_title']); ?></p>
                            <audio id="audio-player" controls <?php echo $autoplay; ?>>
                                <source src="songs/<?php echo htmlspecialchars($song['file_path']); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You have no liked songs.</p>
        <?php endif; ?>
    </div>
</body>
</html>