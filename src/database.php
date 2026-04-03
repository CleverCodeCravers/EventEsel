<?php
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
} else {
    require_once __DIR__ . '/config.example.php';
}

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