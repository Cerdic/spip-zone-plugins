<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/headers');

// acces aux documents joints securise
// verifie soit que le demandeur est authentifie
// soit que le document est publie, c'est-a-dire
// joint a au moins 1 article, breve ou rubrique publie

// https://code.spip.net/@action_acceder_document_dist
function action_acceder_document_dist() {

	// $file exige pour eviter le scan id_document par id_document
	$f = rawurldecode(_request('file'));
	$arg = rawurldecode(_request('arg'));
	$cle = _request('cle');

	$api_docrestreint = charger_fonction('api_docrestreint', 'action');
	return $api_docrestreint("$arg/$cle/$f");

}
