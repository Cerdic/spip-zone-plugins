<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Format un champs en latin pour le pdf
 *
 * @param string $str
 * @return string 
**/
function agessa_pdf_encode($str) { 
  return iconv('UTF-8', 'windows-1252', $str);
} 




//-------------------------
// Charger
//-------------------------
function formulaires_agessa_charger_dist(){   
	$valeurs = array();
	$valeurs['montant_da'] = ''; 

	return $valeurs;
} 
 
//-------------------------
// Verifier
//------------------------- 
function formulaires_agessa_verifier_dist(){    
  $erreurs = array();
  
  if (intval(_request('montant_da') < 1)) 
        $erreur = _T("agessa:erreur_montant_da");
  

	return $erreurs;
} 


//-------------------------
// Traiter
//-------------------------

function formulaires_agessa_traiter_dist(){ 
  
  // etape 1: calcul des montants
  $montant_da = intval(_request('montant_da'));
  $montant_agessa = 0;
  
  $taux = array("taux_maladie", "taux_csg", "taux_rds", "taux_formation", "taux_diffuseur", "taux_diffuseur_formation");

  foreach ($taux as $tau) {
      $$tau = lire_config("agessa/$tau") * $montant_da;
      $montant_agessa += $$tau;   // pour le total, on garde la precision 
      $$tau = round($$tau);
  }
  
  $montant_agessa = round($montant_agessa);
  
  
  // etape 2: creation PDF
  // doc: http://contrib.spip.net/Realiser-un-PDF-personnalise-avec-FPDF
  define('FPDF_FONTPATH','font/'); 
  include_spip('fpdf'); 
  include_spip('fpdi');
   
  // On cree le pdf 
  $pdf = new FPDI('P','mm','A4');   
  $pdf->AddPage();    
  $pdf->SetMargins(0,0);
   
  // On importe le diplome vierge
  $pdf->setSourceFile(find_in_path("pdf/Bordereau_declaratif_2016_nb.pdf")); 
  $tplIdx = $pdf->importPage(1); 
  $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 
  	 
  // on ajouter le texte 
  $pdf->SetFont('Arial','',10); 
  $pdf->SetTextColor(80,80,170); 

     // PAGE 1
     //----------------- 
     // page 1 > demandeur
     $pdf->SetXY(45,62);      $pdf->Cell(0,0, date("d / m / Y"));                      // date
   
   /*  $pdf->SetXY(130,62);     $pdf->Cell(0,0, pdf_encode($test));    // rpps 

    */ 
     // PAGE 2
     //----------------- 
     $pdf->AddPage();
     $tplIdx = $pdf->importPage(2);      
     $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 
         
                  
  // on sauve dans le repertoire dedie
  $pdf_nom = "agessa_".date("Ymd-Hi")."-".md5("ilovespip".time()).".pdf"; 
  $pdf_path = _DIR_IMG."pdf_agessa/".$pdf_nom;  
  $pdf->Output($pdf_path, 'F'); 
   
  // confirmer ok   
  return array(
        'editable' => true,
        'message_ok' => _T("agessa:pdf_cree",array("pdf_nom"=>$nom_pdf, "pdf_path"=>$pdf_path)),
   );       
       
   
}