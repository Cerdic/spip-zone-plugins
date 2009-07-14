<?php
/*
 * Plugin xxx
 * (c) 2009 Cedric Morin cedric@yterium.com
 * Distribue sous licence GPL
 *
 */

/**
 * Mettre a jour les parents d'un objet
 *
 * @param int $id_objet
 * @param string $objet
 * @param array $id_parents
 * @param string $serveur
 */
function polyhier_set_parents($id_objet,$objet,$id_parents,$serveur=''){
	if (is_string($id_parents))
		$id_parents = explode(',',$id_parents);
	if (!is_array($id_parents))
		$id_parents = array();

	$id_parents = array_unique($id_parents);

	$where = "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet);
	// supprimer les anciens parents plus utilises
	sql_delete("spip_rubriques_liens","$where AND ".sql_in('id_parent',$id_parents,"NOT",$serveur),$serveur);

	// selectionner l'intersection entre base et tableau
	$restants = sql_allfetsel("id_parent","spip_rubriques_liens","$where AND ".sql_in('id_parent',$id_parents,"",$serveur),"","","","",$serveur);
	$restants = array_map('reset',$restants);

	$id_parents = array_diff($id_parents,$restants);
	$ins = array();
	foreach($id_parents as $p){
		if ($p)
			$ins[] = array('id_parent'=>$p,'id_objet'=>$id_objet,'objet'=>$objet);
	}
	if (count($ins))
		sql_insertq_multi("spip_rubriques_liens",$ins,"",$serveur);

}

/**
 *
 * @param int $id_objet
 * @param string $objet
 * @param string $serveur
 * @return array
 */
function polyhier_get_parents($id_objet,$objet,$serveur=''){

	$where = "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet);

	// selectionner l'intersection entre base et tableau
	$id_parents = sql_allfetsel("id_parent","spip_rubriques_liens",$where,"","","","",$serveur);
	$id_parents = array_map('reset',$id_parents);

	return $id_parents;
}

?>