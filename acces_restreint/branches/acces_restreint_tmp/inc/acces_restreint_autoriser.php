<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

/* pour que le pipeline ne rale pas ! */
function accesrestreint_autoriser(){}

/**
 * Autorisation a administrer les zones
 *
 * @param unknown_type $faire
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opts
 * @return unknown
 */
function autoriser_zone_administrer($faire,$quoi,$id,$qui,$opts){
	if ($qui['statut']=='0minirezo' AND !$qui['restreint'])
		return true;
	return false;
}

/**
 * Autorisation a affecter les zones a un auteur
 * si un id_zone passe dans opts, cela concerne plus particulierement le droit d'affecter cette zone
 *
 * @param unknown_type $faire
 * @param unknown_type $qui
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opts
 * @return unknown
 */
function autoriser_auteur_affecterzones_dist($faire,$quoi,$id,$qui,$opts){
	if (!autoriser('modifier','auteur',$id)) return false;
	if ($qui['statut']=='0minirezo' AND !$qui['restreint'])
		return true;
	# les non admin ne peuvent pas s'administrer eux meme pour eviter les erreurs
	if ($id == $qui['id_auteur']) return false;
	# les non admin ne peuvent affecter que les zones dont ils font partie
	include_spip('inc/acces_restreint');
	if ($opts['id_zone']
	  AND !accesrestreint_test_appartenance_zone_auteur($opts['id_zone'], $qui['id_auteur']))
	  return false;
 return true;
}

if(!function_exists('autoriser_rubrique_voir')) {
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint');
	static $rub_exclues;
	$publique = isset($opt['publique'])?$opt['publique']:!test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
	if (!isset($rub_exclues[$id_auteur][$publique]) || !is_array($rub_exclues[$id_auteur][$publique])) {
		$rub_exclues[$id_auteur][$publique] = accesrestreint_liste_rubriques_exclues($publique,$id_auteur);
		$rub_exclues[$id_auteur][$publique] = array_flip($rub_exclues[$id_auteur][$publique]);
	}
	return !isset($rub_exclues[$id_auteur][$publique][$id]);
}
}
if(!function_exists('autoriser_article_voir')) {
function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	include_spip('public/quete');
	if (!$id_rubrique = $opt['id_rubrique']){
		$article = quete_parent_lang('spip_articles',$id);
		$id_rubrique = $article['id_rubrique'];
	}
	if (autoriser_rubrique_voir('voir','rubrique',$id_rubrique,$qui,$opt)){
		if ($qui['statut'] == '0minirezo') return true;
		// un article 'prepa' ou 'poubelle' dont on n'est pas auteur : interdit
		$r = sql_getfetsel("statut", "spip_articles", "id_article=".sql_quote($id));
		include_spip('inc/auth'); // pour auteurs_article si espace public
		return
			in_array($r, array('prop', 'publie'))
			OR auteurs_article($id, "id_auteur=".$qui['id_auteur']);
	}
	return false;
}
}
if(!function_exists('autoriser_breve_voir')) {
function autoriser_breve_voir($faire, $type, $id, $qui, $opt) {
	include_spip('public/quete');
	if (!$id_rubrique = $opt['id_rubrique']){
		$breve = quete_parent_lang('spip_breves',$id);
		$id_rubrique = $breve['id_rubrique'];
	}
	return autoriser_rubrique_voir('voir','rubrique',$id_rubrique,$qui,$opt);
}
}
if(!function_exists('autoriser_site_voir')) {
function autoriser_site_voir($faire, $type, $id, $qui, $opt) {
	include_spip('public/quete');
	if (!$id_rubrique = $opt['id_rubrique']){
		$site = quete_parent_lang('spip_syndic',$id);
		$id_rubrique = $site['id_rubrique'];
	}
	return autoriser_rubrique_voir('voir','rubrique',$id_rubrique,$qui,$opt);
}
}
if(!function_exists('autoriser_evenement_voir')) {
function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	static $evenements_statut;
	$publique = isset($opt['publique'])?$opt['publique']:!test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
	if (!isset($evenements_statut[$id_auteur][$publique][$id])){
		$id_article = sql_getfetsel('id_article','spip_evenements','id_evenement='.intval($id));
		$evenements_statut[$id_auteur][$publique][$id] = autoriser_article_voir('voir', 'article', $id_article, $qui, $opt);
	}
	return $evenements_statut[$id_auteur][$publique][$id];
}
}

if(!function_exists('autoriser_document_voir')) {
function autoriser_document_voir($faire, $type, $id, $qui, $opt) {
	include_spip('public/acces_restreint');
	static $documents_statut = array();
	static $where = array();
	$publique = isset($opt['publique'])?$opt['publique']:!test_espace_prive();
	$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
	if (!isset($documents_statut[$id_auteur][$publique][$id])){
		if (!$id)
			$documents_statut[$id_auteur][$publique][$id] = autoriser_document_voir_dist($faire, $type, $id, $qui, $opt);
		else {
			if (!isset($where[$publique])){
				$where[$publique] = accesrestreint_documents_accessibles_where('id_document', $publique?"true":"false");
				// inclure avant le eval, pour que les fonctions soient bien definies
				include_spip('inc/acces_restreint');
				$where[$publique] = eval("return ".$where[$publique].";");
			}
			$documents_statut[$id_auteur][$publique][$id] = sql_getfetsel('id_document','spip_documents',array('id_document='.intval($id),$where[$publique]));
			if ($documents_statut[$id_auteur][$publique][$id])
				$documents_statut[$id_auteur][$publique][$id] = autoriser_document_voir_dist($faire, $type, $id, $qui, $opt);
		}
	}
	return $documents_statut[$id_auteur][$publique][$id];
}
}

?>
