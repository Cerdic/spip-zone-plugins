<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\Document;

include_spip('inc/texte');
include_spip('indexer_fonctions');

class HierarchieRubriques extends SpipDocuments {
	public function __construct() {
		// On force l'objet aux rubriques
		parent::__construct('rubrique');
	}
	
	public function getDocuments($start = 0, $end = 0, $column = '') {
		$doc = array(
			'id' => $this->getObjectId('hierarchie_rubrique', 1),
			'title' => 'Hiérarchie des rubriques',
			'properties' => array(
				'objet' => 'hierarchie',
				'id_objet' => 'rubriques',
				'source' => lire_config('indexer/source', lire_config('adresse_site')),
			),
		);
		
		$doc['properties']['hierarchie'] = $this->getHierarchie();
		
		$documents[] = new Document($doc);
		return $documents;
	}
	
	/**
	 * Récupérer toutes les rubriques du site
	 * 
	 **/
	private function getHierarchie($id_parent = 0, $id_parent_hierarchie = '', $parents = array()) {
		$hierarchie = array();
		
		if ($rubriques = sql_allfetsel('*', 'spip_rubriques', 'id_parent = '.intval($id_parent), '', '0+titre,titre')) {
			foreach ($rubriques as $rubrique) {
				$id_rubrique = intval($rubrique['id_rubrique']);
				$titre = supprimer_numero($rubrique['titre']);
				$rang = recuperer_numero($rubrique['titre']);
				$id_hierarchie = indexer_id_hierarchie($parents, $titre);
				$hierarchie[$id_hierarchie] = array(
					'titre' => $titre,
					'rang' => $rang,
					'profondeur' => count($parents)+1,
					'id_parent' => $id_parent,
					'hash_parent' => $id_parent_hierarchie,
				);
				
				$hierarchie = array_merge(
					$hierarchie,
					$this->getHierarchie($id_rubrique, $id_hierarchie, array_merge($parents, array($id_hierarchie => $titre)))
				);
			}
		}
		
		return $hierarchie;
	}
	
	// On ne fait cette opération qu'une seule fois, pas pour plusieurs objets
	public function getBounds($column = '') {
		return array('min'=>1, 'max'=>1);
	}
}
