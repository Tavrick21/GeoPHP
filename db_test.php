<?php
// Paramètres de connexion à PostgreSQL
$host = '192.168.56.1'; // Adresse IP de Windows où PostgreSQL est installé
$port = '5433';         // Port de PostgreSQL configuré
$dbname = 'carto_db';   // Nom de la base de données
$user = 'postgres';     // Nom d'utilisateur PostgreSQL
$password = 'admin';         // Mot de passe PostgreSQL (remplis si tu as un mot de passe)

// DSN pour PDO
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";

try {
    // Connexion à PostgreSQL
    $pdo = new PDO($dsn);
    echo "Connexion réussie à la base de données PostgreSQL !<br>";

    // Requête de test : afficher les tables de la base
    $query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $query->fetchAll(PDO::FETCH_ASSOC);

    echo "Tables dans la base '$dbname' :<br>";
    foreach ($tables as $table) {
        echo "- " . $table['table_name'] . "<br>";
    }

} catch (PDOException $e) {
    // En cas d'erreur
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
