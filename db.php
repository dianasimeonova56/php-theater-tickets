<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "theater";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if($conn->connect_error) {
        die("Error while connecting to server");
    }
?>