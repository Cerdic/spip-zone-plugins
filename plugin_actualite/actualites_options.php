<?php

/* Definir une bonne fois pour touts les repertoires par defaut */
	// le repertoire des images
	define("_DIR_ACTUALITES_IMG_PACK", _DIR_PLUGIN_ACTUALITES."img_pack/");
	// le repertoire prive
	define("_DIR_ACTUALITES_PRIVE", _DIR_PLUGIN_ACTUALITES."prive/");


/* Plugin Corbeille (compatibilite)  */
	
	// Indique au plugin les nouveaux objets a gerer
	global $corbeille_params;
	$corbeille_params["actualites"] = array (
			"statut" => "poubelle",
			"table" => "spip_actualites",
			"tableliee"  => array("spip_mots_actualites"),
	);

?>
