<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EVAECRITURE',(_DIR_PLUGINS.end($p)));

	function eva_ecriture_AjouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['eva_ecriture']= new Bouton(
			"../"._DIR_PLUGIN_EVAECRITURE."/img_pack/logo_ecriture.png",  // icone
			_T('evaecriture:EVA_ecriture')	// titre
			);
		}
		return $boutons_admin;

	}

?>
