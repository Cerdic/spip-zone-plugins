<?php

/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_REPARTITION',(_DIR_PLUGINS.end($p)));

function odb_repartition_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
	AND $GLOBALS["options"]=="avancees") {
		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['odb_repartition']= new Bouton(
		"../"._DIR_PLUGIN_ODB_SAISIE."/img_pack/siou_carre.png",  // icone
		'R&eacute;partition candidats'	// titre
		);
	}

	return $boutons_admin;

}


function odb_repartition_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

