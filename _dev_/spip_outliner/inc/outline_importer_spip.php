<?php

function importer_rubrique($id_form,$id_parent,$niveau){
	// les articles de la rubrique
	$res = spip_query("SELECT id_article, titre FROM spip_articles WHERE id_rubrique="._q($id_parent));
	while ($row = spip_fetch_array($res)){
		$c=array('select_1'=>"select_1_$niveau",'ligne_1'=>$row['titre']);
		list($id_donnee,$erreur) = Forms_creer_donnee($id_form,$c);
		if ($id_donnee)
			spip_query("INSERT INTO spip_forms_donnees_articles (id_donnee,id_article) VALUES ("._q($id_donnee).","._q($row['id_article']));
	}
	// les rubriques filles
	$res = spip_query("SELECT id_rubrique, titre FROM spip_rubriques WHERE id_parent="._q($id_parent));
	while ($row = spip_fetch_array($res)){
		$c=array('select_1'=>"select_1_$niveau",'ligne_1'=>$row['titre']);
		list($id_donnee,$erreur) = Forms_creer_donnee($id_form,$c);
		if ($id_donnee)
			spip_query("INSERT INTO spip_forms_donnees_rubriques (id_donnee,id_rubrique) VALUES ("._q($id_donnee).","._q($row['id_rubrique']));
		importer_rubrique($id_form,$row['id_rubrique'],$niveau+1);
	}
}

function inc_outline_importer_spip(){
	$titre = $GLOBALS['meta']['nom_site'];
	$descriptif = _L('Recette du site');
	
	include_spip('base/forms_api');
	$f = find_in_path('base/Outliner_Recette.xml');
	$id_form = Forms_creer_table($f,'outline',false,array('titre'=>$titre,'descriptif'=>$descriptif));

	importer_rubrique($id_form,0,1);
}


?>