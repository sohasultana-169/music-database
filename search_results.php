<?php
session_start();




// Include necessary files
include_once 'db.php';  // Make sure this points to the correct file

// Turn off error reporting for debugging messages
ini_set('display_errors', 0); // Turn off display of errors
error_reporting(E_ERROR | E_PARSE); // Only show critical errors

// Create a database connection using the Database class from db.php
$database = new Database();
$db = $database->getConnection();  // This is the PDO connection

// Check if connection was successful
if ($db === null) {
    die("Connection failed: Database connection could not be established.");
}

// Check if there's a search query
if (isset($_GET['search_query'])) {
    $searchQuery = "%" . $_GET['search_query'] . "%"; // Use wildcards for partial matching

    // Prepare and execute the SQL query
    $stmt = $db->prepare("SELECT songs.*, artists.name AS artist_name FROM songs 
                          JOIN artists ON songs.artist_id = artists.artist_id 
                          WHERE songs.title LIKE :searchQuery");
    $stmt->bindParam(':searchQuery', $searchQuery);
    $stmt->execute();

    // Fetch matching songs
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $songs = [];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Songs</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f; /* Dark background */
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }


        /* Container for the search results */
        .container {
            width: 100%;
            max-width: 900px;
            padding: 20px;
            background-color: #121212; 
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Heading */
        h3 {
            color: #00aaff; /* Bright blue color */
            font-size: 2rem;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Search Result Items */
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #121212;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        li:hover {
            background-color: #333;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.7); /* Blue glow effect */
        }

        li strong {
            color: #00aaff; /* Blue title */
            font-size: 1.2rem;
        }

        /* Audio Player */
        audio {
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
            outline: none;
            background-color: #333;
        }

        /* For Mobile Screens (max-width: 768px) */
        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }

            h3 {
                font-size: 1.8rem;
            }

            li {
                padding: 12px;
            }

            li strong {
                font-size: 1rem;
            }

            .search-container input {
                width: 60%;
            }
        }

        /* For Mobile Screens (max-width: 480px) */
        @media screen and (max-width: 480px) {
            h3 {
                font-size: 1.5rem;
            }

            li {
                padding: 10px;
            }

            li strong {
                font-size: 0.9rem;
            }

            audio {
                width: 100%;
                margin-top: 5px;
            }

            .search-container input {
                width: 55%;
            }
        }
    </style>
</head>
<body>

    
    <!-- Search Results -->
    <div class="container">
        <?php if (count($songs) > 0): ?>
            <h3>Search Results:</h3>
            <ul>
                <?php foreach ($songs as $song): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($song['title']); ?></strong>
                        <br>Artist: <?php echo htmlspecialchars($song['artist_name']); ?>
                        <br>
                        <audio controls>
                            <source src="songs/<?php echo htmlspecialchars($song['file_path']); ?>" type="audio/mp3">
                            Your browser does not support the audio element.
                        </audio>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No songs found for your search query.</p>
        <?php endif; ?>
    </div>

    

</body>
</html>
