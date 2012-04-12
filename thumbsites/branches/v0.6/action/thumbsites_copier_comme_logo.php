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

function action_thumbsites_copier_comme_logo_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$arg = explode('--',$arg);
	
	spip_log("action_thumbsites_copier_comme_logo_dist file $arg[2]");
	@rename($arg[2], _DIR_IMG . 'siteon'.$arg[0].'.'.pathinfo($arg[2],PATHINFO_EXTENSION));
	
	return(false);
}

?>
