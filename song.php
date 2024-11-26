<?php
// Include the database connection class
include 'db.php'; // Make sure the path is correct

// Instantiate the Database class
$database = new Database();
$conn = $database->getConnection(); // Get the database connection

// Function to fetch songs from the database
function getSongs($conn) {
    $query = "SELECT s.song_id, s.title, s.duration, s.file_path, s.cover_image, a.name AS artist_name, al.title AS album_title
              FROM Songs s
              JOIN Artists a ON s.artist_id = a.artist_id
              JOIN Albums al ON s.album_id = al.album_id
              ORDER BY s.track_number";  // Order by track number to display songs in sequence
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all songs
}

// Fetch songs from the database
$songs = getSongs($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Songs List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            padding: 20px;
        }

        .song-list {
            margin-top: 30px;
        }

        .song-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #333;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cover-image {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }

        .song-info {
            flex: 1;
        }

        .play-btn {
            background-color: #1db954;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .play-btn:hover {
            background-color: #1ed760;
        }
    </style>
</head>
<body>

<h1>Song List</h1>

<div class="song-list">
    <?php foreach ($songs as $song): ?>
        <div class="song-item">
            <img src="images/<?php echo htmlspecialchars($song['cover_image']); ?>" alt="Cover Image" class="cover-image">
            <div class="song-info">
                <h3><?php echo htmlspecialchars($song['title']); ?></h3>
                <p>Artist: <?php echo htmlspecialchars($song['artist_name']); ?></p>
                <p>Album: <?php echo htmlspecialchars($song['album_title']); ?></p>
                <p>Duration: <?php echo htmlspecialchars($song['duration']); ?></p>
            </div>
            <a href="play_song.php?song_id=<?php echo $song['song_id']; ?>" class="play-btn">Play Song</a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>

<?php
// Close the database connection
$conn = null;
?>
