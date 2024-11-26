<?php
// Include functions file
include 'functions.php';  

// Get album_id from URL
$album_id = isset($_GET['album_id']) ? $_GET['album_id'] : 1;  

// Fetch album and song details
$album = getAlbumDetails($album_id);
$songs = getSongsByAlbum($album_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($album['title']); ?> - Album Details</title>
    <link rel="stylesheet" href="css/home.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .main-content {
            padding: 20px;
        }

        .album-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .album-cover {
            width: 200px;
            height: 200px;
            border-radius: 8px;
        }

        .album-details h1 {
            margin: 0;
            font-size: 32px;
            color: #1DB954;
        }

        .album-details p {
            margin: 5px 0;
            color: #ccc;
        }

        .song-container {
            margin-top: 20px;
        }

        .song-row {
            display: grid;
            grid-template-columns: 50px auto auto 100px;
            gap: 10px;
            align-items: center;
            padding: 10px;
            background-color: #1C1C1C;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .song-row:hover {
            background-color: #333;
        }

        .track-number {
            text-align: center;
            font-size: 14px;
            color: #888;
        }

        .song-details {
            display: flex;
            flex-direction: column;
        }

        .song-title {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
        }

        .song-artist {
            font-size: 14px;
            color: #ccc;
        }

        .duration {
            text-align: right;
            font-size: 14px;
            color: #ccc;
        }

        .controls {
            display: flex;
            gap: 10px;
        }

        .controls button {
            background-color: #1DB954;
            border: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .controls button.like {
            background-color: #333;
        }

        .controls button:hover {
            background-color: #1AA34A;
        }

        .controls button.like:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Album Header -->
        <div class="album-header">
            <img src="images/<?php echo htmlspecialchars($album['cover_image']); ?>" alt="Album Cover" class="album-cover">
            <div class="album-details">
                <h1><?php echo htmlspecialchars($album['title']); ?></h1>
                <p>By <?php echo htmlspecialchars($album['artist']); ?> • <?php echo htmlspecialchars($album['release_date']); ?></p>
                <p><?php echo count($songs); ?> Songs</p>
            </div>
        </div>

        <!-- Song List -->
        <div class="song-container">
            <?php if ($songs): ?>
                <?php foreach ($songs as $index => $song): ?>
                    <div class="song-row">
                        <div class="track-number"><?php echo $index + 1; ?></div>
                        <div class="song-details">
                            <div class="song-title"><?php echo htmlspecialchars($song['title']); ?></div>
                            <div class="song-artist"><?php echo htmlspecialchars($song['artist_id']); ?></div>
                        </div>
                        <div class="controls">
                            <button id="playPause-<?php echo $song['song_id']; ?>" onclick="togglePlayPause(<?php echo $song['song_id']; ?>)">Play</button>
                            <button class="like" onclick="likeSong(<?php echo $song['song_id']; ?>)">Like</button>
                        </div>
                        <audio id="audio-<?php echo $song['song_id']; ?>" src="songs/<?php echo htmlspecialchars($song['file_path']); ?>"></audio>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No songs found for this album.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePlayPause(songId) {
            const audio = document.getElementById('audio-' + songId);
            const button = document.getElementById('playPause-' + songId);

            if (audio.paused) {
                audio.play();
                button.innerText = 'Pause';
            } else {
                audio.pause();
                button.innerText = 'Play';
            }
        }

        function likeSong(songId) {
    fetch('like_song.php?song_id=' + songId, { method: 'GET' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Show success message
            } else {
                alert(data.message); // Show error message if song already liked
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while liking the song.');
        });
}

    </script>
</body>
</html>
