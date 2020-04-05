<?php
include_spip('inc/config');
include_spip('action/editer_liens');
if (!defined('_INDEXERDOC_OBJETS_LIES')){
	define ('_INDEXERDOC_OBJETS_LIES',0);// A mettre sur 1 dans mes_options.php si on veut que le contenu du document soit également indexé dans l'objet lié
}
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
				$extraire_document = charger_fonction('extraire_document', 'inc');
				$extraire = $extraire_document($flux['args']['champs']);
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
	
	// si on a demandé à indexer le documents dans l'objet lié
	if (_INDEXERDOC_OBJETS_LIES and $flux['args']['objet'] != 'document') {
		if (defined('_DIR_PLUGIN_EXTRAIREDOC')) {
			$extraire_document = charger_fonction('extraire_document', 'inc');
		}
		$id_objet = $flux['args']['id_objet'];
		$objet = $flux['args']['objet'];
		// récuperer tous les documents liés
		$documents_lies = objet_trouver_liens(
			array('document'=>'*'),
			array($objet=>$id_objet)
		);
		
		// les parcourir tous
		foreach ($documents_lies as $document){
			$id_document = $document['id_document'];
			$tableau_doc = array('id_document'=>$id_document);
			$extraire = $extraire_document($tableau_doc); // on refait l'extrait plutot que de prendre dans ce qui existe en sphinx, parce qu'on ne sait pas quand le document a été indexé par rapport à l'objet (et en plus la requete sphinx, je sais pas la faire en php)
			$flux['data']->content  .= "\n\n" . $extraire['contenu'];
		}
	}
	
	return $flux;
}

/**
 * Réindexer un objet lorsque la liaison d'un document à cet objet est ajouté ou supprimé
 * @pipeline post_edition_lien
 * @param array $flux Arguments et contenu du pipeline "post_edition"
 * @return Retourne le flux d'origine mais possiblement modifié
 */
function indexerdoc_post_edition_lien($flux){
	if ($flux['args']['objet_source'] == "document"){ // si on modifie la liaison d'un document
		$objet = $flux['args']['objet'];
		$id_objet = $flux['args']['id_objet'];
		indexer_redindex_objet($objet, $id_objet);
	}
	
	return $flux;
}
