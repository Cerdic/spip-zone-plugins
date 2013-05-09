<?php
/*
 * Plugin En Travaux
 * (c) 2006-2009 Arnaud Ventre, Cedric Morin
 * Distribue sous licence GPL
 *
 */

#var_dump($GLOBALS['meta']['entravaux_id_auteur']);

/**
 * Autoriser a voir le site en travaux : par defaut tous les webmestre
 * @return mixed
 */
function autoriser_intranet_dist(){ return (isset($GLOBALS['visiteur_session']['id_auteur']) && $GLOBALS['visiteur_session']['id_auteur'] > 0); }

// dans le site public
// si auteur pas autorise : placer sur un cache dedie
// si auteur autorise, desactiver le cache :
// il voit le site, mais pas de cache car il travaille dessus !
if (!test_espace_prive()){
	include_spip('inc/autoriser');
	if (!autoriser('intranet'))
		$GLOBALS['marqueur'].= ":intranet_out";
}

/**
 * Pipeline styliser pour rerouter tous les fonds vers en_travaux
 * sauf si l'auteur connecte est celui qui a active le plugin
 *
 * @param array $flux
 * @return array
 */
function intranet_styliser($flux){
	include_spip('inc/autoriser');
	// les pages exceptions
	$pages_ok = array('robots.txt','spip_pass','favicon.ico','informer_auteur');
	//spip_log($flux['args'],'test.'._LOG_ERREUR);
	if (!autoriser('intranet')
		AND !in_array($flux['args']['fond'],$pages_ok)
		AND !in_array($flux['args']['contexte'][_SPIP_PAGE],$pages_ok)
		// et on laisse passer modeles et formulaires,
		// qui ne peuvent etre inclus ou appeles que legitimement
		AND strpos($flux['args']['fond'],'/')===false){
		$fond = trouver_fond('inclure/intranet','',true);
		$flux['data'] = $fond['fond'];
	}
	return $flux;
}

?>
