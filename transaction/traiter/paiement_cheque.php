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
		$retours['message_ok'] .= "<span class='transaction_ok cheque'>" . $options['champ_adresse_cheque'] . "</span>";
	} else {
		$retours['message_ok'] .= "<span class='transaction_ok cheque defaut'>" . _T('transaction:traiter_cheque_message_defaut') . "</span>";
	}

	
	// Le formulaire a été validé, on le masque
	$retours['editable'] = false;
	
	return $retours;
}

?>
