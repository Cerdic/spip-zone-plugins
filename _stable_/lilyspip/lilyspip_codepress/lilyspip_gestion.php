<?php

/**
 * definition du plugin "Lilyspip" version "classe statique"
 * creation du bouton
 */

function lilyspip_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {

	  // Pour voir le bouton dans la barre "configuration"
	  $boutons_admin['configuration']->sousmenu['lilyspip']= new Bouton(
		find_in_path("images/icon22.png"), // image
		'Lilyspip'	// titre
		);
	}

	return $boutons_admin;
}


function lilyspip_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

//Fonction ajoutee par Patrice VANNEUFVILLE pour la gestion des variables meta
function lilyspip_install($action){
	include_spip('inc/meta');
	switch ($action){
		case 'test':
			return isset($GLOBALS['meta']['lilyspip_server']);
			break;
		case 'install':
			break;
		case 'uninstall':
			foreach(array_keys($GLOBALS['meta']) as $meta) 
				if(strpos($meta, 'lilyspip_') !== false) effacer_meta($meta);
			ecrire_metas();
			break;
	}
}	

?>
