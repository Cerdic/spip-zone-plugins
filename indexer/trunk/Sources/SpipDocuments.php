<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\SourceInterface;
use \Indexer\Sources\Document;

class SpipDocuments implements SourceInterface {
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
			and $tables_objets = lister_tables_objets_sql()
			and isset($tables_objets[$table])
		) {
			$this->objet = $objet;
			$this->table_objet = $table;
			$this->cle_objet = id_table_objet($objet);
		}
	}

	public function __toString() { return get_class($this); }
	
	/**
	 * Retourne l'objet SPIP défini
	 * @return string Type d'bjet SPIP
	 **/
	public function getObjet() {
		return $this->objet;
	}
	
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
			
			// S'il y a des statuts ignorés… ben on les ignore
			if (
				$statuts_ignores = ('indexer/'.$this->objet.'/statuts_ignores')
				and is_array($statuts_ignores)
			) {
				$where[] = sql_in('statut', $statuts_ignores, 'not');
			}
			
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
		include_spip('inc/texte');
		include_spip('inc/config');
		include_spip('base/objets');
		include_spip('indexer_fonctions');
		
		$doc = array('properties' => array());		
		
		// S'il y a des statuts ignorés et que c'est le cas, on indexe pas
		// Normalement déjà ignoré dans le SQL, mais si jamais la fonction est appelée autre part…
		if (
			$statuts_ignores = lire_config('indexer/'.$this->objet.'/statuts_ignores')
			and is_array($statuts_ignores)
			and isset($contenu['statut'])
			and in_array($contenu['statut'], $statuts_ignores)
		) {
			return null;
		}
		
		// On cherche les éléments dont on va avoir besoin
		$id = intval($contenu[$this->cle_objet]);
		$doc['id'] = $this->getObjectId($this->objet, $id);
		$doc['uri'] = generer_url_entite_absolue($id, $this->objet);
		$doc['properties']['objet'] = $this->objet;
		$doc['properties']['id_objet'] = $id;
		
		// Pour la source du site : config explicite sinon l'URL du site
		$doc['properties']['source'] = lire_config('indexer/source', lire_config('adresse_site'));
		
		// Pour le titre (TODO : mieux détecter, mais la déclaration de l'API est faite pour une requête SQL)
		if (isset($contenu['titre'])) {
			$doc['title'] = supprimer_numero($contenu['titre']);
		}
		elseif (isset($contenu['nom'])) {
			$doc['title'] = supprimer_numero($contenu['nom']);
		}
		else {
			$doc['title'] = '';
		}
		
		// Pour le contenu principal, on va chercher la liste des champs fulltext déclarés
		if ($rechercher_champs = array_keys(objet_info($this->objet, 'rechercher_champs'))) {
			$doc['content'] = '';
			
			foreach ($rechercher_champs as $champ) {
				// On ne remet pas le titre
				if ($champ != 'titre' and isset($contenu[$champ]) and $contenu[$champ]) {
					$doc['content'] .= "\n\n".$contenu[$champ];
				}
			}
		}
		// Sinon on détecte les cas courants
		elseif (isset($contenu['texte'])) {
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
		
		// Pour le résumé, on utilise le filtre d'intro de SPIP
		// = descriptif s'il existe sinon le contenu principal précédent coupé
		$descriptif = isset($contenu['descriptif']) ? $contenu['descriptif'] : ''; // on s'assure que le descriptif soit bien une chaine
		if ($fonction_introduction = chercher_filtre('introduction')) {
			$doc['summary'] = $fonction_introduction($descriptif, $doc['content'], 400, '');
		}
		
		// Pour la date
		// S'il y a un champ de date de rédaction et qu'il est utilisé, on prend en priorité à celle de publication
		if (isset($contenu['date_redac']) and substr($contenu['date_redac'],0,4) != '0000') {
			$doc['date'] = $contenu['date_redac'];
		}
		// Sinon on utilise la date de publication déclarée par l'API
		elseif ($champ_date = objet_info($this->objet, 'date')) {
			$doc['date'] = $contenu[$champ_date];
		}
		// Sinon le champ "date" tout simplement
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
		
		// S'il y a un statut
		if (isset($contenu['statut'])) {
			$doc['properties']['statut'] = $contenu['statut'];
		}
		
		// On garde en mémoire le parent si on trouve quelque chose
		if (isset($contenu['id_parent'])) {
			$doc['properties']['id_parent'] = intval($contenu['id_parent']);
			$doc['properties']['objet_parent'] = $this->objet;
		}
		elseif (isset($contenu['id_rubrique'])) {
			$doc['properties']['id_rubrique'] = intval($contenu['id_rubrique']);
			$doc['properties']['id_parent'] = intval($contenu['id_rubrique']);
			$doc['properties']['objet_parent'] = 'rubrique';
		}
		// Pour les événements au moins
		elseif (isset($contenu['id_article'])) {
			$doc['properties']['id_article'] = intval($contenu['id_article']);
			$doc['properties']['id_parent'] = intval($contenu['id_article']);
			$doc['properties']['objet_parent'] = 'article';
		}
		
		// Et ensuite on tente de trouver une hiérarchie de rubriques
		if (
			$this->objet == 'rubrique' and $id_rubrique_enfant = $id
			or isset($doc['properties']['id_rubrique']) and $id_rubrique_enfant = $doc['properties']['id_rubrique']
			or isset($doc['properties']['id_article']) and $id_rubrique_enfant = intval(sql_getfetsel('id_rubrique', 'spip_articles', 'id_article ='.$doc['properties']['id_article']))
		) {
			// Là normalement on a maintenant la rubrique la plus basse
			$doc['properties']['parents']['ids'] = array();
			$doc['properties']['parents']['titres'] = array();
			while ($f = sql_fetsel(
				'id_parent, titre',
				'spip_rubriques',
				'id_rubrique = '.$id_rubrique_enfant
			)){
				$titre_actuel = supprimer_numero($f['titre']);
				$id_parent = intval($f['id_parent']);
				$doc['properties']['parents']['ids'][] = $id_rubrique_enfant;
				$doc['properties']['parents']['titres'][$id_rubrique_enfant] = $titre_actuel;
				
				$id_rubrique_enfant = $id_parent;
			}
			
			// On ajoute la branche dans le fulltext
			$doc['content'] .= "\n\n".join(' | ', $doc['properties']['parents']['titres']);
		}
		
		// On cherche les jointures pour cet objet
		// Pour chaque, on va déléguer à une fonction dédiée pour plus de lisibilité, et en plus ça permet d'être surchargeable
		foreach (indexer_lister_jointures($this->objet) as $jointure) {
			if (
				lire_config('indexer/'.$this->objet.'/jointure_'.$jointure.'/activer') // indexer/article/jointure_auteurs/activer=oui
				and $jointure_fonction = charger_fonction( // indexer_jointure_auteurs()
					'jointure_'.$jointure,
					'indexer',
					true
				)
			) {
				$doc = $jointure_fonction($this->objet, $id, $doc);
			}
		}
		
		// Transformation UTF-8
		include_spip('inc/charsets');
		$doc['title'] = unicode_to_utf_8(html2unicode($doc['title']));
		$doc['content'] = unicode_to_utf_8(html2unicode($doc['content']));
		$doc['summary'] = unicode_to_utf_8(html2unicode($doc['summary']));
		
		// On crée le Document avec les infos
		$doc = new Document($doc);
		
		// On le passe dans un pipeline pour pouvoir le modifier
		$doc = pipeline(
			'indexer_document',
			array(
				'args' => array(
					'objet' => $this->objet,
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

	public function getObjectId($objet, $id_objet){
		return crc32($GLOBALS['meta']['adresse_site'] . $objet) + intval($id_objet);
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
