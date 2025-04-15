<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom_client = ucwords(strtolower($_POST['nom_client'])) ?? null;
    $prenom_client = ucwords(strtolower($_POST['prenom_client'])) ?? null;
    $telephone = $_POST['telephone'] ?? null;
    $email = $_POST['email'] ?? null;
    $type_vehicule = $_POST['type_vehicule'] ?? null;
    $puissance_vehicule = $_POST['puissance_vehicule'] ?? null;
    $age_vehicule = $_POST['age_vehicule'] ?? null;
    $condition_stationnement = $_POST['condition_stationnement'] ?? null;
    $age_conducteur = $_POST['age_conducteur'] ?? null;
    $experience_conducteur = $_POST['experience_conducteur'] ?? null;
    $bonus_malus = $_POST['bonus_malus'] ?? null;
    $usage = $_POST['usage'] ?? null;
    $environnement = $_POST['environnement'] ?? null;
    $reduction = $_POST['reduction'] ?? 0;
    $surcharge = $_POST['surcharge'] ?? 0;
    $id_garantie = $_POST['id_garantie'] ?? null;
    $date_souscription = $_POST['date_souscription'] ?? null;
    $date_expiration = $_POST['date_expiration'] ?? null;
    $prime = floatval($_POST['prime_calculee'] ?? null);
    $franchise = floatval($_POST['franchise'] ?? null);

    // Validation des données avec une boucle
    $fields = [
        'nom_client' => "Le nom du client est requis.",
        'prenom_client' => "Le prénom du client est requis.",
        'telephone' => "Un numéro de téléphone valide est requis.",
        'email' => "Une adresse email valide est requise.",
        'type_vehicule' => "Le type de véhicule est requis.",
        'puissance_vehicule' => "La puissance du véhicule doit être un nombre.",
        'age_vehicule' => "L'âge du véhicule doit être un nombre.",
        'condition_stationnement' => "La condition de stationnement est requise.",
        'age_conducteur' => "L'âge du conducteur doit être un nombre.",
        'experience_conducteur' => "L'expérience du conducteur doit être un nombre.",
        'bonus_malus' => "Le bonus-malus doit être un nombre.",
        'usage' => "L'usage est requis.",
        'environnement' => "L'environnement est requis.",
        'id_garantie' => "L'identifiant de garantie doit être un nombre.",
        'date_souscription' => "Une date de souscription valide est requise.",
        'date_expiration' => "Une date d'expiration valide est requise.",
        'prime_calculee' => "La prime doit etre un nombre.",
        'franchise' => "La franchise doit etre un nombre."
    ];

    $errors = [];
    foreach ($fields as $field => $error_message) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') { // Vérifier si la variable correspondant au nom du champ est vide ou non définie
            $errors[] = $error_message;
        } elseif (in_array($field, ['telephone']) && !preg_match('/^(\+213|0)(5|6|7)\d{8}$/', $_POST[$field])) {
            $errors[] = $error_message;
        } elseif (in_array($field, ['email']) && !filter_var($_POST[$field], FILTER_VALIDATE_EMAIL)) {
            $errors[] = $error_message;
        } elseif (in_array($field, ['puissance_vehicule', 'age_vehicule', 'age_conducteur', 'experience_conducteur', 'bonus_malus', 'id_garantie', 'prime_calculee', 'franchise']) && !is_numeric($_POST[$field])) {
            $errors[] = $error_message;
        } elseif (in_array($field, ['date_souscription', 'date_expiration']) && !strtotime($_POST[$field])) {
            $errors[] = $error_message;
        }
    }

    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header('Location: formulaire_vehicule.php');
        exit();
    }
    require 'db.php';
    $stmt = $conn->prepare("SELECT prime_base, franchise FROM garanties WHERE id_garantie = ?");
    $stmt->bind_param("i", $id_garantie);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $primeBase = floatval($row['prime_base']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Garantie non trouvée']);
        exit;
    }
    $stmt->close();
    // Vérifier si la prime est cohérente
    $primeMin = $primeBase * 0.5;
    $primeMax = $primeBase * 3.5;
    if ($prime < $primeMin || $prime > $primeMax) {
        $_SESSION['error'] = "Incohérence détectée dans le calcul de la prime";
        header('Location: formulaire_vehicule.php');
        exit();
    }
    try{
    // Préparer la requête d'insertion
        $numero_contrat = uniqid("CTR-"); // Générer un numéro de contrat unique
        $stmt = $conn->prepare("INSERT INTO contrats (numero_contrat, nom_client, prenom_client, telephone, email, date_souscription, date_expiration, type_assurance, montant_prime) VALUES (?, ?, ?, ?, ?, ?, ?, 'automobile', ?)");
        $stmt->bind_param("sssssssd", $numero_contrat, $nom_client, $prenom_client, $telephone, $email, $date_souscription, $date_expiration, $prime);
        if (!$stmt->execute()) {
           throw new Exception("Erreur lors de la création du contrat");
        } 
        $contrat_id = $stmt->insert_id; // Récupérer l'ID du contrat inséré
        $stmt->close();
        // Préparer la requête d'insertion pour les détails du contrat
        $stmt = $conn->prepare("INSERT INTO assurance_automobile (id_contrat, id_garantie, age_conducteur, experience_conducteur, environnement, type_usage, condition_stationnement, puissance_vehicule, type_vehicule, age_vehicule, bonus_malus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssssssd", $contrat_id, $id_garantie, $age_conducteur, $experience_conducteur, $environnement, $usage, $condition_stationnement, $puissance_vehicule, $type_vehicule, $age_vehicule, $bonus_malus);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création du contrat");
        } 
        // header("Location: contrat_auto.php?contrat=$contrat_id");
        echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirection</title>
            <script>
                // 1. D'abord l'alerte
                alert('Contrat généré avec succès !');
                
                // 2. Ensuite ouvrir le PDF (avec délai minimal)
                setTimeout(function() {
                    window.open("contrat_auto.php?contrat= $contrat_id", "_blank");
                    
                    // 3. Redirection après 100ms (garantit l'ouverture du PDF)
                    setTimeout(function() {
                        window.location.href = "dashboard.php";
                    }, 100);
                }, 10);
            </script>
        </head>
        <body>
            <p>Génération du contrat en cours...</p>
        </body>
        </html>
        HTML;
        exit();
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur technique: " . $e->getMessage();
        header('Location: formulaire_vehicule.php');
        exit();
    }
} else {  
    // Si le formulaire n'a pas été soumis, rediriger vers le formulaire
    header('Location: formulaire_vehicule.php');
    exit();
}
?>
