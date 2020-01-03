<?php

namespace Spip\Indexer\Sources;

use \Indexer\Sources\SourceInterface;
use \Indexer\Sources\Document;

include_spip('indexer_fonctions');

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
	 * @return \Indexer\Sources\Document[]
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
	 * @pipeline_appel indexer_document
	 *
	 * @param array $contenu
	 *     Ligne SQL de l’objet éditorial à indexer
	 * @return \Indexer\Sources\Document
	 **/
	public function createDocumentObjet($contenu) {
		include_spip('inc/filtres');
		include_spip('inc/texte');
		include_spip('inc/config');
		include_spip('base/objets');
		include_spip('indexer_fonctions');

		// On cherche les éléments dont on va avoir besoin
		$id = intval($contenu[$this->cle_objet]);

		// Travail specifique sur les auteurs : retirer les parametres de securite
		if ($this->objet === "auteur") {
			foreach (array('low_sec', 'pass', 'htpass', 'alea_actuel', 'alea_futur', 'cookie_oubli') as $key) {
				unset($contenu[$key]);
			}
		}

		$doc = $this->decrire_document($id, $contenu);

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

		// dépiler la langue
		if (isset($contenu['lang'])) {
			lang_select();
		}

		return $doc;
	}

	/**
	 * Retourne une description du document
	 * @param int $id
	 * @param array $contenu
	 *     Ligne SQL de l’objet éditorial
	 * @return \Indexer\Sources\Document
	 */
	public function decrire_document($id, $contenu) {
		$doc = $this->initialiser_document($id);

		// Si indexation à ignorer pour ce contenu/objet,
		// supprimer l’objet éventuellement déjà indexé
		if ($this->ignorer_indexation_objet($contenu)) {
			$doc->to_delete = true;
			return $doc;
		}

		$this->decrire_document_title($doc, $contenu);
		$this->decrire_document_content($doc, $contenu);
		$this->decrire_document_summary($doc, $contenu);
		$this->decrire_document_dates($doc, $contenu);
		$this->decrire_document_lang($doc, $contenu);
		$this->decrire_document_traductions($doc, $contenu);
		$this->decrire_document_statut($doc, $contenu);
		$this->decrire_document_parent($doc, $contenu);
		$this->decrire_document_hierarchie($doc, $contenu);
		$this->decrire_document_jointures($doc, $contenu);
		$this->decrire_document_comptages($doc, $contenu);

		// Nettoyages
		$this->transformer_document_en_utf8($doc);
		$this->nettoyer_document($doc);

		return $doc;
	}

	/**
	 * Retourne tous les documents (Peut provoquer des problèmes de mémoire !)
	 * @return \Indexer\Sources\Document[]
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
	 * Instancie un document à indexer avec quelques informations de base
	 *
	 * @param int $id
	 * @return \Indexer\Sources\Document
	 */
	protected function initialiser_document($id) {
		// On crée le Document avec les infos de base
		$doc = new Document(array(
			'id' => $this->getObjectId($this->objet, $id)
		));
		$doc->uri = generer_url_entite_absolue($id, $this->objet);
		$doc->properties['objet'] = $this->objet;
		$doc->properties['id_objet'] = $id;
		// Pour la source du site : config explicite sinon l'URL du site
		$doc->properties['source'] = lire_config('indexer/source', lire_config('adresse_site'));
		return $doc;
	}

	/**
	 * Doit-on ignorer l’indexation de cet objet ?
	 *
	 * En configuration, certains statuts sont ignorés.
	 *
	 * @param array $contenu
	 *     Ligne SQL de l’objet éditorial
	 * @return bool
	 *     True si indexation à ignorer
	 */
	public function ignorer_indexation_objet($contenu) {
		// S'il y a un statut et qu'il fait partie des statuts à ignorer
		// cet objet n’est pas à indexer
		if (
			isset($this->objet)
			and isset($contenu['statut'])
			and $statuts_ignores = lire_config('indexer/' . $this->objet . '/statuts_ignores')
			and in_array($contenu['statut'], $statuts_ignores)
		) {
			return true;
		}
		return false;
	}

	/**
	 * Retourne le nom la colonne de titre de cet objet
	 * @param string $objet
	 * @return string
	 */
	public function trouver_champ_titre($objet) {
		static $champs = [];
		if (empty($champs[$objet])) {
			// detecter le titre
			$champ_titre = 'titre';

			// Extraire le champ titre SI on a "as titre" dedans
			// car sinon ça veut dire que c'est le champ titre tout court
			$info_titre = objet_info($objet, 'titre');
			if ($info_titre and stripos($info_titre, 'as titre') !== false) {
				// On récupère ce qui est avant le "as titre"
				if (preg_match('/(^|,)([\w\s]+)\s+as titre/i', $info_titre, $trouve)) {
					$champ_titre = trim($trouve[2]);
				} else {
					// On n’a pas trouvé, l'objet n'est donc pas titrable
					$champ_titre = '';
				}
			}
			$champs[$objet] = $champ_titre;
		}
		return $champs[$objet];
	}

	/**
	 * Ajoute le champ 'title' à indexer au document
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_title($doc, $contenu) {
		$champ_titre = $this->trouver_champ_titre($this->objet);
		if ($champ_titre and isset($contenu[$champ_titre])) {
			$doc->title = supprimer_numero($contenu[$champ_titre]);
		}
	}


	/**
	 * Ajoute le champ 'content' à indexer au document
	 *
	 * Pour le contenu principal, on va chercher la liste des champs de recherche déclarés,
	 * sinon on détecte certains cas courants (texte, descriptif, bio)
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_content($doc, $contenu) {
		if ($rechercher_champs = array_keys(objet_info($this->objet, 'rechercher_champs'))) {
			foreach ($rechercher_champs as $champ) {
				// On ne remet pas le titre
				if ($champ != 'titre' and !empty($contenu[$champ])) {
					$doc->content .= "\n\n".$contenu[$champ];
				}
			}
			return;
		}

		// Sinon on détecte les cas courants
		if (isset($contenu['texte'])) {
			$doc->content = $contenu['texte'];
		} elseif (isset($contenu['descriptif'])) {
			$doc->content = $contenu['descriptif'];
		} elseif (isset($contenu['bio'])) {
			$doc->content = $contenu['bio'];
		}
	}

	/**
	 * Ajoute le champ 'summary' à indexer au document
	 *
	 * On calcule une introduction, sinon on prend le début du contenu.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_summary($doc, $contenu) {
		if ($fonction_introduction = chercher_filtre('introduction')) {
			// on s'assure que le descriptif soit bien une chaine
			$descriptif = isset($contenu['descriptif']) ? $contenu['descriptif'] : '';
			$doc->summary = $fonction_introduction($descriptif, $doc->content, 400, '');
		} else {
			$doc->summary = couper($doc->content, 400);
		}
	}

	/**
	 * Ajoute des champs de date à indexer au document,
	 * ainsi que des propriétés autour des dates
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_dates($doc, $contenu) {
		$this->decrire_document_date($doc, $contenu);
		$this->decrire_document_date_maj($doc, $contenu);
		$this->decrire_document_date_intervalle($doc, $contenu);
	}

	/**
	 * Ajoute le champ de date à indexer
	 *
	 * Prioritairement la 'date_redac' si connue, sinon un champ de date déclaré.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_date($doc, $contenu) {
		// Pour la date
		// S'il y a un champ de date de rédaction et qu'il est utilisé, on prend en priorité à celle de publication
		if (isset($contenu['date_redac']) and substr($contenu['date_redac'],0,4) != '0000') {
			$doc->date = $contenu['date_redac'];
			$doc->properties['date_redac'] = $contenu['date_redac'];
			$doc->properties['annee_redac'] = substr($contenu['date_redac'],0,4);
			$doc->properties['date'] = $contenu['date'];
		}
		// Sinon on utilise la date de publication déclarée par l'API, ou 'date'
		else if ($champ_date = objet_info($this->objet, 'date')) {
			$doc->date = $contenu[$champ_date];
		}
		// Sinon le champ "date" en dur s'il existe
		else if (isset($contenu['date'])) {
			$doc->date = $contenu['date'];
		}
		// Sinon le champ "maj" en dur s'il existe
		else if (isset($contenu['maj'])) {
			$doc->date = $contenu['maj'];
		}
		// Sinon la date du jour, et il FAUT une date car "0000…" n'existe pas dans Sphinx
		else {
			$doc->date = date('Y-m-d H:i:s');
		}
	}

	/**
	 * Ajoute le champ de date de modification à indexer
	 *
	 * Prioritairement la 'date_modif' si connue, sinon un champ maj.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_date_maj($doc, $contenu) {
		// Et la mise-à-jour si elle existe
		if (isset($contenu['date_modif']) and substr($contenu['date_modif'],0,4) != '0000') {
			$doc->properties['maj'] = $contenu['date_modif'];
		} elseif (!empty($contenu['maj'])) {
			$doc->properties['maj'] = $contenu['maj'];
		} else {
			$doc->properties['maj'] = '0000-00-00 00:00:00';
		}
	}

	/**
	 * Ajoute les champs de date début et date de fin, si présents
	 **
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_date_intervalle($doc, $contenu) {
		if (isset($contenu['date_debut']) and substr($contenu['date_debut'],0,4) != '0000') {
			$doc->properties['date_debut'] = $this->preparer_date($contenu['date_debut']);
		}
		if (isset($contenu['date_fin']) and substr($contenu['date_fin'],0,4) != '0000') {
			$doc->properties['date_fin'] = $this->preparer_date($contenu['date_fin']);
		}
	}

	/**
	 * Prépare une date selon différents formats
	 *
	 * @param string $date
	 * @return array
	 */
	public static function preparer_date($date) {
		// recalculer dateu pour les dates floues: 2000-00-00 => 2000-01-01
		$dateu = strtotime(str_replace("-00", "-01", $date));
		return array(
			'year' => intval(date('Y', $dateu)),
			'yearmonth' => intval(date('Ym', $dateu)),
			'yearmonthday' => intval(date('Ymd', $dateu)),
			'u' => $dateu,
			'datetime' => $date
		);
	}

	/**
	 * Ajoute la langue à indexer au document,
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_lang($doc, $contenu) {
		// S'il y a une langue, la noter et gérer les bons blocs multi
		if (isset($contenu['lang'])) {
			$doc->properties['lang'] = $contenu['lang'];
			lang_select($doc->properties['lang']);
		}
	}

	/**
	 * Ajoute les liens de traductions à indexer au document.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_traductions($doc, $contenu) {
		if (isset($contenu['id_trad']) and intval($contenu['id_trad']) > 0) {
			$trads = sql_allfetsel(
				'*',
				$this->table_objet, // la table de l'objet défini
				array(
					'id_trad = ' . $contenu['id_trad'],
					$this->cle_objet . '!= ' . $doc->properties['id_objet'],
				), // Where
				'', // Gr By
				'', // Or By
				'' // Limit
			);
			if (count($trads) > 0) {
				$doc->properties['trad'] = array();
				$doc->properties['tradlangs'] = array();
				$champ_titre = $this->trouver_champ_titre($this->objet);
				foreach($trads as $trad) {
					$doc->properties['trad'][] = array(
						'title' => $trad[$champ_titre],
						'uri' => generer_url_entite_absolue($trad[$this->cle_objet], $this->objet),
						'lang' => $trad['lang'],
					);
					$doc->properties['tradlangs'][] = $trad['lang'];
				}
			}
		}
	}

	/**
	 * Ajoute le statut à indexer au document.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_statut($doc, $contenu) {
		// S'il y a un statut
		if (isset($contenu['statut'])) {
			$doc->properties['statut'] = $contenu['statut'];
		}
	}

	/**
	 * Ajoute les infos de parenté à indexer au document.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_parent($doc, $contenu) {
		// On garde en mémoire le parent si on trouve quelque chose
		if (isset($contenu['id_parent'])) {
			$doc->properties['id_parent'] = intval($contenu['id_parent']);
			$doc->properties['objet_parent'] = $this->objet;
			return;
		}
		if (isset($contenu['id_rubrique'])) {
			$doc->properties['id_rubrique'] = intval($contenu['id_rubrique']);
			$doc->properties['id_parent'] = intval($contenu['id_rubrique']);
			$doc->properties['objet_parent'] = 'rubrique';
			return;
		}
		// Pour les événements au moins
		if (isset($contenu['id_article'])) {
			$doc->properties['id_article'] = intval($contenu['id_article']);
			$doc->properties['id_parent'] = intval($contenu['id_article']);
			$doc->properties['objet_parent'] = 'article';
			return;
		}
	}


	/**
	 * Ajoute les infos de hiérarchie à indexer au document.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_hierarchie($doc, $contenu) {
		if ($this->objet == 'rubrique' and $doc->properties['id_objet']) {
			$this->decrire_document_hierarchie_rubrique_enfant($doc, $contenu, $doc->properties['id_objet']);
		} elseif (!empty($doc->properties['id_rubrique'])) {
			$this->decrire_document_hierarchie_rubrique_enfant($doc, $contenu, $doc->properties['id_rubrique']);
		} elseif (!empty($doc->properties['id_article'])) {
			$id_parent = sql_getfetsel(
				'id_rubrique',
				'spip_articles',
				'id_article =' . $doc->properties['id_article']
			);
			$this->decrire_document_hierarchie_rubrique_enfant($doc, $contenu, $id_parent);
		}
	}

	/**
	 * Ajoute les infos de hiérarchie d’une rubrique à indexer au document.
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 * @param int $id_rubrique_enfant Rubrique la plus basse dans la hierarchie
	 */
	public function decrire_document_hierarchie_rubrique_enfant($doc, $contenu, $id_rubrique_enfant) {
		if (!intval($id_rubrique_enfant)) {
			return;
		}
		// Là normalement on a maintenant la rubrique la plus basse
		$doc->properties['parents'] = array();
		$doc->properties['parents']['ids'] = array();
		$doc->properties['parents']['titres'] = array();
		$doc->properties['parents']['ids_hierarchie'] = array();
		$doc->properties['parents']['titres_hierarchie'] = array();

		while ($f = sql_fetsel(
			'id_parent, titre',
			'spip_rubriques',
			'id_rubrique = ' . $id_rubrique_enfant
		)){
			$titre_actuel = supprimer_numero($f['titre']);
			$id_parent = intval($f['id_parent']);

			// On ajoute ce parent suivant au début du tableau
			array_unshift($doc->properties['parents']['ids'], $id_rubrique_enfant);
			$doc->properties['parents']['titres'] = array_merge(
				array($id_rubrique_enfant => $titre_actuel),
				$doc->properties['parents']['titres']
			);

			// On passe au parent suivant
			$id_rubrique_enfant = $id_parent;
		}

		// C'est seulement une fois qu'on a tous les titres qu'on peut réussir à construire les bons hashs
		foreach ($doc->properties['parents']['titres'] as $titre) {
			$id_hierarchie = indexer_id_hierarchie($doc->properties['parents']['titres_hierarchie'], $titre);
			$doc->properties['parents']['ids_hierarchie'][] = $id_hierarchie;
			$doc->properties['parents']['titres_hierarchie'][$id_hierarchie] = $titre;
		}

		// On ajoute la branche dans le fulltext
		$doc->content .= "\n\n".join(' / ', $doc->properties['parents']['titres']);
	}

	/**
	 * Ajoute les infos de jointures à indexer au document.
	 *
	 * Si les jointures sont activées en configurations sur l’ojet,
	 * on délégue à une fonction surchargeable dédiée tel que `indexer_jointure_auteurs`
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_jointures($doc, $contenu) {
		foreach (indexer_lister_jointures($this->objet) as $jointure) {
			if (
				lire_config('indexer/' . $this->objet . '/jointure_' . $jointure . '/activer') // indexer/article/jointure_auteurs/activer=oui
				and $jointure_fonction = charger_fonction( // indexer_jointure_auteurs()
					'jointure_' . $jointure,
					'indexer',
					true
				)
			) {
				$doc = $jointure_fonction($this->objet, $doc->properties['id_objet'], $doc);
			}
		}
	}

	/**
	 * Ajoute les infos de comptages à indexer au document.
	 *
	 * Compte le nombre d’articles / statut si auteurs
	 *
	 * @param \Indexer\Sources\Document $doc
	 * @param array $contenu
	 */
	public function decrire_document_comptages($doc, $contenu) {
		// Travail specifique sur les auteurs :
		if ($this->objet === "auteur") {

			// - compter ses articles publies
			$all = sql_allfetsel(
				"L2.statut, COUNT(*) AS cnt",
				"spip_auteurs_liens AS L1 INNER JOIN spip_articles AS L2 ON L1.objet='article' AND L1.id_objet = L2.id_article",
				"L1.id_auteur=" . intval($doc->properties['id_objet']),
				"L2.statut"
			);
			$articles = array();
			foreach($all as $d) {
				$articles[$d['statut']] = intval($d['cnt']);
			}
			$doc->properties['articles'] = $articles;
		}
	}

	/**
	 * Transforme les champs du document en utf8
	 *
	 * @param \Indexer\Sources\Document $doc
	 */
	public function transformer_document_en_utf8($doc) {
		// Transformation UTF-8
		if (!function_exists('html2unicode')) {
			include_spip('inc/charsets');
		}
		$doc->title = unicode_to_utf_8(html2unicode($doc->title));
		$doc->content = unicode_to_utf_8(html2unicode($doc->content));
		$doc->summary = unicode_to_utf_8(html2unicode($doc->summary));
	}

	/**
	 * Nettoyer le document (balises html, raccourcis spip...)
	 *
	 * @param \Indexer\Sources\Document $doc
	 */
	public function nettoyer_document($doc) {
		// Supprimer les balises HTML
		// (on n'utilise pas textebrut car il semble qu'il y a une fuite
		// de mémoire problématique quand on traite des centaines de textes)
		$doc->content = supprimer_tags($doc->content);
		$doc->summary = supprimer_tags($doc->summary);

		// Supprimer les raccourcis typo SPIP
		include_spip('inc/lien');
		if (strpos($doc->content,'[') !== false
			or strpos($doc->content,'{') !== false
			or strpos($doc->content,'|') !== false) {
			$doc->content = nettoyer_raccourcis_typo($doc->content);
		}
		if (strpos($doc->summary,'[') !== false
			or strpos($doc->summary,'{') !== false
			or strpos($doc->summary,'|') !== false) {
			$doc->summary = nettoyer_raccourcis_typo($doc->summary);
		}
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
