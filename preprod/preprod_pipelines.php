<?php
/**
 * Plugin PreProd pour Spip 2.0
 * Licence GPL (c) 2011 - Ateliers CYM
 */

function preprod_insert_head($flux)
{
	include_spip('inc/autoriser');

	if (autoriser('configurer') || 9070==$GLOBALS['visiteur_session']['id_auteur']) {
		$js = find_in_path("preprod.js");
		if ($js)
			$flux .= '<script type="application/javascript" src="'. $js .'"></script>';
	}
    return $flux;	
}

function preprod_affichage_final($texte)
{
	include_spip('inc/autoriser');
	if (autoriser('configurer') || 9070==$GLOBALS['visiteur_session']['id_auteur']) {
		$self = self();
		$ajout_preprod = recuperer_fond('inclure/inc-boite-preprod',array('preprod_url' => $self));
		$texte = str_replace('</body>', $ajout_preprod.'</body>', $texte);
	}
    return $texte;	
}

function preprod_insert_head_css($flux)
{
    $css = find_in_path('preprod.css');
	if ($css)
    	$flux .= '<link rel="stylesheet" type="text/css" media="all" href="'.$css.'" />';
    return $flux;	
}

?>