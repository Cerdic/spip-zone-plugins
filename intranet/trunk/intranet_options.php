<?php
/*
 * Plugin Intranet
 * 
 * (c) 2013 kent1
 * Distribue sous licence GPL
 *
 */

/**
 * Autoriser a voir le site en Intranet : par defaut toute personne identifiée
 * @return mixed
 */
function autoriser_intranet_dist(){ return (isset($GLOBALS['visiteur_session']['id_auteur']) && $GLOBALS['visiteur_session']['id_auteur'] > 0); }

// dans le site public
// si auteur pas autorise : placer sur un cache dedie
if (!test_espace_prive()){
	include_spip('inc/autoriser');
	if (!autoriser('intranet'))
		$GLOBALS['marqueur'].= ":intranet_out";
}

/**
 * Pipeline styliser pour rerouter tous les fonds vers intranet
 *
 * @param array $flux
 * @return array
 */
function intranet_styliser($flux){
	if ( 
		!test_espace_prive()
		AND include_spip('inc/autoriser')
		AND include_spip('inc/config')
		AND strpos($flux['args']['fond'],'/')===false
		AND !autoriser('intranet')
		AND ($pages_ok = array_filter(pipeline('intranet_pages_ok',array_merge(array('robots.txt','spip_pass','favicon.ico','informer_auteur'),explode(',',lire_config('intranet/pages_intranet',' '))))))
		AND !in_array($flux['args']['fond'],$pages_ok)
		AND !in_array($flux['args']['contexte'][_SPIP_PAGE],$pages_ok)
		AND !in_array(substr($flux['args']['fond'],-3),array('.js','.css'))){
			$fond = trouver_fond('inclure/intranet','',true);
			$flux['data'] = $fond['fond'];
	}
	return $flux;
}

?>