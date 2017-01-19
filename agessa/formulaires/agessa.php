<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Formatte un champs dans le charset latin pour le pdf
 *
 * @param string $str
 * @return string 
**/
function agessa_pdf_encode($str) {
	return iconv('UTF-8', 'windows-1252', strtoupper($str));
}

/**
 * Ajoute des espaces à un champs pour atteindre une longueur donnée
 *
 * @param string $str
 * @return string
**/
function agessa_pdf_white_space($str, $length = 7, $ajout_gauche = true) {
	$str = agessa_pdf_encode($str);

	for ($i = strlen($str); $i < $length; $i++) {
		$str = ($ajout_gauche) ? " " . $str : $str . " ";
	}
	return $str;
}


//-------------------------
// Charger
//-------------------------
function formulaires_agessa_charger_dist() {
	$valeurs = array();
	$valeurs['montant_da'] = '';
	return $valeurs;
}


//-------------------------
// Verifier
//------------------------- 
function formulaires_agessa_verifier_dist() {
	$erreurs = array();
	if (intval(_request('montant_da') < 1))
		$erreur = _T('agessa:erreur_montant_da');
	return $erreurs;
}


//-------------------------
// Traiter
//-------------------------

function formulaires_agessa_traiter_dist() {
	// etape 1: calcul des montants
	$montant_da = intval(_request('montant_da'));
	$montant_agessa = 0;
	$taux = array('taux_maladie', 'taux_csg', 'taux_rds', 'taux_formation', 'taux_diffuseur', 'taux_diffuseur_formation');

	foreach ($taux as $tau) {
		$$tau = lire_config("agessa/$tau") * $montant_da;
		$montant_agessa += $$tau; // pour le total, on garde la precision 
		$$tau = round($$tau);
	}
	$montant_agessa = round($montant_agessa);

	$coords = array('insee', 'sexe', 'nom', 'prenom', 'numero', 'rue', 'cp', 'ville', 'pays', 'activite');
	foreach ($coords as $coord)
		$$coord = lire_config("agessa/$coord");

	// etape 2: creation PDF
	// doc: http://contrib.spip.net/Realiser-un-PDF-personnalise-avec-FPDF
	define('FPDF_FONTPATH', 'font/');
	include_spip('fpdf'); 
	include_spip('fpdi');
	include_spip('lib/fpdf_cellfit');

	// On cree le pdf
	$pdf = new FPDFI_CellFit('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetMargins(0,0);
	$pdf->setSourceFile(find_in_path('pdf/Bordereau_declaratif_2017_nb.pdf'));
	$tplIdx = $pdf->importPage(1);
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

	// on ajouter le texte 
	$pdf->SetFont('Helvetica', '', 10);
	$pdf->SetTextColor(80, 80, 170);

	// PAGE 1
	//-----------------
	$pdf->SetXY(68.8, 136.2);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($montant_da), 0, 0, 'R');

	$pdf->SetXY(157.5, 144.8);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($taux_maladie), 0, 0, 'R');
	$pdf->SetXY(157.5, 150);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($taux_csg), 0, 0, 'R');
	$pdf->SetXY(157.5, 156);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($taux_rds), 0, 0, 'R');
	$pdf->SetXY(157.5, 161);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($taux_formation), 0, 0, 'R');

	$pdf->SetXY(157.5, 196.7);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($taux_diffuseur), 0, 0, 'R');
	$pdf->SetXY(157.5, 202.5);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($taux_diffuseur_formation), 0, 0, 'R');

	$pdf->SetXY(153.6, 214.4);
	$pdf->CellFitSpaceForce(34, 0, agessa_pdf_white_space($montant_agessa, 8), 0, 0, 'R');

	// PAGE 2
	//----------------- 
	$pdf->AddPage();
	$tplIdx = $pdf->importPage(2);
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

	// montant
	$pdf->SetXY(161.4, 63.8);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($montant_da, 6), 0, 0, 'R');

	$pdf->SetXY(13.1, 118);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($taux_maladie, 6), 0, 0, 'R');
	$pdf->SetXY(44.7, 118);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($taux_csg, 6), 0, 0, 'R');
	$pdf->SetXY(75.7, 118);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($taux_rds, 6), 0, 0, 'R');
	$pdf->SetXY(107.2, 118);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($taux_formation, 6), 0, 0, 'R');
	$pdf->SetXY(139.0, 118);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($taux_diffuseur, 6), 0, 0, 'R');
	$pdf->SetXY(170.5, 118);
	$pdf->CellFitSpaceForce(25, 0, agessa_pdf_white_space($taux_diffuseur_formation, 6), 0, 0, 'R');

	// coord auteur
	if ($sexe == "M") {
		$pdf->SetXY(14, 55.7);
		$pdf->Cell(0, 0, "X");
	} else if ($sexe == "Mme") {
		$pdf->SetXY(27, 55.7);
		$pdf->Cell(0, 0, "X");
	}
	$pdf->SetXY(63.2, 51.4);
	$pdf->CellFitSpaceForce(66, 0, agessa_pdf_white_space($insee, 15, false),0);
	$pdf->SetXY(27, 60);
	$pdf->CellFitSpaceForce(102, 0, agessa_pdf_white_space($nom, 23, false),0);
	$pdf->SetXY(27, 65);
	$pdf->CellFitSpaceForce(102, 0, agessa_pdf_white_space($prenom, 23, false),0);
	$pdf->SetXY(27, 70);
	$pdf->CellFitSpaceForce(17, 0, agessa_pdf_white_space($numero, 4, false),0);
	$pdf->SetXY(27, 75);
	$pdf->CellFitSpaceForce(102, 0, agessa_pdf_white_space($rue, 23, false),0);
	$pdf->SetXY(27, 80.4);
	$pdf->CellFitSpaceForce(22, 0, agessa_pdf_white_space($cp, 5, false),0);
	$pdf->SetXY(58.2, 80.4);
	$pdf->CellFitSpaceForce(70, 0, agessa_pdf_white_space($ville, 16, false),0);
	$pdf->SetXY(27, 85.6);
	$pdf->CellFitSpaceForce(102, 0, agessa_pdf_white_space($pays, 23, false),0);
	$pdf->SetXY(62.6, 93.1);
	$pdf->CellFitSpaceForce(133, 0, agessa_pdf_white_space($activite, 30, false),0);

	// on sauve dans le repertoire dedie
	$pdf_nom = 'agessa_' .date('Ymd-Hi') . "-" . md5('ilovespip' . time()) . '.pdf';
	$pdf_path = _DIR_IMG .'pdf_agessa/' . $pdf_nom;
	$pdf->Output($pdf_path, 'F'); 

	return array(
		'editable' => true,
		'message_ok' => _T('agessa:pdf_cree', array('pdf_nom' => $nom_pdf, 'pdf_path' => $pdf_path)),
	);
}