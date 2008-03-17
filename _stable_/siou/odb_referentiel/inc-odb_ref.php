<?php


/**
 Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ODB_REF',(_DIR_PLUGINS.end($p)));


function odb_ref_ajouterBoutons($boutons_admin) {
   // si on est admin
   if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
   AND $GLOBALS["options"]=="avancees") {
      // on voit le bouton dans la barre "configuration"
      $boutons_admin['configuration']->sousmenu['odb_ref']= new Bouton(
      "../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png",  // icone
      'R&eacute;f&eacute;rentiel ODB'	// titre
      );
   }
	
	return $boutons_admin;
}

function odb_ref_ajouterOnglets($flux) {
	$rubrique = $flux['args'];
	return $flux;
}

	
?>

