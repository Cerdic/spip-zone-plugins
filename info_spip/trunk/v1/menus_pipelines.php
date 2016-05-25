<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function menus_header_prive($flux){
	$css = find_in_path('css/menuspip.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}

function menus_pre_boucle($boucle){
	if ($boucle->type_requete == 'menus') {
		$id_table = $boucle->id_table;
		$id_menus_entree = "$id_table.id_menus_entree";
		if (!isset($boucle->modificateur['criteres']['id_menus_entree']) and !isset($boucle->modificateur['criteres']['id_menu']) and !isset($boucle->modificateur['criteres']['identifiant'])){
			$boucle->where[] = array(sql_quote('='), sql_quote($id_menus_entree), 0);
		}
	}
	return $boucle;
}

function menus_menus_lister_disponibles($flux){
	return $flux;
}

function menus_declarer_url_objets($array){
    $array[] = 'menu';
    return $array;
}
?>
