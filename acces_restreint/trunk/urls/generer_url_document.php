<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Generer l'url d'un document dans l'espace public,
 * fonction du statut du document
 *
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @param string $public
 * @param string $connect
 * @return string
 *
 * http://doc.spip.org/@generer_url_ecrire_document
 */
function urls_generer_url_document_dist($id, $args = '', $ancre = '', $public = null, $connect = '') {
	include_spip('inc/autoriser');
	include_spip('inc/documents');

	if (!autoriser('voir', 'document', $id)) {
		return '';
	}

	$res = sql_fetsel('fichier,distant,extension', 'spip_documents', 'id_document='.intval($id));

	if (!$res) {
		return '';
	}

	$f = $res['fichier'];

	if ($res['distant'] == 'oui') {
		return $f;
	}

	// Si droit de voir tous les docs, pas seulement celui-ci
	// il est inutilement couteux de rajouter une protection
	$r = (autoriser('voir', 'document'));
	if (($r and $r !== 'htaccess')) {
		return get_spip_doc($f);
	}

	include_spip('inc/securiser_action');

	// cette url doit etre publique !
	$cle = calculer_cle_action($id.','.$f);

	// renvoyer une url plus ou moins jolie
	if (isset($GLOBALS['meta']['creer_htaccess']) and $GLOBALS['meta']['creer_htaccess']) {
		$url = url_absolue(_DIR_RACINE."docrestreint.api/$id/$cle/$f");
	} else {
		$url = get_spip_doc($f)."?$id/$cle";
	}

	// En absolue afin que les filtres d'image puissent agir sur les documents
	// dû au paramètre d'URL ou au manque d'extension
	if (in_array($res['extension'], array('jpg','png','gif'))) {
		$url = url_absolue($url);
	}
	return $url;
}
