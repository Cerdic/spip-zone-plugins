<?php
// Dotclear à un modèle d'arborescence des catégories fonctionnant sur le mode intervallaires : http://sqlpro.developpez.com/cours/arborescence/

function dot_category_trouver_feuilles($blog_id){
	$feuilles = sql_allfetsel('cat_id,cat_lft,cat_rgt','dc_category',"`cat_rgt` - `cat_lft` = 1 AND `blog_id`=".sql_quote($blog_id));
	return $feuilles;
	
}
function dot_category_trouver_pere($blog_id,$left,$right){
	$pere	= sql_fetsel('cat_id,cat_lft,cat_rgt','dc_category',"`cat_rgt`>".$right." AND `cat_lft` <".$left." AND `blog_id`=".sql_quote($blog_id),'',"`cat_rgt`","0,1");
	return $pere;
}

function dot_category_arbre($blog_id){
	$feuilles = dot_category_trouver_feuilles($blog_id);
	
	$categories[]	= $feuilles;				#on numérote les niveaux par le bas; ici ce sont les categories DC
	$rubriques		= array();
	$niveau			= 0;
	$parents_parcourus = array(); 				#évitons de parcourir plusieur fois le même père
	
	
	while($niveau + 1 == count($categories)){
		foreach($categories[$niveau] as $category){
			$pere		= dot_category_trouver_pere($blog_id,$category['cat_lft'],$category['cat_rgt']);
			if ($pere){
				$rubriques[]= array('cat_id'=>$category['cat_id'],'id_pere'=>$pere['cat_id']);
				if (!in_array($pere['cat_id'],$parents_parcourus)){
					$categories[$niveau+1][]=$pere;
					$parents_parcourus[]=$pere['cat_id'];
				}
			}
			else{
				$rubriques[]= array('cat_id'=>$category['cat_id'],'id_pere'=>'0');
			}
		}	
		$niveau ++;
	}
	return $rubriques;
}
#appelé après dot_category_arbre il transforme le résulta de cette fonction en tableau arborescent
function dot_category_spiper_arbre($arbre){
	$arbre2	= $arbre;
	$arbo	= array();
	$filiation = array();
	$nb_racine = dot_category_spiper_nb_racine($arbre,true);
	$racine	   = dot_category_spiper_nb_racine($arbre);
	foreach($arbre as $branche){
		list($arbre2,$filiation[$branche['cat_id']])	= dot_category_spiper_trouver_fils($arbre2,$branche['cat_id']);
	}
	
	foreach ($racine as $secteur){
		$arbo[$secteur]	= dot_category_spip_ramener_enfants($secteur,$filiation);
	}
	return $arbo;
}

function dot_category_spiper_trouver_fils($arbre,$id_pere){
	foreach ($arbre as $branche){
		if ($branche['id_pere'] == $id_pere){
			$fils[$branche['cat_id']]='';
			}
	}
	return array($arbre,$fils);
}

function dot_category_spiper_nb_racine($arbre,$nob=false){
	$nb = 0;
	$racine = array();
	foreach($arbre as $branche){
		if($branche['id_pere']==0)
			$racine[] = $branche['cat_id'];
			$nb++;
	}
	return $nob ? $nb : $racine;	
}

function dot_category_spip_ramener_enfants($pere,$enfants_potentiels){
	$arbo = '';
	if ($enfants_potentiels[$pere] != ''){
	$arbo = array();
		foreach($enfants_potentiels[$pere] as $pot_id=>$pot_enfant){
			$arbo[$pere][$pot_id]	= dot_category_spip_ramener_enfants($pot_id,$enfants_potentiels);
		}
	
	}
	return $arbo;
}

?>