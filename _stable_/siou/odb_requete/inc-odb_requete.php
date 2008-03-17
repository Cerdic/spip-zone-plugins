<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_REQUETE',(_DIR_PLUGINS.end($p)));


function odb_requete_ajouterBoutons($boutons_admin) {

	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['configuration']->sousmenu['odb_requete']= new Bouton(
		_DIR_PLUGIN_ODB_REQUETE."/img_pack/csvimport-24.png",  // icone
		'Requ&ecirc;tes - CSV'	// titre
		);
	}
	return $boutons_admin;
}


function odb_requete_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

