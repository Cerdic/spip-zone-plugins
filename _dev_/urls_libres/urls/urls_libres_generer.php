<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  as original founders of spip                                           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

//if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function _store_url($url, $type, $id_objet, $prefix = '')
{
	$url = _debut_urls_propres . $prefix . $url .
			strrev($prefix) . _terminaison_urls_propres;
	$q_url = _q($url);
	$q_type = _q($type);
	if (($result = spip_query(
		"SELECT type, id_objet, version from spip_urls WHERE url=$q_url"))
	 &&	($deja = spip_fetch_array($result))) {
		$version = $deja['version'] + 1;
		spip_query("UPDATE spip_urls SET
			version=$version, maj=NOW(), type=$q_type, id_objet=$id_objet
			WHERE url=$q_url");
	} else {
		spip_query("INSERT INTO spip_urls (url, type, id_objet, version, maj)
			VALUE ($q_url, $q_type, $id_objet, 0, NOW())");
	}
	return $url;
}
function _generer_url_libre($type, $id_objet, $prefix = '')
{
	spip_log('_generer_url_libre("' . $type . '",' . $id_objet . ',"' . $prefix .'")');
	static $priotype = array(
		'article'=>0, 'rubrique'=>1, 'mot'=>2, 'auteur'=>3,
		'site'=>4, 'syndic'=>5, 'breve'=>6);

	$q_type = _q($type);
	$table = 'spip_' . table_objet($type);
	$col_id = id_table_objet($type);

	// Auteurs : on prend le nom
	if ($type == 'auteur')
		$champ_titre = 'nom AS titre';
	else if ($type == 'site' OR $type=='syndic')
		$champ_titre = 'nom_site AS titre';
	else
		$champ_titre = 'titre';

	// Mots-cles : pas de champ statut
	if ($type == 'mot')
		$statut = "'publie' as statut";
	else
		$statut = 'statut';

	// Vérifier l'existence de l'objet et recuperer son URL defaut, titre et statut
	$result = spip_query("SELECT url_propre, $statut, $champ_titre
			FROM $table WHERE $col_id=$id_objet");
	if (!($row = spip_fetch_array($result))) return ""; // objet inexistant

	// A-t-on cette url ? Prendre la derniere version
	$result = spip_query("SELECT url, version FROM spip_urls
		WHERE type=$q_type AND id_objet=$id_objet ORDER BY version DESC LIMIT 1");
	if (!($store = spip_fetch_array($result))) {
		// objet non référencé
// .... to do tout doux .... 
	}

	// Si l'on n'est pas dans spip_redirect.php3 sur un objet non publie
	// ou en preview (astuce pour corriger un url-propre) + admin connecte
	// Ne pas recalculer l'url-propre,
	// sauf si :
	// 1) il n'existe pas, ou
	// 2) l'objet n'est pas 'publie' et on est admin connecte, ou
	// 3) on le demande explicitement (preview) et on est admin connecte
	$modif_url_libre = false;
	if (function_exists('action_redirect_dist') AND
	($GLOBALS['preview'] OR ($row['statut'] <> 'publie'))
	AND $GLOBALS['auteur_session']['statut'] == '0minirezo')
		$modif_url_libre = true;

	if ($store && $store['url'] && !$modif_url_libre)
		return $store['url'];

	// Sinon, creer l'URL
	include_spip('inc/filtres');
	include_spip('inc/charsets');
	$url = translitteration(corriger_caracteres(
		supprimer_tags(supprimer_numero(extraire_multi($row['titre'])))
		));

	$url = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $url);
	// S'il reste trop de caracteres non latins, ou trop peu
	// de caracteres latins, utiliser l'id a la place
	if (preg_match(",([^a-zA-Z0-9 ].*){5},", $url, $r)
	OR strlen($url)<3) {
		$url = $type.$id_objet;
	}
	else {
		$mots = preg_split(",[^a-zA-Z0-9]+,", $url);
		$url = '';
		foreach ($mots as $mot) {
			if (!$mot) continue;
			$url2 = $url.'-'.$mot;
			if (strlen($url2) > 35) {
				break;
			}
			$url = $url2;
		}
		$url = substr($url, 1);
		//echo "$url<br>";
		if (strlen($url) < 2) $url = $type.$id_objet;
	}

	// Verifier les eventuels doublons et mettre a jour
	$lock = "url $type $id_objet";
	spip_get_lock($lock, 10);

	// Eviter de tamponner les URLs a l'ancienne (cas d'un article
	// intitule "auteur2")
	if ($type == 'article'
	AND preg_match(',^(article|breve|rubrique|mot|auteur)[0-9]+$,', $url))
		$url = $url.','.$id_objet;

	// Mettre a jour l'url
	// Inserer la nouvelle reference absolue
	$url_abs = _store_url($url, $type, $id_objet, $prefix);

	$url = _debut_urls_propres . $url . _terminaison_urls_propres;

	// url deja utilisee ?
	if (($result = spip_query('SELECT type, id_objet from spip_urls WHERE url=' . _q($url)))
		&& ($deja = spip_fetch_array($result))) {
		// utilisee par un type prioritaire ? ==> que l'url absolue
		if ($priotype[$deja['type']] < $priotype[$type]) {
			return $url_abs;
		}
	}

	$q_url = _q($url);

	// store est ce qui existe deja pour l'objet, si pas vide c'est qu'on change
	$version = $store ? $store['version'] + 1 : 0;
	// deja est ce qui etait reference pour l'url
	if ($deja) {
		// on ecrase l'url non prioritaire
		spip_query("UPDATE spip_urls SET url=$q_url,
			version=$version, maj=NOW() WHERE type=$q_type AND id_objet=$id_objet");
	} else {
		spip_query("INSERT INTO spip_urls (url, type, id_objet, version, maj)
			VALUE ($q_url, $q_type, $id_objet, $version, NOW())");
	}

	// Mettre a jour dans la table objet ?
	spip_query("UPDATE $table SET url_propre=$q_url WHERE $col_id=$id_objet");

	spip_release_lock($lock);

	spip_log("Creation de l'url propre '$url' pour $col_id=$id_objet");

	return $url;
}

// http://doc.spip.org/@generer_url_article
function generer_url_article($id_article) {
	($url = _generer_url_libre('article', $id_article, '')) ||
	($url = get_spip_script('./')."?page=article&id_article=$id_article");
	return $url;
}

// http://doc.spip.org/@generer_url_rubrique
function generer_url_rubrique($id_rubrique) {
	($url = _generer_url_libre('rubrique', $id_rubrique, '-')) ||
	($url = get_spip_script('./')."?page=rubrique&id_rubrique=$id_rubrique");
	return $url;
}

// http://doc.spip.org/@generer_url_breve
function generer_url_breve($id_breve) {
	($url = _generer_url_libre('breve', $id_breve, '+')) ||
	($url = get_spip_script('./')."?page=breve&id_breve=$id_breve");
	return $url;
}

// C'est special pour les forums, generer_url_forum_dist()
// retourne generer_url_xxx($id)."#forum$id_forum"
// http://doc.spip.org/@generer_url_forum
function generer_url_forum($id_forum, $show_thread=false) {
	include_spip('inc/forum');
	return generer_url_forum_dist($id_forum, $show_thread);
}

// http://doc.spip.org/@generer_url_mot
function generer_url_mot($id_mot) {
	($url = _generer_url_libre('mot', $id_mot, '+-')) ||
	($url = get_spip_script('./')."?page=mot&id_mot=$id_mot");
	return $url;
}

// http://doc.spip.org/@generer_url_auteur
function generer_url_auteur($id_auteur) {
	($url = _generer_url_libre('auteur', $id_auteur, '_')) ||
	($url = get_spip_script('./')."?page=auteur&id_auteur=$id_auteur");
	return $url;
}

// http://doc.spip.org/@generer_url_site
function generer_url_site($id_syndic) {
	($url = _generer_url_libre('site', $id_syndic, '@')) ||
	($url = get_spip_script('./')."?page=site&id_syndic=$id_syndic");
	return $url;
}

// http://doc.spip.org/@generer_url_document
function generer_url_document($id_document) {
	if (($id_document = intval($id_document)) <= 0)
		return '';
	if (($GLOBALS['meta']["creer_htaccess"]) == 'oui')
	  return generer_url_action('autoriser',"arg=$id_document", true);
	$row = @spip_fetch_array(spip_query("SELECT fichier FROM spip_documents WHERE id_document = $id_document"));
	if ($row) return ($row['fichier']);
	return '';
}
?>
