<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_IMPORT',(_DIR_PLUGINS.end($p)));


function odb_import_ajouterBoutons($boutons_admin) {
   // si on est admin
   if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['naviguer']->sousmenu['odb_import']= new Bouton(
		"../"._DIR_PLUGIN_ODB_IMPORT."/img_pack/siou_carre.png",  // icone
		'Import_ CSV'	// titre
		);
	}
	return $boutons_admin;
}


function odb_import_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

