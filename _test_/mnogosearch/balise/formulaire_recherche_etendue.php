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

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation


function balise_FORMULAIRE_RECHERCHE_ETENDUE ($p) 
{
	return calculer_balise_dynamique($p, 'FORMULAIRE_RECHERCHE_ETENDUE', array());
}

function balise_FORMULAIRE_RECHERCHE_ETENDUE_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	if ($GLOBALS['meta']["activer_moteur"] != "oui")
		return '';

	// filtres[0] doit etre un script (a revoir)
	else
	  return array($filtres[0], $args[0]);
}
 
function balise_FORMULAIRE_RECHERCHE_ETENDUE_dyn($lien, $rech) {

	if ($GLOBALS['spip_lang'] != $GLOBALS['meta']['langue_site'])
		$lang = $GLOBALS['spip_lang'];
	else
		$lang='';

	return array('formulaires/formulaire_recherche_etendue', 3600, 
		array(
			'lien' => ($lien ? $lien : generer_url_public('recherche')),
			'recherche' => _request('recherche'),
			'site' => _request('site'),
			'lang' => $lang
		));
}

?>
