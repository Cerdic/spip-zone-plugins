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

if (!defined('_ECRIRE_INC_VERSION')) return;

// http://doc.spip.org/@exec_admin_plugin_dist
function exec_admin_plugin_dist() {

	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
	}

	// on fait la verif du path avant tout,
	// et l'installation des qu'on est dans la colonne principale
	// si jamais la liste des plugins actifs change, il faut faire un refresh du hit
	// pour etre sur que les bons fichiers seront charges lors de l'install
	include_spip('inc/plugin'); 
	$new = actualise_plugins_actifs();
	if ($new AND _request('actualise')<2) {
		$url = parametre_url(self(),'actualise',_request('actualise')+1,'&');
		include_spip('inc/headers');
		echo redirige_formulaire($url);
		exit;
	}

	// on lance l'affichage standard Z
	include_spip('exec/fond');
}
