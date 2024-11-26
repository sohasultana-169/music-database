<?php
class Database {
    private $host = "localhost";      // Replace with your DB host
    private $db_name = "musicdb";     // Replace with your DB name
    private $username = "root";       // Replace with your DB username
    private $password = "Sonu@16900"; // Replace with your DB password
    private $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            // Create PDO connection
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Log error instead of displaying to the user (optional for production)
            error_log("Connection failed: " . $exception->getMessage()); // Logs the error to the PHP error log
            die("Database connection failed. Please try again later."); // Generic error message
        }

        return $this->conn;
    }
}

// Initialize and test the connection
$database = new Database();
$conn = $database->getConnection(); // Get the PDO connection


?>
