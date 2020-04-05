<?php
/**
 * Fonctions utiles au plugin Levenshtein
 *
 * @plugin     Levenshtein
 * @copyright  2016
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Levenshtein\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Calculer la distance de Levenshtein et renvoyer un index contenant les mots
 * les plus proches.
 *
 * @param string $term
 * @access public
 * @return array
 */
function levenshtein_calculer($term) {

	// La comparaison ce fait sans les majuscules
	$term = strtolower($term);

	// On demande au pipeline de travailler de sortir les mots qui s'approche
	$get_lev = pipeline('levenshtein_calculer', array('mot' => array(), 'term' => $term));

	// Simplification du tableau allfetsel
	$get_lev = array_column($get_lev['mot'], 'mot');

	// Cette variable va contenu un index des distances de levenshtein
	$lev_index = array();

	// On boucle sur les SOUNDEX
	foreach ($get_lev as $lev) {
		// On calcule la distance
		$distance = levenshtein($term, $lev);

		// Si on trouve un mot, il n'y a pas de faute, on renvoie directement
		if ($distance == 0) {
			return $lev;
		} else {
			// ce n'est pas un perfect match, on va ajouter le mot
			// à notre index de distance
			$index[$lev] = $distance;
		}
	}

    if (!empty($index)) {
        // Trier le tableau pour avoir les plus petites distances au dessus
        asort($index);

        // On chercher la distance la plus proche
        $min = min($index);

        // Renvoyer les mots les plus probables
        $index = array_keys($index, $min);

        return $index;
    } else {
        return false;
    }
}

/**
 * Activer le calcul de la distance de Levenshtein automatiquement
 * La source est récupérer dans une variable d'environnement
 *
 * @param string $source
 * @access public
 * @return string
 */
function levenshtein_recherche_spip($source = 'recherche') {
	$term = _request($source);
	$lev = levenshtein_calculer($term);

	if (is_array($lev)) {
		// Récupérer l'url en cours
		$url = self();

		// On va créer automatiquement des liens avec le paramètre url remplacé
		foreach ($lev as $key => $mot) {
			$url = parametre_url($url, $source, $mot);
			$lev[$key] = '<a href="'.$url.'">'.$mot.'</a>';
		}

		return _T(
			'levenshtein:proposition',
			array('proposition' => implode(', ', $lev))
		);
	}

	return '';
}

/**
 * Une balise à insérer dans les squelettes pour proposer des alternatives
 * Par défaut la balise fonctionne avec le terme recherche. On peux cependant
 * passer changer pour regarder dans une autre variable.
 *
 * @param mixed $p
 * @access public
 * @return mixed
 */
function balise_LEVENSHTEIN_dist($p) {

	$source = interprete_argument_balise(1, $p);

	$p->code = "levenshtein_recherche_spip($source)";
	$p->interdire_script = false;

	return $p;
}
