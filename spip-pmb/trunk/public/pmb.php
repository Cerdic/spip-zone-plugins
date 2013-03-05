<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('iterateur/data');

/**
 * Requeteur pour les boucles (pmb:type_info)
 * tel que (pmb:notices)
 * 
 * Analyse si le nom d'info correspond bien a un type permis
 * et dans ce cas charge l'iterateur PMB avec ce type de donnees.
 * Affichera une erreur dans le cas contraire.
 *
 * @param $boucles Liste des boucles
 * @param $boucle  La boucle parcourue
 * @param $id      L'identifiant de la boucle parcourue
 * 
**/
function requeteur_pmb_dist(&$boucles, &$boucle, &$id) {
	$type = 'pmb_' . $boucle->type_requete;
	if ($h = charger_fonction($type . '_select' , 'inc', true)) {
		$g = charger_fonction('pmb', 'iterateur');
		$boucles[$id] = $g($boucle, $type);
		// from[0] stocke le type de data (pmb_notice, ...)
		$boucles[$id]->from[] = $type;
	} else {
		$boucle->type_requete = false;
		$msg = array('zbug_requeteur_inconnu',
				array(
				'requeteur' => 'pmb',
				'type' => $type
		));
		erreur_squelette($msg, $boucle);
	}
}




/**
 * Creer une boucle sur un iterateur PMB
 * (PMB:NOTICES) ...
 * annonce au compilo les "champs" disponibles
 * 
 * @param 
 * @return 
**/
function iterateur_PMB_dist($b, $type) {
	$b->iterateur = 'PMB'; # designe la classe d'iterateur
	$b->show = array(
		'field' => array(
			'cle' => 'STRING',
			'valeur' => 'STRING',
			'rechercher' => 'STRING',
			'*' => 'ALL' // Champ joker *
		)
	);
	return $b;
}



/**
 * Extension de l'itérateur Data
 * pour modifier la procedure de selection 
 *
**/
class IterateurPMB extends IterateurData {

	protected $type = '';


	/**
	 * Declarer les criteres exceptions
	 * et pouvoir en ajouter au besoin
	 * @return array
	 */
	public function exception_des_criteres($add = '') {
		static $exceptions = array('tableau');

		if (!$add) {
			return $exceptions;
		}
		$exceptions[] = $add;
	}
	
	
	/**
	 * Aller chercher les donnees
	 * Surcharge la selection de l'iterateur DATA
	 * puisque nous n'operons pas pareil.
	 * 
	 *
	 * @throws Exception
	 * @param  $command
	 * @return void
	 */
	protected function select($command) {
		
		$tableau = array();
		$this->type = strtolower($this->command['from'][0]);

		// on ne garde pas les where vides
		$this->command['where'] = array_values(array_filter($this->command['where']));

		// Critere {liste X1, X2, X3}
		if (isset($this->command['liste'])) {
			$this->select_liste();
		}

		// demande sortie du cache ou recalculee
		$cle = $this->creer_cle_cache();
		if ($cache = $this->use_cache($cle)) {
			// attention, il faut recalculer les filtres
			// qui sont a supprimer de la boucle
			// sinon l'usage du critere {rechercher} meurt en changeant de pagination :)
			if ($exceptions = $this->use_cache($cle . '-filtres')) {
				foreach ($exceptions as $ex) {
					$this->exception_des_criteres($ex);
				}
			}
			$this->tableau = $cache;
		} else {
			$select = charger_fonction($this->type . '_select', 'inc', true);
			$this->tableau = $select($this->command, $this);

			// cache d'une heure par defaut.
			$ttl = isset($this->command['datacache']) ? $this->command['datacache'] : 3600;
			
			if (is_array($this->tableau) AND $ttl>0) {
				$this->cache_set($cle, $ttl);
				$this->cache_set($cle.'-filtres', $ttl, $this->exception_des_criteres());
			}
		}


		// Si a ce stade on n'a pas de table, il y a un bug
		if (!is_array($this->tableau)) {
			$this->err = true;
			spip_log("erreur datasource ".$src);
		}


		// tri {par x}
		if ($this->command['orderby']) {
			$this->select_orderby();
		}

		// grouper les resultats {fusion /x/y/z} ;
		if ($this->command['groupby']) {
			$this->select_groupby();
		}

		$this->rewind();
		#var_dump($this->tableau);
	}


