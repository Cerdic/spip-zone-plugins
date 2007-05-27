<?php

define('FPDF_FONTPATH','font/');
include_spip('pdf/pdf_table');

$critere=$_GET['critere'];
$query=spip_query("SELECT nom FROM spip_asso_profil WHERE id_profil=1");
$association=spip_fetch_array($query);

class PDF extends PDF_Table {

	function PDF(){
		$this->FPDF('L', 'mm', 'A4');
	}
	
	function Header(){
		//Titre
		$this->SetFont('Arial','',10);
		$this->Cell(0,6,'Association '.$association,0,1,'L');
		$this->SetFont('Arial','B',14);
		$this->Cell(0,6,_T('asso:adherent_titre_liste_actifs').': '.$statut,0,1,'C');
		$this->Ln(10);
		//Imprime l'en-tête du tableau si nécessaire
		parent::Header();
	}
}

$pdf=new PDF();	

$pdf->Open();
$pdf->AddPage();
//On définit les colonnes (champs,largeur,intitulé,alignement)
$pdf->AddCol('id_adherent',10,_T('asso:adherent_libelle_id'),'R');
$pdf->AddCol('nom',50,_T('asso:adherent_libelle_nom'),'L');
$pdf->AddCol('prenom',40,_T('asso:adherent_libelle_prenom'),'L');
$pdf->AddCol('ville',50,_T('asso:adherent_libelle_ville'),'L');
$pdf->AddCol('id_asso',20,_T('asso:adherent_libelle_reference_interne_abrev'),'R');
$pdf->AddCol('categorie',40,_T('asso:adherent_libelle_categorie'),'L');
$pdf->AddCol('validite',20,_T('asso:adherent_libelle_validite'),'L');
$pdf->AddCol('statut',15,'Statut','L');
$prop=array(
		'HeaderColor'=>array(255,150,100),
          'color1'=>array(224,235,255),
          'color2'=>array(255,255,255),
          'padding'=>2);
$pdf->Table("SELECT * FROM spip_asso_adherents WHERE $critere ORDER BY nom,prenom",$prop);
$pdf->Output();
?>
