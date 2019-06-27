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

		// Récupérer les classes attribuées
		$classes_noisette = explode(' ', $flux['data']['est_conteneur'] == 'oui' ?
			$flux['data']['conteneur_css'] :
			$flux['data']['css']
		);

		// Ajouter les saisies
		// S'il y a un fieldset 'affichage' à la racine, on les met dedans, sinon à la fin.
		$fieldset_affichage = false;
		foreach($flux['data']['_champs_noisette'] as $k => $saisie) {
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
			$flux['data']['_champs_noisette'] = array_merge($flux['data']['_champs_noisette'], $saisies_classes);
		}

		// Ajouter les valeurs au contexte
		foreach($saisies_classes as $saisie) {
			$champ = $saisie['options']['nom'];
			$classes_champ = isset($saisie['options']['data']) ?
				$saisie['options']['data'] :
				(isset($saisie['options']['datas']) ?
					$saisie['options']['datas'] :
					array());
			// Soit c'est une saisie avec des valeurs prédéfinies (option data)
			// On recoupe les classes de la noisette avec celles de la saisie
			if (
				$classes_champ
				and $valeur = array_intersect(array_keys($classes_champ), $classes_noisette)
			){
				$flux['data'][$champ] = $valeur;
			// Soit on prend la valeur postée
			} else {
				$flux['data'][$champ] = _request($champ);
			}
		}

	}

	return $flux;
}


/**
 * Vérifier les valeurs postées
 *
 * => Édition de noisette : gestion des classes
 * On prend les valeurs postées dans les saisies afférentes
 * et on les ajoute aux champs css
 *
 * @param array $flux
 * @return array
 */
function noizetier_extra_formulaire_verifier($flux) {

	if (
		$flux['args']['form'] == 'editer_noisette'
		and $id_noisette = $flux['args']['args'][0]
		and $type_noisette = _request('type_noisette')
		and include_spip('inc/noizetier_extra')
		and is_array($saisies_classes = noizetier_lister_saisies_classes($type_noisette))
	) {

		$classes_noisette = array_filter(explode(' ', _request('conteneur_css').' '. _request('css')));

		// Giboliner les classes
		foreach($saisies_classes as $saisie) {
			$champ = $saisie['options']['nom'];
			// D'abord, nettoyer
			$classes_champ = isset($saisie['options']['data']) ?
				$saisie['options']['data'] :
				(isset($saisie['options']['datas']) ?
					$saisie['options']['datas'] :
					array());
			$classes_noisette = array_diff($classes_noisette, $classes_champ);
			// Puis ajouter la valeur postée
			if (!is_null($valeur = _request($champ))) {
				if (is_array($valeur)) {
					$classes_noisette = array_merge($classes_noisette, $valeur);
				} else {
					$classes_noisette[] = $valeur;
				}
			}
		}

		// Mise à jour du champ
		$classes_noisette = implode(' ', array_unique($classes_noisette));
		set_request('css', $classes_noisette);
		set_request('conteneur_css', $classes_noisette);

		// var_dump($classes_noisette);
		// $flux['message_erreur'] = 'stop : debug';
	}

	return $flux;
}