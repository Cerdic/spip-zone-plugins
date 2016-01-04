<?php
	/**
	* Plugin Bannières
	*
	* Copyright (c) 2008
	* François de Montlivault
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_BANNIERES',(_DIR_PLUGINS.end($p)));

	/* public static */
	function bannieres_ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		  // on voit le bouton dans la barre "naviguer"
		  
		  /* Changer la page de preentation pour une version alternative axee sur les abonnements */
		  /* 
		  $boutons_admin['naviguer']->sousmenu['abonnes_payants']= new Bouton(
			"../"._DIR_PLUGIN_BANNIERES."/img_pack/annonce.gif",  // icone
			"abonn&eacute;s" //titre
			);
		  */		
			
			/* Accueil original  */
			
			$boutons_admin['naviguer']->sousmenu['bannieres']= new Bouton(
			"../"._DIR_PLUGIN_BANNIERES."/img_pack/bannieres.png",  // icone
			_T('ban:gestion_bannieres') //titre
			);
			
		}
		return $boutons_admin;
	}

	/* public static */
	function bannieres_ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}
	
?>