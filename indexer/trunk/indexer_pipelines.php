<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Réindexer des choses lorsqu'il y a des modifications sur un objet
 *
 * @pipeline post_edition
 * @param array $flux Arguments et contenu du pipeline "post_edition"
 * @return Retourne le flux d'origine mais possiblement modifié
 */
function indexer_post_edition($flux){
	include_spip('base/connect_sql'); // pour être sûr d'avoir objet_type()
	
	// On trouve le type de l'objet
	if (isset($flux['args']['type'])){
		$objet = $flux['args']['type'];
	}
	elseif (isset($flux['args']['table'])){
		$objet = objet_type($flux['args']['table']);
	}
	// On trouve son identifiant
	if (isset($flux['args']['id_objet'])){
		$id_objet = $flux['args']['id_objet'];
	}
	
	// Si on a ce qu'il faut, on continue
	if ($objet and $id_objet){
		include_spip('inc/indexer');
		
		// On récupère toutes les sources compatibles avec l'indexation
		$sources = indexer_sources();
		
		// On parcourt toutes les sources et on garde celles on un rapport avec l'objet du pipeline
		foreach ($sources as $alias => $source){
			// Si une méthode pour définir explicitement existe, on l'utilise
			if (method_exists($source, 'getObjet')){
				$objet_source = $source->getObjet();
			}
			// Sinon on cherche avec l'alias donné à la source
			else{
				$objet_source = objet_type(strtolower($alias));
			}
			
			// Si l'objet de la source est le même que dans l'édition, on met à jour l'indexation de l'objet
			if ($objet_source == $objet){
				job_queue_add(
					'indexer_job_indexer_source',
					"Réindexer l'objet ($objet - $id_objet)",
					array($alias, $id_objet, $id_objet+1), // +1 car le test est normalement : id < $end
					'inc/indexer',
					true // pas de duplication
				);
			}
		}
	}
	
	return $flux;
}
