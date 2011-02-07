<?php
/* csv2spip est un plugin pour créer/modifier les visiteurs, rédacteurs et administrateurs restreints d'un SPIP à partir de fichiers CSV
*	 					VERSION : 3.1 => plugin pour spip 2.*
*
* Auteur : cy_altern
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/

	function csv2spip_ajouter_boutons($boutons_admin) {
		// si on est admin
		global $spip_version_code;
		if ($spip_version_code < 14864) {
			if ($GLOBALS['connect_statut'] == "0minirezo") {
			  // on voit le bouton comme  sous-menu de "auteurs"
				$boutons_admin['auteurs']->sousmenu['csv2spip']= new Bouton("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.png", _T('csvspip:module_titre') );
			}
		}

		return $boutons_admin;

	}


?>