<?php

$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "UsTechComputers";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = $_POST['category'];
$sqlFetchNames = "SELECT DISTINCT Name FROM Inventory WHERE Category = '$category'";
$result = $conn->query($sqlFetchNames);

$names = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $names[] = $row['Name'];
    }
}

echo json_encode($names);

$conn->close();
?>
