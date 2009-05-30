<?php
include_ecrire('inc_db_mysql');
include_ecrire('inc_abstract_sql');
include_ecrire('inc_rubriques');

	/* public static */
	function push_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['push']= new Bouton(
			"../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png",  // icone
			_T('push:icone_menu_config')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function push_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

?>