<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EVAJCLIC',(_DIR_PLUGINS.end($p)));

	function evajclic_AjouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['eva_jclic']= new Bouton(
			"../"._DIR_PLUGIN_EVAJCLIC."/img_pack/jclic.gif",  // icone
			_T('evajclic:EVA_jclic')	// titre
			);
		}
		return $boutons_admin;

	}

?>
