<?php
class DB {
    private static $pdo = null;

    // Renommez la méthode en getConnexion() pour correspondre à votre code existant
    public static function getConnexion() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=evenement;charset=utf8mb4',
                    'root', 
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Initialisation de la connexion
DB::getConnexion();

// Configuration de l'environnement
define('APP_ENV', 'development'); // ou 'production'

// Configuration des erreurs
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


// Autoloader basique (optionnel mais recommandé)
spl_autoload_register(function($class) {
    $paths = [
        __DIR__.'/../controller/',
        __DIR__.'/../model/',
        __DIR__.'/../lib/'
    ];
    
    foreach ($paths as $path) {
        $file = $path.$class.'.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
?>