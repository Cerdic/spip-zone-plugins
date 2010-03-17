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




?>
