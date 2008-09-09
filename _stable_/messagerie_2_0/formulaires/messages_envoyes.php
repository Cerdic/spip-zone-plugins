<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


/**
 * Chargement des valeurs par defaut de #FORMULAIRE_MESSAGES_ENVOYES
 *
 * @return array
 */
function formulaires_messages_envoyes_charger_dist(){
	$valeurs = array();

	return $valeurs;
}


/**
 * Traitement de la saisie de #FORMULAIRE_MESSAGES_ENVOYES
 *
 * @return string
 */
function formulaires_messages_envoyes_traiter_dist(){
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