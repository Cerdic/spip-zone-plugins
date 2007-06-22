<?php
$p = explode(basename(_DIR_PLUGINS)."/", str_replace('\\','/',realpath(dirname(__FILE__))));
  define('_DIR_PLUGIN_FENETREFLO',(_DIR_PLUGINS.end($p)));

function FenFlo_ajout_bouton_prive($boutons_prive) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_prive['configuration']->sousmenu['fenetresflo_config']= new Bouton(
			""._DIR_PLUGIN_FENETREFLO."/images/fenetre.gif",  // icone
			_T('fenetresflottantes:module_titre')	// titre
			);
		}
		return $boutons_prive;
	}

	/* public static */
	function FenFlo_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}
