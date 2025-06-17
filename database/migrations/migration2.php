<?php
require_once '../config.php';

$sql = "CREATE TABLE IF NOT EXISTS favorite_movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    imdb_id VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    poster VARCHAR(255),
    year VARCHAR(10),
    genre VARCHAR(255),
    label VARCHAR(255) NULL,
    UNIQUE KEY unique_fav (user_id, imdb_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;";

if ($conn->query($sql) === TRUE) {
    echo "Table 'favorite_movies' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