	/**
	 * Retourne les donnees en caches
	 * pour la boucle demandees
	 * si elles existent et ne sont
	 * pas perimees
	 *
	**/
	protected function use_cache($cle) {

		$cache = $this->cache_get($cle);

		// Time to live
		if (isset($this->command['datacache'])) {
			$ttl = intval($this->command['datacache']);
		}
		
		if ($cache AND ($cache['time'] + (isset($ttl) ? $ttl : $cache['ttl']) > time())
		AND !(_request('var_mode') === 'recalcul' AND include_spip('inc/autoriser') AND autoriser('recalcul'))) {
			return $cache['data'];
		}

		return false;
	}


	/**
	 * Cree une cle unique
	 * pour sauvegarder une analyse de donnees
	 * basee sur les criteres de boucle demandes 
	 *
	**/
	protected function creer_cle_cache() {
		$cle = $this->command;
		$cle['from'][0] = $this->type; 
		unset($cle['id']); // pas le nom de la boucle
		$cle = md5(serialize($cle));
		return $cle;
	}
}



/**
 * Passer une liste d'identifiants a l'iterateur PMB
 * (PMB:NOTICES){liste 1,3}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_PMB_liste_dist($idb, &$boucles, $crit) {
	return critere_DATA_liste_dist($idb, $boucles, $crit);
}


/**
 * Modifier la duree du cache des boucles PMB
 * par defaut a 1 heure (si memoization actif)
 * {datacache 3600}
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_PMB_datacache_dist($idb, &$boucles, $crit) {
	return critere_DATA_datacache_dist($idb, $boucles, $crit);
}

/**
 * Modifier le critere racine
 */
function critere_PMB_racine_dist($idb, &$boucles, $crit) {
	$c = array("'='", "racine", 1);
	$boucles[$idb]->where[] = $c;
}

/**
 * Modifier le critere id_parent pour la boucle PMB:SECTIONS
 */
function critere_PMB_SECTIONS_id_parent_dist($idb, &$boucles, $crit) {
	$id_section = kwote(calculer_argument_precedent($idb, 'id_section', $boucles));
	$c = array("'='", "id_parent", $id_section);
	$boucles[$idb]->where[] = $c;
}

/**
 *
 * Selectionne les notices demandees
 * et retourne un tableau des elements parsees
 * 
 * Une ou n notices
 * (PMB:NOTICES) {id}
 * (PMB:NOTICES) {id_notice}
 * (PMB:NOTICES) {liste #TABLEAU_IDS}
 *
 * Notices lies a celle(s) donnees
 * (PMB:NOTICES) {id} {autres_lectures} 
 * (PMB:NOTICES) {liste #TABLEAU_IDS} {autres_lectures}
 *
 * Notices issues des syndications d'articles
 * (PMB:NOTICES) {nouveautes}
 *
 * Notices issues des auteurs
 * (PMB:NOTICES) {id_auteur}
 *
 * Notices issues des recherches
 * (PMB:NOTICES) {rechercher}
 * (PMB:NOTICES) {rechercher}{look ?}{id_section ?}{id_location ?}{id_location_memo ?}
 * 
 */
