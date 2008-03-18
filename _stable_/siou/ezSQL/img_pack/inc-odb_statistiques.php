<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_STATISTIQUES',(_DIR_PLUGINS.end($p)));


function odb_statistiques_ajouterBoutons($boutons_admin) {

	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['statistiques_visites']->sousmenu['odb_statistiques']= new Bouton(
		"../"._DIR_PLUGIN_ODB_STATISTIQUES."/img_pack/siou_carre.png",  // icone
		'Compter candidats ODB'	// titre
		);
	}
	return $boutons_admin;
}


function odb_statistiques_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

