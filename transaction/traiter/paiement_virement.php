<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_virement_dist($args, $retours){
	
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);
	
	

	if ($options['champ_adresse_virement']){
		$retours['message_ok'] .= "<div class='transaction_ok virement'>" . $options['champ_adresse_virement'] . "</div>";
	} else {
		$retours['message_ok'] .= "<div class='transaction_ok virement defaut'>" . _T('transaction:traiter_virement_message_defaut') . "</div>";
	}

	
	// Le formulaire a été validé, on le masque
	$retours['editable'] = false;
	
	return $retours;
}

?>
