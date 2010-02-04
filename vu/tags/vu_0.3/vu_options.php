<?php

/* Definir une bonne fois pour touts les repertoires par defaut */
	// le repertoire des images
	define("_DIR_VU_IMG_PACK", _DIR_PLUGIN_VU."img_pack/");
	// le repertoire prive
	define("_DIR_VU_PRIVE", _DIR_PLUGIN_VU."prive/");


/* Plugin Corbeille (compatibilite)  */
	
	// Indique au plugin les nouveaux objets a gerer
	global $corbeille_params;
	$corbeille_params["annonces"] = array (			"statut" => "poubelle",
			"table" => "spip_vu_annonces",
			"tableliee"  => array("spip_mots_vu_annonces"),
	);

	$corbeille_params["evenements"] = array (
			"statut" => "poubelle",
			"table" => "spip_vu_evenements",
			"tableliee"  => array("spip_mots_vu_evenements"),
	);

	$corbeille_params["publications"] = array (
			"statut" => "poubelle",
			"table" => "spip_vu_publications",
			"tableliee"  => array("spip_mots_vu_publications"),
	);
	
?>
