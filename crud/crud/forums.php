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


/**
 * Interface C(r)UD
 */
function crud_forums_create_dist($dummy,$set=null){
	$id = sql_insertq('spip_forum',$set);
	if (!$id)
		$e = _L('message_erreur');
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_forums_update_dist($id,$set=null){
	if (sql_getfetsel('id_forum','spip_forum','id_forum='.$id)==$id){
		sql_updateq('spip_forum',$set,'id_forum='.$id);
		return array('success'=>true,'message'=>$ok,'result'=>array('id'=>$id));
	}
	else{
		return array('success'=>false,'message'=>_T('forum inexistant'),'result'=>array('id'=>$id));
	}
}
function crud_forums_delete_dist($id){
	if (sql_updateq('spip_forum',array('statut'=>'off'),'id_forum='.$id))
		return array('success'=>true,'message'=>$ok,'result'=>array('id'=>$id));
	else
		return array('success'=>false,'message'=>sql_error(),'result'=>array('id'=>$id));
}

?>