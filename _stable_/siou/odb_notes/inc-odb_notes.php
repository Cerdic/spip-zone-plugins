<?php

/**
 * Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_NOTES',(_DIR_PLUGINS.end($p)));
function odb_notes_ajouterBoutons($boutons_admin) {
	$tab_auteur=$GLOBALS["auteur_session"];
	
	// si on est admin ou operateur de saisie
	if ($GLOBALS['connect_statut'] == "0minirezo" || $tab_auteur['bio']=='Operateur de saisie') {
		// on voit le bouton dans la barre "configuration"
		$boutons_admin['naviguer']->sousmenu['odb_notes']= new Bouton(
		"../"._DIR_PLUGIN_ODB_NOTES."/img_pack/siou_carre.png",  // icone
		'Saisie des notes'	// titre
		);
	}
	return $boutons_admin;
}

function odb_notes_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

?>

