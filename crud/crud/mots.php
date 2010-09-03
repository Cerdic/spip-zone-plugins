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
	if ($id_groupe=intval($set['id_groupe'])
	 AND $id = sql_insertq("spip_mots", array('id_groupe' => $id_groupe)))
		list($id,$ok,$e) = mot_update($id,$set);
	else
		$e = _L('create error');
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_mots_update_dist($id,$set=null){
	// modifier le contenu via l'API
	include_spip('inc/modifier');

	$c = array();
	foreach (array(
		'titre', 'descriptif', 'texte', 'id_groupe'
	) as $champ)
		$c[$champ] = _request($champ,$set);

	revision_mot($id, $c);
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_mots_delete_dist($id){
	$ok = sql_delete("spip_mots","id_mot=".intval($id));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>