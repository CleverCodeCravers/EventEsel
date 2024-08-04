<?php
require_once 'config.php';

function getDatabaseConnection() {
    global $servername, $username, $password, $dbname;
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("Verbindung fehlgeschlagen: " . $conn->connect_error);
        }
        return $conn;
    } catch (mysqli_sql_exception $e) {
        return null; 
    }
}
?>