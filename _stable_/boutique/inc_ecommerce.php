<?php

/***************************************************************************
 *  BOUTIQUE : Plugin, version lite d'un e-commerce pour SPIP              *
 *                                                                         *
 *  Copyright (c) 2006-2007                                                *
 *  Laurent RIEFFEL : mailto:laurent.rieffel@laposte.net			   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 ***************************************************************************/

/*
 * Boutique
 * version plug-in d'un e-commerce
 *
 * Auteur : Laurent RIEFFEL
 * 
 * Module pour SPIP version 1.9.x
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */


$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_BOUTIQUE',(_DIR_PLUGINS.end($p)));


/* static public */

/* public static */
function ecommerce_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) 
		{
	// on voit le bouton dans la barre "naviguer"
		$boutons_admin['configuration']->sousmenu['boutiques']= new Bouton(
			"../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png",  // icone
			_T('boutique:Boutiques')	// titre
			);
	// on voit le bouton dans la barre "naviguer"
		$boutons_admin['statistiques_visites']->sousmenu['sessions']= new Bouton(
			"../"._DIR_PLUGIN_BOUTIQUE."/img_pack/session.gif",  // icone
			_T('sessions:Sessions')	// titre
			);
	// on voit le bouton dans la barre "naviguer"
		$boutons_admin['statistiques_visites']->sousmenu['paniers']= new Bouton(
			"../"._DIR_PLUGIN_BOUTIQUE."/img_pack/panier.png",  // icone
			_T("paniers:Paniers") //titre
			);
		}
	return $boutons_admin;
}

/* public static */
function Corbeille_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

// affiche l'icone poubelle (vide ou pleine)
function boutique_icone($total_table) {

}
function boutique_affiche($page)
{
}


?>
