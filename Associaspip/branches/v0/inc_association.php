<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ASSOCIATION',(_DIR_PLUGINS.end($p)));

/* public static */
function association_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "naviguer"
	  
	  /* Changer la page de preentation pour une version alternative axee sur les abonnements */
	  /* 
	  $boutons_admin['naviguer']->sousmenu['abonnes_payants']= new Bouton(
		"../"._DIR_PLUGIN_ASSOCIATION."/img_pack/annonce.gif",  // icone
		"abonn&eacute;s" //titre
		);
	  */		
		
	 /* Accueil original  */

	  $boutons_admin['naviguer']->sousmenu['association']= new Bouton(
		"../"._DIR_PLUGIN_ASSOCIATION."/img_pack/annonce.gif",  // icone
		_T('asso:titre_menu_gestion_association') //titre
		);
		
	}
	return $boutons_admin;
}

/* public static */
function association_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}
?>