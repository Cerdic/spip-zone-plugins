<?php
/**
 * Gestion du formulaire de d'édition de vimeo
 *
 * @plugin     Videos
 * @copyright  2014
 * @author     Charles Stephan
 * @licence    GNU/GPL
 * @package    SPIP\Video\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');


/*
	Déclaration des champs du formulaire
*/


function formulaires_configurer_vimeo_saisies_dist(){

	include_spip('inc/yaml');
	include_spip('inc/config');

	$fichier = find_in_path("formulaires/configurer_vimeo_saisies.yaml");
	lire_fichier($fichier, $yaml);
	$yaml = yaml_decode($yaml);

	$compte = lire_config('vimeo');
	
	foreach ($yaml as $key => $value)
		$yaml[$key]['options']['defaut'] = $compte[$value['options']['nom']];

	return $yaml;

}

?>