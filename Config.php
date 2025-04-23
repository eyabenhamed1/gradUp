<?php
/**
 * Classe de configuration pour la connexion à la base de données
 */
class config
{
    private static $pdo = null;

    /**
     * Établit une connexion PDO à la base de données
     * @return PDO L'objet PDO pour interagir avec la base
     * @throws PDOException Si la connexion échoue
     */
    public static function getConnexion()
    {
        if (self::$pdo === null) {
            try {
                // Paramètres de connexion
                $host = '127.0.0.1';  // ou 'localhost'
                $dbname = 'projetweb2a';
                $username = 'root';
                $password = '';
                
                // Options de configuration PDO
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => true
                ];

                // Chaîne de connexion
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

                // Tentative de connexion
                self::$pdo = new PDO($dsn, $username, $password, $options);
                
                // Vérification supplémentaire que la table commande existe
                self::verifyCommandeTable();
                
                return self::$pdo;

            } catch (PDOException $e) {
                // Journalisation détaillée de l'erreur
                error_log("Erreur de connexion DB: " . $e->getMessage());
                throw new Exception("Impossible de se connecter à la base de données. Veuillez vérifier la configuration.");
            }
        }
        
        return self::$pdo;
    }

    /**
     * Vérifie l'existence de la table commande
     * @throws Exception Si la table n'existe pas
     */
    private static function verifyCommandeTable()
    {
        $stmt = self::$pdo->query("SHOW TABLES LIKE 'commande'");
        if ($stmt->rowCount() === 0) {
            throw new Exception("La table 'commande' n'existe pas dans la base de données.");
        }
        
        // Vérification supplémentaire de la structure de la table
        $requiredColumns = ['id_commande', 'nom', 'prenom', 'tlf', 'adresse', 'produits', 'prix_total', 'etat'];
        $stmt = self::$pdo->query("DESCRIBE commande");
        $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $missingColumns = array_diff($requiredColumns, $existingColumns);
        if (!empty($missingColumns)) {
            throw new Exception("Colonnes manquantes dans la table 'commande': " . implode(', ', $missingColumns));
        }
    }
}

// Test de connexion automatique (peut être commenté en production)
try {
    $pdo = config::getConnexion();
    echo "";
} catch (Exception $e) {
    die("<strong>ERREUR CRITIQUE:</strong> " . $e->getMessage());
}
?>