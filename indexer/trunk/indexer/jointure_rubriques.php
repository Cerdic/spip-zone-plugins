<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/filtres');

function indexer_jointure_rubriques_dist($objet, $id_objet, $infos) {
	// On va chercher tous les rubriques de cet objet
	if (defined('_DIR_PLUGIN_POLYHIER') and $rubriques = sql_allfetsel(
		'r.id_rubrique, titre',
		'spip_rubriques as r join spip_rubriques_liens as l on r.id_rubrique=l.id_parent',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		$infos['properties']['rubriques']['ids'] = array();
		$infos['properties']['rubriques']['titres'] = array();
		$infos['properties']['rubriques']['ids_hierarchie'] = array();
		$infos['properties']['rubriques']['titres_hierarchie'] = array();
		
		foreach ($rubriques as $rubrique) {
			$id_rubrique_enfant = intval($rubrique['id_rubrique']);
			$ids_de_cette_branche = array();
			$titres_de_cette_branche = array();
			$ids_hierarchie_de_cette_branche = array();
			$titres_hierarchie_de_cette_branche = array();
			
			while ($f = sql_fetsel(
				'id_parent, titre',
				'spip_rubriques',
				'id_rubrique = '.$id_rubrique_enfant
			)){
				$titre_actuel = supprimer_numero($f['titre']);
				$id_parent = intval($f['id_parent']);
				
				// On ajoute ce parent suivant au début du tableau
				array_unshift($ids_de_cette_branche, $id_rubrique_enfant);
				$titres_de_cette_branche = array_merge(array($id_rubrique_enfant=>$titre_actuel), $titres_de_cette_branche);
				
				// On passe au parent suivant
				$id_rubrique_enfant = $id_parent;
			}
			// C'est seulement une fois qu'on a tous les titres qu'on peut réussir à construire les bons hashs
			foreach ($titres_de_cette_branche as $titre) {
				$id_hierarchie = indexer_id_hierarchie($titres_hierarchie_de_cette_branche, $titre);
				$ids_hierarchie_de_cette_branche[] = $id_hierarchie;
				$titres_hierarchie_de_cette_branche[$id_hierarchie] = $titre;
			}
			
			// On ajoute la branche dans le fulltext
			$infos['content'] .= "\n\n".join(' | ', $titres_de_cette_branche);
			
			// On ajoute cette branche dans les infos
			$infos['properties']['rubriques']['ids'] = array_values(array_unique(array_merge($infos['properties']['rubriques']['ids'], $ids_de_cette_branche)));
			$infos['properties']['rubriques']['titres'] = array_merge($infos['properties']['rubriques']['titres'], $titres_de_cette_branche);
			$infos['properties']['rubriques']['ids_hierarchie'] = array_values(array_unique(array_merge($infos['properties']['rubriques']['ids_hierarchie'], $ids_hierarchie_de_cette_branche)));
			$infos['properties']['rubriques']['titres_hierarchie'] = array_merge($infos['properties']['rubriques']['titres_hierarchie'], $titres_hierarchie_de_cette_branche);
		}
	}
	
	return $infos;
}
