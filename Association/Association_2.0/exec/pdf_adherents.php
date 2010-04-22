<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

	define('FPDF_FONTPATH','font/');
	include_spip('pdf/pdf_table');
	include_spip('inc/charsets');

	if ( isset ($_REQUEST['filtre'] )) { $filtre = $_REQUEST['filtre']; }
	else { $filtre = 'defaut'; }

	switch($filtre) {
		case "defaut": 		$critere="statut_interne IN ('ok','echu','relance')";break;
		case "ok": 			$critere="statut_interne='ok'";break;
		case "echu": 		$critere="statut_interne='echu'";break;
		case "relance": 	$critere="statut_interne='relance'";break;
		case "sorti": 		$critere="statut_interne='sorti'";break;	   
		case "prospect": 	$critere="statut_interne='prospect'";break;
		case "tous": 		$critere="statut_interne LIKE '%'";break;	
	}

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
	$pdf->Table("SELECT * FROM spip_auteurs_elargis WHERE $critere ORDER BY nom_famille,".lire_config('association/indexation'),$prop);
	$pdf->Output();
?>
