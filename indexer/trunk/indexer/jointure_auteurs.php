<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajoute les informations d’auteurs liés
 *
 * @param string $objet
 * @param int $id_objet
 * @param \Indexer\Sources\Document $doc
 * @return \Indexer\Sources\Document
 */
function indexer_jointure_auteurs_dist($objet, $id_objet, $doc) {
	// On va chercher tous les auteurs de cet objet
	if ($auteurs = sql_allfetsel(
		'*',
		'spip_auteurs as a join spip_auteurs_liens as l on a.id_auteur=l.id_auteur',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		foreach ($auteurs as $auteur) {
			$id_auteur = intval($auteur['id_auteur']);
			$doc->properties['auteurs']['noms'][$id_auteur] = trim(supprimer_numero($auteur['nom']));
			$doc->properties['auteurs']['ids'][] = $id_auteur;
			
			// Peut-être indexer l'email de chaque auteur⋅e ?
			if ($auteur['email'] and lire_config('indexer/'.$objet.'/jointure_auteurs/indexer_email')) {
				$doc->properties['auteurs']['emails'][$auteur['id_auteur']] = $auteur['email'];
			}
		}
		// et on garde la property authors
		$doc->properties['authors'] = array_values($doc->properties['auteurs']['noms']);

		// On ajoute le nom des auteurs en fulltext à la fin
		$doc->content .= "\n\n".join(', ', $doc->properties['auteurs']['noms']);
	}
	
	return $doc;
}
