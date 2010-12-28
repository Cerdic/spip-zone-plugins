<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_cmcic_dist($args, $retours){
	    
	// Empecher le traitement en AJAX car on sait que le formulaire va rediriger autre part
    refuser_traiter_formulaire_ajax();
    
    // On stocke le montant et la référence de la transaction
    session_start();
    
    if ($_REQUEST['montant_1']) $_SESSION['total'] = $_REQUEST['montant_1'];
    if ($_REQUEST['montant_selection_1']) $_SESSION['total'] = $_REQUEST['montant_selection_1'];
    
    $_SESSION['ref'] = uniqid();
     
	$retours['redirect'] = find_in_path("paiement/cmcic/paiement.php");
	
	return $retours;
}

?>
