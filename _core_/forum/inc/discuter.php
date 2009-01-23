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

// http://doc.spip.org/@inc_discuter_dist
function inc_discuter_dist($id, $script, $objet, $statut='prive', $debut=NULL, $pas=NULL, $id_parent = 0)
{
	if ($GLOBALS['meta']['forum_prive_objets'] == 'non')
		return '';

	// provisoire, en attendant le refactoring des scripts appelants !
	$contexte = $_GET;
	
	return recuperer_fond('',
	  array_merge(
	  $contexte,
	  array(
	  	'type'=>'interne',
	  	'statut'=>$statut,
	    'fond'=>'prive/discuter_forum')
	  ),array('ajax'=>true));

}
?>