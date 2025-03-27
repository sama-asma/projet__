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
            <button class="vehicule">
                 <i class="fas fa-car"></i> Assurance véhicule
            </button>
            <button class="habitation">
                 <i class="fas fa-home"></i> Assurance habitation
            </button>
             <button class="individu">
                 <i class="fas fa-user-shield"></i>  Assurance individu
             </button>
             <button class="finance">
                  <i class="fas fa-chart-line"></i> Assurance finance
             </button>
         </div>
    <script src="js/script.js"></script> 
</body>
