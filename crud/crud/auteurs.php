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
	if (autoriser('voir','auteur') AND ($id = insert_auteur($set['source'])))
		list($e,$ok) = auteurs_set($id,$set);
	else
		$e = _T('crud:erreur_creation',array('objet'=>'auteur'));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_auteurs_update_dist($id,$set=null){
	$id_auteur = sql_getfetsel('id_auteur','spip_auteurs','id_auteur='.intval($id));
	if($id_auteur && autoriser('modifier','auteur',$id)){
		list($e,$ok) = auteurs_set($id,$set);
	}else if(!$id_auteur){
		$e = _T('crud:erreur_objet_inexistant',array('objet'=>'auteur','id_objet'=>$id));
	}else{
		$e = _T('crud:erreur_update',array('objet'=>'auteur','id'=>$id));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_auteurs_delete_dist($id){
	if(autoriser('modifier','auteur',$id)){
		list($e,$ok) = auteurs_set($id,array('statut'=>'5poubelle'));
	}else{
		$e = _T('crud:erreur_suppression',array('objet'=>'auteur','id_objet'=>$id));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}



?>