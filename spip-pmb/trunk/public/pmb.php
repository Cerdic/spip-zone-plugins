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
 * (PMB:NOTICES) {id} 
 * (PMB:NOTICES) {liste #TABLEAU_IDS} 
 * (PMB:NOTICES) {id} {autres_lectures} 
 * (PMB:NOTICES) {liste #TABLEAU_IDS} {autres_lectures}
 * 
 * Selectionner les notices demandees
 * et retourner un tableau des elements parsees
 */
function inc_pmb_notices_select_dist(&$command) {

	// on peut fournir une liste l'id
	// ou egalement un critere id=x
var_dump($command);
	$ids = array();

	// depuis une liste
	if (is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}

	// depuis un critere id=x ou {id?}
	if ($id = pmb_critere_donne($command['where'], 'id')) {
		if ($ids) {
			$ids = array_intersect($ids, $id);
		} else {
			$ids = $id;
		}
	}

	// autres lecteurs : ceux qui ont lu ceci ont aussi emprunte cela
	if (pmb_recherche_critere($command['where'], 'autres_lectures')) {
		$ids = pmb_ws_ids_notices_autres_lecteurs($ids);
	}
	
	// retourner les notices selectionnees
	$res = pmb_tabnotices_extraire($ids);

	return $res;
}






/**
 * Recuperer un critere dans le tableau where selon une contrainte. 
 *
 * @return array, un element par valeur trouvee
**/
function pmb_critere_donne($criteres, $cle, $op = '=') {
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
		return $res;
	}
	foreach ($criteres as $c) {
		if (is_array($c) AND $c[1] == $cle) {
			return true;
		}
	}
	return false;
}

?>
