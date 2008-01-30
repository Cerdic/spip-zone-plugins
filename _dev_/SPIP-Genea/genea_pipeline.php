<?php
/*	*********************************************************************
	*
	* Copyright (c) 2006
	* Xavier Burot
	*
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	*
	*********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// --
function genea_ajouter_boutons($boutons_admin){
	global $connect_statut, $connect_toutes_rubriques;
	// si on est admin
	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques){
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['genea_naviguer']= new Bouton(
			url_absolue(find_in_path('/img_pack/arbre-24.png')),  // icone
			'genea:titre' //titre
			);
	}
	return $boutons_admin;
}

function genea_calculer_rubriques(){
	global $table_prefix;
	// Publier et dater les rubriques qui ont un arbre genealogique
	$r = spip_query("SELECT rub.id_rubrique AS id,
	FROM ".$table_prefix."_rubriques AS rub, ".$table_prefix."_genea AS fille
	WHERE rub.id_rubrique = fille.id_rubrique
//	AND rub.date_tmp <= fille.date_heure AND fille.statut='publie'
	GROUP BY rub.id_rubrique");
	while ($row = spip_fetch_array($r))
		spip_query("UPDATE spip_rubriques
		SET statut_tmp='publie',
		WHERE id_rubrique=".$row['id']);
}
?>