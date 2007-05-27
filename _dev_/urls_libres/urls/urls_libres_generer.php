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

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function _store_url($url, $type, $id_objet, $prefix = '', $version)
{
	$url = $prefix . $url .	strrev($prefix);
	$q_url = _q($url);
	$q_type = _q($type);
	if (($result = spip_query(
		"SELECT type, id_objet, version from spip_urls WHERE url=$q_url"))
	 &&	($deja = spip_fetch_array($result))) {
		spip_query("UPDATE spip_urls SET
			version=$version, maj=NOW(), type=$q_type, id_objet=$id_objet
			WHERE url=$q_url");
	} else {
		spip_query("INSERT INTO spip_urls (url, type, id_objet, version, maj)
			VALUE ($q_url, $q_type, $id_objet, $version, NOW())");
	}
	return $url;
}
function _generer_url_libre($type, $id_objet, $prefix = '',
						$opt = array('args' => '', 'ancre' => ''))
{
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
	$result = spip_query("SELECT url_propre, maj, $statut, $champ_titre
			FROM $table WHERE $col_id=$id_objet");
	if (!($row = spip_fetch_array($result))) return ""; // objet inexistant

	// A-t-on cette url ? Prendre la derniere version
	$result = spip_query("SELECT id_url, url, version, maj FROM spip_urls
		WHERE type=$q_type AND id_objet=$id_objet ORDER BY version DESC LIMIT 1");
	$store = spip_fetch_array($result);

	// Recalculer l'url-propre seulement si l'objet a change apres l'url libre
	if ($store && $store['url'] && $store['maj'] >= $row['maj']) {
		return finir_url_libre_dist($store['url'], $opt);
	}

	// Sinon, creer l'URL
	spip_log('_generer_url_libre("' . $type . '",' . $id_objet . ',"' . $prefix .'")', 'urls');
	if (!$store && $row['url_propre']) {
		// objet sans reference dans urls libres
		// se rappeler de cette version comme la plus ancienne ...
		// meme si semblable aux genereration qui suivent
		_store_url($row['url_propre'], $type, $id_object, '', -1);
	}
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

	// l'url a-t-elle change ?
	$url_abs = $prefix . $url .	strrev($prefix);
	if ($store && ($store['url'] == $url || $store['url'] == $url_abs)) {
		// mettre a jour la date pour ne plus y revenir
		spip_query('UPDATE spip_urls set maj=' . _q($row['maj']) . ' WHERE id_url=' . $store['id_url']);
		return finir_url_libre_dist($store['url'], $opt);
	}

	// Changement ou creation, on regenere
	// Verifier les eventuels doublons et mettre a jour
	$lock = "url $type $id_objet";
	spip_get_lock($lock, 10);

	// Eviter de tamponner les URLs a l'ancienne (cas d'un article
	// intitule "auteur2")
	if ($type == 'article'
	AND preg_match(',^(article|breve|rubrique|mot|auteur)[0-9]+$,', $url))
		$url = $url.','.$id_objet;

	// store est ce qui existe deja pour l'objet, si pas vide c'est qu'on change
	$version = $store ? $store['version'] + 1 : 0;

	// Mettre a jour l'url
	// Inserer la nouvelle reference absolue
	if ($prefix) {
		_store_url($url_abs, $type, $id_objet, '', $version++);
	}

	$q_url = _q($url);

	// url deja utilisee ?
	if (($result = spip_query('SELECT type, id_objet from spip_urls WHERE url=' . $q_url))
		&& ($deja = spip_fetch_array($result))) {
		// utilisee par un type prioritaire ? ==> que l'url absolue
		if ($priotype[$deja['type']] < $priotype[$type]) {
			$q_url = _q($url = $url_abs);
		} elseif ($priotype[$deja['type']] > $priotype[$type]) {
			// on ecrase l'url non prioritaire (on ne la connaitra plus, dommage)
			spip_query("UPDATE spip_urls SET type=$q_type, id_objet=$id_objet,
				version=$version, maj=NOW() WHERE url=$q_url");
		} else {
			// 2 objets du meme type ont la meme url
			$result = spip_query('SELECT COUNT(*) from spip_urls WHERE url LIKE ' .
					_q($url . ',%'));
			$compte = spip_fetch_array($result, SPIP_NUM);
			$compte = $compte[0] + 1;
			$url = $url . ',' . $compte;
			$q_url = _q($url);
			spip_query("INSERT INTO spip_urls (url, type, id_objet, version, maj)
				VALUE ($q_url, $q_type, $id_objet, $version, NOW())");
		}
	} else {
		spip_query("INSERT INTO spip_urls (url, type, id_objet, version, maj)
			VALUE ($q_url, $q_type, $id_objet, $version, NOW())");
	}

	// Mettre a jour dans la table objet ?
	spip_query("UPDATE $table SET url_propre=$q_url WHERE $col_id=$id_objet");

	spip_release_lock($lock);

	spip_log("Creation de l'url propre '$url' pour $col_id=$id_objet", 'urls');

	return finir_url_libre_dist($url, $opt);
}

// calcul a partir de $url et $opt
function finir_url_libre_calcul($url, $opt)
{
	return ($url = ($url ?
			  (isset($opt['qs']) ? $opt['qs'] : _qs_urls_libres)
			. (isset($opt['debut']) ? $opt['debut'] : _debut_urls_libres)
			. $url
			. (isset($opt['terminaison']) ? $opt['terminaison'] : _terminaison_urls_libres) 
			: $opt['def']))
		. ($opt['args'] ?
			(((strpos($url, '?') === false) ? '?' : '&') . $opt['args']) : '')
		. ($opt['ancre'] ? '#'. $opt['ancre'] : '');
}

// le traitement final
function finir_url_libre_dist($url, $opt = array(
			// defaut si $url est vide
			'def' =>			'',
			// a rajouter en marque query string ("./?")
			'qs' =>				null,
			// avant $url
			'debut' =>			null,
			// apres $url
			'terminaison' =>	null,
			// arguments (GET) optionnels, ne pas commencer par ? ni &
			'args' => 			'',
			// l'ancre finale dans la page sans le #
			'ancre' => 			''
			))
{
	// ce qu'on rend sans custom mais qu'on lui soumet au cas ou
	$opt['dist'] = finir_url_libre_calcul($url, $opt);
	// finir_url_libre redefinie ?
	if (function_exists('finir_url_libre')) {
		// custom ne dit rien ... mais peut changer $url ou $opt
		if (is_null($custom = finir_url_libre($url, $opt))) {
			// on recalcule donc
			return finir_url_libre_calcul($url, $opt);
		}
		return $custom;
	}
	// pas de custom, rien ne change
	return $opt['dist'];
}

// http://doc.spip.org/@generer_url_article
function generer_url_article($id_article, $args='', $ancre='') {
	return _generer_url_libre('article', $id_article, '',
				array('args' => $args, 'ancre' => $ancre,
				 'def' =>  get_spip_script('./')."?page=article&id_article=$id_article"));
}

// http://doc.spip.org/@generer_url_rubrique
function generer_url_rubrique($id_rubrique, $args='', $ancre='') {
	return _generer_url_libre('rubrique', $id_rubrique, '-',
				array('args' => $args, 'ancre' => $ancre,
				 'def' =>  get_spip_script('./')."?page=rubrique&id_rubrique=$id_rubrique"));
}

// http://doc.spip.org/@generer_url_breve
function generer_url_breve($id_breve, $args='', $ancre='') {
	return _generer_url_libre('breve', $id_breve, '+',
				array('args' => $args, 'ancre' => $ancre,
				 'def' =>  get_spip_script('./')."?page=breve&id_breve=$id_breve"));
}

// C'est special pour les forums, generer_url_forum_dist()
// retourne generer_url_xxx($id)."#forum$id_forum"
// http://doc.spip.org/@generer_url_forum
function generer_url_forum($id_forum, $args='', $ancre='', $show_thread=false) {
	include_spip('inc/forum');
	return generer_url_forum_dist($id_forum, $show_thread);
}

// http://doc.spip.org/@generer_url_mot
function generer_url_mot($id_mot, $args='', $ancre='') {
	return _generer_url_libre('mot', $id_mot, '+-',
				array('args' => $args, 'ancre' => $ancre,
				 'def' =>  get_spip_script('./')."?page=mot&id_mot=$id_mot"));
}

// http://doc.spip.org/@generer_url_auteur
function generer_url_auteur($id_auteur, $args='', $ancre='') {
	return _generer_url_libre('auteur', $id_auteur, '_',
				array('args' => $args, 'ancre' => $ancre,
				 'def' =>  get_spip_script('./')."?page=auteur&id_auteur=$id_auteur"));
}

// http://doc.spip.org/@generer_url_site
function generer_url_site($id_syndic, $args='', $ancre='') {
	return _generer_url_libre('site', $id_syndic, '@',
				array('args' => $args, 'ancre' => $ancre,
				 'def' =>  get_spip_script('./')."?page=site&id_syndic=$id_syndic"));
}

// http://doc.spip.org/@generer_url_document
function generer_url_document($id_document, $args='', $ancre='') {
	if (($id_document = intval($id_document)) <= 0)
		return '';
	if (($GLOBALS['meta']["creer_htaccess"]) == 'oui')
	  return generer_url_action('autoriser',"arg=$id_document", true);
	$row = @spip_fetch_array(spip_query("SELECT fichier FROM spip_documents WHERE id_document = $id_document"));
	if ($row) return ($row['fichier']);
	return '';
}
?>
