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
			$this->tableau = $cache;
		} else {
			$select = charger_fonction($this->type . '_select', 'inc', true);
			$this->tableau = $select($this->command);

			// cache d'une heure par defaut.
			$ttl = isset($this->command['datacache']) ? $this->command['datacache'] : 3600;
			
			if (is_array($this->tableau) AND $ttl>0) {
				$this->cache_set($cle, $ttl);
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
 *
 * Selectionne les notices demandees
 * et retourne un tableau des elements parsees
 * 
 * Une ou n notices
 * (PMB:NOTICES) {id} 
 * (PMB:NOTICES) {liste #TABLEAU_IDS}
 *
 * Notices lies a celle(s) donnees
 * (PMB:NOTICES) {id} {autres_lectures} 
 * (PMB:NOTICES) {liste #TABLEAU_IDS} {autres_lectures}
 *
 * Notices issues des syndications d'articles
 * (PMB:NOTICES) {nouveautes}
 * 
 */
function inc_pmb_notices_select_dist(&$command) {
	$criteres = &$command['where'];
	
	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();
	

	// depuis une liste
	if (is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}

	// depuis un critere id=x ou {id?}
	if ($id = pmb_critere_valeur($criteres, 'id')) {
		if ($ids) {
			$ids = array_intersect($ids, $id);
		} else {
			$ids = $id;
		}
	}

	// autres lecteurs : ceux qui ont lu ceci ont aussi emprunte cela
	if (pmb_recherche_critere($criteres, 'autres_lecteurs')) {
		$ids = pmb_ws_ids_notices_autres_lecteurs($ids);
	}

	// nouveautes de la syndication
	if (pmb_recherche_critere($criteres, 'nouveautes')) {
		// prendra 50 nouveautes par defaut...
		// sauf si {nouveautes 3}
		$nombre = pmb_interprete_argument_critere($criteres, 'nouveautes', 1);
		$ids = pmb_ids_notices_nouveautes('', $nombre);
	}
	
	// retourner les notices selectionnees
	$res = pmb_tabnotices_extraire($ids);

	return $res;
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
			$res[] = $c[2];
		}
	}
	return $res;
}


/**
 * Chercher la presence d'un critere dans le tableau where. 
 *
 * @return array, un element par valeur trouvee
**/
function pmb_recherche_critere($criteres, $cle) {
	if (!is_array($criteres) OR !$criteres) {
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
 * Balise #URL_NOTICE
 * et #URL_NOTICE{18}
 * 
**/
function balise_URL_NOTICE_dist($p) {

	if (!$id = interprete_argument_balise(1, $p)) {
		$id = champ_sql('id', $p);
	}

	$page = 'pmb_notice';
	$p->code = "(($id) ? generer_url_public('$page', 'id='.$id) : '')";
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Balise #URL_COLLECTION
 * et #URL_COLLECTION{18}
 * 
**/
function balise_URL_COLLECTION_dist($p) {

	if (!$id = interprete_argument_balise(1, $p)) {
		$id = champ_sql('id_collection', $p);
	}

	$page = 'pmb_collection';
	$p->code = "(($id) ? generer_url_public('$page', 'id='.$id) : '')";
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Balise #URL_EDITEUR
 * et #URL_EDITEUR{18}
 * 
**/
function balise_URL_EDITEUR_dist($p) {

	if (!$id = interprete_argument_balise(1, $p)) {
		$id = champ_sql('id_editeur', $p);
	}

	$page = 'pmb_editeur';
	$p->code = "(($id) ? generer_url_public('$page', 'id='.$id) : '')";
	$p->interdire_scripts = false;
	return $p;
}

?>
