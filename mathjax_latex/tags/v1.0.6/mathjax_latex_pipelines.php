<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function mathjax_latex_insert_head($flux){
	$flux .= mathjax_latex_flux();
	return $flux;
}

function mathjax_latex_header_prive($flux){
	$flux .= mathjax_latex_flux();
	return $flux;
}
	
function mathjax_latex_flux(){

    // doc http://docs.mathjax.org/en/latest/options/CommonHTML.html

	$flux = '<script type="text/x-mathjax-config">';
	$flux .= "MathJax.Hub.Config({";
	$flux .= "tex2jax: {inlineMath: [['$$$','$$$']]},";
	$flux .= "CommonHTML: {scale:90},";
	$flux .= "});";
	$flux .= '</script>';

	// chargement MathJax.js en local sinon depuis le CDN
	if (find_in_path('js/MathJax.js')) {
		$js_lib = find_in_path('js/MathJax.js');
	} else {
		$js_lib = "https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js";
	}

	$flux .= '<script type="text/javascript" async  src="'.$js_lib.'?config=TeX-MML-AM_CHTML"></script>';

	// noisette js pour la compatibilité avec AJAX de SPIP
	$js_refresh = find_in_path('js/refresh_math_ml.js');
	$flux .= '<script type="text/javascript" src="'.$js_refresh.'"></script>';


	return $flux;
}


