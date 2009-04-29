<?php
include_spip('base/abstract_sql');

// Filtres
function n_to_br($texte){
	$texte = str_replace("\n", "<br />", $texte);
	return $texte;
}

function id_pays_to_pays($id_pays){
	if($id_pays != 0){
		$pays = sql_getfetsel('nom', 'spip_geo_pays', 'id_pays ='.$id_pays) ;
		return $pays;
	}
	else return;
}

function inscription2_recuperer_champs($champs,$id_auteur){
	if($champs == 'login'){
		$champs = 'spip_auteurs.login';
	}
	if($champs == 'pays'){
		spip_log('champs = pays');
		$resultat = sql_getfetsel("b.nom","spip_auteurs_elargis a LEFT JOIN spip_geo_pays b on a.pays = b.id_pays","a.id_auteur=$id_auteur");
		return propre($resultat);
	}
	if($champs == 'pays_pro'){
		spip_log('champs = pays_pro');
		$resultat = sql_getfetsel("b.nom","spip_auteurs_elargis a LEFT JOIN spip_geo_pays b on a.pays_pro = b.id_pays","a.id_auteur=$id_auteur");
		return propre($resultat);
	}
	$resultat = sql_getfetsel($champs,"spip_auteurs_elargis LEFT JOIN spip_auteurs USING(id_auteur)","spip_auteurs_elargis.id_auteur=$id_auteur");
	return propre($resultat);
}
?>
