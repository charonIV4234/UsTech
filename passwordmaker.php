<?php

$password = 'developer123';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
echo $hashedPassword;

?>

<!---
USE ustechcomputers;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES ('', '');
--->