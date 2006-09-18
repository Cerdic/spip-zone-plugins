<?
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_AUTH_BD_EXTERNE',(_DIR_PLUGINS.end($p)));

	/* public static */
	function AuthBdExterne_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['auth_bd_externe']= new Bouton(
			"../"._DIR_PLUGIN_AUTH_BD_EXTERNE."/auth_bd_externe-24.png",  // icone
			_L('Authentification externe')	// titre
			);
		}
		return $boutons_admin;
	}
?>
