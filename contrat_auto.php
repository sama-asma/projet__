<?php
require_once('contrat_pdf.php');

class ContratAutoAssurance extends ContratPDF {
    //Titre spécifique pour les contrats auto
    
    protected function getContractTitle() {
        return 'CONTRAT D\'ASSURANCE AUTOMOBILE';
    }
    
    // Génère la section des informations du véhicule

    public function addVehicleSection($contrat,$annee_vehicule) {
        $this->SectionTitle('VÉHICULE ASSURÉ');
        $this->InfoLine('Type de véhicule :', $contrat['type_vehicule'] );
        // $this->InfoLine('Immatriculation :', $vehicule['immatriculation']);
        $this->InfoLine('Année :', $annee_vehicule);
        $this->InfoLine('Puissance :', $contrat['puissance_vehicule'] . ' CV');
        $this->Ln(5);
    }
    
    // Afficher les garanties spécifiques auto
    
    public function addGarantiesAuto($formuleNom,$description,$franchise) {
        $this->SectionTitle('FORMULE ET GARANTIES INCLUSES');
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, 'Formule : ' . $formuleNom, 0, 1);
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, 'Franchise : ' . $franchise, 0, 1);
        $this->Ln(5);
         // Afficher les garanties sous forme de liste
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, 'Garanties incluses :', 0, 1);
        $this->SetFont('Arial', '', 10);
    
    // Découper et afficher les garanties
    $garanties = explode('.', $description);
    
    foreach ($garanties as $garantie) {
        $garantie = trim($garantie);
        if (!empty($garantie)) {
            $this->Cell(10); // Indentation
            $this->Cell(5, 5, '•', 0, 0); // Puces
            $this->MultiCell(0, 5, $garantie);
        }
    }  
        $this->Ln(15);
    }  
}
    // Vérification de l'ID du contrat
        if (!isset($_GET['contrat']) || !is_numeric($_GET['contrat'])) {
            die("Numéro de contrat invalide.");
        }
        $id_contrat = $_GET['contrat'];

        require 'db.php';
        
        // Connexion à la base de données et récupération des données
    try {
        // assurer que la connexion utilise UTF-8
            mysqli_set_charset($conn, "utf8");
        // Récupération des informations du contrat
        $stmt = $conn->prepare("
            SELECT c.*,v.*
            FROM contrats c
            JOIN assurance_automobile v ON c.id_contrat = v.id_contrat
            WHERE c.id_contrat = ?
        ");
        $stmt->bind_param("i", $id_contrat);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            die("Contrat introuvable.");
        }

        $contrat = $result->fetch_assoc();
        $annee_vehicule = date("Y") - $contrat['age_vehicule'];

        // Récupération des garanties du contrat
        $stmt_garanties = $conn->prepare("
            SELECT g.nom_garantie, g.description, g.prime_base, g.franchise
            FROM garanties g
            JOIN assurance_automobile v ON g.id_garantie = v.id_garantie
            WHERE v.id_contrat = ?
        ");
        $stmt_garanties->bind_param("i", $id_contrat);
        $stmt_garanties->execute();
        $result_garanties = $stmt_garanties->get_result();
        
        $garanties = $result_garanties->fetch_assoc();
        
        } catch (Exception $e) {
        die("Erreur lors de la récupération des données : " . $e->getMessage());
        }

        // Création du PDF
        $pdf = new ContratAutoAssurance();
        $pdf->AliasNbPages(); // Pour {nb} dans le pied de page
        
        $pdf->AddPage();
        
        // Informations générales
        $pdf->SectionTitle('INFORMATIONS GÉNÉRALES');
        $pdf->InfoLine('Numéro du contrat :', $contrat['numero_contrat']);
        $pdf->InfoLine('Date de souscription :', date("d/m/Y", strtotime($contrat['date_souscription'])));
        $pdf->InfoLine('Date d\'expiration :', date("d/m/Y", strtotime($contrat['date_expiration'])));
        $pdf->InfoLine('Prime annuelle :', number_format($contrat['montant_prime'], 2, ',', ' ') . ' DZD');
        
        // Informations du souscripteur
        $pdf->SectionTitle('INFORMATIONS DU SOUSCRIPTEUR');
        $pdf->InfoLine('Nom et prénom :', $contrat['nom_client'] . ' ' . $contrat['prenom_client']);
        $pdf->InfoLine('Téléphone :', $contrat['telephone']);
        $pdf->InfoLine('Email :', $contrat['email']);
        $pdf->Ln(5);
        
        // Véhicule (méthode spécifique)
        $pdf->addVehicleSection($contrat,$annee_vehicule);
        
        // Garanties (méthode spécifique)
        $pdf->addGarantiesAuto($garanties['nom_garantie'],$garanties['description'],$garanties['franchise']);
        
        // Signatures et clause légale (méthodes communes)
        $pdf->AddSignatureBlock();
        $pdf->SetTitle($pdf->customUtf8Decode('Contrat d\'assurance véhicule'));
        $pdf->Output('Contrat_' . $contrat['numero_contrat'] . '.pdf', 'I');
       
       ?>
