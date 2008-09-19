<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL
 * 
 *
 */

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
	if ($opts['id_zone']
	  AND !AccesRestreint_test_appartenance_zone_auteur($opts['id_zone'], $qui['id_auteur']))
	  return false;
 return true;
}


if(!function_exists('autoriser_rubrique_voir')) {
function autoriser_rubrique_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint_autorisations');
	static $rub_exclues;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = !test_espace_prive();
	if (!isset($rub_exclues[$publique]) || !is_array($rub_exclues[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$rub_exclues[$publique] = AccesRestreint_liste_rubriques_exclues($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$rub_exclues[$publique] = AccesRestreint_liste_rubriques_exclues($publique,$qui['id_auteur']);
		else
			$rub_exclues[$publique] = AccesRestreint_liste_rubriques_exclues($publique);
		$rub_exclues[$publique] = array_flip($rub_exclues[$publique]);
	}
	return !isset($rub_exclues[$publique][$id]);
}
}
if(!function_exists('autoriser_article_voir')) {
function autoriser_article_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint_autorisations');
	static $art_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = !test_espace_prive();
	if (!isset($art_exclus[$publique]) || !is_array($art_exclus[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$art_exclus[$publique] = AccesRestreint_liste_articles_exclus($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$art_exclus[$publique] = AccesRestreint_liste_articles_exclus($publique,$qui['id_auteur']);
		else
			$art_exclus[$publique] = AccesRestreint_liste_articles_exclus($publique);
		$art_exclus[$publique] = array_flip($art_exclus[$publique]);
	}
	return !isset($art_exclus[$publique][$id]);
}
}
if(!function_exists('autoriser_breve_voir')) {
function autoriser_breve_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint_autorisations');
	static $breves_exclues;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = !test_espace_prive();
	if (!isset($breves_exclues[$publique]) || !is_array($breves_exclues[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$breves_exclues[$publique] = AccesRestreint_liste_breves_exclues($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$breves_exclues[$publique] = AccesRestreint_liste_breves_exclues($publique,$qui['id_auteur']);
		else
			$breves_exclues[$publique] = AccesRestreint_liste_breves_exclues($publique);
		$breves_exclues[$publique] = array_flip($breves_exclues[$publique]);
	}
	return !isset($breves_exclues[$publique][$id]);
}
}
if(!function_exists('autoriser_site_voir')) {
function autoriser_site_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint_autorisations');
	static $sites_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = !test_espace_prive();
	if (!isset($sites_exclus[$publique]) || !is_array($sites_exclus[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$sites_exclus[$publique] = AccesRestreint_liste_syndic_exclus($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$sites_exclus[$publique] = AccesRestreint_liste_syndic_exclus($publique,$qui['id_auteur']);
		else
			$sites_exclus[$publique] = AccesRestreint_liste_syndic_exclus($publique);
		$sites_exclus[$publique] = array_flip($sites_exclus[$publique]);
	}
	return !isset($sites_exclus[$publique][$id]);
}
}
if(!function_exists('autoriser_evenement_voir')) {
function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint_autorisations');
	static $evenements_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = !test_espace_prive();
	if (!isset($evenements_exclus[$publique]) || !is_array($evenements_exclus[$publique])) {
		// Si autoriser est appelee pour un autre auteur que l'auteur connecte  ou si pas d'auteur connecte , on passe $id_auteur en parametre
		if(isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']) && $qui['id_auteur']!=$GLOBALS['auteur_session']['id_auteur'])
			$evenements_exclus[$publique] = AccesRestreint_liste_evenements_exclus($publique,$qui['id_auteur']);
		elseif (!isset($GLOBALS['auteur_session']['id_auteur']) && isset($qui['id_auteur']))
			$evenements_exclus[$publique] = AccesRestreint_liste_evenements_exclus($publique,$qui['id_auteur']);
		else
			$evenements_exclus[$publique] = AccesRestreint_liste_evenements_exclus($publique);
		$evenements_exclus[$publique] = array_flip($evenements_exclus[$publique]);
	}
	return !isset($evenements_exclus[$publique][$id]);
}
}
if(!function_exists('autoriser_document_voir')) {
function autoriser_document_voir($faire, $type, $id, $qui, $opt) {
	include_spip('inc/acces_restreint_autorisations');
	static $documents_exclus;
	if (isset($opt['publique']))
		$publique = $opt['publique'];
	else
		$publique = !test_espace_prive();
	if (!isset($documents_exclus[$publique]) || !is_array($documents_exclus[$publique])) {
		$documents_exclus[$publique] = AccesRestreint_liste_documents_exclus($publique,$qui['id_auteur']);
		$documents_exclus[$publique] = array_flip($documents_exclus[$publique]);
	}
	return !isset($documents_exclus[$publique][$id]);
}
}

// fonctions de filtrage article
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_articles_exclus($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_art_exclus=array();
	if (!isset($liste_art_exclus[$publique]) || !is_array($liste_art_exclus[$publique])){
		$liste_art_exclus[$publique] = array();
		$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		$s = spip_query("SELECT id_article FROM spip_articles WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_art_exclus[$publique][] = $row['id_article'];
		}
	}
	return $liste_art_exclus[$publique];
}

// fonctions de filtrage breves
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_breves_exclues($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_breves_exclues=array();
	if (!isset($liste_breves_exclues[$publique]) || !is_array($liste_breves_exclues[$publique])){
		$liste_breves_exclues[$publique] = array();
		$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		$s = spip_query("SELECT id_breve FROM spip_breves WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_breves_exclues[$publique][] = $row['id_breve'];
		}
	}
	return $liste_breves_exclues[$publique];
}

// fonctions de filtrage forums
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_forum_exclus($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_forum_exclus=array();
	if (!isset($liste_forum_exclus[$publique]) || !is_array($liste_forum_exclus[$publique])){
		$liste_forum_exclus[$publique] = array();
		// rattaches aux rubriques
		$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		// rattaches aux articles
		$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
		$where .= " OR " . calcul_mysql_in('id_article', join(",",$liste_art));
		// rattaches aux breves
		$liste_breves = AccesRestreint_liste_breves_exclues($publique, $id_auteur);
		$where .= " OR " . calcul_mysql_in('id_breve', join(",",$liste_art));

		$s = spip_query("SELECT id_forum FROM spip_forum WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_forum_exclus[$publique][] = $row['id_forum'];
		}
	}
	return $liste_forum_exclus[$publique];
}

// fonctions de filtrage signatures
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_signatures_exclues($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_signatures_exclues=array();
	if (!isset($liste_signatures_exclues[$publique]) || !is_array($liste_signatures_exclues[$publique])){
		$liste_signatures_exclues[$publique] = array();
		// rattaches aux articles
		$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
		$where = calcul_mysql_in('id_article', join(",",$liste_art));
		$s = spip_query("SELECT id_signature FROM spip_signatures WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_signatures_exclues[$publique][] = $row['id_signature'];
		}
	}
	return $liste_signatures_exclues[$publique];
}

// fonctions de filtrage documents
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_documents_exclus($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_documents_exclus=array();
	if (!isset($liste_documents_exclus[$publique]) || !is_array($liste_documents_exclus[$publique])){
		$liste_documents_exclus[$publique] = array();
		// rattaches aux articles
		$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
		$where = calcul_mysql_in('id_article', join(",",$liste_art));
		$s = spip_query("SELECT id_document FROM spip_documents_articles WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_documents_exclus[$publique][$row['id_document']]=1;
		}
		// rattaches aux rubriques
		$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		$s = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_documents_exclus[$publique][$row['id_document']]=1;
		}
		// rattaches aux breves
		$liste_breves = AccesRestreint_liste_breves_exclues($publique, $id_auteur);
		$where = calcul_mysql_in('id_breve', join(",",$liste_breves));
		$s = spip_query("SELECT id_document FROM spip_documents_breves WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_documents_exclus[$publique][$row['id_document']]=1;
		}
		// rattaches aux syndic
		/*$liste_syn = AccesRestreint_liste_syndic_exclus($publique);
		$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
		$s = spip_query("SELECT id_document FROM spip_documents_syndic WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_documents_exclus[$publique][$row['id_document']]=1;
		}*/
		$liste_documents_exclus[$publique] = array_keys($liste_documents_exclus[$publique]);
	}
	return $liste_documents_exclus[$publique];
}

// fonctions de filtrage syndic
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_syndic_exclus($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_syndic_exclus=array();
	if (!isset($liste_syndic_exclus[$publique]) || !is_array($liste_syndic_exclus[$publique])){
		$liste_syndic_exclus[$publique] = array();
		$liste_rub = AccesRestreint_liste_rubriques_exclues($publique, $id_auteur);
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		$s = spip_query("SELECT id_syndic FROM spip_syndic WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_syndic_exclus[$publique][] = $row['id_syndic'];
		}
	}
	return $liste_syndic_exclus[$publique];
}

// fonctions de filtrage syndic_articles
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_syndic_articles_exclus($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_syndic_articles_exclus=array();
	if (!isset($liste_syndic_articles_exclus[$publique]) || !is_array($liste_syndic_articles_exclus[$publique])){
		$liste_syndic_articles_exclus[$publique] = array();
		$liste_syn = AccesRestreint_liste_syndic_exclus($publique, $id_auteur);
		$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
		$s = spip_query("SELECT id_syndic_article FROM spip_syndic_articles WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_syndic_articles_exclus[$publique][] = $row['id_syndic_article'];
		}
	}
	return $liste_syndic_articles_exclus[$publique];
}

// fonctions de filtrage evenements
// plus performant a priori : liste des rubriques exclues uniquement
// -> condition NOT IN
function AccesRestreint_liste_evenements_exclus($publique=true, $id_auteur=NULL){
	include_spip('base/abstract_sql');
	static $liste_evenements_exclus=array();
	if (!isset($liste_evenements_exclus[$publique]) || !is_array($liste_evenements_exclus[$publique])){
		$liste_evenements_exclus[$publique] = array();
		// rattaches aux articles
		$liste_art = AccesRestreint_liste_articles_exclus($publique, $id_auteur);
		$where = calcul_mysql_in('id_article', join(",",$liste_art));
		
		$s = spip_query("SELECT id_evenement FROM spip_evenements WHERE $where");
		while ($row = spip_fetch_array($s)){
			$liste_evenements_exclus[$publique][] = $row['id_evenement'];
		}
	}
	return $liste_evenements_exclus[$publique];
}

	
?>