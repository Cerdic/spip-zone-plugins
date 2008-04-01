<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_SYNCHRO',(_DIR_PLUGINS.end($p)));


function odb_synchro_ajouterBoutons($boutons_admin) {

	// si on est admin
	list($email,$serveur)=explode('@',$GLOBALS['auteur_session']['email']);
	
	if ($GLOBALS['connect_statut'] == "0minirezo") {
		$bouton='configuration';
		// on voit le bouton dans la barre "configuration" 
		$boutons_admin[$bouton]->sousmenu['odb_synchro']= new Bouton(
		"../"._DIR_PLUGIN_ODB_SYNCHRO."/img_pack/siou_carre.png",  // icone
		'Synchronisation des bases'	// titre
		);
	}
	return $boutons_admin;
}


function odb_synchro_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}


?>

