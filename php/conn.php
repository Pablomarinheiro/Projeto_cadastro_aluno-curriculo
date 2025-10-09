<?php

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'nac';

$conn = new mysqli($host, $user, $password, $database);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die ("ERROR : Falha na conexão" . $conn->connect_error);
}