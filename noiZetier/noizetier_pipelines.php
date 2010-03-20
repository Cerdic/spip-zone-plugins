<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function noizetier_header_prive($flux){
	$css = find_in_path('css/noizetier.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}


/**
 * Pipeline recuperer_fond pour ajouter les noisettes
 *
 * @param array $flux
 * @return array
 */
function noizetier_recuperer_fond($flux){
	include_spip('inc/noizetier');
	$fond = $flux['args']['fond'];
	$composition = $flux['args']['contexte']['composition'];
	// Si une composition est définie et si elle n'est pas déjà dans le fond, on l'ajoute au fond
	// sauf s'il s'agit d'une page de type page (les squelettes page.html assurant la redirection)
	if ($composition!='' AND noizetier_page_composition($fond)=='' AND noizetier_page_type($fond)!='page')
		$fond .= '-'.$composition;
	
	if (in_array($fond,noizetier_lister_blocs_avec_noisettes())) {
		$contexte = $flux['data']['contexte'];
		$contexte['bloc'] = substr($fond,0,strpos($fond,'/'));
		$complements = recuperer_fond('noizetier-generer-bloc',$contexte,array('raw'=>true));
		$flux['data']['texte'] .= $complements['texte'];
	}
	
	return $flux;
}

/**
 * Pipeline compositions_lister_disponibles pour ajouter les compositions du noizetier
 *
 * @param array $flux
 * @return array
 */

function noizetier_compositions_lister_disponibles($flux){
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (!is_array($noizetier_compositions))
		$noizetier_compositions = array();
	$type = $flux['args']['type'];
	$informer = $flux['args']['informer'];
	
	include_spip('inc/texte');
	foreach($noizetier_compositions as $t => $compos_type)
		foreach($compos_type as $c => $info_compo) {
			if($informer) {
				$noizetier_compositions[$t][$c]['nom'] = typo($info_compo['nom']);
				$noizetier_compositions[$t][$c]['description'] = propre($info_compo['description']);
				$noizetier_compositions[$t][$c]['icon'] = $info_compo['icon']!='' ? find_in_path($info_compo['icon']) : '';
			}
			else
				$noizetier_compositions[$t][$c] = 1;
			}
	
	if ($type=='') {
		if (!is_array($flux['data']))
			$flux['data'] = array();
		$flux['data'] = array_merge($flux['data'],$noizetier_compositions);
	}
	else {
		if (!is_array($flux['data'][$type]))
			$flux['data'][$type] = array();
		$flux['data'][$type] = array_merge($flux['data'][$type],$noizetier_compositions[$type]);
	}
	return $flux['data'];
}
?>
