<?php
session_start();
include('db.php');  // Include your database connection file

// Initialize the Database object
$database = new Database();
$conn = $database->getConnection();  // Ensure the connection is established

// Fetch playlists for the user
$user_id = $_SESSION['user_id'];  // Assuming the user is logged in
$query = "SELECT playlist_id, name FROM Playlists WHERE user_id = :user_id";
$stmt = $conn->prepare($query);

// Bind the user ID to the placeholder using bindValue()
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

// Execute the statement
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if playlists exist and display them
echo "<h1>Your Playlists</h1>";

if ($result) {
    foreach ($result as $row) {
        echo "<p>" . htmlspecialchars($row['name']) . "</p>";
    }
} else {
    echo "<p>No playlists found. Create a new one!</p>";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Playlists</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        #playlists {
            margin-top: 20px;
        }
        .playlist {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .no-playlists {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

<!-- Form to create a new playlist -->
<form id="createPlaylistForm">
    <input type="text" name="playlist_name" placeholder="Enter Playlist Name" required>
    <button type="submit">Create Playlist</button>
</form>

<!-- List of Playlists -->
<div id="playlists">
    <h2>Your Playlists</h2>
    <div id="playlistContainer"></div>
</div>

<!-- JavaScript to handle adding/removing songs and displaying playlists -->
<script>
// Handle Playlist Creation
document.getElementById('createPlaylistForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const playlistName = event.target.playlist_name.value;

    fetch('playlist_management.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ create_playlist: true, playlist_name: playlistName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Playlist Created!');
            loadPlaylists(); // Reload playlists
        }
    });
});

// Load Playlists
function loadPlaylists() {
    fetch('playlist_management.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ get_playlists: true })
    })
    .then(response => response.json())
    .then(data => {
        const playlistContainer = document.getElementById('playlistContainer');
        playlistContainer.innerHTML = ''; // Clear previous playlists

        if (data.length === 0) {
            // Show a message if there are no playlists
            playlistContainer.innerHTML = '<p class="no-playlists">No playlists available. Create a new playlist!</p>';
        } else {
            // Display each playlist
            data.forEach(playlist => {
                const playlistDiv = document.createElement('div');
                playlistDiv.classList.add('playlist');
                playlistDiv.innerHTML = `
                    <h3>${playlist.name}</h3>
                    <p>Song Count: ${playlist.song_count}</p>
                    <button onclick="addSongToPlaylist(${playlist.id}, 123)">Add Song</button> <!-- Example Song ID 123 -->
                    <button onclick="removeSongFromPlaylist(${playlist.id}, 123)">Remove Song</button> <!-- Example Song ID 123 -->
                `;
                playlistContainer.appendChild(playlistDiv);
            });
        }
    });
}

// Add Song to Playlist
function addSongToPlaylist(playlistId, songId) {
    fetch('playlist_management.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ add_song: true, playlist_id: playlistId, song_id: songId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Song Added!');
            loadPlaylists(); // Reload playlists
        }
    });
}

// Remove Song from Playlist
function removeSongFromPlaylist(playlistId, songId) 
    fetch('playlist_management.php'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ remove_song: true, playlist_id: playlistId, song_id: songId })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Song Removed!');
            loadPlaylists(); // Reload playlists
        }
    });
}

// Load playlists when the page is loaded
window.onload = loadPlaylists;
</script>

</body>
</html>