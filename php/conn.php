<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'nac';

$conn = new mysqli(hostname: $host, username: $user, password: $pass, database: $db);

$conn->set_charset(charset: "utf8mb4");

if ($conn->error) {
    die("Error : falha na conexão" . $conn->connect_error);
}
