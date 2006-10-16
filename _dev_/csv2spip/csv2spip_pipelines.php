<?php
/* csv2spip est un plugin pour créer/modifier les rédacteurs et administrateurs restreints d'un SPIP à partir de fichiers CSV
*
* Auteur : cy_altern (cy_altern@yahoo.fr)
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
		 $p = explode(basename(_DIR_PLUGINS)."/", str_replace('\\','/',realpath(dirname(__FILE__))));
		 define('_DIR_PLUGIN_CSV2SPIP',(_DIR_PLUGINS.end($p)));

	function csv2spip_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "auteurs"
			$boutons_admin['auteurs']->sousmenu['csv2spip']= new Bouton("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", _T('csvspip:module_titre') );
		}
		return $boutons_admin;
	}


?>