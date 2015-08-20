<?php

/**
 * Ajouter la source de données documents
 *
 *
 * @param $sources les sources déjà déclarées pour indexer
 * @return Sources Retourne le flux du pipeline complété
 */
function indexerdoc_indexer_sources($sources) {

    include_spip('Sources/Documents');

    if (is_null($sources)){
		// On crée la liste des sources
		$sources = new Indexer\Sources\Sources();
    }
    // Par défaut on enregistre les articles du SPIP
    $sources->register('documents', new Spip\Indexer\Sources\Documents());

    return $sources;
}