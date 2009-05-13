<?php
/*
 * Plugin En Travaux
 * (c) 2006-2009 Arnaud Ventre, Cedric Morin
 * Distribue sous licence GPL
 *
 */

#var_dump($GLOBALS['meta']['entravaux_id_auteur']);

function autoriser_travaux_dist(){
	return autoriser('webmestre');
}

if (isset($GLOBALS['meta']['entravaux_id_auteur']) AND $GLOBALS['meta']['entravaux_id_auteur']){
	// desactiver le cache
	define('_NO_CACHE',1);

	// au cas ou, placer tout nouveau calcul dans le cache
	$GLOBALS['marqueur'].= ":en_travaux";

	if (test_espace_prive() AND (_request('exec')!='admin_plugin'
	//		OR _request('action')!='activer_plugins'
	)){
		include_spip('inc/autoriser');
		if (!autoriser('travaux')){
			echo recuperer_fond("en_travaux",array());
			die();
		}
	}

}
else {
		include_spip('inc/autoriser');
		if (!autoriser('travaux')){
			// se desactiver tout seul car on ne sert a rien
			// sauf a ralentir le site !
			include_spip('inc/plugin');
			ecrire_plugin_actifs(array('ENTRAVAUX'=>trim(substr(_DIR_PLUGIN_ENTRAVAUX,strlen(_DIR_PLUGINS)),'/')),false,'enleve');
			// avec un message d'erreur smart
			ecrire_meta('plugin_erreur_activation',_T('entravaux:erreur_droit'));
		}
}

/**
 * Pipeline styliser pour rerouter tous les fonds vers en_travaux
 * sauf si l'auteur connecte est celui qui a active le plugin
 *
 * @param array $flux
 * @return array
 */
function entravaux_styliser($flux){
	if (isset($GLOBALS['meta']['entravaux_id_auteur']) AND $GLOBALS['meta']['entravaux_id_auteur']){
		include_spip('inc/autoriser');
		if (!autoriser('travaux')
			AND !in_array($flux['args']['fond'],array('login_secours','formulaires/login','formulaires/menu_lang','formulaires/inc-logo_auteur','formulaires/administration'))){
			$ext = $flux['args']['ext'];
			$fond = find_in_path('en_travaux.html');
			$flux['data'] = substr($fond, 0, - strlen(".$ext"));
		}
	}
	return $flux;
}


/**
 * Afficher une icone de travaux sur tout le site pour que le webmestre n'oublie pas
 * de retablir le site
 * 
 * @param <type> $flux 
 */
function entravaux_affichage_final($flux){
	$image = find_in_path("en_travaux.png");
	if ($GLOBALS['html']){
		include_spip('inc/minipres'); // pour http_img_pack
		$x = '<div id="icone_travaux" style="
		padding-right: 5px;
		padding-top: 2px;
		padding-bottom: 5px;
		top: 0px;
		left: 0px;
		position: absolute;
		">'
		. http_img_pack(find_in_path('en_travaux-64.png'), _T('entravaux:en_travaux'), '')
		. '</div>';
		if (!$pos = strpos($flux, '</body>'))
			$pos = strlen($flux);
		$flux = substr_replace($flux, $x, $pos, 0);
	}
	return $flux;
}

?>