<?php
/**
 * Plugin cornertease
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */

function cornertease_insert_head_css($texte) {
	if(lire_config('cornertease/article')){
		$texte .= "\n".'<link rel="stylesheet" href="'.find_in_path('css/cornertease.css').'" type="text/css" />';
	}
	return $texte;				
}

function cornertease_insert_head($texte) {
	if(lire_config('cornertease/article')){
		$code = recuperer_fond("cornertease", array('id_article' => lire_config('cornertease/article')));
		$texte .= '<script type="text/javascript">/*<!\[CDATA\[*/
						var cornertease_aff = "'.lire_config('cornertease/mode_affichage', 'never').'";
						var cornertease_dur = '.lire_config('cornertease/duree').';
						var cornertease_cont = '.json_encode($code).';
						/*\]\]>*/</script>';
		$texte .= "\n".'<script type="text/javascript" src="'.find_in_path('js/cornertease.js').'"></script>';
	}
	return $texte;				
}
?>