function inc_pmb_notices_select_dist(&$command, $iterateur) {
	$criteres = $command['where'];

	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();

	// depuis une liste
	if (is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}

	// depuis un critere id=x ou {id?}
	// a supprimer ?
	if ($id = pmb_critere_valeur($criteres, 'id')) {
		$ids = pmb_intersect_ids($ids, $id);
	}

	// depuis un critere id_notice=x ou {id_notice?}
	if ($id = pmb_critere_valeur($criteres, 'id_notice')) {
		$ids = pmb_intersect_ids($ids, $id);
	}

	// autres lecteurs : ceux qui ont lu ceci ont aussi emprunte cela
	if (pmb_recherche_critere($criteres, 'autres_lecteurs')) {
		$ids = pmb_ids_notices_autres_lecteurs($ids);
	}

	// nouveautes de la syndication
	if (pmb_recherche_critere($criteres, 'nouveautes')) {
		// prendra 50 nouveautes par defaut...
		// sauf si {nouveautes 3}
		// /!\ ça ne recupere que 3, pas #ENV{nb,3}, peut etre parce
		// que c'est le premier argument d'un critere (limitation connue de SPIP).
		// on peut utiliser : {nouveautes 3}{0,#ENV{nb,3}} pour limiter entre 1 et 3 donc.
		$nombre = pmb_interprete_argument_critere($criteres, 'nouveautes', 1);
		$idsn = pmb_ids_notices_nouveautes('', $nombre);
		$ids = pmb_intersect_ids($ids, $idsn);
	}

	// id_auteur :     trouver les notices d'un auteur
	// id_collection : trouver les notices d'une collection
	// id_editeur :    trouver les notices d'un editeur
	foreach (array(
		'auteurs' => 'id_auteur',
		'collections' => 'id_collection',
		'editeurs' => 'id_editeur') as $objet => $_id_objet)
	{
		if ($ids_objet = pmb_critere_valeur($criteres, $_id_objet)) {
			$pmb_extraire_objet_ids = 'pmb_extraire_' . $objet . '_ids';
			$objets = $pmb_extraire_objet_ids($ids_objet);
			if ($objets) {
				$n = array();
				foreach ($objets as $o) {
					$n = array_unique(array_merge($n, $o['ids_notice']));
				}
				$ids = pmb_intersect_ids($ids, $n);
			}
			unset($objets);
		}
	}


	// recherche de notices
	if (pmb_recherche_critere($criteres, 'rechercher')) {
		// valeur cherchee (parametre)
		$recherche = pmb_interprete_argument_critere($criteres, 'rechercher', 1);
		// valeur cherchee (env)
		if (!$recherche) {
			$recherche = pmb_critere_valeur($criteres, 'rechercher');

			// le premier trouve...
			if ($recherche) {
				$recherche = array_shift($recherche);
			}
		}

		if (!$recherche) {
			$recherche = '';
		}
		$iterateur->exception_des_criteres('rechercher');

		$total_resultats = 0; // sera renseigne par la fonction de recherche
		$demande = array('recherche' => $recherche);
		
		// on prend au debut, et on limite la recherche a 100 elements
		$debut = '0'; $nombre = '5';

		// si la boucle contient une limite {0,50}
		if ($command['limit']) {
			list($debut, $nombre) = explode(',', $command['limit']);
		}
		
		// si la boucle contient une pagination {pagination 5}
		// on retrouve les valeurs de position et de pas, et on pose un
		// flag 'pagination' pour un hack sur la recherche
		// permettant de ne pas demander tous les resultats
		// mais seulement ceux a afficher dans le cadre en cours
		$pagination = false;
		if ($command['pagination']) {
			list($debut, $nombre) = $command['pagination'];
			if (!$debut) $debut = 0;
			$pagination = true;
		}

		// on affine notre demande avec d'autres contraintes si elles sont presentes.
		foreach (array(
			'id_section' => 'id_section',
			'id_section_memo' => 'id_section_parent',
			'id_location_memo' => 'id_location',
			'id_location' => 'id_location',
			'look' => 'look') as $nom=>$requete)
		{
			if ($valeurs = pmb_critere_valeur($criteres, $nom)) {
				$iterateur->exception_des_criteres($nom);
				// on ajoute le premier venu...
				// sauf pour look, où on veut toutes les valeurs...
				if ($nom == 'look') {
					$demande[$requete] = $valeurs;
				} else {
					$demande[$requete] = array_shift($valeurs);
				}
			}
		}

		$idsr = pmb_ids_notices_recherches($demande, $total_resultats, $debut, $nombre, $pagination);
		$ids = pmb_intersect_ids($ids, $idsr);
		$iterateur->total = $total_resultats;

	}

	// retourner les notices selectionnees
	$res = pmb_extraire_notices_ids($ids);
	
	return $res;
}




/**
 *
 * Selectionne un ou plusieurs auteurs PMB
 * et retourne un tableau des elements parsees
 * 
 * Un auteur
 * (PMB:AUTEURS) {id_auteur}
 *
 * Des auteurs
 * (PMB:AUTEURS) {liste #TABLEAU_IDS_AUTEUR}
 * 
 */
function inc_pmb_auteurs_select_dist(&$command, $iterateur) {
	return inc_pmb_select_abstract_dist($command, $iterateur, 'auteurs', 'id_auteur');
}


