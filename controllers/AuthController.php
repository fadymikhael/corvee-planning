    <?php
    class AuthController
    {
        private $userModel;

        public function __construct($database)
        {
            $this->userModel = new User($database);
        }

        public function login()
        {
            $errorMessage = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = trim($_POST['username']);
                $password = trim($_POST['password']);

                // Recherche de l'utilisateur
                $user = $this->userModel->findByUsername($username);

                if ($user) {
                    // Vérification du mot de passe haché
                    if (password_verify($password, $user['password'])) {
                        session_start();
                        $_SESSION['user_id'] = (string)$user['_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'] ?? 'user';
                        $_SESSION['email'] = $user['email'] ?? 'email'; 
                        header('Location: index.php?action=planning');
                        exit;
                    } else {
                        $errorMessage = "Mot de passe incorrect.";
                    }
                } else {
                    $errorMessage = "Utilisateur introuvable.";
                }
            }

            require 'views/login.php';
        }

        public function logout()
        {
            session_start();
            session_destroy();
            header('Location: index.php');
            exit;
        }
    }
