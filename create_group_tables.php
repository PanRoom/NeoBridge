<?php
require 'db_connect.php';

try {
    $pdo->exec("CREATE TABLE groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        creator_user_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (creator_user_id) REFERENCES users(id) ON DELETE CASCADE
    );");

    $pdo->exec("CREATE TABLE group_members (
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (group_id, user_id),
        FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );");

    echo "Tables 'groups' and 'group_members' created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
    exit(1);
}
?>
