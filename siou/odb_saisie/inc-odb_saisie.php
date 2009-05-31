<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_SAISIE',(_DIR_PLUGINS.end($p)));

function odb_saisie_ajouterBoutons($boutons_admin) {
	// si on est admin ou chef d'etablissement ou opérateur de saisie ou encadrant
	list($email,$serveur)=explode('@',$GLOBALS['auteur_session']['email']);
	if ($GLOBALS['connect_statut'] == '0minirezo' 
		|| in_array($email,array('etablissement','operateur','encadrant'))) {

		// on voit le bouton dans la barre "naviguer"
		$boutons_admin['naviguer']->sousmenu['odb_saisie']= new Bouton(
		"../"._DIR_PLUGIN_ODB_SAISIE."/img_pack/siou_carre.png",  // icone
		'Saisie Candidats'	// titre
		);
	}
	return $boutons_admin;
}


function odb_saisie_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

	
?>

