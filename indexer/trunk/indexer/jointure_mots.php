<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function indexer_jointure_mots_dist($objet, $id_objet, $infos) {
	$where = array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet));
	
	// On cherche s'il y a des groupes à ignorer
	if (
		$groupes_ignores = lire_config('indexer/'.$objet.'/jointure_mots/groupes_ignores')
		and is_array($groupes_ignores)
	) {
		$where[] = sql_in('id_groupe', $groupes_ignores, 'not');
	}
	
	// On va chercher tous les mots de cet objet
	if ($mots = sql_allfetsel(
		'*',
		'spip_mots as m join spip_mots_liens as l on m.id_mot=l.id_mot',
		$where
	)) {
		foreach ($mots as $mot) {
			$id_mot = intval($mot['id_mot']);
			$infos['properties']['mots']['titres'][$id_mot] = supprimer_numero($mot['titre']);
			$infos['properties']['mots']['ids'][] = $id_mot;
		}
		
		// On ajoute le nom des mots en fulltext à la fin
		$infos['content'] .= "\n\n".join(' | ', $infos['properties']['mots']['titres']);
	}
	
	return $infos;
}
