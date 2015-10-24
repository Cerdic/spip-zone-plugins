<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\Document;

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
				'id_objet' => 'rubrique',
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
	 * <BOUCLE_rubriques(DATA){source table,#GET{properties/tree}}>
	 * 
	 * </BOUCLE_rubriques>
	 **/
	private function getHierarchie($id_parent = 0, $id_parent_hierarchie = '', $parents = array()) {
		include_spip('inc/texte');
		$hierarchie = array();
		
		if ($rubriques = sql_allfetsel('*', 'spip_rubriques', 'id_parent = '.intval($id_parent), '', '0+titre,titre')) {
			foreach ($rubriques as $rubrique) {
				$id_rubrique = intval($rubrique['id_rubrique']);
				$titre = supprimer_numero($rubrique['titre']);
				$rang= recuperer_numero($rubrique['titre']);
				$id_hierarchie = $this->getIdHierarchie($parents, $titre);
				$hierarchie[$id_hierarchie] = array(
					'titre' => $titre,
					'rang' => $rang,
					'profondeur' => count($parents)+1,
					'id_parent' => $id_parent,
					'hash_parent' => $id_parent_hierarchie,
				);
				
				$hierarchie = array_merge_recursive(
					$hierarchie,
					$this->getHierarchie($id_rubrique, $id_hierarchie, array_merge($parents, array($titre)))
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
