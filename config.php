<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "2a41"; // Add your database name here

            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Testing the connection
try {
    $conn = config::getConnexion();
    $stmt = $conn->query("SELECT 1"); // A lightweight query to test the connection
    if ($stmt) {
        echo "Connection successful!";
    }
} catch (Exception $e) {
    echo "Test query failed: " . $e->getMessage();
}
?>
