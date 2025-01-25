<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning d'épluchage des patates</title>
    <link rel="stylesheet" href="css/planning.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <h1>Planning d'épluchage des patates</h1>
            <div class="logout">
                <form method="GET" action="index.php">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit">Se déconnecter</button>
                </form>
            </div>
        </header>

        <div class="header-flex">
            <div class="year-select card">
                <form method="GET" action="index.php">
                    <input type="hidden" name="action" value="planning">
                    <select name="annee" onchange="this.form.submit()">
                        <?php for ($i = 2023; $i <= 2025; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == $annee ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>

            <div class="welcome-message card">
                <?php
                if (!isset($_SESSION)) {
                    session_start();
                }
                if (isset($_SESSION['username'])) {
                    echo '<p>Bonjour ' . htmlspecialchars($_SESSION['username']);
                    if (isset($_SESSION['role'])) {
                        echo ' (' . ($_SESSION['role'] === 'admin' ? 'Administrateur' : 'Utilisateur') . ')';
                    }
                    echo '</p>';
                } else {
                    echo '<p class="error">Erreur : Utilisateur non connecté.</p>';
                }
                ?>
            </div>
        </div>

        <div class="dashboard">
            <!-- Statistiques -->
            <div class="card stats">
                <h2>Statistiques</h2>
                <ul>
                    <?php
                    $stats = [];
                    foreach ($planning['semaines'] as $semaine) {
                        $responsable = strtolower($semaine['responsable'] ?? 'personne');
                        if ($responsable !== 'personne') {
                            $stats[$responsable] = ($stats[$responsable] ?? 0) + 1;
                        }
                    }
                    ?>
                    <?php foreach ($stats as $responsable => $total): ?>
                        <li><?= htmlspecialchars(ucfirst($responsable)) ?> : <?= $total ?> semaines</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Ajouter un utilisateur -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="card add-person">
                    <h2>Ajouter un utilisateur</h2>

                    <!-- Affichage du message de succès -->
                    <?php if (isset($_SESSION['message_add'])): ?>
                        <p class="success-message"><?= htmlspecialchars($_SESSION['message_add']) ?></p>
                        <?php unset($_SESSION['message_add']); ?>
                    <?php endif; ?>

                    <!-- Affichage du message d'erreur -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <p class="error-message"><?= htmlspecialchars($_SESSION['error']) ?></p>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" action="index.php?action=add_person">
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" id="username" name="username" placeholder="Entrez un nom d'utilisateur" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Entrez un mot de passe"
                                required
                                pattern="(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,}"
                                title="Le mot de passe doit contenir au moins 8 caractères, incluant des lettres et des chiffres.">
                        </div>
                        <button type="submit">Ajouter</button>
                    </form>
                </div>
            <?php endif; ?>


            <!-- Supprimer un utilisateur -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="card delete-user">
                    <h2>Supprimer un utilisateur</h2>
                    <?php if (isset($_SESSION['message_delete'])): ?>
                        <p class="success-message"><?= htmlspecialchars($_SESSION['message_delete']) ?></p>
                        <?php unset($_SESSION['message_delete']); ?>
                    <?php endif; ?>
                    <ul>
                        <?php foreach ($users as $user): ?>
                            <li>
                                <?= htmlspecialchars(ucfirst($user['username'])) ?>
                                <form method="POST" action="index.php?action=delete_user" style="display:inline;" onsubmit="return confirmDelete('<?= htmlspecialchars($user['username']) ?>');">
                                    <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                                    <button type="submit" class="delete-button">Supprimer</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            <?php endif; ?>
        </div>


        <div class="tables-container">
            <?php for ($i = 0; $i < count($planning['semaines']); $i += 4): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Personne assignée</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($j = $i; $j < $i + 4 && $j < count($planning['semaines']); $j++): ?>
                                <?php
                                $responsable = strtolower($planning['semaines'][$j]['responsable'] ?? 'personne');
                                $color = $responsable !== 'personne' ? '#' . substr(md5($responsable), 0, 6) : '#ffffff'; // Couleur dynamique ou blanc
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime("+$j week", strtotime("$annee-01-01"))) ?></td>
                                    <td style="background-color: <?= htmlspecialchars($color) ?>;">
                                        <form method="POST" action="index.php?action=update_planning">
                                            <input type="hidden" name="annee" value="<?= $annee ?>">
                                            <input type="hidden" name="semaine" value="<?= $j + 1 ?>">
                                            <select name="responsable" class="user-select" onchange="this.form.submit()">
                                                <option value="personne" <?= ($responsable === 'personne') ? 'selected' : '' ?>>Personne</option>
                                                <?php foreach ($users as $user): ?>
                                                    <option value="<?= htmlspecialchars($user['username']) ?>"
                                                        <?= ($responsable === strtolower($user['username'])) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars(ucfirst($user['username'])) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endfor; ?>
        </div>


    </div>
</body>

</html>
<script>
    function confirmDelete(username) {
        return confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${username}" ?`);
    }
</script>