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
	if ($id_document = 'non' AND $set['source']) {
		include_spip('action/editer_document');
		$chemin = $set['source'];
		$f = chercher_filtre('info_plugin');
		// gerer la mediatheque aussi avant son entree dans le core
		if ($f('medias', 'est_actif')) {
			$f = charger_fonction('ajouter_documents','action');
			$id = $f('new', array(array('tmp_name' => $chemin, 'name' => basename($chemin))), $set['type'], $set['id_objet'], $set['mode']);
		}
		else {
			$f = charger_fonction('ajouter_documents', 'inc');
			$id = $f($chemin, basename($chemin), $set['type'], $set['id_objet'], $set['mode'], 0, basename($chemin));
		}
		if (intval($id = $id[0])) {
			$resultat = array($id, 'ok');
			$champs = array();
			foreach (array('titre', 'descriptif', 'date', 'taille', 'largeur','hauteur','mode','credits','fichier','distant','extension', 'id_vignette') as $champ) {
				if (($set[$champ]) !== null)
					$champs[$champ] = $set[$champ];
			}
			document_set($id, $champs);
		}
		list($id,$ok) = $resultat;
	}
	else
		$e = _L('create error');
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_documents_update_dist($id,$set=null){
	if (include_spip('action/editer_document'))
		$ok = document_set($id, $set);
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}
function crud_documents_delete_dist($id){
	$ok = sql_delete("spip_documents","id_document=".intval($id));
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>