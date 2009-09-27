<?php 

	// inc/player_pipeline_affiche_milieu.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Ajoute la boite en fin de page de configuration Fonctions avancees
function player_affiche_milieu ($flux) {

	$exec = $flux['args']['exec'];

	if ($exec == 'config_fonctions'){	
		include_spip('inc/player_affiche_config_form');
		$flux['data'] .= player_affiche_config_form($exec);
	}

	return($flux);
}

?>