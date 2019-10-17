<?php
/**
 * Pipelines utilisées par plugin Noizetier : compléments
 *
 * @plugin    Noizetier : compléments
 * @copyright 2019
 * @author    Mukt
 * @licence   GNU/GPL
 * @package   SPIP\Noizetier_complements\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Modifier le tableau de valeurs envoyé par la fonction charger d’un formulaire CVT
 *
 * => Édition de noisette : ajout des saisies pour les classes
 *
 * @param array $flux
 * @return array
 */
function noizetier_extra_formulaire_charger($flux) {

	if (
		$flux['args']['form'] == 'editer_noisette'
		and $id_noisette = $flux['args']['args'][0]
		and $type_noisette = $flux['data']['type_noisette']
		and include_spip('inc/noizetier_extra')
		and is_array($saisies_classes = noizetier_lister_saisies_classes($type_noisette))
	) {

		// Ajouter les saisies dans un fieldset 'affichage'.
		$fieldset_affichage = false;
		foreach ($flux['data']['_champs_noisette'] as $k => $saisie) {
			if (
				$saisie['saisie'] === 'fieldset'
				and $saisie['options']['nom'] === 'affichage'
			) {
				$fieldset_affichage = true;
				$flux['data']['_champs_noisette'][$k]['saisies'] = array_merge($flux['data']['_champs_noisette'][$k]['saisies'], $saisies_classes);
				break;
			}
		}
		if (!$fieldset_affichage) {
			$flux['data']['_champs_noisette'][] = array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'affichage',
					'label' => _T('noizetier:label_saisies_affichage'),
					'pliable' => 'oui',
					'plie' => '',
				),
				'saisies' => $saisies_classes,
			);
		}

		// Récupérer les classes attribuées
		$classes_noisette = $flux['data']['est_conteneur'] == 'oui' ? $flux['data']['conteneur_css'] : $flux['data']['css'];
		$classes_noisette = explode(' ', trim($classes_noisette));
		$classes_noisette = array_filter($classes_noisette);

		// Ajouter les valeurs au contexte à partir des classes
		foreach ($saisies_classes as $saisie) {
			$type_saisie = $saisie['saisie'];
			$champ = $saisie['options']['nom'];

			// On identifie toutes les classes qui font partie
			// des valeurs acceptables de la saisie.
			$valeurs = array();
			if (include_spip("saisies/$type_saisie")) {
				$verifier_valeurs_acceptables = $type_saisie.'_valeurs_acceptables';
				if (function_exists($verifier_valeurs_acceptables)) {
					foreach ($classes_noisette as $classe) {
						if ($verifier_valeurs_acceptables($classe, $saisie)) {
							$valeurs[] = $classe;
						}
					}
				}
			}

			// Saisie à valeur unique ou multiple ?
			// On prend l'option explicite, sinon on compte le nombre.
			// Nb : pas 100% fiable :(
			if (!$valeur = _request($champ)) {
				// Valeur unique
				if (
					empty($saisie['option']['multiple'])
					and count($valeurs) === 1
				) {
					$valeur = $valeurs[0];
				// Valeurs multiples
				} else {
					$valeur = $valeurs;
				}
			}

			$flux['data'][$champ] = $valeur;
		}
	}

	return $flux;
}


/**
 * Vérifier les valeurs postées
 *
 * => Édition de noisette : gestion des classes.
 * On prend les valeurs postées dans les saisies afférentes
 * et on les ajoute aux champs css.
 *
 * @param array $flux
 * @return array
 */
function noizetier_extra_formulaire_verifier($flux) {

	if (
		$flux['args']['form'] == 'editer_noisette'
		and !$flux['data'] // pas d'erreur
		and $id_noisette = $flux['args']['args'][0]
		and $type_noisette = _request('type_noisette')
		and include_spip('inc/noizetier_extra')
		and is_array($saisies_classes = noizetier_lister_saisies_classes($type_noisette))
	) {

		// Vérifier d'abord les erreurs des saisies extras
		// (le noizetier ne vérifie que celles déclarées dans le yaml).
		include_spip('inc/saisies');
		if (!$erreurs = saisies_verifier($saisies_classes)) {

			// Récupérer les classes attribuées
			$classes_noisette = trim(_request('conteneur_css').' '. _request('css'));
			$classes_noisette = explode(' ', $classes_noisette);
			$classes_noisette = array_filter($classes_noisette);

			// On met à jour la liste des classes attribuées
			// en fonction des valeurs postées dans les saisies extras.
			foreach ($saisies_classes as $saisie) {
				$type_saisie = $saisie['saisie'];
				$champ = $saisie['options']['nom'];

				// D'abord on nettoie en retirant toutes les valeurs possibles de la saisie.
				$classes_champ = array();
				if (include_spip("saisies/$type_saisie")) {
					$verifier_valeurs_acceptables = $type_saisie.'_valeurs_acceptables';
					if (function_exists($verifier_valeurs_acceptables)) {
						foreach ($classes_noisette as $classe) {
							if ($verifier_valeurs_acceptables($classe, $saisie)) {
								$classes_champ[] = $classe;
							}
						}
					}
				}
				$classes_noisette = array_diff($classes_noisette, $classes_champ);

				// Puis on ajoute la valeur postée
				if (!is_null($valeur = _request($champ))) {
					if (is_array($valeur)) {
						$classes_noisette = array_merge($classes_noisette, $valeur);
					} else {
						$classes_noisette[] = $valeur;
					}
				}
			}

			// On met à jour le champ contenant les classes.
			$classes_noisette = implode(' ', array_unique($classes_noisette));
			set_request('css', $classes_noisette);
			set_request('conteneur_css', $classes_noisette);

		} else {
			$flux = $erreurs;
		}

		//var_dump($classes_noisette);
		// $flux['message_erreur'] = 'Debug noizetier_extra';
	}

	return $flux;
}