/**
 *
 * Selectionne les reservations d'un ou plusieurs auteurs identifies a PMB
 * et retourne un tableau des elements parsees
 * 
 * Liste des reservations
 * (PMB:RESERVATIONS) {pmb_session}
 * (PMB:RESERVATIONS) {liste #TABLEAU_PMB_SESSIONS}
 * 
 */
function inc_pmb_reservations_select_dist(&$command, $iterateur) {
	return inc_pmb_select_abstract_dist($command, $iterateur, 'reservations', 'pmb_session');
}


/**
 *
 * Selectionne les prets d'un ou plusieurs auteurs identifies a PMB
 * et retourne un tableau des elements parsees
 * 
 * Liste des prets
 * (PMB:PRETS) {pmb_session}{type=0} // en retard // todo : ameliorer le nom du critere type :p
 * (PMB:PRETS) {pmb_session}{type=1} // en cours
 * (PMB:PRETS) {liste #TABLEAU_PMB_SESSIONS}
 *
 */
function inc_pmb_prets_select_dist(&$command, $iterateur) {
	$criteres = $command['where'];
	
	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();

	// depuis une liste
	if (is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}

	// depuis un critere pmb_session=x ou {pmb_session?}
	if ($id = pmb_critere_valeur($criteres, 'pmb_session')) {
		$iterateur->exception_des_criteres('pmb_session'); 
		$ids = pmb_intersect_ids($ids, $id);
	}

	$type = 0;
	// {type=1} 
	if ($types = pmb_critere_valeur($criteres, 'type')) {
		$iterateur->exception_des_criteres('type');
		// le premier trouve...
		$type = array_shift($types);
	}

	// retourner les objets selectionnees
	$res = pmb_extraire_prets_ids($ids, $type);

	return $res;
}


/**
 *
 * Selectionne une ou plusieurs collections PMB
 * et retourne un tableau des elements parsees
 * 
 * Une collection
 * (PMB:COLLECTIONS) {id_collection}
 *
 * Des collections
 * (PMB:COLLECTIONS) {liste #TABLEAU_IDS_COLLECTION}
 * 
 */
function inc_pmb_collections_select_dist(&$command, $iterateur) {
	return inc_pmb_select_abstract_dist($command, $iterateur, 'collections', 'id_collection');
}



/**
 *
 * Selectionne une ou plusieurs editeurs PMB
 * et retourne un tableau des elements parsees
 * 
 * Un editeur
 * (PMB:EDITEURS) {id_editeur}
 *
 * Des editeurs
 * (PMB:EDITEURS) {liste #TABLEAU_IDS_EDITEUR}
 * 
 */
function inc_pmb_editeurs_select_dist(&$command, $iterateur) {
	return inc_pmb_select_abstract_dist($command, $iterateur, 'editeurs', 'id_editeur');
}



/**
 *
 * Selectionne les exemplaires disponibles d'une notice
 * et retourne un tableau des elements parsees
 * 
 * Liste des exemplaires
 * (PMB:EXEMPLAIRES) {id_notice}
 * (PMB:EXEMPLAIRES) {liste #TABLEAU_IDS_NOTICE}
 * 
 */
function inc_pmb_exemplaires_select_dist(&$command, $iterateur) {
	return inc_pmb_select_abstract_dist($command, $iterateur, 'exemplaires', 'id_notice');
}



/**
 *
 * Selectionne les documents d'une ou plusieurs notices
 * et retourne un tableau des elements parsees
 * 
 * Liste des documents
 * (PMB:DOCUMENTS) {id_notice}
 * (PMB:DOCUMENTS) {id_notice}{image=oui} // s'appuie sur le mime type image/
 * (PMB:DOCUMENTS) {id_notice}{image=non}
 *
 */
function inc_pmb_documents_select_dist(&$command, $iterateur) {
	// comme boucle_PMB_DOCUMENTS() ne fonctionne pas
	// on annule certains criteres passes par boucle_DOCUMENTS() de mediatheque
	$iterateur->exception_des_criteres('.mode'); // hum le .
	$iterateur->exception_des_criteres('.taille'); // hum le .
	return inc_pmb_select_abstract_dist($command, $iterateur, 'documents', 'id_notice');
}




