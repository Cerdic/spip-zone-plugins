<?php
/**
 * Utilisations de pipelines par Redirection
 *
 * @plugin     Redirection
 * @copyright  2020
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Redirection\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function redirection_affichage_final($page) {
	$texte_redirect = lire_config('redirection/texte_redirect');
	$url_redirect = lire_config('redirection/url_redirect');
	$time_redirect = lire_config('redirection/time_redirect');
	$head = "<div id='redirect_parent'><div id='redirect_enfant'><p>$texte_redirect</p>
	<a href='$url_redirect'>Cliquez ici si cette page n'est pas redirigée automatiquement</a></div></div>";
	$pos_head = strpos($page, '</body>');
	return substr_replace($page, $head, $pos_head, 0);
}

function redirection_insert_head($flux){
	$url_redirect = lire_config('redirection/url_redirect');
	$time_redirect = lire_config('redirection/time_redirect');
	$time = $time_redirect*1000;
	if($url_redirect AND $time_redirect>0){
		$flux .= '<script type="text/javascript">
		  window.setTimeout("location=('."'".$url_redirect."'".');",'.$time.');
		  </script>\n';
    }
	return $flux;
}

function redirection_insert_head_css($flux) {
	$css = find_in_path('css/redirection.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	return $flux;
}