<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function indexer_jointure_auteurs_dist($objet, $id_objet, $infos) {
	// On va chercher tous les auteurs de cet objet
	if ($auteurs = sql_allfetsel(
		'*',
		'spip_auteurs as a join spip_auteurs_liens as l on a.id_auteur=l.id_auteur',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		foreach ($auteurs as $auteur) {
			$id_auteur = intval($auteur['id_auteur']);
			$infos['properties']['auteurs']['noms'][$id_auteur] = $auteur['nom'];
			$infos['properties']['auteurs']['ids'][] = $id_auteur;
			
			// Peut-être indexer l'email de chaque auteur⋅e ?
			if ($auteur['email'] and lire_config('indexer/'.$objet.'/jointure_auteurs/indexer_email')) {
				$infos['properties']['auteurs']['emails'][$auteur['id_auteur']] = $auteur['email'];
			}
		}
		// et on garde la property authors
		$infos['properties']['authors'] = array_values($infos['properties']['auteurs']['noms']);

		// On ajoute le nom des auteurs en fulltext à la fin
		$infos['content'] .= "\n\n".join(' | ', $infos['properties']['auteurs']['noms']);
	}
	
	return $infos;
}
