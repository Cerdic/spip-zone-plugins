<?php

function opml2tree($id_form,$id_parent,&$arbre,&$trans){
	$id_enfant = 0;
	if(is_array($arbre) && count($arbre))
		foreach($arbre as $tag=>$sousarbre){
			list($tagname,$attributs) = spip_xml_decompose_tag($tag);
			$c=array();
			foreach($attributs as $col=>$v){
				if (!isset($trans[$col]))
					$trans[$col]=Forms_creer_champ($id_form,'texte',$col,array('public'=>'oui'));
				$c[$trans[$col]] = $v;
			}
			list($id_enfant,$erreur) = $id_parent?Forms_arbre_inserer_donnee($id_form,$id_parent,'fils_cadet',$c):Forms_arbre_inserer_donnee($id_form,$id_enfant,'petit_frere',$c);
			if ($id_enfant)
				foreach($sousarbre as $opmls)
					opml2tree($id_form,$id_enfant,$opmls,$trans);
		}
}

function importer_rubrique($id_rubrique,$id_form,$id_parent){
	$id_enfant = 0;
	// les articles de la rubrique
	$res = spip_query("SELECT id_article, titre FROM spip_articles WHERE id_rubrique="._q($id_rubrique));
	while ($row = spip_fetch_array($res)){
		$c=array('ligne_1'=>$row['titre']);
		list($id_enfant,$erreur) = $id_parent?Forms_arbre_inserer_donnee($id_form,$id_parent,'fils_cadet',$c):Forms_arbre_inserer_donnee($id_form,$id_enfant,'petit_frere',$c);
		if ($id_enfant)
			spip_query("INSERT INTO spip_forms_donnees_articles (id_donnee,id_article) VALUES ("._q($id_enfant).","._q($row['id_article']).")");
	}
	// les rubriques filles
	$res = spip_query("SELECT id_rubrique, titre FROM spip_rubriques WHERE id_parent="._q($id_rubrique));
	while ($row = spip_fetch_array($res)){
		$c=array('ligne_1'=>$row['titre']);
		list($id_enfant,$erreur) = $id_parent?Forms_arbre_inserer_donnee($id_form,$id_parent,'fils_cadet',$c):Forms_arbre_inserer_donnee($id_form,$id_enfant,'petit_frere',$c);
		if ($id_enfant)
			spip_query("INSERT INTO spip_forms_donnees_rubriques (id_donnee,id_rubrique) VALUES ("._q($id_enfant).","._q($row['id_rubrique']).")");
		importer_rubrique($row['id_rubrique'],$id_form,$id_enfant);
	}
}

function inc_outline_importer_spip(){
	$titre = $GLOBALS['meta']['nom_site'];
	$descriptif = _L('Recette du site');
	
	include_spip('base/forms_api');
	$f = find_in_path('base/Outliner_Recette.xml');
	$id_form = Forms_creer_table($f,'outline',false,array('titre'=>$titre,'descriptif'=>$descriptif));

	importer_rubrique(0,$id_form,0);
}


?>