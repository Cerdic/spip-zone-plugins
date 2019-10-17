<?php
/**
 * Fonctions spécifiques à une saisie
 *
 * @package SPIP\Saisies\checkbox
 */


// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Vérifie que la valeur postée
 * correspond aux valeurs proposées lors de la config de valeur
 * @param string|array $valeur la valeur postée
 * @param array $description la description de la saisie
 * @return bool true si valeur ok, false sinon,
 */
function medias_selection_valeurs_acceptables($valeur, $description) {

	$acceptable = false;
	$options = $description['options'];

	// La valeur peut être un tableau avec les valeurs pour chaque média.
	$valeurs = is_array($valeur) ? array_filter($valeur) : array($valeur);

	foreach ($valeurs as $k => $valeur) {
		if ($valeur == '' and !isset($options['obligatoire'])) {
			$acceptable = true;
		}
		if (saisies_verifier_gel_saisie($description) and isset($options['defaut'])) {
			$acceptable = ($valeur == $options['defaut']);
		} else {
			$data = saisies_trouver_data($description, true);
			$data = saisies_aplatir_tableau($data);
			$data = array_keys($data);
			// Ajouter les variantes médias aux datas
			if (!empty($description['options']['medias'])) {
				include_spip('inc/noizetier_layout');
				$medias = array_filter(array_keys(noizetier_layout_decrire_grille('medias')));
				foreach ($data as $classe) {
					foreach ($medias as $media) {
						$data[] = noizetier_layout_creer_classe_media($classe, $media);
					}
				}
			}
			if (isset($options['disable_choix'])) {
				$disable_choix = explode(',', $options['disable_choix']);
				$data = array_diff($data, $disable_choix);
			}
			$acceptable = (in_array($valeur, $data));
		}
		if (!$acceptable) {
			break;
		}
	}

	return $acceptable;
}
