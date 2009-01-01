<?php
/**
	 * Kayé
	 * Le cahier de texte électronique spip spécial primaire
	 * Copyright (c) 2007
	 * Cédric Couvrat
	 * http://alecole.ac-poitiers.fr/
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
**/

/**
 * definition du plugin kaye
**/

if (!defined('_DIR_PLUGIN_KAYE')){
		$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
		define('_DIR_PLUGIN_KAYE',(_DIR_PLUGINS.end($p))."/");
	}
	
function Kaye_insert_head($flux){
	$flux .= '<script src="'._DIR_PLUGIN_KAYE.'js/calendar.js" type="text/javascript" language="javascript"></script>';
	$flux .= '<script src="'._DIR_PLUGIN_KAYE.'js/jquery.tablesorter.pack.js" type="text/javascript" language="javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_KAYE.'css/calendar.css" type="text/css" media="all" />';
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_KAYE.'css/style.css" type="text/css" media="all" />';
	
	return $flux; 
	}



/* public static */
function Kaye_ajouterBoutons($boutons_admin) {
	// si on est auteur
	  $boutons_admin['naviguer']->sousmenu['kaye_noter']= new Bouton(
		"../"._DIR_PLUGIN_KAYE."/img_pack/ecole.gif",  // icone
		'Cahier de texte'	// titre
		);
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['configuration']->sousmenu['kaye']= new Bouton(
		"../"._DIR_PLUGIN_KAYE."/img_pack/gest_ref.gif",  // icone
		'Gestion du cahier de texte &eacute;lectronique'	// titre
		);
	}
	return $boutons_admin;
}

/* public static */
function Kaye_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

?>
