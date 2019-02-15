<?php
/**
 * Pipelines utilisées par le plugin Noizetier : agencements
 *
 * @plugin    Noizetier : agencements
 * @copyright 2019
 * @author    Mukt
 * @licence   GNU/GPL
 * @package   SPIP\Noizetier_agencements\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Modifier le résultat de la compilation du squelette d'un formulaire
 *
 * => Édition de noisette : ajout des scripts pour les saisies
 *
 * @param array $flux
 * @return array
 */
function noizetier_layout_formulaire_fond($flux) {

	if (
		noizetier_layout_grille()
		and $flux['args']['form'] == 'editer_noisette'
		and !empty($flux['args']['contexte']['id_noisette'])
	) {
		// Le javascript
		$js = recuperer_fond('prive/javascript/saisies_grid_init.js');
		$flux['data'] .= $js;
	}

	return $flux;
}


/**
 * Modifier le tableau de valeurs envoyé par la fonction charger d’un formulaire CVT
 *
 * => Édition de noisette : ajout des saisies pour les classes et le layout
 *
 * @param array $flux
 * @return array
 */
function noizetier_layout_formulaire_charger($flux) {

	if (
		noizetier_layout_grille()
		and $flux['args']['form'] == 'editer_noisette'
		and $id_noisette = $flux['args']['args'][0]
		and include_spip('inc/noizetier_layout')
		and $elements_grille = noizetier_layout_identifier_element_grille($id_noisette)
	) {

		include_spip('inc/saisies');

		foreach ($elements_grille as $element) {
			// Ajout des saisies
			$saisies = noizetier_layout_lister_saisies($element, $id_noisette);
			$flux['data']['_champs'] = array_merge($flux['data']['_champs'], $saisies);
			// Récupération des valeurs
			$parametre = 'css_' . $element;
			$classes_element = $flux['data'][$parametre];
			if ($contexte = noizetier_layout_contextualiser_classes($element, $classes_element, $id_noisette)) {
				$flux['data'] = array_merge($flux['data'], $contexte);
			}
		}
	}

	return $flux;
}


/**
 * Complète les traitements d’un formulaire CVT
 *
 * => Édition de noisette :
 * - container : enregistrer le paramètre
 *
 * @param array $flux
 * @return array
 */
function noizetier_layout_formulaire_traiter($flux) {

	if (
		noizetier_layout_grille()
		and $flux['args']['form'] == 'editer_noisette'
		and $id_noisette = $flux['args']['args'][0]
		and include_spip('inc/noizetier_layout')
		and $elements_grille = noizetier_layout_identifier_element_grille($id_noisette)
	) {

		include_spip('inc/saisies_lister');
		include_spip('inc/ncore_noisette');

		$parametres = unserialize(sql_getfetsel('parametres', 'spip_noisettes', 'id_noisette='.intval($id_noisette)));
		$grille     = noizetier_layout_decrire_grille();

		// Préparer les paramètres
		foreach ($elements_grille as $element) {
			$saisies         = noizetier_layout_lister_saisies($element, $id_noisette);
			$saisies_par_nom = saisies_lister_par_nom($saisies);
			$parametre       = 'css_' . $element;
			$classe_base     = $grille['classes_base'][$element];
			$classes_element = array();
			// Récupérer les valeurs postées
			foreach($saisies_par_nom as $champ => $saisie) {
				// Toutes les saisies ne sont pas forcément pertinentes
				if (
					isset($saisie['grille'])
					and $saisie['saisie'] != 'fieldset'
				) {
					if ($valeur = _request($champ)) {
						if (is_array($valeur)) {
							$valeur = array_filter(array_values($valeur));
							$classes_element = array_merge($classes_element, $valeur);
						} else {
							$classes_element[] = $valeur;
						}
					}
				}
			}
			// S'assurer de la présence des classes de base (.row, .column...)
			if (!in_array($classe_base, $classes_element)) {
				array_unshift($classes_element, $classe_base);
			}
			$classes_element = implode(' ', $classes_element);
			$parametres[$parametre] = $classes_element;
		}

		// Mettre à jour la noisette
		noisette_parametrer(
			'noizetier',
			intval($id_noisette),
			array('parametres' => serialize($parametres))
		);

	}

	return $flux;
}


/**
 * Ajoute des choses dans le head de l'espace privé
 *
 * => Radios to slider + Rangeslider
 *
 * @param string $flux
 * @return string
 */
function noizetier_layout_header_prive($flux) {

	if (noizetier_layout_grille()) {

		// Radios to slider
		$js   = find_in_path('prive/javascript/radios-to-slider/jquery.radios-to-slider.min.js');
		$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
		$css  = find_in_path('prive/javascript/radios-to-slider/radios-to-slider.min.css');
		$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";

		// Rangeslider
		/*
		$js   = find_in_path('prive/javascript/rangeslider/rangeslider.min.js');
		$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
		$css  = find_in_path('prive/javascript/rangeslider/rangeslider.css');
		$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";
		*/
	}

	return $flux;
}


/**
 * Ajoute des choses dans le head du site public
 *
 * => Feuille de style de la grille
 *
 * @param string $flux
 * @return string
 */
function noizetier_layout_insert_head($flux) {

	if (
		noizetier_layout_grille()
		and include_spip('inc/config')
		and lire_config('noizetier_layout/inclure_css_public')
		and include_spip('inc/noizetier_layout')
		and is_string($css = noizetier_layout_decrire_grille('css_public'))
		and $css = find_in_path($css)
	) {

		$flux .= "\n<!--Plugin noiZetier : agencements-->\n<link rel='stylesheet' href='$css' type='text/css' media='all' />\n";

	}

	return $flux;
}