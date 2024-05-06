<?php

$db_server = "localhost";  
$db_user = "root";         
$db_pass = "password";             
$db_name = 'FLIGHTSDB';    


$conn = null;

try {
    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

    if ($conn->connect_errno) {
        throw new Exception("Failed to connect to MySQL: " . $conn->connect_error);
    }

    echo "Connected to MySQL successfully.";
} catch (Exception $e) {

    echo "Error: " . $e->getMessage();
}