/**
 * Editeurs, auteurs et collections sont les memes principes
 * on les regroupe dans une fonction d'abstraction...
 *
 * @param array $command
 * 		Le tableau command de l'iterateur
 *
 * @param array $iterateur
 * 		L'iterateur complet
 *
 * @param string $objet
 * 		Le nom de l'objet (pluriel) ex:auteurs
 *
 * @param string $_id_objet
 *		Le nom de l'identifant de l'objet 'id_auteur'
**/ 
// 
function inc_pmb_select_abstract_dist(&$command, $iterateur, $objet, $_id_objet) {
	$criteres = $command['where'];
	
	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();

	// depuis une liste
	if (is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}

	// depuis un critere id_objet=x ou {id_objet?}
	if ($id = pmb_critere_valeur($criteres, $_id_objet)) {
		$ids = pmb_intersect_ids($ids, $id);
		// pas la peine de filtrer dessus... surtout pour {pmb_session} qui n'y est plus ensuite
		$iterateur->exception_des_criteres($_id_objet);
	}

	// retourner les objets selectionnees
	$pmb_extraire_ids = 'pmb_extraire_' . $objet . '_ids';
	$res = $pmb_extraire_ids($ids);

	return $res;
}




/**
 *
 * Selectionne les lieux de classement des notices
 * et retourne un tableau des elements parsees
 * Un lieu est au sens PMB "location"
 * 
 * - Location : c'est comme un centre de doc physique, une adresse.
 * 		1 PMB permet de gérer plusieurs Locations. On peut dire
 * 		qu'il permet de gérer un groupement de bibliotheques/centre de docs.
 *
 * - Section : C'est comme un rayonnage de centre de doc. Un theme de classement en quelque sorte.
 * 		Une section est independante d'une location, au sens ou une meme section
 * 		peut être presente sur plusieurs locations.
 *
 * En SPIP, on pourrait dire que
 * - Location = rubriques racines,
 * - Sections = Groupes de mots clés (hierarchiques)
 * 
 * Les centres de docs a la racine
 * (PMB:LIEUX)
 * (PMB:LIEUX) {racine}
 * 
 * (PMB:LIEUX) {id_location}
 * 
 */
function inc_pmb_lieux_select_dist(&$command, $iterateur) {
	$criteres = $command['where'];

	// racine indique... on ne s'occupe pas du reste...
	// depuis un critere {racine}
	/*
	if (pmb_recherche_critere($criteres, 'racine')) {
		// retourner les auteurs selectionnees
		$iterateur->exception_des_criteres('racine');
		return pmb_extraire_locations_racine();
	}*/
	
	$res = pmb_extraire_locations_racine();

	if (pmb_recherche_critere($criteres, 'id_location')) {
		if (!$ids_location = pmb_critere_valeur($criteres,  'id_location')) {
			return array();
		}

		$iterateur->exception_des_criteres('id_location');
		foreach ($res as $c => $l) {
			if (!in_array($l['id_location'], $ids_location)) {
				unset($res[$c]);
			}
		}
		$res = array_values($res);
	}



	return $res;
}




/**
 *
 * Selectionne les themes (sections) de classement des notices
 * et retourne un tableau des elements parsees
 * Un lieu est au sens PMB "location"
 * 
 * Les centres de docs a la racine
 * (PMB:SECTIONS)
 * (PMB:SECTIONS) {id_section ?} // la section
 * (PMB:SECTIONS) {id_parent ?}  // sections enfants
 * (PMB:SECTIONS) {id_location ?} // sections dans le lieu...
 * 
 */
function inc_pmb_sections_select_dist(&$command, $iterateur) {
	$criteres = $command['where'];

	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();

	// depuis une liste
	if (is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}

	// depuis un critere id_section=x ou {id_section ?}
	if ($id = pmb_critere_valeur($criteres, 'id_section')) {
		$ids = pmb_intersect_ids($ids, $id);
	}

	// testons la presence de id_parent (un id_section) ou id_location
	if ($ids_parents = pmb_critere_valeur($criteres,  'id_parent')) {
		$iterateur->exception_des_criteres('id_parent');
		$sections = pmb_extraire_sections_depuis_sections_ids($ids_parents);
		return $sections;
	}

	if ($ids_location = pmb_critere_valeur($criteres,  'id_location')) {
		$iterateur->exception_des_criteres('id_location');
		$sections = pmb_extraire_sections_depuis_locations_ids($ids_location);
		return $sections;
	}

	return pmb_extraire_sections_ids($ids);
}




