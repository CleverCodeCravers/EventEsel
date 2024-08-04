<?php
require_once 'config.php';

function getDatabaseConnection() {
    global $DBSERVER, $DBUSER, $DBPASSWORD, $DBNAME;
    try {
        $conn = new mysqli($DBSERVER, $DBUSER, $DBPASSWORD, $DBNAME);
        if ($conn->connect_error) {
            throw new Exception("Verbindung fehlgeschlagen: " . $conn->connect_error);
        }
        return $conn;
    } catch (mysqli_sql_exception $e) {
        return null; 
    }
}
?>