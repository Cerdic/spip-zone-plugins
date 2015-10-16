<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


/**
 * Chargement des valeurs par defaut de #FORMULAIRE_MESSAGES_RECUS
 *
 * @return array
 */
function formulaires_messages_recus_charger_dist($url_repondre=""){
	if (!$url_repondre AND defined('_URL_ENVOYER_MESSAGE'))
		$url_repondre = _URL_ENVOYER_MESSAGE;
	include_spip('inc/lien');
	$valeurs = array('_url_ecrire_message'=>calculer_url($url_repondre));
	return $valeurs;
}


function formulaires_messages_recus_verifier_dist($url_repondre=""){
    $erreurs = array();
    $liste = _request('selectionne');
    $id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
    if (is_array($liste) && count($liste)) {
        foreach ($liste as $id_mess){
            if ( ! sql_fetsel('id_auteur', 'spip_auteurs_liens',array("id_auteur='$id_auteur'", "id_objet='$id_mess'", "objet='message'")) ) {
                $erreurs['message_erreur'] = _T('erreur');
            }    
        }
    }
    return $erreurs;
}


/**
 * Traitement de la saisie de #FORMULAIRE_MESSAGES_RECUS
 *
 * @return string
 */
function formulaires_messages_recus_traiter_dist($url_repondre=""){
	include_spip('base/abstract_sql');
	include_spip('inc/texte');
	include_spip('inc/messagerie');

	$liste = _request('selectionne');
	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	if (is_array($liste) && count($liste)) {
		include_spip('inc/messages');
		if (_request('marquer_lus'))
			messagerie_marquer_lus($id_auteur,$liste);
		elseif (_request('marquer_non_lus'))
			messagerie_marquer_non_lus($id_auteur,$liste);
		elseif (_request('effacer'))
			messagerie_effacer_message_recu($id_auteur,$liste);
	}
	return array(true,"");
}

?>