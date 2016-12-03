<?php
include_spip('inc/config');

/**
 * Modifier la source pour l'objet "document"
 *
 * @pipeline indexer_document
 * @param array $flux Tableau du flux du pipeline
 * @return array Retourne le flux possiblement modifié
 */
function indexerdoc_indexer_document($flux) {
	if ($flux['args']['objet'] == 'document') {
		$document = &$flux['data'];
		$extraire = array('contenu' => false);
		
		// On teste les types de document :
		// s'il y a des types précis configurés et que ce doc n'en fait PAS partie, on supprime
		if (
			isset($flux['args']['champs']['extension'])
			and $types = lire_config('indexer/document/types_acceptes')
			and !empty($types)
			and !in_array($flux['args']['champs']['extension'], $types)
		) {
			$document->to_delete = true;
		}
		// Sinon on essaye d'extraire le contenu du fichier
		else {
			// Extraire le contenu si possible
			if (defined('_DIR_PLUGIN_EXTRAIREDOC')) {
				include_spip('inc/extraire_document');
				$extraire = inc_extraire_document($flux['args']['champs']);
			}
			
			// Si le document n'avait pas de titre, on met le nom du fichier
			if (empty($document->title)) {
				$document->title = $flux['args']['champs']['fichier'];
			}
			
			// Si on a réussi à extraire le document, on ajoute son contenu
			if ($extraire['contenu']) {
				$document->content .= "\n\n" . $extraire['contenu'];
			}
		}
	}
	
	return $flux;
}
