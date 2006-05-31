<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ACCES_RESTREINT',(_DIR_PLUGINS.end($p)));

	/* public static */
	function AccesRestreint_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['acces_restreint']= new Bouton(
			"../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png",  // icone
			_T('accesrestreint:icone_menu_config')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function AccesRestreint_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

?>