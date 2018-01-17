<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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

/**
 * Retourne les décimals d'un chiffre 12.2356 --> 24
 *
 * @param float $str
 * @return integer
**/
function agessa_decimal($value) {
	$entier = floor($value) * 100;
	$decimal = round(($value*100) - $entier);

	if (!$decimal) {
		return "00";
	} else {
        return $decimal;
	}
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
	$taux = array('taux_precompte', 'taux_diffuseur');

	foreach ($taux as $tau) {
		$$tau = lire_config("agessa/$tau") * $montant_da;
		$montant_agessa += $$tau; // pour le total, on garde la precision
	}
	$montant_agessa = round($montant_agessa);

	$coords = array('insee', 'sexe', 'nom', 'prenom', 'numero', 'rue', 'cp', 'ville', 'pays', 'activite');
	foreach ($coords as $coord)
		$$coord = lire_config("agessa/$coord");

	// etape 2: creation PDF
	// doc: https://contrib.spip.net/Realiser-un-PDF-personnalise-avec-FPDF
	define('FPDF_FONTPATH', 'font/');
	include_spip('fpdf'); 
	include_spip('fpdi');
	include_spip('lib/fpdf_cellfit');

	// On cree le pdf
	$pdf = new FPDFI_CellFit('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetMargins(0,0);
	$pdf->setSourceFile(find_in_path('pdf/Bordereau_declaratif_2018.pdf'));
	$tplIdx = $pdf->importPage(1);
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

	// on ajouter le texte 
	$pdf->SetFont('Helvetica', '', 11);
	$pdf->SetTextColor(80, 80, 170);

	// PAGE 1
	//-----------------
	$pdf->SetXY(150.2, 99.3);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($montant_da), 0, 0, 'R');
	$pdf->SetXY(183.5, 99.3);
	$pdf->CellFitSpaceForce(9, 0, '00', 0, 0, 'R');
	$pdf->SetXY(150.2, 110);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space(floor($taux_diffuseur)), 0, 0, 'R');
	$pdf->SetXY(183.5, 110);
	$pdf->CellFitSpaceForce(9, 0, agessa_decimal($taux_diffuseur), 0, 0, 'R');

	$pdf->SetXY(150.2, 126.5);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($montant_da), 0, 0, 'R');
	$pdf->SetXY(183.5, 126.5);
	$pdf->CellFitSpaceForce(9, 0, '00', 0, 0, 'R');
	$pdf->SetXY(150.2, 138);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space(floor($taux_precompte)), 0, 0, 'R');
	$pdf->SetXY(183.5, 138);
	$pdf->CellFitSpaceForce(9, 0, agessa_decimal($taux_precompte), 0, 0, 'R');

	$pdf->SetXY(150.5, 167);
	$pdf->CellFitSpaceForce(42, 0, agessa_pdf_white_space($montant_agessa,9), 0, 0, 'R');

	// PAGE 2
	//----------------- 
	$pdf->AddPage();
	$tplIdx = $pdf->importPage(2);
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

	// montant
	$pdf->SetXY(15.5, 109);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space($montant_da), 0, 0, 'R');
	$pdf->SetXY(50, 109);
	$pdf->CellFitSpaceForce(9, 0, '00', 0, 0, 'R');

	$pdf->SetXY(151, 102);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space(floor($taux_diffuseur)), 0, 0, 'R');
	$pdf->SetXY(184, 102);
	$pdf->CellFitSpaceForce(9, 0, agessa_decimal($taux_diffuseur), 0, 0, 'R');
	$pdf->SetXY(151, 109);
	$pdf->CellFitSpaceForce(30, 0, agessa_pdf_white_space(floor($taux_precompte)), 0, 0, 'R');
	$pdf->SetXY(184, 109);
	$pdf->CellFitSpaceForce(9, 0, agessa_decimal($taux_precompte), 0, 0, 'R');


	// coord auteur
	if ($sexe == "M") {
		$pdf->SetXY(35, 65.7);
		$pdf->Cell(0, 0, "X");
	} else if ($sexe == "Mme") {
		$pdf->SetXY(12.5, 65.7);
		$pdf->Cell(0, 0, "X");
	}

	$insee1 = substr($insee, 0, -2);
    $insee2 = substr($insee, 13, 2);
	$pdf->SetXY(54.0, 71.2);
	$pdf->CellFitSpaceForce(60, 0, agessa_pdf_white_space($insee1, 0, false),0);
    $pdf->SetXY(118.0, 71.2);
	$pdf->CellFitSpaceForce(8, 0, agessa_pdf_white_space($insee2, 0, false),0);
	$pdf->SetXY(22, 77);
	$pdf->CellFitSpaceForce(70, 0, agessa_pdf_white_space($nom, 18, false),0);
	$pdf->SetXY(135, 77);
	$pdf->CellFitSpaceForce(52, 0, agessa_pdf_white_space($prenom, 13, false),0);
	$pdf->SetXY(19.5, 83.5);
	$pdf->CellFitSpaceForce(17, 0, agessa_pdf_white_space($numero, 5, false),0);
	$pdf->SetXY(63.8, 83.5);
	$pdf->CellFitSpaceForce(90, 0, agessa_pdf_white_space($rue, 28, false),0);
	$pdf->SetXY(20, 90.1);
	$pdf->CellFitSpaceForce(23, 0, agessa_pdf_white_space($cp, 5, false),0);
	$pdf->SetXY(63.8, 90.1);
	$pdf->CellFitSpaceForce(90, 0, agessa_pdf_white_space($ville, 28, false),0);
	$pdf->SetXY(40, 96);
	$pdf->CellFitSpaceForce(112, 0, agessa_pdf_white_space($pays, 33, false),0);
	$pdf->SetXY(48.6, 115);
	$pdf->CellFitSpaceForce(125, 0, agessa_pdf_white_space($activite, 31, false),0);

	// on sauve dans le repertoire dedie
	$pdf_nom = 'agessa_' .date('Ymd-Hi') . "-" . md5('ilovespip' . time()) . '.pdf';
	$pdf_path = _DIR_IMG .'pdf_agessa/' . $pdf_nom;
	$pdf->Output($pdf_path, 'F'); 

	return array(
		'editable' => true,
		'message_ok' => _T('agessa:pdf_cree', array('pdf_nom' => $nom_pdf, 'pdf_path' => $pdf_path)),
	);
}