<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2013 Olivier Sallou, Cedric Morin, Gilles Vincent
 * Distribue sous licence GPL
 *
 */

 if (!defined('_ECRIRE_INC_VERSION')) {
	 return;
 }

/**
 * Supprimer un ensemble de favoris dont on connait les id
 *
 * @param int $id_favori
 */
function mesfavoris_supprimer($paires) {
	if (count($paires)) {
		$cond = array();
		
		foreach($paires as $k=>$v) {
			$cond[] = "$k=" . sql_quote($v);
		}
		$cond = implode(' AND ',$cond);
		
		$res = sql_select('id_favori,categorie,objet,id_objet,id_auteur', 'spip_favoris', $cond);
		
		include_spip('inc/invalideur');
		while ($row = sql_fetch($res)) {
			sql_delete("spip_favoris","id_favori=".intval($row['id_favori']));
			suivre_invalideur("favori/".$row['objet']."/".$row['id_objet']);
			suivre_invalideur("favori/auteur/".$row['id_auteur']);
		}
	}
}

function mesfavoris_ajouter($id_objet, $objet, $id_auteur, $categorie='') {
	if (
		$id_auteur = intval($id_auteur)
		and $id_objet = intval($id_objet)
		and preg_match(",^\w+$,",$objet)
	) {
		if (!mesfavoris_trouver($id_objet, $objet, $id_auteur, $categorie)) {
			sql_insertq(
				'spip_favoris',
				array(
					'id_auteur' => $id_auteur,
					'id_objet'  => $id_objet,
					'categorie' => $categorie,
					'objet'     => $objet
				)
			);
			
			include_spip('inc/invalideur');
			suivre_invalideur("favori/$objet/$id_objet");
			suivre_invalideur("favori/auteur/$id_auteur");
		}
	}
	else {
		spip_log("erreur ajouter favori $id_objet-$objet-$categorie-$id_auteur");
	}
}

function mesfavoris_trouver($id_objet, $objet, $id_auteur, $categorie='') {
	$row = false;
	
	if (
		$id_auteur = intval($id_auteur)
		and $id_objet = intval($id_objet)
		and preg_match(",^\w+$,", $objet)
	) {
		$row = sql_fetsel(
			'*',
			'spip_favoris',
			array(
				'id_auteur = ' . $id_auteur,
				'id_objet = ' . $id_objet,
				'objet = ' . sql_quote($objet),
				'categorie = ' . sql_quote($categorie),
			)
		);
	}
	
	return $row;
}
