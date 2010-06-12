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
include_spip('action/editer_rubrique');

/**
 * Interface C(r)UD
 */
function rubriques_create($dummy,$set=null){
	if ($id = insert_rubrique($set['id_parent']))
		list($e,$ok) = revisions_rubriques($id,$set);
	else
		$e = _L('create error');
	return array($id,$ok,$e);
}
function rubriques_update($id,$set=null){
	revisions_rubriques($id,$set);
	return array($id,$ok,$e);
}
function rubriques_delete($id){
	// que fait on ici ? suppression sans precaution ?
	return array($id,$ok,$e);
}

?>