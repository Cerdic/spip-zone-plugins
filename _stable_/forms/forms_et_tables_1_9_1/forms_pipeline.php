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
		define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p))."/");
	}

	function Forms_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" 
		AND (!isset($GLOBALS['meta']['activer_forms']) OR $GLOBALS['meta']['activer_forms']!="non") ) {

		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu["forms_tous"]= new Bouton(
			"../"._DIR_PLUGIN_FORMS."img_pack/form-24.gif",  // icone
			_T("forms:formulaires_sondages") //titre
			);
			
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu["tables_tous"]= new Bouton(
			"../"._DIR_PLUGIN_FORMS."img_pack/table-24.gif",  // icone
			_T("forms:tables") //titre
			);
		}
		return $boutons_admin;
	}
	
	function Forms_affiche_milieu($flux) {
		$exec =  $flux['args']['exec'];
		if ($exec=='articles'){
			$id_article = $flux['args']['id_article'];
			$forms_lier_donnees = charger_fonction('forms_lier_donnees','inc');
			$flux['data'] .= "<div id='forms_lier_donnees'><div id='forms_lier_donnees-$id_article'>";
			$flux['data'] .= $forms_lier_donnees($id_article, $exec);
			$flux['data'] .= "</div></div>";
		}
		return $flux;
	}
?>