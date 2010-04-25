<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pdf_adherents()
{
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	define('FPDF_FONTPATH','font/');
	include_spip('pdf/pdf_table');
	include_spip('inc/charsets');

	class PDF extends PDF_Table {
		
		function PDF(){
			$this->FPDF('L', 'mm', 'A4');
		}
		
		function Header(){
			//Titre
			$this->SetFont('Arial','',10);
			$this->Cell(0,6,lire_config('association/nom'),0,1,'L');
			$this->SetFont('Arial','B',14);
			$this->Cell(0,6,_T('asso:adherent_titre_liste_actifs').' ('.$filtre.')',0,1,'C');
			$this->Ln(10);
			//Imprime l'en-tête du tableau si nécessaire
			parent::Header();
		}
	}

	$pdf=new PDF();	

	$pdf->Open();
	$pdf->AddPage();
	//On définit les colonnes (champs,largeur,intitulé,alignement)
	$pdf->AddCol(lire_config('association/indexation'),15,_T('asso:adherent_libelle_'.lire_config('association/indexation')),'R');
	$pdf->AddCol('nom_famille',50,_T('asso:adherent_libelle_nom'),'L');
	$pdf->AddCol('prenom',40,_T('asso:adherent_libelle_prenom'),'L');
	$pdf->AddCol('ville',50,_T('asso:adherent_libelle_ville'),'L');
	$pdf->AddCol(unicode_to_utf_8('categorie'),30,_T('asso:adherent_libelle_categorie'),'C');
	$pdf->AddCol('validite',20,_T('asso:adherent_libelle_validite'),'L');
	$pdf->AddCol('statut_interne',15,_T('asso:adherent_entete_statut'),'C');
	$prop=array(
		'HeaderColor'=>array(255,150,100),
		'color1'=>array(224,235,255),
		'color2'=>array(255,255,255),
		'padding'=>2
	);
	$order = lire_config('association/indexation');
	$order = 'nom_famille' . ($order ? (",$order") : '');
	$pdf->Query(association_auteurs_elargis_select('*', '', request_statut_interne(), '', $order), $prop);
	$pdf->Output();
}
?>
