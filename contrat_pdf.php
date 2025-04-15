<?php
require('fpdf186/fpdf.php');

// Classe de base pour tous les types de contrats

class ContratPDF extends FPDF {
    // Propriétés communes
    protected $companyName = "Assurance Daman";
    protected $companyLogo = "img/logo.png";
    protected $footerText = "Ce document est un contrat légal. Conservez-le précieusement.";
    
     // Fonction pour convertir UTF-8 en ISO-8859-1
    public function customUtf8Decode($str) {
        return iconv('UTF-8', 'windows-1252//IGNORE', $str);
    }

    //Constructeur
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4') {
        // Appel du constructeur parent
        parent::__construct($orientation, $unit, $format);
        
        // Configuration commune
        $this->SetMargins(10, 40, 10);
        $this->SetAutoPageBreak(true, 25);
    }
    
     // En-tête commun à tous les contrats
    public function Header() {
        $this->Image($this->companyLogo, 10, 5, 20);
        // Police
        $this->SetFont('Arial', 'B', 15);
        $this->SetXY(35, 10); // Positionner le titre à côté du logo
        // Titre encadré - sera surchargé par les classes enfants
        $this->SetFillColor(230, 230, 230);
        $this->Cell(160, 10, $this->customUtf8Decode($this->companyName . ' - ' . $this->getContractTitle()), 0, 1, 'L', true);
        // Saut de ligne
        $this->Ln(15);
    }
      // Surcharge de Cell pour gérer automatiquement l'UTF-8
      public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        parent::Cell($w, $h, $this->customUtf8Decode($txt), $border, $ln, $align, $fill, $link);
    }
    
    // Surcharge de MultiCell pour gérer automatiquement l'UTF-8
    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {
        parent::MultiCell($w, $h, $this->customUtf8Decode($txt), $border, $align, $fill);
    }
     //Retourne le titre du contrat - à surcharger dans les classes enfants
    protected function getContractTitle() {
        return 'CONTRAT';
    }
    
     // Pied de page commun à tous les contrats
    public function Footer() {
        // Position à 1,5 cm du bas
        $this->SetY(-15);
        // Police
        $this->SetFont('Arial', 'I', 8);
        // Texte
        $this->Cell(0, 10, $this->footerText, 0, 0, 'C');
        // Numéro de page
        $this->SetX(10);
        $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'R');
    }
    
    // Fonction pour créer un titre de section
    public function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(70, 130, 180);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 8, $title, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 11);
        $this->Ln(2);
    }
    
     // Fonction pour créer une ligne d'information
    public function InfoLine($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 6, $label, 0, 0);
        $this->SetFont('Arial', '', 10);
        $this->Cell(130, 6, $value, 0, 1);
    }
    
    // Bloc signature à la fin du contrat
    public function AddSignatureBlock() {
        $this->Cell(0, 6, 'Fait à __________________________, le ' . date('d/m/Y'), 0, 1);
        $this->Ln(10);

        $this->Cell(95, 6, 'Signature de l\'assureur', 0, 0, 'C');
        $this->Cell(95, 6, 'Signature du souscripteur', 0, 1, 'C');
        $this->Ln(20);

        $this->Cell(95, 6, '_______________________', 0, 0, 'C');
        $this->Cell(95, 6, '_______________________', 0, 1, 'C');
    }
    
     // Clause légale générique
    // public function AddLegalClause($text = null) {
    //     if ($text === null) {
    //         $text = 'Ce contrat est soumis aux dispositions du Code des Assurances. Le souscripteur reconnaît avoir reçu un exemplaire des conditions générales applicables à ce contrat et en avoir pris connaissance avant la signature du présent document.';
    //     }
        
    //     $this->Ln(15);
    //     $this->SetFont('Arial', 'I', 8);
    //     $this->MultiCell(0, 4, $text);
    // }
}
