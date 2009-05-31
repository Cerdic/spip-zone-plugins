<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('balise/formulaire_profil_etendu');
include_spip('public/assembler'); 
include_spip('inc/lang');
include_spip('inc/headers');

// http://doc.spip.org/@action_inscription
function action_inscription() {

	utiliser_langue_visiteur();
	http_no_cache();

	echo _DOCTYPE_ECRIRE,
		html_lang_attributes(),
		'<head><title>',
		_T('pass_vousinscrire'), 
		'</title>',
		'<link rel="stylesheet" type="text/css" href="',
		find_in_path('spip_style.css'),
		'"></head><body>';

	inclure_balise_dynamique(balise_FORMULAIRE_PROFIL_ETENDU_dyn());
	echo "</body></html>";
}

?>
