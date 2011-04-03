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

// Fusion des informations de chaque balise plugin en considerant la compatibilite SPIP
function plugins_fusion_plugin($plugins) {
	$fusion = array();

	// Version temporaire pour obtenir un plugin fonctionnel
	// => A modifier
	$fusion = $plugins[0];
	
	return $fusion;
}

?>
