<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- navigation -->
     <?php include 'commun/nav.php'; ?>
    <!-- Contenu principal -->
    <div class="main-content">
        <!-- En-tête -->
        <?php include 'commun/header.php'; ?>

        <!-- Section des statistiques
        <div class="stats-section">
            <div class="stat-card">
                <h2>150</h2>
                <p>Contrats signés</p>
            </div>
            <div class="stat-card">
                <h2>€25,000</h2>
                <p>Chiffre d'affaires</p>
            </div>
            <div class="stat-card">
                <h2>95%</h2>
                <p>Taux de satisfaction</p>
            </div>
        </div> -->

        <!-- Section des contrats récents -->
        <div class="recent-contracts">
            <h2>Contrats Récents</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Jean Dupont</td>
                        <td>Véhicule</td>
                        <td>2023-10-01</td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Marie Curie</td>
                        <td>Habitation</td>
                        <td>2023-10-02</td>
                    </tr>
                    <tr>
                        <td>003</td>
                        <td>Pierre Durand</td>
                        <td>Individuelle</td>
                        <td>2023-10-03</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="js/script.js"></script>
</body>
</html>