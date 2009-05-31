<?php
		 $p = explode(basename(_DIR_PLUGINS)."/", str_replace('\\','/',realpath(dirname(__FILE__))));
		 define('_DIR_PLUGIN_ACCESGROUPES',(_DIR_PLUGINS.end($p)));

	function accesgroupes_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "auteurs"
			$boutons_admin['auteurs']->sousmenu['accesgroupes_admin']= new Bouton("../"._DIR_PLUGIN_ACCESGROUPES."/img_pack/groupe-24.png", _T('accesgroupes:module_titre') );
		}
		return $boutons_admin;
	}


?>