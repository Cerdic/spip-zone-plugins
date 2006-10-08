<?php
	/* public static */
	function w3cgh_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
			if (!defined(_DIR_PLUGIN_W3CGH))
				$icone = find_in_path('/images/w3cgh-icone.gif');
			else
				$icone = _DIR_PLUGIN_W3CGH."/images/w3cgh-icone.gif";
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['configuration']->sousmenu['w3c_go_home']= new Bouton(
			$icone,  // icone
			_L('Conformit&eacute;')	// titre
			);
		}
		return $boutons_admin;
	}
?>