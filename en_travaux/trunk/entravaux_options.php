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
function autoriser_travaux_dist(){ return autoriser('webmestre'); }

/**
 * A-t-on active les travaux oui ou non ?
 * @return bool
 */
function is_entravaux(){ return (isset($GLOBALS['meta']['entravaux_id_auteur']) AND $GLOBALS['meta']['entravaux_id_auteur']);}

if (is_entravaux()){
	include_spip('inc/autoriser');

	// dans le site public
	// si auteur pas autorise : placer sur un cache dedie
	// si auteur autorise, desactiver le cache :
	// il voit le site, mais pas de cache car il travaille dessus !
	if (!test_espace_prive()){
		if (!autoriser('travaux')){
			$GLOBALS['marqueur'].= ":en_travaux";
		}
		else {
			// desactiver le cache sauf si inhibe par define
			if (!defined('_ENTRAVAUX_GARDER_CACHE'))
				define('_NO_CACHE',1);
		}
	}
	else {
		if (_request('exec')!='admin_plugin'
		//		OR _request('action')!='activer_plugins'
		){
			if (!autoriser('travaux')){
				spip_initialisation_suite();
				$travaux = recuperer_fond("entravaux",array());
				// fallback : le fond renvoie parfois du vide ...
				if (!strlen($travaux)){
					@define('_SPIP_SCRIPT','spip.php');
					echo "Acces interdit (en travaux) <a href='"
					.generer_url_action('logout',"logout=public",false,true)
					."'>Deconnexion</a>";
				}
				else
					echo $travaux;
				die();
			}
		}
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
	if (is_entravaux()){
		include_spip('inc/autoriser');
		if (!autoriser('travaux')
			AND !in_array(
				    $flux['args']['fond'],
						// les exceptions
			      array('login_sos','formulaires/login','formulaires/menu_lang','formulaires/inc-logo_auteur','formulaires/administration',
			      'spip_pass','formulaires/oubli','formulaires/mot_de_passe',
			      )
			    )){
			$fond = trouver_fond('entravaux','',true);
			$flux['data'] = $fond['fond'];
		}
	}
	return $flux;
}


/**
 * Afficher une icone de travaux sur tout le site public pour que le webmestre n'oublie pas
 * de retablir le site
 * 
 * @param <type> $flux
 */
function entravaux_affichage_final($flux){
	if (is_entravaux()
		AND !test_espace_prive()
		AND $GLOBALS['html']
		AND !_AJAX){
		include_spip('inc/filtres'); // pour http_img_pack
		$x = '<div id="icone_travaux" style="
		padding-right: 5px;
		padding-top: 2px;
		padding-bottom: 5px;
		position: absolute;
		left: 26px;
		top: 26px;
		">'
		. http_img_pack(chemin_image('entravaux-64.png'), _T('entravaux:en_travaux'), '')
		. '</div>';
		if (!$pos = strpos($flux, '</body>'))
			$pos = strlen($flux);
		$flux = substr_replace($flux, $x, $pos, 0);
	}
	return $flux;
}

?>
