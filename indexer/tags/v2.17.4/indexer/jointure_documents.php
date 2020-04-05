<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajoute les informations de documents joints
 *
 * @param string $objet
 * @param int $id_objet
 * @param \Indexer\Sources\Document $doc
 * @return \Indexer\Sources\Document
 */
function indexer_jointure_documents_dist($objet, $id_objet, $doc) {
	// On va chercher tous les documents de cet objet
	if ($documents = sql_allfetsel(
		'*',
		'spip_documents as a join spip_documents_liens as l on a.id_document=l.id_document',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		foreach ($documents as $document) {
			if ($document['titre'] or $document['descriptif'])
				$doc->content .= "\n\n" . trim(supprimer_numero($document['titre'])) . " | " . trim($document['descriptif']);
		}
	}
	
	return $doc;
}
