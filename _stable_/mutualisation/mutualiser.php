<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


// Demarrer un site dans le sous-repertoire sites/$f/
// Options :
// creer_site => on va creer les repertoires qui vont bien (defaut: false)
// cookie_prefix, table_prefix => regler les prefixes (defaut: true)
// http://doc.spip.org/@demarrer_site
function demarrer_site($site = '', $options = array()) {
	if (!$site) return;

	$options = array_merge(
		array(
			'creer_site' => false,
			'creer_base' => false,
			'creer_user_base' => false,
			'mail' => '',
			'code' => 'ecureuil', // code d'activation par defaut
			'table_prefix' => false,
			'cookie_prefix' => false,
			'repertoire' => 'sites',
			'utiliser_panel' => false,
			'url_img_courtes' => false
		),
		$options
	);

	$GLOBALS['mutualisation_dir'] = $options['repertoire'];
	
	if ($options['cookie_prefix'])
		$GLOBALS['cookie_prefix'] = prefixe_mutualisation($site);
	if ($options['table_prefix'])
		$GLOBALS['table_prefix'] = prefixe_mutualisation($site);

	/*
	 * Si le dossier du site n'existe pas
	 * ou si le fichier de connexion a la bdd est absent, 
	 * le site n'est pas totalement installe.
	 * 
	 * Il faut lancer la creation de mutualisation
	 */
	if  ($ok = !is_dir($e = _DIR_RACINE . $options['repertoire'].'/' . $site . '/')
	    OR !(defined('_DIR_CONNECT')?
			(defined('_FILE_CONNECT_INS')?
				   file_exists(_DIR_CONNECT . _FILE_CONNECT_INS . '.php'):
				   file_exists(_DIR_CONNECT . 'connect.php')):
			(defined('_FILE_CONNECT_INS')?
				   file_exists($e . _NOM_PERMANENTS_INACCESSIBLES . _FILE_CONNECT_INS . '.php'):
				   file_exists($e . _NOM_PERMANENTS_INACCESSIBLES . 'connect.php'))
			)
		)
	{	
		/*
		 * - Recuperer les identifiants manquants
		 * 
		 * Nota :
		 * Il faut que _INSTALL_(NAME|USER|PASS) soient definis ici
		 * et non dans le fichier mutualiser_creer() car
		 * ces constantes sont necessaires a l'installation de SPIP
		 * (ecrire/?exec=install). Dans le cas contraire, le formulaire
		 * d'installation n'est pas prerempli.
		 * 
		 * 
		 * > Cas de la gestion d'un pannel
		 * Recuperer les mots de passes du futur compte dans une table speciale
		 * Voir http://www.spip-contrib.net/Service-d-hebergement-mutualise
		 */
		if ($options['utiliser_panel']) {
			include_spip('inc/minipres');
			include_spip('base/abstract_sql');
			include_once(dirname(__FILE__).'/base/abstract_mutu.php');
			
			// On cherche en BD si le site est enregistre et on recupere
			// password et code d'activation
			$link = @mutu_connect_db(_INSTALL_PANEL_HOST_DB, 0, _INSTALL_PANEL_USER_DB, _INSTALL_PANEL_PASS_DB, '', _INSTALL_SERVER_DB);
			@sql_selectdb(_INSTALL_PANEL_NAME_DB, _INSTALL_SERVER_DB);
			$result=@sql_query("SELECT "
						. _INSTALL_PANEL_FIELD_CODE . " AS code," 
						. _INSTALL_PANEL_FIELD_PASS . " AS pass," 
						. _INSTALL_PANEL_FIELD_SITE . " AS site," 
						. " FROM " . _INSTALL_PANEL_NAME_TABLE 
						. " WHERE "._INSTALL_PANEL_FIELD_SITE . " = '$site'"
						, _INSTALL_SERVER_DB);
			if (sql_count($result)>0) {
				$data = sql_fetch($result);
				$options['code'] = $data['code'];
				define ('_INSTALL_NAME_DB', _INSTALL_NAME_DB);
				define ('_INSTALL_USER_DB', _INSTALL_NAME_DB);
				define ('_INSTALL_PASS_DB', $data['pass']);
			}
			else {
				echo minipres(
					_L('<h2>Erreur 404 : page inexistante</h2>')
				);
				exit;
			}
		/*
		 * > Cas de creation d'utilisateur de la base SQL
		 * (nom et pass non attribuees par un panel)
		 */		
		} elseif ($options['creer_user_base']) {
		
			// nom d'utilisateur et mot de passe
			define('_INSTALL_USER_DB', _INSTALL_NAME_DB);
			define('_INSTALL_PASS_DB',
				substr(md5(
					_INSTALL_PASS_DB_ROOT   # secret du site
					. $_SERVER['REMOTE_ADDR'] # un truc variable
					. _INSTALL_USER_DB # un autre truc variable
				), 0, 8)
			);
		}
		
		/*
		 * Si le site n'existe pas encore, 
		 * lancer la procedure de creation
		 */
		if (!is_dir($e)) {
			spip_initialisation();
			include_once dirname(__FILE__).'/mutualiser_creer.php';
			mutualiser_creer($e, $options);
			exit;
		}

	}


	/*
	 * Tout est pret, on execute la mutualisation.
	 */
	define('_SPIP_PATH',
		$e . 'dist/:' .  // + 
		$e . ':' .
		_DIR_RACINE .':' . 
		_DIR_RACINE .'dist/:' .
		_DIR_RACINE .'dist/javascript/:' . // +
		_DIR_RESTREINT);


	if (is_dir($e.'squelettes'))
		$GLOBALS['dossier_squelettes'] = $options['repertoire'].'/' . $site . '/squelettes';

	if (is_readable($f = $e._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php')) 
		include($f); // attention cet include n'est pas en globals

	spip_initialisation(
		($e . _NOM_PERMANENTS_INACCESSIBLES),
		($e . _NOM_PERMANENTS_ACCESSIBLES),
		($e . _NOM_TEMPORAIRES_INACCESSIBLES),
		($e . _NOM_TEMPORAIRES_ACCESSIBLES)
	);	


	/*
	 * Ajouter le chemin vers l'exec=mutualisation pour le site maitre
	 * et seulement pour lui (pour en mettre plusieurs, les separer par
	 * des virgules).
	 */
	if (_request('exec') === 'mutualisation') {
		if (!defined('_SITES_ADMIN_MUTUALISATION')
		OR in_array($site, explode(',',_SITES_ADMIN_MUTUALISATION))) {
			_chemin(dirname(__FILE__));
		}

		// Si un upgrade est demande dans le site fils, et securise par md5
		// depuis le panneau de controle, le faire directement
		if (_request('upgrade') == 'oui') {
			include_once dirname(__FILE__).'/mutualiser_upgrade.php';
			mutualiser_upgrade();
		}
	}
	
	
	/*
	 * Gestion des url d'images courtes
	 * sites/nom/IMG/image.jpg -> IMG/image.jpg
	 * 
	 * Ne fonctionne que pour de la mutualisation 
	 * sur des noms de domaines.
	 * 
	 * Une mutualisation de repertoire
	 * ne pourra fonctionner car les fichiers
	 * .htaccess de /IMG et /local n'ont pas
	 * connaissance du nom du repertoire.
	 * 
	 */
	if ($options['url_img_courtes']) {
		$GLOBALS['spip_pipeline']['affichage_final']
			.= '|mutualisation_url_img_courtes';
	}
}

// transformer les sites/truc/IMG/rtf/chose.rtf en /IMG/...
function mutualisation_url_img_courtes($flux) {
	if (strpos($flux, _DIR_IMG)
	OR strpos($flux, _DIR_VAR)) {
		require_once dirname(__FILE__).'/mutualiser_gerer_img.php';
		return mutualisation_traiter_url_img_courtes($flux);
	}
	else
		return $flux;
}

// Cette fonction cree un prefixe acceptable par MySQL a partir du nom
// du site ; a utiliser comme prefixe des tables, comme suffixe du nom
// de la base de donnees ou comme prefixe des cookies... unicite quasi garantie
// Max 12 caracteres a-z0-9, qui ressemblent au domaine et ne commencent
// pas par un chiffre
// http://doc.spip.org/@prefixe_mutualisation
function prefixe_mutualisation($site) {
	static $prefix = array();

	if (!isset($prefix[$site])) {
		$p = preg_replace(',^www\.|[^a-z0-9],', '', strtolower($site));
		// si c'est plus long que 12 on coupe et on pose un md5 d'unicite
		// meme chose si ca contenait un caractere autre que [a-z0-9],
		// afin d'eviter de se faire chiper c.a.domaine.tld par ca.domaine.tld
		if (strlen($p) > 12
		OR $p != $site)
			$p = substr($p, 0, 8) . substr(md5($site),-4);
		// si ca commence par un chiffre on ajoute a
		if (ord($p) < 58)
			$p = 'a'.$p;
		$prefix[$site] = $p;
	}
	return $prefix[$site];

}



?>
