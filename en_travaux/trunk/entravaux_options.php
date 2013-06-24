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
 * Verifier un verrou fichier pose dans local/entravaux_xxx.lock
 * pour ne pas qu'il saute si on importe une base
 * La meta n'est qu'un cache qu'on met a jour si pas dispo.
 * @param string $nom
 * @param bool $force
 * @return bool
 */
function entravaux_check_verrou($nom, $force=false){
	if (!isset($GLOBALS['meta'][$m='entravaux_'.$nom]) OR $force){
		ecrire_meta($m,file_exists(_DIR_VAR.$m.".lock")?"oui":"non",'non');
	}
	return $GLOBALS['meta'][$m]=="oui"; // si oui : verrou pose
}

/**
 * A-t-on active les travaux oui ou non ?
 * @return bool
 */
function is_entravaux(){
	// upgrade sauvage ?
	include_spip('entravaux_administrations');
	if (isset($GLOBALS['meta']['entravaux_id_auteur'])){include_spip('entravaux_install');entravaux_poser_verrou("accesferme");effacer_meta('entravaux_id_auteur');}
	if (defined('_ENTRAVAUX_IP_EXCEPTIONS') AND in_array($GLOBALS['ip'],explode(',',_ENTRAVAUX_IP_EXCEPTIONS))) return false;
	return entravaux_check_verrou("accesferme");
}

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
	// si espace prive : die avec page travaux
	// sauf si pas loge => redirection
	else {
		if (
		  !in_array(_request('action'),array('logout'))
		  AND !in_array(_request('exec'),array('install'))
		){
			if (!autoriser('travaux')){
				spip_initialisation_suite();
				// si on est loge : die() avec travaux
				if ($GLOBALS['visiteur_session']['id_auteur']){
					$travaux = recuperer_fond("inclure/entravaux",array());
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
				// sinon retour sur login_sos
				else {
					$redirect = parametre_url(generer_url_public('login_sos'),'url',self(),'&');
					include_spip('inc/headers');
					redirige_par_entete($redirect);
				}
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
		// les pages exceptions
		$pages_ok = array('login_sos','robots.txt','spip_pass','favicon.ico','informer_auteur');
        // des squelettes autorisés configurables via mes_options
		if (defined('_SKEL_HORS_TRAVAUX')) $skels_ok = explode(',',_SKEL_HORS_TRAVAUX); 
		else $skels_ok = array();
		if (!autoriser('travaux')
			AND !in_array($flux['args']['fond'],$pages_ok)
			AND !in_array($flux['args']['fond'],$skels_ok)
			AND !in_array($flux['args']['contexte'][_SPIP_PAGE],$pages_ok)
			// et on laisse passer modeles et formulaires,
			// qui ne peuvent etre inclus ou appeles que legitimement
		  AND strpos($flux['args']['fond'],'/')===false){
			$fond = trouver_fond('inclure/entravaux','',true);
			$flux['data'] = $fond['fond'];
		}
	}
	return $flux;
}


/**
 * Afficher une icone de travaux sur tout le site public pour que le webmestre n'oublie pas
 * de retablir le site
 * 
 * @param string $flux
 * @return string
 */
function entravaux_affichage_final($flux){
	if (is_entravaux()
		AND !test_espace_prive()
		AND $GLOBALS['html']
		AND !_AJAX){
		include_spip('inc/filtres'); // pour http_img_pack
		$x = '<div id="icone_travaux" style="
		position: fixed;
		left: 40px;
		top: 40px;
		">'
		. http_img_pack(chemin_image('entravaux-64.png'), _T('entravaux:en_travaux'), '', _T('entravaux:en_travaux'))
		. '</div>';
		if (!$pos = strpos($flux, '</body>'))
			$pos = strlen($flux);
		$flux = substr_replace($flux, $x, $pos, 0);
	}
	return $flux;
}

/**
 * Afficher une notice sur l'accueil de ecrire
 * @param array $flux
 * @return array
 */
function entravaux_affiche_milieu($flux){
	if (is_entravaux()){
		if ($flux['args']['exec']=='accueil'){
			$notice = recuperer_fond('inclure/entravaux_notice_ecrire',array());
			if (strlen(trim($notice)))
				$flux['data'] =  $notice . $flux['data'];
		}
	}
	if ($flux['args']['exec']=='configurer_identite'){
		$flux['data'] .= recuperer_fond('prive/squelettes/contenu/configurer_entravaux',array());
	}
	return $flux;
}

?>
