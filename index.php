<?php

$servername = "";
$username = "";
$password = "";

$connection = new MySQLi($servername, $username, $password, "");

if ($connection->connect_error) {
    die("Connection failed: " . $this->connection->connect_error);
}

switch ($_POST["action"]) {
    case "mitarbeiter":
        die(handleMitarbeiter($_POST["mitarbeiterAction"], $_POST["args"]));
    case "kunde";
        die(handleKunde($_POST["kundeAction"], $_POST["args"]));
    default:
        echo "An error occured while processing your request.";
}

function handleMitarbeiter(string $mitarbeiterAction, string $args): string
{
    global $connection;
    $reString = "";
    $splitArgs = explode(";", $args);
    switch ($mitarbeiterAction) {
        case "login":
            $username = $splitArgs[0];
            $password = $splitArgs[1];

            if ($query = $connection->prepare("SELECT mID, username, password, vorname, nachname FROM mitarbeiter WHERE username = ?")) {
                // Bind variables to the prepared statement as parameters
                $query->bind_param("s", $username);

                // Attempt to execute the prepared statement
                if ($query->execute()) {
                    // Store result
                    $query->store_result();

                    // Check if username exists, if yes then verify password
                    if ($query->num_rows == 1) {
                        // Bind result variables
                        $query->bind_result($mID, $username, $hashed_password, $vorname, $nachname);
                        if ($query->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                $reString = "worker;" . $mID . ";" . $vorname . ";" . $nachname;
                            } else {
                                $reString = "wrongcreds";
                            }
                        }
                    } else {
                        $reString = "nouser";
                    }
                }
            }
            break;
        case
        "list":
            $preparedStatement = $connection->prepare("SELECT * FROM mitarbeiter");

            $reString = $preparedStatement->execute();

            break;
    }

    return $reString;
}

function handleKunde(string $kundeAction, string $args): string
{
    global $connection;
    $reString = "";
    $splitArgs = explode(";", $args);
    switch ($kundeAction) {
        case "login":
            $username = $splitArgs[0];
            $password = $splitArgs[1];

            if ($query = $connection->prepare("SELECT kID, username, password FROM klienten WHERE username = ?")) {
                // Bind variables to the prepared statement as parameters
                $query->bind_param("s", $username);

                // Attempt to execute the prepared statement
                if ($query->execute()) {
                    // Store result
                    $query->store_result();

                    // Check if username exists, if yes then verify password
                    if ($query->num_rows == 1) {
                        // Bind result variables
                        $query->bind_result($kID, $username, $hashed_password);
                        if ($query->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                $reString = "client;" . $kID;
                            } else {
                                $reString = "wrongcreds";
                            }
                        }
                    } else {
                        $reString = "nouser";
                    }
                }
            }

            break;
        case "remove":
            $id = $splitArgs[0];

            $query = $connection->prepare("DELETE FROM klienten WHERE kID = ?");
            $query->bind_param("i", $id);
            $query->execute();

            break;
        case "list":
            $preparedStatement = $connection->prepare("SELECT * FROM klienten");

            $reString = $preparedStatement->execute();

            break;
    }

    return $reString;
}
