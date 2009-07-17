<?php
/**
 * Balise #PIWIK
 * 
 * Au final ne correspond qu'à un inclure mais est plus rapide à écrire
 * et ne casse pas à la compilation si le plugin n'est pas activé
 * 
 * @param object $p
 * @return 
 */

function balise_PIWIK_dist($p){
	if(lire_config('piwik/mode_insertion','pipeline') == 'balise'){
		$p->code = "recuperer_fond('prive/piwik',
			'', array('trim'=>true))";
		$p->interdire_scripts = false;
		return $p;
	}
}
?>