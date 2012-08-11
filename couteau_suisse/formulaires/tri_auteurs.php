<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
// Recuperer le reglage des forums publics de l'article x
// http://doc.spip.org/@get_forums_publics
function get_forums_publics($id_objet=0, $objet='article') {

	if ($objet=='article' AND $id_objet) {
		$obj = sql_fetsel("accepter_forum", "spip_articles", "id_article=".intval($id_objet));

		if ($obj) return $obj['accepter_forum'];
	} else { // dans ce contexte, inutile
		return substr($GLOBALS['meta']["forums_publics"],0,3);
	}
	return $GLOBALS['meta']["forums_publics"];
}*/

/**
 * Charger
 *
 * @param int $id_objet
 * @param string $objet
 * @return array
 */
function formulaires_tri_auteurs_charger_dist($id_objet=0, $objet='article'){
	if (defined('boites_privees_TRI_AUTEURS') && $id_objet && autoriser('modifier', $objet, $id_objet)) {
	/*	include_spip('inc/presentation');
		include_spip('base/abstract_sql');
		$nb_forums = sql_countsel("spip_forum", "objet=".sql_quote($objet)." AND id_objet=".intval($id_objet)." AND statut IN ('publie', 'off', 'prop', 'spam')");
		$editable = ($objet=='article')?true:false;
		if (!$editable AND !$nb_forums)
			return false;
	*/

		return array(
			'objet' => $objet,
			'id_objet' => $id_objet,
			'editable' => $objet=='article',
	/*		'accepter_forum' => get_forums_publics($id_objet, $objet),
			'_suivi_forums' => $nb_forums?_T('forum:icone_suivi_forum', array('nb_forums' => $nb_forums)):"",
	*/	);
	}
	return false;
}

/**
 * Traiter
 *
 * @param int $id_objet
 * @param string $objet
 * @return array
 */
function formulaires_tri_auteurs_traiter_dist($id_objet=0, $objet='article'){
	include_spip('inc/autoriser');
	if ($objet=='article' AND autoriser('modifier', $objet, $id_objet)){
		$id_article = _request('bp_article');
		$id_auteur = abs(_request('bp_auteur'));
		$monter = _request('bp_auteur')>0;
		include_spip('base/abstract_sql');
		// liste des auteurs de l'article
		$a = sql_allfetsel('id_auteur, ordre', 'spip_auteurs_liens', "objet='article' AND id_objet=$id_article", '', 'ordre');
		$c = count($a);
		// recherche des auteurs a permuter
		for($i=$j=0;$i<$c;$i++)
			if($a[$i]['id_auteur']==$id_auteur) { $j=!$monter?min($i+1,$c-1):max($i-1,0); break; }
		spip_log("formulaires_tri_auteurs_traiter_dist, article $id_article : echange entre l'auteur {$a[$i][id_auteur]} et l'auteur {$a[$j][id_auteur]}");
		// permutation
		$tmp = $a[$i]; $a[$i] = $a[$j]; $a[$j] = $tmp;
		// mise a jour en base 
		// note : l'ordre est un nombre negatif, permettant aux auteurs ajoutes ulterieurement d'etre les derniers (ordre 0 par defaut)
		for($i=0;$i<$c;$i++)
			sql_update('spip_auteurs_liens', array('ordre'=>$i-$c), "objet='article' AND id_objet=$id_article AND id_auteur=".$a[$i]['id_auteur']);

		include_spip('inc/invalideur');
		suivre_invalideur("id='$objet/$id_objet'");
	}
		
	return array('message_ok'=>_T('config_info_enregistree'), 'editable'=>true);
}

?>