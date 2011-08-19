<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_lier_commande_charger_dist($id_commande, $objet, $id_objet = null, $redirect=''){
	$valeurs = array(
		'recherche_objet' => '',
        '_id_commande' => $id_commande,
        'objet' => $objet,		
		'id_objet' => intval($id_objet),
		'redirect' => $redirect
	);
	return $valeurs;
}

function formulaires_lier_commande_verifier_dist($id_commande, $objet, $id_objet = null, $redirect=''){

    $id_objet = _request('objet_id');

	$erreurs = array();
	$erreurs[''] = ''; // toujours en erreur : ce sont des actions qui lient les contacts

	return $erreurs;
}

function formulaires_lier_commande_traiter_dist($id_commande, $objet, $id_objet = null, $redirect=''){

    set_request('recherche_objet');
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
