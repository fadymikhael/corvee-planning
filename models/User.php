<?php
class User
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function findByUsername($username)
    {
        return $this->db->users->findOne(['username' => $username]);
    }

    public function create($userData)
    {
        if (empty($userData['username']) || empty($userData['password'])) {
            throw new Exception("Le nom d'utilisateur et le mot de passe sont requis");
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->findByUsername($userData['username']);
        if ($existingUser) {
            throw new Exception(message: "Ce nom d'utilisateur existe déjà");
        }

        // Préparation des données utilisateur
        $newUser = [
            'username' => $userData['username'],
            'password' => password_hash($userData['password'], PASSWORD_DEFAULT),
            'role' => $userData['role'] ?? 'user',
            'email' => $userData['email'] ?? $userData['username'] . '@example.com',
        ];

        return $this->db->users->insertOne($newUser);
    }
    public function delete($username)
    {
        try {
            $result = $this->db->users->deleteOne(['username' => $username]);
            return $result->getDeletedCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }


    public function getAll()
    {
        try {
            // Convertir le curseur en tableau pour éviter les problèmes d'itération
            return $this->db->users->find()->toArray();
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
            return [];
        }
    }
}
