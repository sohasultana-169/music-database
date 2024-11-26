<?php
session_start();
include 'functions.php';  // Include the database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

// Handle Add to Favorites
if (isset($_POST['favorite'])) {
    $song_id = $_POST['song_id'];  // Get the song ID from the form
    $query = "INSERT INTO UserFavourite (user_id, song_id, favorited_at) VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $song_id]);
    header("Location: favorites.php");  // Reload page to show updated favorites
    exit();
}

// Handle Remove from Favorites
if (isset($_POST['removeFavorite'])) {
    $song_id = $_POST['song_id'];  // Get the song ID from the form
    $query = "DELETE FROM UserFavourite WHERE user_id = ? AND song_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $song_id]);
    header("Location: favorites.php");  // Reload page to show updated favorites
    exit();
}

// Fetch the user's favorite songs
$query = "SELECT Songs.song_id, Songs.title, Songs.artist, Songs.album, Songs.audio_file
          FROM UserFavourite
          JOIN Songs ON UserFavourite.song_id = Songs.song_id
          WHERE UserFavourite.user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all songs not yet favorited by the user
$query = "SELECT * FROM Songs WHERE song_id NOT IN (SELECT song_id FROM UserFavourite WHERE user_id = ?)";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Favorite Songs</title>
    <link rel="stylesheet" href="css/style.css">  <!-- Include your styles -->
</head>
<body>
    <h1>Your Favorite Songs</h1>
    <div class="favorites-container">
        <?php foreach ($favorites as $song): ?>
            <div class="song-box">
                <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                <p>Artist: <?php echo htmlspecialchars($song['artist']); ?></p>
                <p>Album: <?php echo htmlspecialchars($song['album']); ?></p>
                <audio controls>
                    <source src="<?php echo htmlspecialchars($song['audio_file']); ?>" type="audio/mp3">
                    Your browser does not support the audio element.
                </audio>
                <form method="POST">
                    <input type="hidden" name="song_id" value="<?php echo $song['song_id']; ?>">
                    <button type="submit" name="removeFavorite">Remove from Favorites</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Display Songs Available for Adding to Favorites -->
    <h2>Available Songs</h2>
    <div class="songs-container">
        <?php foreach ($songs as $song): ?>
            <div class="song-box">
                <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                <p>Artist: <?php echo htmlspecialchars($song['artist']); ?></p>
                <p>Album: <?php echo htmlspecialchars($song['album']); ?></p>
                <audio controls>
                    <source src="<?php echo htmlspecialchars($song['audio_file']); ?>" type="audio/mp3">
                    Your browser does not support the audio element.
                </audio>
                <form method="POST">
                    <input type="hidden" name="song_id" value="<?php echo $song['song_id']; ?>">
                    <button type="submit" name="favorite">Add to Favorites</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
