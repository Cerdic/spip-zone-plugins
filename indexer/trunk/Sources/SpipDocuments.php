<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\SourceInterface;

class SpipDocuments implements SourceInterface {
	/** SPIP récent ? spip_xx_liens ou spip_xx_yy */
	private $tables_liens = true;
	/** Type d'objet SPIP */
	private $objet = null;
	private $table_objet = null;
	private $cle_objet = null;

	public function __construct($objet='') {
		if (
			is_string($objet)
			and include_spip('base/objets')
			and $objet = objet_type($objet)
			and $table = table_objet_sql($objet)
			and in_array($table, lister_tables_objets_sql())
		) {
			$this->objet = $objet;
			$this->table_objet = $table;
			$this->cle_objet = id_table_objet($objet);
		}
	}

	public function __toString() { return get_class($this); }

	/**
	 * Retourne les documents ayant certaines conditions
	 *
	 * @param mixed $start     Condition qui remplira `$column >= $start`
	 * @param mixed $end       Condition qui remplira `$column < $end`
	 * @param string $column   Colonne affectée
	 * @return \Indexer\Sources\Documents[]
	 */
	public function getDocuments($start = 0, $end = 0, $column = '') {
		$documents = array();
		
		// S'il y a bien un objet défini
		if ($this->cle_objet) {
			// On définit la colonne de test par défaut s'il n'y en a pas
			if (!$column) {
				$column = $this->cle_objet;
			}
			
			$where = array();
			if ($start) $where[] = "$column >= $start";
			if ($end)   $where[] = "$column < $end";

			$all = sql_allfetsel(
				'*',
				$this->table_objet, // la table de l'objet défini
				$where, // Where
				'', // Gr By
				'', // Or By
				'' // Limit
			);

			foreach ($all as $contenu) {
				$documents[] = $this->createDocumentObjet($contenu);
			}
		}
		
		return $documents;
	}
	
	/**
	 * Retourne un Document formaté pour l'indexation avec le contenu d'un objet SPIP
	 * 
	 * @param array $objet
	 * @return \Indexer\Sources\Documents
	 **/
	public function createDocumentObjet($contenu) {
		include_spip('inc/filtres');
		$doc = array('properties' => array());		
		
		// On cherche les éléments dont on va avoir besoin
		$id = $contenu[$this->cle_objet];
		$doc['id'] = $this->getObjectId($this->objet, $id);
		$doc['uri'] = generer_url_entite_absolue($id, $this->objet);
		$doc['properties']['objet'] = $this->objet;
		$doc['properties']['id_objet'] = $id;
		
		// Pour le titre
		if (isset($contenu['titre'])) {
			$doc['title'] = supprimer_numero($contenu['titre']);
		}
		elseif (isset($contenu['nom'])) {
			$doc['title'] = supprimer_numero($contenu['nom']);
		}
		else {
			$doc['title'] = '';
		}
		
		// Pour le contenu principal
		if (isset($contenu['texte'])) {
			$doc['content'] = $contenu['texte'];
		}
		elseif (isset($contenu['descriptif'])) {
			$doc['content'] = $contenu['descriptif'];
		}
		elseif (isset($contenu['bio'])) {
			$doc['content'] = $contenu['bio'];
		}
		else {
			$doc['content'] = '';
		}
		
		// Pour le résumé
		// (on gère direct le cas particulier des articles qui sont dans le core et qu'on connait bien)
		if (
			$this->objet == 'article'
			and $summary = trim($contenu['surtitre']."\n".$contenu['soustitre']."\n".$article['chapo'])
		) {
			$doc['summary'] = $summary;
		}
		elseif (isset($contenu['descriptif'])) {
			$doc['summary'] = couper($contenu['descriptif'], 200);
		}
		else {
			$doc['summary'] = couper($doc['content'], 200);
		}
		
		// Pour la date
		if (isset($contenu['date_redac']) and substr($contenu['date_redac'],0,4) != '0000') {
			$doc['date'] = $contenu['date_redac'];
		}
		elseif (isset($contenu['date'])) {
			$doc['date'] = $contenu['date'];
		}
		else {
			$doc['date'] = '0000-00-00 00:00:00';
		}
		
		// S'il y a une langue
		if (isset($contenu['lang'])) {
			$doc['properties']['lang'] = $contenu['lang'];
		}
		
		// Les auteurs
		if ($authors = $this->getAuthorsProperties($this->objet, $id)) {
			$doc['properties']['authors'] = $authors;
		}
		
		// Les mots/tags basiquement
		if ($tags = $this->getTagsProperties($this->objet, $id)) {
			$doc['properties']['tags'] = $tags;
		}
		
		// On crée le Document avec les infos
		$doc = new Document($doc);
		
		// On le passe dans un pipeline pour pouvoir le modifier
		$doc = pipeline(
			'indexer_document',
			array(
				'args' => array(
					'objet ' => $this->objet,
					'id_objet' => $id,
					'champs' => $contenu,
				),
				'data' => $doc,
			)
		);
		
		return $doc;
	}

