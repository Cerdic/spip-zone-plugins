<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Fonctions reprise du plugin Mes favoris de Olivier Sallou, Cedric Morin.
 */


function alertes_supprimer($paires){
	if (count($paires)){
		$cond = array();
		foreach($paires as $k=>$v)
				$cond[] = "$k=".sql_quote($v);
		$cond = implode(' AND ',$cond);
		$res = sql_select('id_alerte,objet,id_objet,id_auteur','spip_alertes',$cond);
		include_spip('inc/invalideur');
		while ($row = sql_fetch($res)){
			sql_delete("spip_alertes","id_alerte=".intval($row['id_alerte']));
			suivre_invalideur("alerte/".$row['objet']."/".$row['id_objet']);
			suivre_invalideur("alerte/auteur/".$row['id_auteur']);
		}
	}
}

function alertes_ajouter($id_objet,$objet,$id_auteur){
	if ($id_auteur
		AND $id_objet = intval($id_objet)
		AND preg_match(",^\w+$,",$objet)){

		if (!alertes_trouver($id_objet,$objet,$id_auteur)){
			sql_insertq("spip_alertes",array('id_auteur'=>$id_auteur,'id_objet'=>$id_objet,'objet'=>$objet));
			include_spip('inc/invalideur');
			suivre_invalideur("alerte/$objet/$id_objet");
			suivre_invalideur("alerte/auteur/$id_auteur");
		}
	}
	else
		spip_log("erreur ajouter alerte $id_objet-$objet-$id_auteur");
}

function alertes_trouver($id_objet,$objet,$id_auteur){
	$row = false;
	if ($id_auteur=intval($id_auteur)
		AND $id_objet = intval($id_objet)
		AND preg_match(",^\w+$,",$objet)){
		$row = sql_fetsel("*","spip_alertes","id_auteur=".intval($id_auteur)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($objet));
	}
	return $row;
}

?>