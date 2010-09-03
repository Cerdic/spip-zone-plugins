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
include_spip('action/editer_auteur');

/**
 * Interface C(r)UD
 */
function crud_auteurs_create_dist($dummy,$set = null){
	if ($id = insert_auteur($set['source']))
		list($e,$ok) = auteurs_set($id,$set);
	else
		$e = _L('create error');
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_auteurs_update_dist($id,$set=null){
	list($e,$ok) = auteurs_set($id,$set);
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_auteurs_delete_dist($id){
	list($e,$ok) = auteurs_set($id,array('statut'=>'5poubelle'));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}



?>