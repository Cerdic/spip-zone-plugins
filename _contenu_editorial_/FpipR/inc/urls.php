<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction produisant les URL d'acces en lecture ou en ecriture 
// des items des tables SQL principales, selon le statut de publication

function generer_url_ecrire_article($id_article, $statut='') {
	$args = "id_article=" . intval($id_article);
	if (!$statut)
		$statut = spip_fetch_array(spip_query("SELECT statut FROM spip_articles WHERE $args"));
	if ($statut['statut'] == 'publie')
		return generer_url_action('redirect', $args);
	else	return generer_url_ecrire('articles', $args);
}

function generer_url_ecrire_rubrique($id_rubrique, $statut='') {
	$args = "id_rubrique=" . intval($id_rubrique);
	if (!$statut)
		$statut = spip_fetch_array(spip_query("SELECT statut FROM spip_rubriques WHERE $args"));
	if ($statut['statut'] == 'publie')
		return generer_url_action('redirect', $args);
	else	return generer_url_ecrire('naviguer',$args);
}

function generer_url_ecrire_breve($id_breve, $statut='') {
	$args = "id_breve=" . intval($id_breve);
	if (!$statut)
		$statut = spip_fetch_array(spip_query("SELECT statut FROM spip_breves WHERE $args"));
	if ($statut['statut'] == 'publie')
		return generer_url_action('redirect', $args);
	else	return generer_url_ecrire('breves_voir',$args);
}

function generer_url_ecrire_mot($id_mot, $statut='') {
	$args = "id_mot=" . intval($id_mot);
	if (!$statut)
		return generer_url_action('redirect', $args);
	else	return generer_url_ecrire('mots_edit',$args);
}

function generer_url_ecrire_site($id_syndic, $statut='') {
	$args = "id_syndic=" . intval($id_syndic);
	if (!$statut)
		return generer_url_action('redirect', $args);
	else	return generer_url_ecrire('sites',$args);
}

function generer_url_ecrire_auteur($id_auteur, $statut='') {
	$args = "id_auteur=" . intval($id_auteur);
	if (!$statut)
		return generer_url_action('redirect', $args);
	else	return generer_url_ecrire('auteurs_edit',$args);
}

function generer_url_ecrire_forum($id_forum, $statut='') {
	return generer_url_action('redirect', "id_forum=$id_forum");
}

//modifier pour fpipr
function generer_url_ecrire_document($id_document, $statut='') {
	if (intval($id_document) <= 0) 
		return '';
	$row = @spip_fetch_array(spip_query("SELECT fichier,distant	FROM spip_documents WHERE id_document = $id_document"));
	  if ($row) {
		if ($row['distant'] == 'oui') {
		  if(preg_match('#http://static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$row['fichier'],$matches)) {
			$id = $matches[2];
			$secret = $matches[3];
			include_spip('inc/flickr_api');
			$details = flickr_photos_getInfo($id,$secret);
			if($details->urls['photopage']) return $details->urls['photopage'];
			if($details->owner_nsid) 
			  return "http://www.flickr.com/photos/".$details->owner_nsid."/$id/";
			else return $row['fichier'];
		  } else 
			return $row['fichier'];
		} else {
			if (($GLOBALS['meta']["creer_htaccess"]) != 'oui')
				return _DIR_RACINE . ($row['fichier']);
			else 	return generer_url_action('autoriser', "arg=$id_document");
		}
	}

}

function generer_url_ecrire_statistiques($id_article) {
	return generer_url_ecrire('statistiques_visites', "id_article=$id_article");
}

// en cas de chargement a partir de l'espace de redac, rabattre la production
// des URL publiques vers les URL privees en cas d'item non publies 

if (!_DIR_RESTREINT) {

  if (!function_exists('generer_url_article')) {
	function generer_url_article($id, $stat='')
		{ return generer_url_ecrire_article($id, $stat);}
  }
  if (!function_exists('generer_url_rubrique')) {
	function generer_url_rubrique($id, $stat='')
		{ return generer_url_ecrire_rubrique($id, $stat);}
  }
  if (!function_exists('generer_url_breve')) {
	function generer_url_breve($id, $stat='')
		{ return generer_url_ecrire_breve($id, $stat);}
  }
  if (!function_exists('generer_url_mot')) {
	function generer_url_mot($id, $stat='')
		{ return generer_url_ecrire_mot($id, $stat);}
  }
  if (!function_exists('generer_url_site')) {
	function generer_url_site($id, $stat='')
		{ return generer_url_ecrire_site($id, $stat);}
  }
  if (!function_exists('generer_url_auteur')) {
	function generer_url_auteur($id, $stat='')
		{ return generer_url_ecrire_auteur($id, $stat);}
  }
  if (!function_exists('generer_url_forum')) {
	function generer_url_forum($id, $stat='')
		{ return generer_url_ecrire_forum($id, $stat);}
  }
  if (!function_exists('generer_url_document')) {
	function generer_url_document($id, $stat='')
		{ return generer_url_ecrire_document($id, $stat);}
  }
 }
?>
