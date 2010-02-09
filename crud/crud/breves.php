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
include_spip('action/editer_breve');


/**
 * Interface C(r)UD
 */
function breves_create($dummy,$set=null){
	if ($id = insert_breve($set['id_rubrique']))
		list($e,$ok) = revisions_breves($id,$set);
	else
		$e = _L('create error');
	return array($id,$ok,$e);
}
function breves_update($id,$set=null){
	list($e,$ok) = revisions_breves($id,$set);
	return array($id,$ok,$e);
}
function breves_delete($id){
	list($e,$ok) = revisions_breves($id,array('statut'=>'poubelle'));
	return array($id,$ok,$e);
}

?>