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
include_spip('action/editer_mot');


/**
 * Interface C(r)UD
 */
function crud_mots_create_dist($dummy,$set=null){
	$crud = charger_fonction('crud','action');
	if ($id_groupe=intval($set['id_groupe'])
	 AND autoriser('modifier','groupemots',$id_groupe)
	 AND $id = sql_insertq("spip_mots", array('id_groupe' => $id_groupe))){
		$result = $crud('update','mots',$id,$set);
		$ok 	= $result['message'];
		$id		= $result['result']['id'];
		$e		= $result['success'] ? false : true;
	 }
	else{
		$e = _T('crud:erreur_creation',array('objet'=>'mot'));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_mots_update_dist($id,$set=null){
	// modifier le contenu via l'API
	if(autoriser('modifier','mot',$id)){
		include_spip('inc/modifier');
		$c = array();
		foreach (array(
			'titre', 'descriptif', 'texte', 'id_groupe'
		) as $champ)
			$c[$champ] = _request($champ,$set);
	
		revision_mot($id, $c);
	}else{
		$e = _T('crud:erreur_update',array('objet'=>'mot','id_objet'=>$id));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_mots_delete_dist($id){
	if(autoriser('modifier','mot',$id)){
		$ok = sql_delete("spip_mots","id_mot=".intval($id));
	}else{
		$e = _T('crud:erreur_suppression',array('objet'=>'mot','id_objet'=>$id));
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>