	/**
	 * Retourne tous les documents (Peut provoquer des problèmes de mémoire !)
	 * @return \Indexer\Sources\Documents[]
	 */
	public function getAllDocuments() {
		return $this->getDocuments();
	}

	/** @param bool $bool */
	public function setTablesLiens($bool) {
		$this->tables_liens = $bool;
	}

	public function getObjectId($objet, $id_objet){
		return crc32($GLOBALS['meta']['adresse_site'] . $objet) + intval($id_objet);
	}

	public function getAuthorsProperties($objet, $id_objet) {
		if ($this->tables_liens) {
			$auteurs = sql_allfetsel('a.nom', 'spip_auteurs AS a, spip_auteurs_liens AS al', array(
				"al.id_objet = " . intval($id_objet),
				"al.objet    = " . sql_quote($objet),
				"a.id_auteur = al.id_auteur",
			));
		} else {
			$auteurs = sql_allfetsel('a.nom', 'spip_auteurs AS a, spip_auteurs_articles AS al', array(
				"al.id_article = " . intval($id_objet),
				"a.id_auteur = al.id_auteur",
			));
		}
		
		return array_map('array_shift', $auteurs);
	}

	public function getTagsProperties($objet, $id_objet) {
		if ($this->tables_liens) {
			$tags = sql_allfetsel('m.titre', 'spip_mots AS m, spip_mots_liens AS ml', array(
				"ml.id_objet = " . intval($id_objet),
				"ml.objet    = " . sql_quote($objet),
				"m.id_mot = ml.id_mot",
			));
		} else {
			$tags = sql_allfetsel('m.titre', 'spip_mots AS m, spip_mots_articles AS ml', array(
				"ml.id_article = " . intval($id_objet),
				"m.id_mot = ml.id_mot",
			));
		}
		return array_map('array_shift', $tags);
	}

	public function getBounds($column = '') {
		$bornes = array('min' => 0, 'max' => 0);
		
		// S'il y a bien un objet défini
		if ($this->cle_objet) {
			// On définit la colonne de test par défaut s'il n'y en a pas
			if (!$column) {
				$column = $this->cle_objet;
			}
			
			$bornes = sql_fetsel(
				array("MIN($column) AS min", "MAX($column) AS max"),
				$this->table_objet
			);
		}
		
		return $bornes;
	}

	/**
	 * Crée un tableau de parts
	 *
	 * @param int $count
	 * @return array
	**/
	public function getParts($count) {
		$bornes = $this->getBounds();
		$parts = array();
		for ($i = $bornes['min']; $i <= $bornes['max']; $i += $count) {
			$parts[] = array(
				'start' => $i,
				'end'   => $i + $count
			);
		}
		return $parts;
	}
}
