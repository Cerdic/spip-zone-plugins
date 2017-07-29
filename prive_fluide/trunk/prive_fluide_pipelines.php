<?php
/**
 * Pipelines utiles au plugin Espace privé fluide.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Pipelines
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Affichage des formulaires.
 *
 * Configuration des préférences : ajout d'un message sur l'option de l'argeur d'écran
 *
 * @param array $flux
 *     tableau
 * @return array
 */
function prive_fluide_formulaire_fond ($flux){

	if ($flux['args']['form'] == 'configurer_preferences'){

		$ajouter = '<p class="explication">' . _T('prive_fluide:message_configurer_largeur_ecran') . '</p>';
		$cherche = "/(editer_spip_ecran[\"\']>\s*<label>[^<]+<\/label>)/is";
		$remplace = "$1$ajouter";
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);

	}

	return $flux;
}
