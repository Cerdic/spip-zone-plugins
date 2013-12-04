<?php
/**
 * Plugin Séminaires (Squelette)
 * Licence GNU/GPL
 * 
 * @package SPIP\Seminaires_skel\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function seminaire_skel_insert_head_css($flux) {
	$css = find_in_path('styles/calendrier-seminaire.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}

function seminaire_skel_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'formulaires/configurer_seminaire'){
	$ajout = recuperer_fond('inc/choix_couleur');
	$flux['data'] = preg_replace('%(<!--extra-->)%is', '<ul class="champs_extras">'.$ajout.'</ul>'."\n".'$1', $flux['data']);	}
	return $flux;
}
?>