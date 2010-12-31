<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_cheque_dist($args, $retours){
	
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);
	
	if ($options['champ_adresse_cheque']){
		$retours['message_ok'] .= "\n<br/>". $options['champ_adresse_cheque'] . $options['champ_banque'];
	} else {
		$retours['message_ok'] .= "\n<br/>". _T('transaction:traiter_cheque_message_defaut');
	}
	
	 //On ajoute les liens vers paiements CMCIC
    foreach($traitements as $type_traitement=>$options){
		if ($type_traitement == "paiement_cmcic"){
		 	$retours['message_ok'] .= "\n<br/><a href='".find_in_path("paiement/cmcic/paiement.php")."'>"._T('transaction:traiter_cheque_message_cmcic')."</a>";
		}
	}
	
	
	// Le formulaire a été validé, on le masque
	$retours['editable'] = false;
	
	return $retours;
}

?>
