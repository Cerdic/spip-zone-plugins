<?php

function notation_header_prive($flux){
	$flux = notation_insert_head_css($flux);
	return $flux;
}

function notation_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/notation.v2.css').'" type="text/css" media="all" />';
	return $flux;
}

function notation_affichage_final($flux){
	if (strpos($flux, "'notation_note notation_note_on_load'") === false)
		return $flux;
	$incHead = "";
	$incHead .= "<script src='".find_in_path('javascript/jquery.MetaData.js')."' type='text/javascript'></script>\n";
	$incHead .= "<script src='".find_in_path('javascript/jquery.rating.js')."' type='text/javascript'></script>\n";
	$incHead .= "<script src='".find_in_path('javascript/notation.js')."' type='text/javascript'></script>\n";
	include_spip('inc/filtres');
	if(function_exists('compacte_head')){
		$incHead = compacte_head($incHead);
	}
	return substr_replace($flux, $incHead, strpos($flux, '</body>'), 0);
}

/**
 * Boite de configuration des objets articles
 *
 * @param array $flux
 * @return array
 */
function notation_afficher_config_objet($flux){
	if (($type = $flux['args']['type'])
		AND $id = $flux['args']['id']){
		if (autoriser('moderernote', $type, $id)) {
			$id_table_objet = id_table_objet($type);
			$flux['data'] .= recuperer_fond("prive/configurer/configurer_note",array('id_objet'=>$id,'objet'=>  objet_type(table_objet($type))));
		}
	}
	return $flux;
}

/**
 * Remplissage des champs a la creation d'objet
 *
 * @param array $flux
 * @return array
 */
function notation_pre_insertion($flux){
	if ($flux['args']['table']=='spip_articles'){
		$flux['args']['data']['accepter_note'] = substr($GLOBALS['meta']['notations_public'],0,3);
	}
	return $flux;
}

/**
 * Definir les meta de configuration liee aux notations
 *
 * @param array $metas
 * @return array
 */
function notation_configurer_liste_metas($metas){
	$metas['notations_publics'] = 'oui';

	return $metas;
}
?>