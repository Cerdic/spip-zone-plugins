<?php
/**
 * Utilisations de pipelines par Lim
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function lim_afficher_config_objet($flux){
	$type = $flux['args']['type'];
	if ($type == 'article'){

		$tab_data = explode("<div class='ajax'>", $flux['data']);
		$tab_data[1] = "<div class='ajax'>".$tab_data[1];
		$tab_data[2] = "<div class='ajax'>".$tab_data[2];

		if ( strpos($tab_data[1], 'formulaire_activer_forums') AND lire_config('forums_publics') == 'non' AND lire_config('lim/forums_publics') == 'on' ) {
			$tab_data[1] = '';
		}
		if ( strpos($tab_data[2], 'formulaire_activer_petition') AND lire_config('lim/petitions') == 'on') {
			$tab_data[2] = '';
		}
		$flux['data'] = $tab_data[1].$tab_data[2];
	}
	return $flux;
}
?>