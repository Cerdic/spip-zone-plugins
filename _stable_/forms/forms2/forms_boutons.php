<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	if (!defined('_DIR_PLUGIN_FORMS')){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p)));
	}

	function Forms_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" 
		AND (!isset($GLOBALS['meta']['activer_forms']) OR $GLOBALS['meta']['activer_forms']!="non") ) {

		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu["forms_tous"]= new Bouton(
			"../"._DIR_PLUGIN_FORMS."/img_pack/form-24.gif",  // icone
			_T("forms:formulaires_sondages") //titre
			);
		}
		return $boutons_admin;
	}

?>