<?php
/* générateur de clones de rubrique avec articles

  gener_cont_pipelines.php (c) cy_altern 2006 -- licence GPL

*/
		 $p = explode(basename(_DIR_PLUGINS)."/", str_replace('\\','/',realpath(dirname(__FILE__))));
		 define('_DIR_PLUGIN_GENER_CONT',(_DIR_PLUGINS.end($p)));

	function gener_cont_ajouter_boutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo") {
		  // on voit le bouton comme  sous-menu de "auteurs"
			$boutons_admin['naviguer']->sousmenu['gener_cont']= new Bouton("../"._DIR_PLUGIN_GENER_CONT."/img_pack/gener_cont-24.png", _T('Generateur de contenu') );
		}
		return $boutons_admin;
	}


?>