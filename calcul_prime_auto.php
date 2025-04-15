<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit;
}

// Récupération des données
$data = [
    'type_vehicule' => $_POST['type_vehicule'] ?? null,
    'puissance' => floatval($_POST['puissance_vehicule'] ?? 0),
    'age_vehicule' => intval($_POST['age_vehicule'] ?? 0),
    'age_conducteur' => intval($_POST['age_conducteur'] ?? 0),
    'experience' => intval($_POST['experience_conducteur'] ?? 0),
    'bonus_malus' => floatval($_POST['bonus_malus'] ?? 1.0),
    'usage' => $_POST['usage'] ?? 'personnel',
    'environnement' => $_POST['environnement'] ?? 'mixte',
    'stationnement' => $_POST['condition_stationnement'] ?? 'rue',
    'reduction' => floatval($_POST['reduction'] ?? 0),
    'surcharge' => floatval($_POST['surcharge'] ?? 0),
    'id_garantie' => intval($_POST['id_garantie'] ?? 0),
];

// Validation côté serveur
$errors = [];
if ($data['age_conducteur'] < 18) $errors[] = "Age conducteur invalide";
if ($data['bonus_malus'] < 0.5 || $data['bonus_malus'] > 3.5) $errors[] = "Bonus-malus invalide";

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Calcul de la prime 
$stmt = $conn->prepare("SELECT prime_base, franchise FROM garanties WHERE id_garantie = ?");
$stmt->bind_param("i", $data['id_garantie']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $primeBase = floatval($row['prime_base']);
    $franchise = floatval($row['franchise']);
} else {
    echo json_encode(['success' => false, 'message' => 'Garantie non trouvée']);
    exit;
}
$stmt->close();
// Facteurs de calcul
$facteurs_vehicule = [
    'citadine' => 0.9, 'berline' => 1.0, 'SUV' => 1.1, 'utilitaire' => 1.2, 'sport' => 1.4
];
$coef_puissance = ($data['puissance'] <  80) ? 0.8 : (($data['puissance'] > 150) ? 1.3 : 1.0);
$coef_age_vehicule = ($data['age_vehicule'] < 3) ? 0.9 : (($data['age_vehicule'] > 10) ? 1.2 : 1.0);
$ceof_stationnement = ($data['stationnement'] == 'garage') ? 0.8 : (($data['stationnement'] == 'parking privé') ? 1.1 : 1.3);
$coef_age_conducteur = ($data['age_conducteur'] < 26) ? 1.4 : (($data['age_conducteur'] > 65) ? 1.3 : 1.0);
$coef_experience = ($data['experience'] < 2) ? 1.5 : (($data['experience'] > 10) ? 0.8 : 1.0);
$coef_usage = ($data['usage'] == 'professionnel') ? 1.4 : (($data['usage'] == 'mixte') ? 1.2 : 1.0);
$coef_environnement = ($data['environnement'] == 'urbain') ? 1.3 : (($data['environnement'] == 'rural') ? 0.9 : 1.0);
$primeNet = $primeBase * $facteurs_vehicule[$data['type_vehicule']] * $coef_puissance * $coef_age_vehicule * $ceof_stationnement * $coef_age_conducteur * $coef_experience * $coef_usage * $coef_environnement * $data['bonus_malus'];


// Application des réductions/surcharges
$prime = $primeNet * (1 - $data['reduction']/100) * (1 + $data['surcharge']/100);
echo json_encode([
    'success' => true,
    'primeNet' => round($primeNet, 2),
    'prime' => round($prime, 2),
    'franchise' => round($franchise, 2),
    'details' => "Calcul basé sur les critères fournis"
]);
?>