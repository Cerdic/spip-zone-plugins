<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EZSQL',(_DIR_PLUGINS.end($p)));


function ezsql_ajouterBoutons($boutons_admin) {

	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['configuration']->sousmenu['ezsql']= new Bouton(
		_DIR_PLUGIN_EZSQL."/img_pack/csvimport-24.png",  // icone
		'ezSQL+CSV'	// titre
		);
	}
	return $boutons_admin;
}


function ezSQL_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

