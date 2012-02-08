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
function crud_rubriques_create_dist($dummy,$set=null){
	if (autoriser('creerrubriquedans','rubrique',$set['id_parent']?$set['id_parent']:0) && ($id = insert_rubrique($set['id_parent'])))
		list($e,$ok) = revisions_rubriques($id,$set);
	else
		$e = _T('crud:erreur_creation',array('objet'=>'rubrique'));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_rubriques_update_dist($id,$set=null){
	if(autoriser('modifier','rubrique',$id)){
		list($e,$ok) = revisions_rubriques($id,$set);
	}else{
		$e = _T('crud:erreur_update',array('objet'=>'rubrique','id_objet'=>$id));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_rubriques_delete_dist($id){
	// que fait on ici ? suppression sans precaution ?
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>