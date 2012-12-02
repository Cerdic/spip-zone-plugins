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

    //Ne pas passer en action si on a un objet clairement identifié
    if (!is_null($id_objet) && intval($id_objet))
            $erreurs = array();

	return $erreurs;
}

function formulaires_lier_commande_traiter_dist($id_commande, $objet, $id_objet = null, $redirect=''){

    if (is_null($id_objet)) 
        $id_objet = _request('objet_id');

    if ($f=charger_fonction('lier_commande_'.$objet, 'inc')) {
        $f($id_commande,$id_objet);
    } else {
		spip_log("cvt_lier_commande_".$objet."_dist $arg pas compris", "commandes");
    }    

    set_request('recherche_objet');
	return array(
		'message_ok' => '',
		'editable' => true,
	);
}

?>
