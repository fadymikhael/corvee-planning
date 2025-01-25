<?php
class PlanningController
{
    private $planningModel;
    private $userModel;

    public function __construct($database)
    {
        $this->planningModel = new Planning($database);
        $this->userModel = new User($database);
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $annee = isset($_GET['annee']) ? (int)$_GET['annee'] : date('Y');
        $planning = $this->planningModel->getPlanningByYear($annee);
        $stats = $this->planningModel->getStats($annee);

        // Conversion du curseur en tableau pour éviter l'erreur de rewind
        $users = iterator_to_array($this->userModel->getAll(['role' => 'user']), false);

        require 'views/planning.php';
    }
    public function update()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $annee = (int)$_POST['annee'];
            $semaine = (int)$_POST['semaine'];
            $responsable = trim($_POST['responsable']);

            $this->planningModel->updatePlanning($annee, $semaine, $responsable);
            header('Location: index.php?action=planning&annee=' . $annee);
            exit;
        }
    }
    public function deleteUser()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $username = trim($_POST['username']);

            if (empty($username)) {
                $_SESSION['error'] = "Nom d'utilisateur manquant.";
                header('Location: index.php?action=planning');
                exit;
            }

            try {
                // Vérifier si l'utilisateur existe
                $existingUser = $this->userModel->findByUsername($username);
                if (!$existingUser) {
                    $_SESSION['error'] = "Utilisateur introuvable.";
                } else {
                    // Supprimer l'utilisateur
                    $result = $this->userModel->delete($username);

                    if ($result) {
                        // Réattribuer les semaines de l'utilisateur supprimé
                        $this->planningModel->resetResponsable($username);

                        // Mettre à jour les statistiques
                        $_SESSION['message_delete'] = "L'utilisateur $username a été supprimé avec succès.";
                    } else {
                        $_SESSION['error'] = "Erreur : impossible de supprimer l'utilisateur $username.";
                    }
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: index.php?action=planning');
            exit;
        } else {
            $_SESSION['error'] = "Accès refusé.";
            header('Location: index.php?action=planning');
            exit;
        }
    }

    public function addPerson()
    {
        session_start();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $role = 'user'; // Rôle par défaut
    
            // Validation
            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Le nom d'utilisateur et le mot de passe sont requis.";
                header('Location: index.php?action=planning');
                exit;
            }
    
            try {
                // Tenter d'ajouter l'utilisateur
                $this->userModel->create([
                    'username' => $username,
                    'password' => $password, 
                    'role' => $role,
                    'email' => $username . '@example.com', // Génération d'un e-mail par défaut
                ]);
                $_SESSION['message_add'] = "Utilisateur ajouté avec succès.";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
    
            header('Location: index.php?action=planning');
            exit;
        } else {
            $_SESSION['error'] = "Accès refusé.";
            header('Location: index.php?action=planning');
            exit;
        }
    }
    
}
