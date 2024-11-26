<?php
session_start();
include('db.php');  

$database = new Database();
$conn = $database->getConnection();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to manage playlists.";
    exit();
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID

// Fetch all playlists for the user
$query = "SELECT playlist_id, name FROM Playlists WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all songs for the user to display in the playlist
$songsQuery = "SELECT song_id, title, file_path, cover_image FROM Songs";
$songsStmt = $conn->prepare($songsQuery);
$songsStmt->execute();
$songs = $songsStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Playlists</title>
    <style>
        /* General Reset */
* {
    margin-left: 30px;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Body Styles */
html, body {
    background-color: #1a1a1a; /* Dark background */
    color: #fff; /* White text */
    font-size: 16px;
    line-height: 1.6;
    overflow-x: hidden; /* Prevent horizontal scroll */
}

/* Header */
h2 {
    text-align: center;
    color: #0db9ff; /* Blue color */
    font-size: 2.5rem;
    margin-top: 80px; /* Space to avoid navbar overlap */
}

/* Playlist List */
ul {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}

ul li {
    margin-bottom: 20px;
}

/* Song Item */
.song-container {
    display: flex;
    text-align: center;
    align-items: center;
    background-color: #222222;
    padding: 8px;
    height:100px;
    border-radius: 8px;
    margin-bottom: 10px;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2); /* Light blue shadow */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 80% ;
}

.song-container:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4); /* Enhanced hover effect */
}

/* Song Cover Image */
.song-container img {
    width: 70px; /* Cover image size */
    height: 70px;
    margin-right: 15px;
    border-radius: 5px;
}

/* Song Title */
.song-container span {
    flex: 1;
    color: #fff;
    font-size: 1rem;
    margin-right: 15px;
}

/* Play/Pause Button */
.song-container button {
    background-color: #0db9ff;
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.song-container button:hover {
    background-color: #006bb3; /* Darker blue on hover */
    transform: scale(1.05); /* Button enlarges on hover */
}


#songTitle {
    color: #fff;
    font-size: 1.1rem;
}

@media screen and (max-width: 768px) {
    h2 {
        font-size: 2rem;
    }

    .song-container {
        flex-direction: column;
        text-align: center;
        padding: 10px;
    }

    .song-container img {
        width: 60px;
        height: 60px;
    }

    .song-container span {
        margin-bottom: 10px;
    }

    .footer-controls button {
        font-size: 24px;
    }

    .footer-song-info img {
        width: 40px;
        height: 40px;
    }

    #footer {
        padding: 10px 0;
    }
}

/* For Mobile Screens (max-width: 480px) */
@media screen and (max-width: 480px) {
    h2 {
        font-size: 1.5rem;
    }

    .song-container img {
        width: 50px;
        height: 50px;
    }

    .song-container span {
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .footer-controls button {
        font-size: 20px;
    }

    .footer-song-info img {
        width: 35px;
        height: 35px;
    }

    #footer {
        padding: 8px 0;
    }
}

.navbar ul {
    list-style: none;
    display: flex;
    justify-content: center;
    align-items: center;
}

.navbar ul li {
    margin: 0 20px;
}

.navbar ul li a {
    color: #0db9ff; /* Light blue color for navbar links */
    text-decoration: none;
    font-size: 18px;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}


    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
          
        </ul>
    </div>
    
    <h2>Your Playlists</h2>
    <ul>
        <?php foreach ($playlists as $playlist): ?>
            <li>
                <?php echo $playlist['name']; ?> 
                <ul>
                    <?php
                    // Fetch songs in the current playlist
                    $playlistSongsQuery = "SELECT s.song_id, s.title, s.file_path, s.cover_image 
                                           FROM Songs s 
                                           JOIN PlaylistSongs ps ON s.song_id = ps.song_id 
                                           WHERE ps.playlist_id = :playlist_id";
                    $stmt = $conn->prepare($playlistSongsQuery);
                    $stmt->bindParam(':playlist_id', $playlist['playlist_id']);
                    $stmt->execute();
                    $playlistSongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($playlistSongs as $song): ?>
                        <li class="song-container">
                            <!-- Display Cover Image -->
                            <?php if ($song['cover_image']): ?>
                                <img src="images/<?php echo $song['cover_image']; ?>" alt="Cover image of <?php echo $song['title']; ?>">
                            <?php else: ?>
                                <img src="default-cover.jpg" alt="Default cover image">
                            <?php endif; ?>

                            <!-- Display Song Title -->
                            <span><?php echo $song['title']; ?></span>
                            
                            <!-- Audio Player -->
                            <audio id="audio-<?php echo $song['song_id']; ?>" class="audio-player" controls>
                                <source src="songs/<?php echo $song['file_path']; ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            
                           
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>

    
</body>
</html>
