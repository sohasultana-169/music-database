<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $artist_id = $_POST['artist_id'];
    $album_id = $_POST['album_id'];
    $duration = $_POST['duration'];
    $file_path = $_POST['file_path'];
    $cover_image = $_POST['cover_image'];

    $sql = "INSERT INTO Songs (title, duration, album_id, artist_id, file_path, cover_image)
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $title, $duration, $album_id, $artist_id, $file_path, $cover_image);

    if ($stmt->execute()) {
        echo "Song added successfully";
    } else {
        echo "Error adding song: " . $stmt->error;
    }
    $stmt->close();
}
header("Location: admin_dashboard.php");
exit();
?>