// retourne l'intersection des ids trouves
// equivalent {...} AND {...}
function pmb_intersect_ids($anciens, $nouveaux) {
	if ($anciens) {
		return array_intersect($anciens, $nouveaux);
	}
	return $nouveaux;
}

/**
 * Obtenir les identifiants de nouveautes
 * issues des syndications
 * @return array liste des identifiants de notices trouvees
**/
function pmb_ids_notices_nouveautes($debut, $nombre) {
	$contexte = array();
	if (!$debut) {
		$debut = 0;
	}
	$contexte['debut'] = $debut;
	if ($nombre) {
		$contexte['nombre'] = $nombre;
	}
	$ids = explode(',', trim(recuperer_fond('public/pmb_nouveautes', $contexte)));
	return $ids;
}



/**
 * Recuperer un critere dans le tableau where selon une contrainte. 
 *
 * @return array, un element par valeur trouvee
**/
function pmb_critere_valeur($criteres, $cle, $op = '=') {
	$res = array();
	if (!is_array($criteres) OR !$criteres) {
		return $res;
	}
	foreach ($criteres as $c) {
		if (is_array($c) AND $c[0] == $op AND $c[1] == $cle) {
			// enlever les guillemets si presents
			$v = $c[2];
			if (($v[0] == "'") and ($v[ count($v)-1 ] == "'")) {
				$v = substr($v, 1,-1);
			}
			$res[] = $v;
		// ((machin IN ('34','TRUC'))) // magnifique :/
		// ((look  IN ('PMB','FIRSTACCESS','ALL')))
		} elseif (is_string($c)) {
			// cf iterateurs->calculer_filtres()

			$op = $c;
			
			// traiter {cle IN a,b} ou {valeur !IN a,b}
			// prendre en compte le cas particulier de sous-requetes
			// produites par sql_in quand plus de 255 valeurs passees a IN
			if (preg_match_all(',\s+IN\s+(\(.*\)),', $op, $s_req)) {
				$req = '';
				foreach($s_req[1] as $key => $val) {
					$req .= trim($val, '(,)') . ',';
				}
				$req = '(' . rtrim($req, ',') . ')';
			}
			if (preg_match(',^\(\(([\w/]+)(\s+NOT)?\s+IN\s+(\(.*\))\)(?:\s+(AND|OR)\s+\(([\w/]+)(\s+NOT)?\s+IN\s+(\(.*\))\))*\)$,', $op, $regs)) {
				// 1 'look'
				// 2 NOT
				// 3 ('TRUC','CHOSE')
				if ($regs[1] == $cle and !$regs[2]) {
					$v = explode(',', trim($regs[3], ' ()'));
					// enlever tous les guillemets entourants
					foreach($v as $a=>$b) { $v[$a] = trim($b, "'"); }
					// comme c'est deja un tableau, on le merge aux resultats deja obtenus
					$res = array_unique(array_merge($res, $v));
				}
			}

		}
	}
	// on enleve les valeurs vides ''
	$res = array_filter($res);
	return $res;
}


/**
 * Chercher la presence d'un critere dans le tableau where. 
 *
 * @return bool vrai si critere trouve.
**/
function pmb_recherche_critere($criteres, $cle) {
	if (!is_array($criteres) OR !$criteres OR !$cle) {
		return false;
	}
	foreach ($criteres as $c) {
		// {c}   =>  array('=', 'c', '')
		// {c=3} =>  array('=', 'c', '3')
		// {c 3} =>  array('c', '3')
		if (is_array($c) AND ($c[1] == $cle OR $c[0] == $cle)) {
			return true;
		}
	}
	return false;
}


/**
 * Chercher la valeur d'un parametre dans un critere
 * {critere un,deux}
 *
 * @return mixed, valeur trouvee, sinon null
**/
function pmb_interprete_argument_critere($criteres, $cle, $index) {
	if (!is_array($criteres) OR !$criteres) {
		return null;
	}
	foreach ($criteres as $c) {
		// {c 3} =>  array('c', '3')
		if (is_array($c) AND ($c[0] == $cle)) {
			if (isset($c[$index])) {
				return $c[$index];
			}
		}
	}
	return null;
}



