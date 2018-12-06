<?php
/**
 * Définit les fonctions du plugin Documentation Noizetier
 *
 * @plugin     Noizetier_doc
 * @copyright  2018
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\NoizetierDoc\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
function lister_saisies_options() {
	include_spip('inc/saisies_lister');
	// On a déjà la liste par saisie
	$saisies = saisies_lister_disponibles();

	// On construit une liste par options
	$options = array();
	foreach ($saisies as $type_saisie => $saisie) {
		$options_saisie = saisies_lister_par_nom($saisie['options'], false);
		foreach ($options_saisie as $nom => $option) {
			// Si l'option n'existe pas encore
			if (!isset($options[$nom])) {
				$options[$nom] = _T_ou_typo($option['options']);
			}
			// On ajoute toujours par qui c'est utilisé
			$options[$nom]['utilisee_par'][] = $type_saisie;
		}
		ksort($options_saisie);
		$saisies[$type_saisie]['options'] = $options_saisie;
	}
	ksort($options);

	return array(
		'saisies' => $saisies,
		'options' => $options,
	);
}