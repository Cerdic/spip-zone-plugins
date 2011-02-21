<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_paiement_cheque_dist($args, $retours){
	
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);
	
	if ($_REQUEST['montant_fixe_1']) $montant = $_REQUEST['montant_fixe_1'];
    	if ($_REQUEST['montant_selection_1']) $montant = $_REQUEST['montant_selection_1'];
    	if ($_REQUEST['montant_1']) $montant = $_REQUEST['montant_1'];
       
    	if (intval($_REQUEST['montant_multiplicateur_1']) > 0) {
		$montant = $montant * intval($_REQUEST['montant_multiplicateur_1']);
    	} 

	if ($options['champ_adresse_cheque']){
		$retours['message_ok'] .= "<div class='transaction_ok cheque'>" . str_replace("%montant%", $montant, $options['champ_adresse_cheque']) . "</div>";
	} else {
		$retours['message_ok'] .= "<div class='transaction_ok cheque defaut'>" . _T('transaction:traiter_cheque_message_defaut', array('montant'=>$montant)) . "</div>";
	}

	// Le formulaire a été validé, on le masque
	$retours['editable'] = false;
	
	return $retours;
}

?>
