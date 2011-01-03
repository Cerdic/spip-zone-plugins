<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_cmcic_dist($args, $retours){
	
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);
	
	// Empecher le traitement en AJAX car on sait que le formulaire va rediriger autre part
    refuser_traiter_formulaire_ajax();
    
    // On stocke le montant et la référence de la transaction
    session_start();
    
    // Le serveur doit-il être appelé en mode test ou prod ?
    if ($options['champ_test'] == "test"){ $test = "test/"; }
    if ($options['champ_test'] == "prod"){ $test = ""; }
        
    // On récupère la banque à utiliser et on établi une variable de session contenant l'adresse du serveur
	if ($options['champ_banque']){
		switch ($options['champ_banque']) {
	    case "banque1":
	        $_SESSION['banque'] = "https://ssl.paiement.cic-banques.fr/" . $test;
	        break;
	    case "banque2":
	        $_SESSION['banque'] = "https://paiement.creditmutuel.fr/" . $test;
	        break;
	    case "banque3":
	        $_SESSION['banque'] = "https://ssl.paiement.banque-obc.fr/" . $test;
	        break;
		}
	}
    
    if ($_REQUEST['montant_1']) $_SESSION['total'] = $_REQUEST['montant_1'];
    if ($_REQUEST['montant_selection_1']) $_SESSION['total'] = $_REQUEST['montant_selection_1'];
    
    // ID unique de la transaction
    $_SESSION['ref'] = uniqid();

	$nb_paiement = 0;
    //On compte le nombre de paiement utilisé par le formulaire
    foreach($traitements as $type_traitement=>$options){
		if (substr($type_traitement,0,9) == "paiement_") $nb_paiement++;
	}
	
	//On envoi vers la banque si CMCIC est le seul mode de paiement sinon on affiche les choix possibles
	if ($nb_paiement == 1) {
		$retours['redirect'] = find_in_path("paiement/cmcic/paiement.php");
	} else {
		// Le formulaire a été validé, on le masque
		$retours['editable'] = false;
		$retours['message_ok'] .=  "<span class='transaction_ok cmcic'><a href='".find_in_path("paiement/cmcic/paiement.php")."'>"._T('transaction:traiter_cheque_message_cmcic')."</a></span>";
	}
	
	return $retours;
}

?>
