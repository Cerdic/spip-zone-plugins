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

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/acces');
include_spip('inc/texte');
include_spip('inc/forum');
include_spip('balise/formulaire_forum');

// http://doc.spip.org/@balise_FORMULAIRE_FORUM
function balise_FORMULAIRE_ANNOTATIONS ($p) {

	$p = calculer_balise_dynamique($p,'FORMULAIRE_ANNOTATIONS', array('id_rubrique', 'id_forum', 'id_article', 'id_breve', 'id_syndic', 'ajouter_mot', 'ajouter_groupe', 'afficher_texte'));

	// Ajouter le code d'invalideur specifique aux forums
	include_spip('inc/invalideur');
	if (function_exists($i = 'code_invalideur_forums'))
		$p->code = $i($p, $p->code);

	return $p;
}

// http://doc.spip.org/@balise_FORMULAIRE_FORUM_stat
function balise_FORMULAIRE_ANNOTATIONS_stat($args, $filtres) {

	return balise_FORMULAIRE_FORUM_stat($args, $filtres);
	
}

?>