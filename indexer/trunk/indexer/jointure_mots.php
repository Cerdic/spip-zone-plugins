<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('indexer_fonctions');

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
		$infos['properties']['mots']['ids_hierarchie'] = array();
		$infos['properties']['mots']['titres_hierarchie'] = array();
		$infos['properties']['tagsbygroups'] = array();

		foreach ($mots as $mot) {
			$id_groupe = $mot['id_groupe'];
			// old school: recupere le titre du groupe de mots…
			$type = trim(supprimer_numero($mot['type']));
			// ajouter le mot à la propriété tagsbygroups.ID_GROUPE
			// et tagsbytype.NOMDUGROUPE
			// créer le tableau de groupe le cas échéant
			if (!array_key_exists($id_groupe,$infos['properties']['tagsbygroups'])){
				$infos['properties']['tagsbygroups'][$id_groupe] = array();
				$infos['properties']['tagsbytype'][$type] = array();
			}
			$infos['properties']['tagsbygroups'][$id_groupe][] = trim(supprimer_numero($mot['titre'])); 
			$infos['properties']['tagsbytype'][$type][] = trim(supprimer_numero($mot['titre'])); 

			$id_mot = intval($mot['id_mot']);
			$infos['properties']['mots']['titres'][$id_mot] = trim(supprimer_numero($mot['titre']));
			$infos['properties']['mots']['ids'][] = $id_mot;
			
			// On s'occupe de la hiérarchie de chaque mot
			$id_parent = $mot['id_groupe'];
			$titres = array($infos['properties']['mots']['titres'][$id_mot]);
			$ids_hierarchie = array();
			$titres_hierarchie = array();
			
			// Si on a le plugin groupes arborescents, on le prend en compte
			$select = array('titre');
			if (defined('_DIR_PLUGIN_GMA')) {
				$select[] = 'id_parent';
			}
			
			while ($id_parent && ($f = sql_fetsel(
				$select,
				'spip_groupes_mots',
				'id_groupe = '.$id_parent
			))) {
				$titre_actuel = trim(supprimer_numero($f['titre']));
				
				// On ajoute ce parent suivant au début du tableau
				$titres = array_merge(array($id_parent=>$titre_actuel), $titres);
				
				// On passe au parent suivant s'il existe
				if (isset($f['id_parent'])) {
					$id_parent = $f['id_parent'];
				}
				else {
					$id_parent = 0;
				}
			}
			// C'est seulement une fois qu'on a tous les titres qu'on peut réussir à construire les bons hashs
			foreach ($titres as $titre) {
				$id_hierarchie = indexer_id_hierarchie($titres_hierarchie, $titre);
				$ids_hierarchie[] = $id_hierarchie;
				$titres_hierarchie[$id_hierarchie] = $titre;
			}
			// Maintenant on peut ajouter cette hiérarchie au truc complet
			$infos['properties']['mots']['ids_hierarchie'] = array_merge($infos['properties']['mots']['ids_hierarchie'], $ids_hierarchie);
			$infos['properties']['mots']['titres_hierarchie'] = array_merge($infos['properties']['mots']['titres_hierarchie'], $titres_hierarchie);
		}
		// et on garde la property tags
		$infos['properties']['tags'] = array_values($infos['properties']['mots']['titres']);
		
		// On ajoute le nom des mots en fulltext à la fin
		$infos['content'] .= "\n\n".join(' / ', array_map('extraire_multi', $infos['properties']['mots']['titres']));
	}
	return $infos;
}
