<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// afficher a gauche dans edition de rubrique et article (ou plus si defini dans config) un formulaire pour materialicons
function materialicons_affiche_gauche($flux){

	$e = trouver_objet_exec($flux['args']['exec']);
	$table_objet_sql = $e['table_objet_sql'];
	$objets_config = lire_config('materialicons/objets',array());
	if (
		in_array($table_objet_sql,$objets_config) // si configuration objets ok
		AND $e !== false // page d'un objet éditorial
		AND $e['edition'] === false // pas en mode édition
		AND $id_objet=$flux['args'][$e['id_table_objet']]
	){
		$objet = $e['type'];
		$row = sql_fetsel("style,categorie,icone", "spip_materialicons_liens", "objet=".sql_quote($objet)." AND id_objet=".intval($id_objet));
		$style = $row['style'];
		$categorie = $row['categorie'];
		$icone = $row['icone'];
		$svg = '';
		if (isset($icone) AND $icone != '') {
			$svg = file_get_contents( _DIR_PLUGIN_MATERIALICONS ."images/". $style ."/". $categorie ."/". $icone .".svg");
		}
		$contexte = array('objet' => $objet, 'id_objet' => $id_objet, 'style' => $style, 'categorie' => $categorie, 'icone' => $icone, 'svg' => $svg);
		$flux["data"] .= recuperer_fond("inclure/material_icone", $contexte);
	}
	return $flux;
}

function materialicons_header_prive($flux){
    $flux .= '<link rel="stylesheet" href="'. _DIR_PLUGIN_MATERIALICONS .'css/materialicons.css" type="text/css" media="all" />';
    return $flux;
}

function materialicons_insert_head_css($flux){
    $flux .= '<link rel="stylesheet" href="'. _DIR_PLUGIN_MATERIALICONS .'css/materialicons.css" type="text/css" media="all" />';
    return $flux;
}
