<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "musicdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get songs from the database
$sql = "SELECT title, file_path FROM Songs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player</title>
</head>
<body>

<h2>Music Player</h2>

<?php
if ($result->num_rows > 0) {
    // Output data of each song
    echo "<ul id='songList'>";
    while ($row = $result->fetch_assoc()) {
        echo "<li data-file='songs/" . htmlspecialchars($row['file_path']) . "'>" . htmlspecialchars($row['title']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "No songs found in the database.";
}
$conn->close();
?>

<audio id="audioPlayer" controls>
    <source id="audioSource" src="" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<button onclick="playPrevious()">Previous</button>
<button onclick="playNext()">Next</button>

<script>
   const songs = document.querySelectorAll('#songList li');
let currentSongId = null;
const audioPlayer = document.getElementById('audioPlayer');
const audioSource = document.getElementById('audioSource');

function loadSongById(id) {
    const song = Array.from(songs).find(song => song.getAttribute('data-id') == id);
    if (song) {
        currentSongId = id;
        audioSource.src = song.getAttribute('data-file');
        audioPlayer.load();
        audioPlayer.play();
    }
}

function playNext() {
    const nextSong = Array.from(songs).find(song => song.getAttribute('data-id') == parseInt(currentSongId) + 1);
    if (nextSong) {
        loadSongById(nextSong.getAttribute('data-id'));
    }
}

function playPrevious() {
    const previousSong = Array.from(songs).find(song => song.getAttribute('data-id') == parseInt(currentSongId) - 1);
    if (previousSong) {
        loadSongById(previousSong.getAttribute('data-id'));
    }
}

// Auto-play the first song on page load
window.onload = function() {
    if (songs.length > 0) {
        loadSongById(songs[0].getAttribute('data-id'));
    }
};

// Enable click-to-play functionality
songs.forEach(song => {
    song.addEventListener('click', () => {
        loadSongById(song.getAttribute('data-id'));
    });
});

</script>

</body>
</html>
