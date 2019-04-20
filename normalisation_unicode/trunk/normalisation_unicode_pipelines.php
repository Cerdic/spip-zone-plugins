<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Normalise l'unicode juste avant l'édition d'un texte
 * @param array $flux l'entrée du pipeline
 * @return array $flux le flux modifié
**/
function normalisation_unicode_pre_edition($flux){
	if ($flux['args']['action'] == 'modifier') {
		foreach ($flux['data'] as $champ => $valeur) {
			$flux['data'][$champ] = normalizer_normalize ($valeur, Normalizer::FORM_C);
		}
	}
	return $flux;
}
