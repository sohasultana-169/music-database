<?php
// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=musicdb', 'root', 'Sonu@16900'); // Update with your credentials

// Get the song ID from the URL
$song_id = isset($_GET['song_id']) ? (int)$_GET['song_id'] : 1;
$autoplay = isset($_GET['autoplay']) && $_GET['autoplay'] == 1 ? 'autoplay' : '';

// Fetch the song details including artist and album
$stmt = $pdo->prepare("SELECT s.*, a.name AS artist_name, al.title AS album_title 
                       FROM Songs s
                       JOIN Artists a ON s.artist_id = a.artist_id
                       JOIN Albums al ON s.album_id = al.album_id
                       WHERE s.song_id = :song_id");
$stmt->execute(['song_id' => $song_id]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$song) {
    die("Song not found.");
}

// Fetch previous and next song IDs
$prevStmt = $pdo->prepare("SELECT song_id FROM Songs WHERE song_id < :song_id ORDER BY song_id DESC LIMIT 1");
$prevStmt->execute(['song_id' => $song_id]);
$prevSong = $prevStmt->fetch(PDO::FETCH_ASSOC);

$nextStmt = $pdo->prepare("SELECT song_id FROM Songs WHERE song_id > :song_id ORDER BY song_id ASC LIMIT 1");
$nextStmt->execute(['song_id' => $song_id]);
$nextSong = $nextStmt->fetch(PDO::FETCH_ASSOC);

// Default to looping to first and last song if no previous or next song
if (!$prevSong) {
    $prevStmt = $pdo->query("SELECT song_id FROM Songs ORDER BY song_id DESC LIMIT 1");
    $prevSong = $prevStmt->fetch(PDO::FETCH_ASSOC);
}
if (!$nextSong) {
    $nextStmt = $pdo->query("SELECT song_id FROM Songs ORDER BY song_id ASC LIMIT 1");
    $nextSong = $nextStmt->fetch(PDO::FETCH_ASSOC);
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($song['title']); ?> - Track Page</title>
    <style>
        /* General Body and Layout */
body {
    font-family: Arial, sans-serif;
    background-color: #1c1c1c; /* Dark background for the page */
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
}

.track-container {
    text-align: center;
    max-width: 950px;
    padding: 35px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    background-color: #333; /* Dark background for the track container */
}

.cover-image {
    width: 100%;
    max-width: 300px;
    border-radius: 10px;
    margin-bottom: 15px;
}

h2 {
    font-size: 24px;
    color: #fff;
    margin-bottom: 15px;
}

audio {
    width: 100%;
    margin-bottom: 20px;
    border-radius: 5px;
}

button {
    background-color: #0db9ff; /* Blue color for buttons */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 0 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #006bb3; /* Darker blue on hover */
}

/* Footer Player */
.footer-player {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #222; /* Dark footer background */
    color: white;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.3);
}

.footer-player img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
    margin-right: 10px;
}

.footer-player .footer-info {
    flex-grow: 1;
    display: flex;
    align-items: center;
}

.footer-player .footer-info p {
    margin: 0;
    font-size: 14px;
}

.footer-controls button {
    background-color: transparent;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 24px;
}

.footer-controls button:hover {
    color: #aaa;
}

.footer-options button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    font-size: 24px;
    color: white;
}

.footer-options button:hover {
    color: #aaa;
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
    h2 {
        font-size: 1.8rem;
    }

    .track-container {
        padding: 15px;
        max-width: 350px;
    }

    .cover-image {
        max-width: 250px;
    }

    .footer-player {
        padding: 15px;
    }

    .footer-player img {
        width: 40px;
        height: 40px;
    }

    .footer-player .footer-info p {
        font-size: 12px;
    }

    .footer-controls button {
        font-size: 20px;
    }
}

/* For smaller screens (max-width: 480px) */
@media screen and (max-width: 480px) {
    h2 {
        font-size: 1.5rem;
    }

    .track-container {
        padding: 10px;
        max-width: 280px;
    }

    .cover-image {
        max-width: 200px;
    }

    
}
    </style>
</head>
<body>
    <div class="track-container">
        <img src="images/<?php echo htmlspecialchars($song['cover_image']); ?>" alt="Cover Image" class="cover-image">
        <h2><?php echo htmlspecialchars($song['title']); ?></h2>
        <p>Artist: <?php echo htmlspecialchars($song['artist_name']); ?></p>
        <p>Album: <?php echo htmlspecialchars($song['album_title']); ?></p>
        
        <audio id="audio-player" controls <?php echo $autoplay; ?>>
            <source src="songs/<?php echo htmlspecialchars($song['file_path']); ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
        
        <div class="controls">
            <a href="track.php?song_id=<?php echo $prevSong['song_id']; ?>&autoplay=1"><button>Previous</button></a>
            <a href="track.php?song_id=<?php echo $nextSong['song_id']; ?>&autoplay=1"><button>Next</button></a>
        </div>
    </div>

   
</body>
</html>