<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


/**
 * Chargement des valeurs par defaut de #FORMULAIRE_MESSAGES_ENVOYES{url_repondre}
 *
 * @return array
 */
function formulaires_messages_envoyes_charger_dist($url_repondre=""){
	if (!$url_repondre AND defined('_URL_ENVOYER_MESSAGE'))
		$url_repondre = _URL_ENVOYER_MESSAGE;
	include_spip('inc/lien');
	$valeurs = array('_url_ecrire_message'=>calculer_url($url_repondre));

	return $valeurs;
}


/**
 * Traitement de la saisie de #FORMULAIRE_MESSAGES_ENVOYES
 *
 * @return string
 */
function formulaires_messages_envoyes_traiter_dist($url_repondre=""){
	include_spip('base/abstract_sql');
	
	$liste = _request('selectionne');
	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	if (is_array($liste) && count($liste)) {
		if (_request('effacer')){
			sql_updateq('spip_messages',array('statut'=>'poub'),array('id_auteur='.intval($id_auteur),'id_message IN ('.implode(',',$liste).')'));
			
			include_spip('inc/invalideur');
			suivre_invalideur("message/".implode(',',$liste));
		}
	}
	
	return array(true,"");
}

?>