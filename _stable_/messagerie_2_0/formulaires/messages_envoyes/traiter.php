<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


include_spip('base/abstract_sql');
include_spip('inc/texte');
include_spip('base/abstract_sql');

/**
 * Traitement de la saisie de #FORMULAIRE_MESSAGES_ENVOYES
 *
 * @return string
 */
function formulaires_messages_envoyes_traiter_dist(){
	$liste = _request('selectionne');
	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	if (is_array($liste) && count($liste)) {
		if (_request('effacer')){
			sql_updateq('spip_messages',array('statut'=>'poub'),array('id_auteur='.intval($id_auteur),'id_message IN ('.implode(',',$liste).')'));
		}
	}
	
	return array(true,"");
}

?>