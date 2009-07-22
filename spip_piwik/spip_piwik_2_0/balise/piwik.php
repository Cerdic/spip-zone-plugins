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

function balise_PIWIK_dist($p) {
    return calculer_balise_dynamique($p, 'PIWIK', array());
}
function balise_PIWIK_stat($args, $filtres) {
    return $args;
}
function balise_PIWIK_dyn() {
	global $visiteur_session;
	if(lire_config('piwik/urlpiwik') && lire_config('piwik/idpiwik') && lire_config('piwik/mode_insertion','pipeline') == 'balise'){
		if(in_array($visiteur_session['statut'],lire_config('piwik/restreindre_statut_public',array()))||in_array($visiteur_session['id_auteur'],lire_config('piwik/restreindre_auteurs_public',array()))){
			return;
		}
		$piwik_ips = array_flip(preg_split('/(\s*[;,]\s*|\s+)/',trim(lire_config('piwik/exclure_ips','')),-1,PREG_SPLIT_NO_EMPTY));
		if (isset($piwik_ips[$GLOBALS['ip']])) {
			return;
		}
		return array(
        	'prive/piwik', 
        	0,
        	array('ip'=>$GLOBALS['ip'])
    	);
	}
}
?>