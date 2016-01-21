<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\Document;

include_spip('inc/texte');
include_spip('indexer_fonctions');

class HierarchieMots extends SpipDocuments {
	public function __construct() {
		// On force l'objet aux mots
		parent::__construct('mot');
	}
	
	public function getDocuments($start = 0, $end = 0, $column = '') {
		$doc = array(
			'id' => $this->getObjectId('hierarchie_mot', 1),
			'title' => 'Hiérarchie des mots-clés et de leurs groupes',
			'properties' => array(
				'objet' => 'hierarchie',
				'id_objet' => 'mots',
				'source' => lire_config('indexer/source', lire_config('adresse_site')),
			),
		);
		
		$doc['properties']['hierarchie'] = $this->getHierarchieGroupes();
		
		$documents[] = new Document($doc);
		return $documents;
	}
	
	/**
	 * Récupérer toutes les mots et groupes du site
	 * 
	 **/
	private function getHierarchieGroupes($id_parent = 0, $id_parent_hierarchie = '', $parents = array()) {
		$hierarchie = array();
		
		// Si on a le plugin groupes arborescents, on le prend en compte
		$where = array();
		if (defined('_DIR_PLUGIN_GMA')) {
			$where[] = 'id_parent = '.intval($id_parent);
		}
		
		if ($groupes = sql_allfetsel('*', 'spip_groupes_mots', $where, '', '0+titre,titre')) {
			foreach ($groupes as $groupe) {
				$id_groupe = intval($groupe['id_groupe']);
				$titre = supprimer_numero($groupe['titre']);
				$rang = recuperer_numero($groupe['titre']);
				$id_hierarchie = indexer_id_hierarchie($parents, $titre);
				$hierarchie[$id_hierarchie] = array(
					'titre' => $titre,
					'rang' => $rang,
					'profondeur' => count($parents)+1,
					'id_parent' => $id_parent,
					'id_groupe' => $id_groupe,
					'hash_parent' => $id_parent_hierarchie,
				);
				
				// Si on a le plugin groupes arborescents, on cherche les sous-groupes
				if (defined('_DIR_PLUGIN_GMA')) {
					$hierarchie = array_merge(
						$hierarchie,
						$this->getHierarchieGroupes($id_groupe, $id_hierarchie, array_merge($parents, array($id_hierarchie => $titre)))
					);
				}
				
				// Puis on ajoute les mots de ce groupe
				$hierarchie = array_merge(
					$hierarchie,
					$this->getHierarchieMots($id_groupe, $id_hierarchie, array_merge($parents, array($id_hierarchie => $titre)))
				);
			}
		}
		
		return $hierarchie;
	}
	
	/**
	 * Récupérer toutes les mots et groupes du site
	 * 
	 **/
	private function getHierarchieMots($id_parent = 0, $id_parent_hierarchie = '', $parents = array()) {
		$hierarchie = array();
		
		if ($mots = sql_allfetsel('*', 'spip_mots', 'id_groupe = '.intval($id_parent), '', '0+titre,titre')) {
			foreach ($mots as $mot) {
				$id_mot = intval($mot['id_mot']);
				$titre = supprimer_numero($mot['titre']);
				$rang = recuperer_numero($mot['titre']);
				$id_hierarchie = indexer_id_hierarchie($parents, $titre);
				$hierarchie[$id_hierarchie] = array(
					'titre' => $titre,
					'rang' => $rang,
					'profondeur' => count($parents)+1,
					'id_parent' => $id_parent,
					'hash_parent' => $id_parent_hierarchie,
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
