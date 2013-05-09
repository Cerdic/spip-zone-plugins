<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

// Recuperer le reglage des forums publics de l'article x
// http://doc.spip.org/@get_forums_publics
function get_notes_publics($id_objet=0, $objet='article') {
	if ($objet=='article' AND $id_objet) {
		$obj = sql_getfetsel("accepter_note", "spip_articles", "id_article=".intval($id_objet));

		if (in_array($obj,array('oui','non'))) 
			return $obj;
	} else { // dans ce contexte, inutile
		return $GLOBALS['meta']["notations_publics"];
	}
	return $GLOBALS['meta']["notations_publics"];
}

/**
 * Charger
 *
 * @param int $id_article
 * @return array
 */
function formulaires_activer_notes_objet_charger_dist($id_objet, $objet='article'){
	include_spip('inc/autoriser');
	if (!autoriser('moderernote', $objet, $id_objet))
		return false;
	
	$nb_notes = sql_countsel("spip_notations", "objet=".sql_quote($objet)." AND id_objet=".intval($id_objet));
	return array(
		'editable' => ($objet=='article')?true:false,
		'objet' => $objet,
		'id_objet' => $id_objet,
		'accepter_note' => get_notes_publics($id_objet, $objet),
		'_suivi_notes' => $nb_notes?_T('icone_suivi_notes', array('nb_notes' => $nb_notes)):"",
	);
	
}

/**
 * Traiter
 *
 * @param int $id_objet
 * @param string $objet
 * @return array
 */
function formulaires_activer_notes_objet_traiter_dist($id_objet, $objet='article'){
	include_spip('inc/autoriser');
	if ($objet=='article' AND autoriser('moderernote', $objet, $id_objet)){
		$statut = _request('accepter_note');
		include_spip('base/abstract_sql');
		sql_updateq("spip_articles", array("accepter_note" => $statut), "id_article=". intval($id_objet));
		
		include_spip('inc/invalideur');
		suivre_invalideur("$objet/$id_objet");
	}
		
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>