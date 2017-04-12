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

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation


// http://code.spip.net/@balise_FORMULAIRE_RECHERCHEWALMA
function balise_FORMULAIRE_RECHERCHEWALMA ($p) 
{
	return calculer_balise_dynamique($p, 'FORMULAIRE_RECHERCHEWALMA', array());
}

// http://code.spip.net/@balise_FORMULAIRE_RECHERCHEWALMA_stat
function balise_FORMULAIRE_RECHERCHEWALMA_stat($args, $filtres) {
	// Si le moteur n'est pas active, pas de balise
	if ($GLOBALS['meta']["activer_moteur"] != "oui")
		return '';

	// filtres[0] doit etre un script (a revoir)
	else
	  return array($filtres[0], $args[0]);
}
 
// http://code.spip.net/@balise_FORMULAIRE_RECHERCHEWALMA_dyn
function balise_FORMULAIRE_RECHERCHEWALMA_dyn($lien, $rech) {

	if ($GLOBALS['spip_lang'] != $GLOBALS['meta']['langue_site'])
		$lang = $GLOBALS['spip_lang'];
	else
		$lang='';

	return array('formulaires/recherchewalma', 3600, 
		array(
			'lien' => ($lien ? $lien : generer_url_public($lien)),
			'recherche' => _request('recherche'),
			'lang' => $lang
		));
}

?>