/**
 * Boucle PMB:DOCUMENTS
 * Éviter les traitements automatiques de SPIP sur les boucles Documents
**/
function boucle_PMB_DOCUMENTS($id_boucle, &$boucles) {
	return calculer_boucle($id_boucle, $boucles);
}

/**
 * 
 * Critere d'extraction des nouveautes de PMB
 * 
 * (SYNDIC_ARTICLES){pmb_notices}
 * (SYNDIC_ARTICLES){!pmb_notices}
 *
 * Recherche dans les syndications les articles
 * ce qui concerne des notices PMB...
 * 
**/
function critere_SYNDIC_ARTICLES_pmb_notices($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$prim = $boucle->primary;
	$table = $boucle->id_table;
	
	$c = array("'REGEXP'", "'$table.url'", "sql_quote('notice_display')");

	if ($crit->not) {
		$c = array("'NOT'", $c);
	}
	
	$boucle->where[] = $c;
}


/**
 * Balise #URL_PMB_NOTICE et #URL_PMB_NOTICE{18}
**/
function balise_URL_PMB_NOTICE_dist($p) {
	return pmb_balise_url($p, 'id_notice', 'pmb_notice');
}


/**
 * Balise #URL_PMB_COLLECTION et #URL_PMB_COLLECTION{18}
**/
function balise_URL_PMB_COLLECTION_dist($p) {
	return pmb_balise_url($p, 'id_collection', 'pmb_collection');
}


/**
 * Balise #URL_PMB_EDITEUR et #URL_PMB_EDITEUR{18}
**/
function balise_URL_PMB_EDITEUR_dist($p) {
	return pmb_balise_url($p, 'id_editeur', 'pmb_editeur');
}

/**
 * Balise #URL_PMB_AUTEUR et #URL_PMB_AUTEUR{18}
**/
function balise_URL_PMB_AUTEUR_dist($p) {
	return pmb_balise_url($p, 'id_auteur', 'pmb_auteur');
}

/**
 * Balise URL_PMB_NOUVEAUTES
**/
function balise_URL_PMB_NOUVEAUTES_dist($p) {
	$page = 'pmb_nouveautes';
	$p->code = "generer_url_public('$page')";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Balise URL_PMB_COMPTE
**/
function balise_URL_PMB_COMPTE_dist($p) {
	$page = 'pmb_compte';
	$p->code = "generer_url_public('$page')";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Balise URL_PMB_RECHERCHE
**/
function balise_URL_PMB_RECHERCHE_dist($p) {
	$page = 'pmb_recherche';
	$p->code = "generer_url_public('$page')";
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Balise URL_PMB_CATALOGUE
 * et #URL_PMB_CATALOGUE{#ID_LOCATION}
**/
function balise_URL_PMB_CATALOGUE_dist($p) {
	$page = 'pmb_catalogue';
	if ($id_location = interprete_argument_balise(1, $p)) {
		$p->code = "parametre_url(generer_url_public('$page'), 'id_location', $id_location)";
	} else {
		$p->code = "generer_url_public('$page')";
	}
	$p->interdire_scripts = false;
	return $p;
}



function pmb_balise_url($p, $champ, $page) {
	if (!$id = interprete_argument_balise(1, $p)) {
		$id = champ_sql($champ, $p);
	}

	$p->code = "(($id) ? generer_url_public('$page', '$champ='.$id) : '')";
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Pour afficher dans une boucle notices avec {pagination}
 * "Résultats de x à y sur z ouvrages"
**/
function balise_PMB_NOMBRE_RESULTATS_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	
	$pas = $p->boucles[$b]->total_parties;
	$type = $p->boucles[$b]->modificateur['debut_nom'];
	$nb = "(isset(\$Numrows['$b']['grand_total']) ? \$Numrows['$b']['grand_total'] : \$Numrows['$b']['total'])";

	$p->boucles[$b]->numrows = true;
	$p->code = "recuperer_fond('inclure/inc-pmb-nombre-resultats', array(
		'resultats' => $nb,
		'debut' => _request('debut' . $type),
		'fin' => $pas))";
	return $p;
}

?>
