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

if (!defined("_ECRIRE_INC_VERSION")) return;

// reaffichage du formulaire d'une option de configuration 
// apres sa modification par appel du script action/configurer 
// redirigeant ici.

// http://doc.spip.org/@exec_configurer_dist
function exec_configurer_dist()
{
	// pour la petite histoire, les formulaires de configurations
	// tres anciens de SPIP utilisent cette fonction aussi, appelee en ajax.
	$configuration = charger_fonction(_request('configuration'), 'configuration', true);
	if ($configuration) {
		ajax_retour($configuration ? $configuration() : 'configure quoi?');
	} else {
		// traitements specifiques pour CFG
		set_request('exec','cfg');
		$exec = charger_fonction('fond');
		$exec();
	}
}
?>
