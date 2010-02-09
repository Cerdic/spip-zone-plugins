<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('action/editer_site');

/**
 * Interface C(r)UD
 */
function syndic_create($dummy,$set=null){
	if ($id = insert_syndic($set['id_rubrique']))
		list($e,$ok) = revisions_sites($id,$set);
	else
		$e = _L('create error');
	return array($id,$ok,$e);
}
function syndic_update($id,$set=null){
	list($e,$ok) = revisions_sites($id,$set);
	return array($id,$ok,$e);
}
function syndic_delete($id){
	list($e,$ok) = revisions_sites($id,array('statut'=>'poubelle'));
	return array($id,$ok,$e);
}

?>