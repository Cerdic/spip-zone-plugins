<?php

/**
 * Modifier la source pour l'objet "document"
 *
 * @pipeline indexer_document
 * @param array $flux Tableau du flux du pipeline
 * @return array Retourne le flux possiblement modifié
 */
function indexerdoc_indexer_document($flux) {
	if ($flux['args']['objet'] == 'document') {
		$document =& $flux['data'];
		$extraire = array('contenu' => false);
		
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
	
	return $flux;
}
