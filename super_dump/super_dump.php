<?php

/*
 * gestion_documents
 *
 * interface de gestion des documents
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */
define('_DIR_PLUGIN_SUPER_DUMP',(_DIR_PLUGINS . basename(dirname(__FILE__))));

class SuperDump {
	/* static public */

	/* public static */
	function ajouterBoutons($boutons_admin) {
	  // remplacer l'icone si elle est la
	  if (isset($boutons_admin['configuration']->sousmenu['admin_tech']))
			$boutons_admin['configuration']->sousmenu['admin_tech']= 
				new Bouton("../"._DIR_PLUGIN_SUPER_DUMP."/super-dump-24.png", "icone_maintenance_site");

		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}
}

?>