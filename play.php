<?php
require 'db.php';

$songId = $_GET['song_id'];

// Fetch song details
$sql = "SELECT * FROM Songs WHERE song_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $songId);
$stmt->execute();
$result = $stmt->get_result();
$song = $result->fetch_assoc();

// Log user play action
$userId = $_SESSION['user_id'];
$sql = "INSERT INTO UserActions (user_id, song_id, action_type) VALUES (?, ?, 'play')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $songId);
$stmt->execute();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Playing <?= htmlspecialchars($song['title']) ?></title>
</head>
<body>
    <h1>Now Playing: <?= htmlspecialchars($song['title']) ?></h1>
    <audio controls autoplay>
        <source src="<?= htmlspecialchars($song['file_path']) ?>" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</body>
</html>
