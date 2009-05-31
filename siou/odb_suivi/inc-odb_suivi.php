<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_SUIVI',(_DIR_PLUGINS.end($p)));


function odb_suivi_ajouterBoutons($boutons_admin) {

	// si on est admin ou encadrant
	list($email,$serveur)=explode('@',$GLOBALS['auteur_session']['email']);
	
	if ($GLOBALS['connect_statut'] == "0minirezo" || $email=='encadrant') {
		if($email=='encadrant') $bouton='naviguer';
		else $bouton='statistiques_visites';
		// on voit le bouton dans la barre "naviguer" ou 'satistiques_visites'
		$boutons_admin[$bouton]->sousmenu['odb_suivi']= new Bouton(
		"../"._DIR_PLUGIN_ODB_SUIVI."/img_pack/siou_carre.png",  // icone
		'Suivi des saisies'	// titre
		);
	}
	return $boutons_admin;
}


function odb_suivi_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

