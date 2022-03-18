<?php

$servername = "";
$username = "";
$password = "";

$connection = new MySQLi($servername, $username, $password, "");

if ($connection->connect_error) {
    die("Connection failed: " . $this->connection->connect_error);
}

$kUsername = $_POST['username'];
$kEmail = $_POST['email'];
$kPassword = $_POST['password'];

$kPassword_hashed = password_hash($kPassword, PASSWORD_DEFAULT);

if ($query = $connection->prepare("SELECT * FROM klienten WHERE username = ?")) {
    // Bind variables to the prepared statement as parameters
    $query->bind_param("s", $kUsername);

    // Attempt to execute the prepared statement
    if ($query->execute()) {
        // Store result
        $query->store_result();

        // Check if username exists, if yes then verify password
        if ($query->num_rows == 0) {
            $query = $connection->prepare("INSERT INTO klienten (username, password) VALUES (?, ?)");
            $query->bind_param("ss", $kUsername, $kPassword_hashed);
            $query->execute();
            
            die();
        } else {
            die("Dieser Benutzer existiert bereits!");
        }
    }
}