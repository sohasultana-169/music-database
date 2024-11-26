<?php
session_start();
include_once 'db.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle playlist creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playlistName = trim($_POST['playlist_name']);
    $playlistDescription = trim($_POST['playlist_description']);
    $selectedSongs = $_POST['selected_songs'] ?? [];

    if (!empty($playlistName)) {
        try {
            // Insert new playlist
            $query = "INSERT INTO Playlists (user_id, name, description) VALUES (:user_id, :name, :description)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':name', $playlistName);
            $stmt->bindParam(':description', $playlistDescription);
            $stmt->execute();

            $playlistId = $conn->lastInsertId();

            // Insert selected songs into PlaylistSongs table
            $songCount = 0;  // Initialize song count
            if (!empty($selectedSongs)) {
                $query = "INSERT INTO PlaylistSongs (playlist_id, song_id, added_at) VALUES (:playlist_id, :song_id, NOW())";
                $stmt = $conn->prepare($query);

                foreach ($selectedSongs as $songId) {
                    $stmt->bindParam(':playlist_id', $playlistId);
                    $stmt->bindParam(':song_id', $songId);
                    $stmt->execute();
                    $songCount++;  // Increment song count for each song added
                }
            }

            // Debug: Check if song count is correct
            echo "Songs added: $songCount";  // Temporary debug output

            // Update the Playlists table with the song count
            $query = "UPDATE Playlists SET song_count = :song_count WHERE playlist_id = :playlist_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':song_count', $songCount);
            $stmt->bindParam(':playlist_id', $playlistId);
            $stmt->execute();

            // Confirm update success
            $success = "Playlist created successfully with $songCount song(s)!";

        } catch (PDOException $e) {
            $error = "Error creating playlist: " . $e->getMessage();
        }
    } else {
        $error = "Playlist name cannot be empty.";
    }
}

// Fetch all songs with album and artist details
$query = "
    SELECT 
        Songs.song_id, 
        Songs.title, 
        Songs.cover_image, 
        Artists.name AS artist_name, 
        Albums.title AS album_name
    FROM Songs 
    LEFT JOIN Artists ON Songs.artist_id = Artists.artist_id
    LEFT JOIN Albums ON Songs.album_id = Albums.album_id
";

$songs = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <div class="navbar">
        <ul>
            <li><a href="home.php">Home</a></li>
            
        </ul>
    </div>
    <title>Create Playlist</title>
    <style>
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

.navbar ul li a:hover {
    color: #2ac3ff; /* Dark text on hover */
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body */
body {
    font-family: 'Arial', sans-serif;
    background-color: #181818; /* Dark background */
    color: #f1f1f1; /* Light text color */
    line-height: 1.6;
}

/* Container */
.container {
    max-width: 900px;
    margin: 30px auto;
    background-color: #181818; /* Dark gray background */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
}

/* Title */
h2 {
    font-size: 28px;
    color: #1e90ff; /* Blue color */
    margin-bottom: 20px;
}

/* Form Inputs */
input[type="text"], textarea {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 2px solid #444;
    border-radius: 5px;
    background-color: #333;
    color: #f1f1f1;
    font-size: 16px;
}

input[type="text"]:focus, textarea:focus {
    border-color: #1e90ff; /* Blue border when focused */
    outline: none;
}

/* Button */
button {
    background-color: #1e90ff; /* Blue button */
    color: #fff;
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

button:hover {
    background-color: #0073e6; /* Darker blue on hover */
    box-shadow: 0 0 15px rgba(30, 144, 255, 0.8); /* Glowing effect on hover */
}

/* Message (Success and Error) */
.message {
    margin-top: 20px;
    text-align: center;
}

.success {
    color: #32cd32; /* Light green for success */
    font-size: 18px;
}

.error {
    color: #ff6347; /* Red for errors */
    font-size: 18px;
}

/* Song List */
.songs-list {
    margin-top: 15px;
    padding-left: 0;
    list-style-type: none;
}

.songs-list li {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #333; /* Dark background for each song item */
    border-radius: 5px;
    transition: box-shadow 0.3s ease; /* Smooth transition for glow effect */
}

.songs-list li:hover {
    box-shadow: 0 0 15px rgba(30, 144, 255, 0.8); /* Glowing effect on hover */
}

.songs-list img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
    margin-right: 15px;
}

.songs-list label {
    color: #f1f1f1;
    flex: 1;
    font-size: 16px;
}

/* Increase the size of the checkbox */
.songs-list input[type="checkbox"] {
    transform: scale(1.5); 
    margin-right: 15px; 
    cursor: pointer;
}

/* Input Fields & Labels */
label {
    font-size: 18px;
    color: #f1f1f1;
    display: block;
    margin-bottom: 10px;
}

textarea {
    height: 120px;
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h2 {
        font-size: 24px;
    }

    button {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Create a New Playlist</h2>
        <form method="POST">
            <label for="playlist_name">Playlist Name:</label>
            <input type="text" name="playlist_name" id="playlist_name" placeholder="Enter Playlist Name" required>
            
            <label for="playlist_description">Playlist Description:</label>
            <textarea name="playlist_description" id="playlist_description" placeholder="Enter Playlist Description" rows="4"></textarea>
            
            <h3>Select Songs</h3>
            <ul class="songs-list">
                <?php foreach ($songs as $song): ?>
                    <li>
                        <img src="images/<?php echo htmlspecialchars($song['cover_image']); ?>" alt="Cover Image">
                        <input type="checkbox" name="selected_songs[]" value="<?php echo $song['song_id']; ?>" id="song-<?php echo $song['song_id']; ?>">
                        <label for="song-<?php echo $song['song_id']; ?>">
                            <?php echo htmlspecialchars($song['title']); ?> - <?php echo htmlspecialchars($song['artist_name']); ?> (<?php echo htmlspecialchars($song['album_name']); ?>)
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button type="submit">Create Playlist</button>
        </form>
        <div class="message">
            <?php if (!empty($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
