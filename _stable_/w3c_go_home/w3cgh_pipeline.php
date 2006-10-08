<?php
	/* public static */
	function w3cgh_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['w3c_go_home']= new Bouton(
			_DIR_PLUGIN_W3CGH."/images/w3cgh-icone.gif",  // icone
			_L('Conformit&eacute;')	// titre
			);
		}
		return $boutons_admin;
	}
?>