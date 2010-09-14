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
function crud_documents_create_dist($dummy,$set=null){
	if ($id_document = 'non' AND $set['source'] AND $set['titre']) {
		$chemin = find_in_path($set['source']);
		$f = chercher_filtre('info_plugin');
		// gerer la mediatheque aussi avant son entree dans le core
		if ($f('gestdoc', 'est_actif')) {
			$f = charger_fonction('ajouter_un_document','action');
			$id = $f('non', array('tmp_name' => $chemin, 'name' => basename($chemin)), $set['type'], $set['id_objet'], $set['mode']);
		}
		else {
			$f = charger_fonction('ajouter_documents', 'inc');
			$id = $f($chemin, basename($chemin), $set['type'], $set['id_objet'], $set['mode'], 0, basename($chemin));
		}
		if (intval($id)) {
			$resultat = array($id, true, 'ok');
			$champs = array('titre' => $set['titre']);
			if (isset($set['descriptif']))
				$champs['descriptif'] = $set['descriptif'];
			sql_updateq("spip_documents", $champs, "id_document=".intval($id));
		}
		list($id,$ok,$e) = $resultat;
	}
	else
		$e = _L('create error');
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_documents_update_dist($id,$set=null){
	$ok = sql_updateq("spip_documents", $set, "id_document=".intval($id));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_documents_delete_dist($id){
	$ok = sql_delete("spip_documents","id_document=".intval($id));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>