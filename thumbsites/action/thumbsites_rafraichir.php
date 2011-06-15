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

function action_thumbsites_rafraichir_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$arg = explode('--',$arg);

	include_spip("inc/thumbsites_filtres");
	$retour = thumbshot($arg[1],true) ;
	spip_log("action_thumbsites_rafraichir_dist $arg[0] $arg[1] - retour $retour","thumbsites");
	return($retour);
}

?>
