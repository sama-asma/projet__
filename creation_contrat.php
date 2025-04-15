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
    <title>nouveau contrat</title>
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
        <!-- Contenu -->
         <div class="container-type">
            <button class="vehicule" onclick="window.location.href='formulaire_vehicule.php'">
                 <i class="fas fa-car"></i> Assurance véhicule
            </button>
            <button class="habitation">
                 <i class="fas fa-home"></i> Assurance habitation
            </button>
            <div class="btn-container">
                <button class="individu">
                    <i class="fas fa-user-shield"></i>  Assurance individu
                </button>
                <div id="individu-options">
                    <button class="vie">
                        <i class="fas fa-heartbeat"></i> Vie
                    </button>
                    <button class="sante">
                        <i class="fas fa-stethoscope"></i> Santé
                    </button>
                    <button class="scolarite">
                        <i class="fas fa-graduation-cap"></i> Scolarité
                    </button>
                </div>
            </div>
            <div class="btn-container">
                <button class="finance">
                    <i class="fas fa-chart-line"></i> Assurance finance
                </button>
                <div id="finance-options">
                    <button class="emprunt">
                        <i class="fas fa-hand-holding-usd"></i> Emprunt
                    </button>
                    <button class="protection-juridique">
                        <i class="fas fa-gavel"></i> Protection Juridique
                    </button>
                    <button class="cyberattaque">
                        <i class="fas fa-shield-alt"></i> Cyberattaque
                    </button>
                </div>
           </div>
         </div>
    <script src="js/script.js"></script> 
</body>
