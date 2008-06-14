<?php
    if (!defined("_ECRIRE_INC_VERSION")) return;
    
	$p = explode(basename(_DIR_PLUGINS)."/", str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_AFF_ZONE',(_DIR_PLUGINS.end($p)));

	function aff_zone_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "auteurs"
			$boutons_admin['naviguer']->sousmenu['aff_zone']= new Bouton("../"._DIR_PLUGIN_AFF_ZONE."/img_pack/mag_maj.png", _T('aff_zone:module_titre') );
		}
		return $boutons_admin;
	}
    
    function aff_zone_header_prive($head) {
        $head .= '<script language="JavaScript" type="text/javascript" src="'._DIR_PLUGIN_AFF_ZONE.'aff_zone.js"></script>';
        return $head;
    }

?>