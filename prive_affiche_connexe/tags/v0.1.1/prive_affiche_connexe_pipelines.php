<?php
/**
 * Utilisations de pipelines par Affiche connexe
 *
 * @plugin     Affiche connexe
 * @copyright  2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Affiche_connexe\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Modifier le HTML compilé d'un squelette
 *
 * Supprimer le contenu ajouté par affiche_milieu
 *
 * @note
 * On ne peut pas faire ça dans afficher_fiche_objet car il est appelé *avant* affiche_milieu
 *
 * @pipeline recuperer_fond
 * @param  array $page Données du pipeline
 * @return array       Données du pipeline
 */
function prive_affiche_connexe_recuperer_fond($flux) {

	$fond_contenu = 'prive/squelettes/contenu';
	if (
		// C'est un squelette de contenu
		substr($flux['args']['fond'], 0, strlen($fond_contenu)) === $fond_contenu
		// C'est le contenu d'un objet éditorial
		and !empty($flux['args']['contexte']['exec'])
		and $exec = trouver_objet_exec($flux['args']['contexte']['exec'])
		// Pas en mode édition
		and $exec['edition'] === false
	) {

		// On récupère le contenu ajouté par le pipeline afiche_milieu
		$affiche_milieu = pipeline(
			'affiche_milieu',
			array(
				'args' => $flux['args']['contexte'],
				'data' => '',
			)
		);
		// Puis on le supprime
		$flux['data']['texte'] = str_replace($affiche_milieu, '', $flux['data']['texte']);

	}

	return $flux;
}


/**
 * Ajouter ou modifier du contenu dans la colonne de droite de l'espace privé
 *
 * Insérer le contenu de affiche_milieu avant le reste
 *
 * @note
 * La position du contenu ajouté n'est pas complètement fiable : un autre plugin peut passer après coup.
 *
 * @pipeline affiche_droite
 * @param  array $page Données du pipeline
 * @return array       Données du pipeline
 */
function prive_affiche_connexe_affiche_droite($flux) {

	// On récupère le contenu de affiche_milieu
	$affiche_connexe = pipeline(
		'affiche_milieu',
		array(
			'args' => $flux['args'],
			'data' => '',
		)
	);
	$affiche_connexe_wrap = '<div class="affiche_connexe">' . $affiche_connexe . '</div>';

	// Vérifier qu'il ne s'agisse pas d'un div.ajax vide
	if (!preg_match('/<div\s*class=["\']ajax["\']>\s*<\/div>/i', $affiche_connexe)) {

		// S'il y a un marqueur, on s'insère à ce niveau
		// On encapsule dans un div.affiche_connexe s'il n'y en a pas de présent
		$marqueur = '<!--affiche_connexe-->';
		if (($pos = strpos($flux['data'], $marqueur)) !== false) {
			$div = preg_match('/<div\s*class=["\'][^"\']*affiche_connexe[^"\']*["\']>/i', $flux['data']);
			$replace = $div ? $affiche_connexe : $affiche_connexe_wrap;
			$flux['data'] = substr_replace($flux['data'], $replace, $pos, 0);

		// Sinon, on s'insère au début (en encapsulant dans un div.affiche_connexe)
		} else {
			$flux['data'] = $affiche_connexe_wrap . $flux['data'];
		}

	}

	return $flux;
}