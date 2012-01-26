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
include_spip('action/editer_article');

/**
 * Interface C(r)UD
 */
function crud_articles_create_dist($dummy,$set=null){
	$id_rubrique = sql_getfetsel('id_rubrique','spip_rubriques','id_rubrique='.intval($set['id_rubrique']));
	if (($id_rubrique > 0) && autoriser('creerarticledans','rubrique',$set['id_rubrique'],$GLOBALS['visiteur_session']) && ($id = insert_article($set['id_rubrique'])))
		list($e,$ok) = articles_set($id,$set);
	else if(!$id_rubrique){
		$e = _T('crud:erreur_rubrique_inconnue',array('id'=>$set['id_rubrique']));
	}else{
		$e = _T('crud:erreur_creation',array('objet'=>'article'));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_articles_update_dist($id,$set=null){
	$id_article = sql_getfetsel('id_article','spip_articles','id_article='.intval($id));
	if(!$id_article){
		$e = _T('crud:erreur_article_inconnue',array('id'=>$id));
	}else if(autoriser('modifier','article',$id)){
		list($e,$ok) = articles_set($id,$set);
	}else{
		$e = _T('crud:erreur_update',array('objet'=>'article','id'=>$id));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_articles_delete_dist($id){
	list($e,$ok) = articles_set($id,array('statut'=>'poubelle'));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>