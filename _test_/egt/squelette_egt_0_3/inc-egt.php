<?php
/**
 squelette egt v0.3 - auteur: sBa - licence GPL 
*/
/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_EGT',(_DIR_PLUGINS.end($p)));

function egt_ajouterBoutons($boutons_admin) {

	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['configuration']->sousmenu['egt_conf']= new Bouton(
		"../"._DIR_PLUGIN_EGT."/img_pack/egt.png",  // icone
		'egt'	// titre
		);
	
	return $boutons_admin;
}

function egt_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}
	
?>

