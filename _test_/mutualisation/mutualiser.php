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
			'utiliser_panel' => false
		),
		$options
	);

	if ($options['cookie_prefix'])
		$GLOBALS['cookie_prefix'] = prefixe_mutualisation($site);
	if ($options['table_prefix'])
		$GLOBALS['table_prefix'] = prefixe_mutualisation($site);

	if (($options['creer_user_base']) AND (!$options['utiliser_panel'])) {
		define('_INSTALL_USER_DB', _INSTALL_NAME_DB);
		define('_INSTALL_PASS_DB',
			substr(md5(
				_INSTALL_PASS_DB_ROOT   # secret du site
				. $_SERVER['REMOTE_ADDR'] # un truc variable
				. _INSTALL_USER_DB # un autre truc variable
			), 0, 8)
		);
	}
	
	if ($options['utiliser_panel']) {
		
		// Voir http://www.spip-contrib.net/Service-d-hebergement-mutualise
				
		// On cherche en BD si le site est enregistre et on recupere
		// password et code d'activation

		$link = @mysql_connect(_INSTALL_PANEL_HOST_DB,_INSTALL_PANEL_USER_DB,_INSTALL_PANEL_PASS_DB);
		@mysql_select_db(_INSTALL_PANEL_NAME_DB);
		$result=@mysql_query("SELECT * FROM "._INSTALL_PANEL_NAME_TABLE." WHERE "._INSTALL_PANEL_FIELD_SITE."='$site'");
		if (mysql_num_rows($result)>0) {
			$data = mysql_fetch_assoc($result);
			$options['code'] =$data[_INSTALL_PANEL_FIELD_CODE];
			define ('_INSTALL_NAME_DB',_INSTALL_NAME_DB);
			define ('_INSTALL_USER_DB',_INSTALL_NAME_DB);
			define ('_INSTALL_PASS_DB',$data[_INSTALL_PANEL_FIELD_PASS]);
		}
		else {
			echo ('<h2>Erreur 404 : page inexistante</h2>');
			exit;
	
		}

	}

	$adr_site = $options['repertoire'].'/' . $site . '/';
	if (!is_dir($e = _DIR_RACINE . $adr_site)) {
		spip_initialisation();
		require dirname(__FILE__).'/mutualiser_creer.php';
		mutualiser_creer($e, $options);
		exit;
	}

	define('_SPIP_PATH',
		$e . ':' .
		_DIR_RACINE .':' .
		_DIR_RACINE .'dist/:' .
		_DIR_RESTREINT
	);

	if (is_dir($e.'squelettes'))
		$GLOBALS['dossier_squelettes'] = $adr_site.'squelettes';

	if (is_readable($f = $e._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php')) 
		include($f); // attention cet include n'est pas en globals

	spip_initialisation(
		($e . _NOM_PERMANENTS_INACCESSIBLES),
		($e . _NOM_PERMANENTS_ACCESSIBLES),
		($e . _NOM_TEMPORAIRES_INACCESSIBLES),
		($e . _NOM_TEMPORAIRES_ACCESSIBLES)
	);

	// Ajouter le chemin vers l'exec=mutualisation pour le site maitre
	// et seulement pour lui (pour en mettre plusieurs, les separer par
	// des virgules).
	if (_request('exec') === 'mutualisation') {
		if (!defined('_SITES_ADMIN_MUTUALISATION')
		OR in_array($site, explode(',',_SITES_ADMIN_MUTUALISATION))) {
			_chemin(dirname(__FILE__));
		}

		// Si un upgrade est demande dans le site fils, et securise par md5
		// depuis le panneau de controle, le faire directement
		if (_request('upgrade') == 'oui') {
			require dirname(__FILE__).'/mutualiser_upgrade.php';
			mutualiser_upgrade();
		}
	}
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
