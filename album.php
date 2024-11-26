<?php
session_start();
include_once 'functions.php';
include_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get the album ID from the URL
if (isset($_GET['album_id'])) {
    $albumId = (int)$_GET['album_id'];
} else {
    // Redirect if album ID is not provided
    header("Location: home.php");
    exit();
}

// Fetch album details
$db = getDbConnection();
$albumQuery = "SELECT title, cover_image, release_date FROM albums WHERE album_id = :album_id";
$albumStmt = $db->prepare($albumQuery);
$albumStmt->bindParam(':album_id', $albumId, PDO::PARAM_INT);
$albumStmt->execute();
$album = $albumStmt->fetch(PDO::FETCH_ASSOC);

// Fetch songs in the album
$songQuery = "SELECT song_id, title, duration, file_path, cover_image FROM songs WHERE album_id = :album_id";
$songStmt = $db->prepare($songQuery);
$songStmt->bindParam(':album_id', $albumId, PDO::PARAM_INT);
$songStmt->execute();
$songs = $songStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch liked songs for the user
$likedSongs = getUserLikedSongs($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($album['title'] ?? "Album Details"); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            padding: 20px;
        }

        .album-header {
            text-align: center;
        }

        .album-header img {
            width: 200px;
            height: 200px;
            border-radius: 10px;
        }

        .song-list {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .song {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .audio-player {
            display: flex;
            align-items: center;
            gap: 15px;
            background-color: #333;
            padding: 10px;
            border-radius: 5px;
            width: 400px;
            margin-top: 10px;
        }

        .play-btn, .like-btn {
            background-color: #0db9ff;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .play-btn:hover, .like-btn:hover {
            background-color: #0027b3; 
            box-shadow: 0 6px 12px rgba(13, 185, 255, 0.7); 
            transform: scale(1.05); 
        }
    </style>
</head>
<body>
    <!-- Album Details -->
    <div class="album-header">
        <?php if ($album): ?>
            <h1><?php echo htmlspecialchars($album['title']); ?></h1>
            <p>Release Date: <?php echo htmlspecialchars($album['release_date']); ?></p>
            <img src="images/<?php echo htmlspecialchars($album['cover_image']); ?>" alt="Album Cover">
        <?php else: ?>
            <h1>Album Not Found</h1>
        <?php endif; ?>
    </div>

    <!-- Songs in Album -->
    <?php if ($songs): ?>
        <h2>Songs</h2>
        <div class="song-list">
            <?php foreach ($songs as $song): ?>
                <div class="song">
                    <div>
                        <img src="images/<?php echo htmlspecialchars($song['cover_image']); ?>" alt="Song Cover" style="width: 100px; height: 100px; border-radius: 10px;">
                    </div>
                    <div>
                        <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                        <p>Duration: <?php echo htmlspecialchars($song['duration']); ?></p>
                        <audio id="audio-<?php echo $song['song_id']; ?>" src="songs/<?php echo htmlspecialchars($song['file_path']); ?>" preload="auto"></audio>
                        <button class="play-btn" onclick="togglePlay(<?php echo $song['song_id']; ?>)" id="play-btn-<?php echo $song['song_id']; ?>">Play</button>
                        
                        <?php
                        // Check if the song is liked by the user
                        $liked = in_array($song['song_id'], $likedSongs) ? 'Unlike' : 'Like';
                        ?>
                        <form action="like_unlike_song.php" method="POST" style="display:inline;">
                            <input type="hidden" name="song_id" value="<?php echo $song['song_id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <button type="submit" class="like-btn"><?php echo $liked; ?></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No songs found for this album.</p>
    <?php endif; ?>

    <script>
        function togglePlay(songId) {
            const audio = document.getElementById('audio-' + songId);
            const playButton = document.getElementById('play-btn-' + songId);

            if (audio.paused) {
                audio.play();
                playButton.textContent = 'Pause';
            } else {
                audio.pause();
                playButton.textContent = 'Play';
            }
        }
    </script>
</body>
</html>