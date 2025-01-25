<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

try {
    // Charger les variables d'environnement depuis .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    // Récupérer l'URI depuis les variables d'environnement
    $uri = $_ENV['MONGODB_URI'];

    $client = new Client($uri);

    $database = $client->planning_corvees;

    $database->command(['ping' => 1]);

    return $database;
} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Erreur de connexion à MongoDB Atlas : " . $e->getMessage());
}
