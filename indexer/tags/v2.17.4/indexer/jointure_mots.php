<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('indexer_fonctions');

/**
 * Ajoute les informations de mots clés liés
 *
 * @param string $objet
 * @param int $id_objet
 * @param \Indexer\Sources\Document $doc
 * @return \Indexer\Sources\Document
 */
function indexer_jointure_mots_dist($objet, $id_objet, $doc) {
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
		$doc->properties['mots']['ids_hierarchie'] = array();
		$doc->properties['mots']['titres_hierarchie'] = array();
		$doc->properties['tagsbygroups'] = array();

		foreach ($mots as $mot) {
			$id_groupe = $mot['id_groupe'];
			// old school: recupere le titre du groupe de mots…
			$type = trim(supprimer_numero($mot['type']));
			// ajouter le mot à la propriété tagsbygroups.ID_GROUPE
			// et tagsbytype.NOMDUGROUPE
			// créer le tableau de groupe le cas échéant
			if (!array_key_exists($id_groupe,$doc->properties['tagsbygroups'])){
				$doc->properties['tagsbygroups'][$id_groupe] = array();
				$doc->properties['tagsbytype'][$type] = array();
			}
			$doc->properties['tagsbygroups'][$id_groupe][] = trim(supprimer_numero($mot['titre'])); 
			$doc->properties['tagsbytype'][$type][] = trim(supprimer_numero($mot['titre'])); 

			$id_mot = intval($mot['id_mot']);
			$doc->properties['mots']['titres'][$id_mot] = trim(supprimer_numero($mot['titre']));
			$doc->properties['mots']['ids'][] = $id_mot;
			
			// On s'occupe de la hiérarchie de chaque mot
			$id_parent = $mot['id_groupe'];
			$titres = array($doc->properties['mots']['titres'][$id_mot]);
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
			$doc->properties['mots']['ids_hierarchie'] = array_merge(
				$doc->properties['mots']['ids_hierarchie'],
				$ids_hierarchie
			);
			$doc->properties['mots']['titres_hierarchie'] = array_merge(
				$doc->properties['mots']['titres_hierarchie'],
				$titres_hierarchie
			);
		}
		// et on garde la property tags
		$doc->properties['tags'] = array_values($doc->properties['mots']['titres']);
		
		// On ajoute le nom des mots en fulltext à la fin
		$doc->content .= "\n\n".join(' / ', array_map('extraire_multi', $doc->properties['mots']['titres']));
	}
	return $doc;
}
