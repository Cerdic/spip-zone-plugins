<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/prestashop_webservice');
include_spip('inc/prestashop_webservice_utils');
include_spip('iterateur/data');

/**
 * Requeteur pour les boucles (prestashop:products)
 *
 * @param $boucles Liste des boucles
 * @param $boucle  La boucle parcourue
 * @param $id      L'identifiant de la boucle parcourue
 *
 **/
function requeteur_PRESTASHOP_dist(&$boucles, &$boucle, &$id) {
	$resource = $boucle->type_requete;
	if ($g = charger_fonction('prestashop', 'iterateur', true)) {
		$boucles[$id] = $g($boucle, $resource);
		// from[0] stocke le type de data (products, categories, ...)
		$boucles[$id]->from[] = $resource;
	} else {
		$boucle->type_requete = false;
		$msg = array('zbug_requeteur_inconnu',
			array(
				'requeteur' => 'prestashop',
				'type' => $resource
			));
		erreur_squelette($msg, $boucle);
	}
}




/**
 * Creer une boucle sur un iterateur PRESTASHOP
 * (PRESTASHOP:Products) ...
 * annonce au compilo les "champs" disponibles
 **/
function iterateur_PRESTASHOP_dist($b, $type) {
	$b->iterateur = 'PRESTASHOP'; # designe la classe d'iterateur
	$b->show = array(
		'field' => array(
			'cle' => 'STRING',
			'valeur' => 'STRING',
			#'rechercher' => 'STRING',
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
class IterateurPRESTASHOP extends IterateurData {

	/** La 'resource' souhaitée sur le WS de Prestashop. */
	protected $resource = '';

	/** Liste des langues (id => code) dans Prestashop */
	protected $langues = [];

	/**
	 * Retourne la 'resource' souhaitée
	 * @return string
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * Retourne la liste des langues (id => code) de Prestashop.
	 * @return array
	 */
	public function get_langues() {
		if (empty($this->langues)) {
			$this->langues = prestashop_ws_list_languages();
		}
		return $this->langues;
	}

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

		$this->resource = strtolower($this->command['from'][0]);

		// on ne garde pas les where vides
		$this->command['where'] = array_values(array_filter($this->command['where']));

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
			if (!$select = charger_fonction('prestashop_ws_' . $this->type . '_select', 'inc', true)) {
				$select = charger_fonction('prestashop_ws_select', 'inc', true);
			}
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
			spip_log("erreur datasource PRESTASHOP : " .$this->type);
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

		if (
			$cache
			AND ($cache['time'] + (isset($ttl) ? $ttl : $cache['ttl']) > time())
			AND !prestashop_ws_cache_update()
		) {
			return $cache['data'];
		}

		return false;
	}


	/**
	 * Cree une cle unique
	 * pour sauvegarder une analyse de donnees
	 * basee sur les criteres de boucle demandes
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
 * Interroge le Webservice Prestashop et retourne ce qui est demandé.
 *
 * @param array $command
 *     Le tableau command de l'iterateur
 * @param array $iterateur
 *     L'iterateur complet
 **/
function inc_prestashop_ws_select_dist(&$command, $iterateur) {
	$criteres = $command['where'];
	$resource = $iterateur->get_resource();

	$query = [
		'resource' => $resource,
	];

	// on peut fournir une liste l'id
	// ou egalement un critere id=x
	$ids = array();

	// depuis une liste
	if (isset($command['liste']) and is_array($command['liste']) and count($command['liste'])) {
		$ids = $command['liste'];
	}


	// depuis un critere id=x ou {id?}
	if ($id = prestashop_ws_critere_valeur($criteres, 'id')) {
		$ids = prestashop_ws_intersect_ids($ids, $id);
		// pas la peine de filtrer dessus...
		$iterateur->exception_des_criteres('id');
		$query['filter[id]'] = '[' . implode('|', $ids) . ']';
	}

	// liste des champs possibles pour cette ressource
	// cela permet de renseigner les filtres automatiquement
	// et réduire ainsi la taille de la requête retournée,
	// évitant que ce soit l'itérateur data qui filtre après coup.
	$champs = prestashop_ws_show_resource($resource);
	foreach ($champs as $champ => $desc) {
		if ($val = prestashop_ws_critere_valeur($criteres, $champ)) {
			// pas la peine de filtrer dessus...
			$iterateur->exception_des_criteres($champ);
			$query['filter[' . $champ . ']'] = '[' . implode('|', $val) . ']';
		}
	}

	// display (full par défaut)
	if (!$display = prestashop_ws_critere_valeur($criteres, 'display')) {
		$display = 'full';
	}
	$iterateur->exception_des_criteres('display');
	$query['display'] = $display;


	/*
		Si on met une limite… on ne sait plus paginer
		car on ne connait pas le nombre total de résultats.

		// si la boucle contient une pagination {pagination 5}
		// on retrouve les valeurs de position et de pas
		if (!empty($command['pagination'])) {
			list($debut, $nombre) = $command['pagination'];
			if (!$debut) $debut = 0;
			$query['limit'] = $debut . ',' . $nombre;
		}
	*/

	try {
		$lang = !empty($iterateur->info[4]) ? $iterateur->info[4] : null;
		$wsps = \SPIP\Prestashop\Webservice::getInstanceByLang($lang);
	} catch (PrestaShopWebserviceException $ex) {
		spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage(), 'prestashop.' . _LOG_ERREUR);
		return [];
	}

	// Demander les données au Prestashop.
	try {
		if ($xml = $wsps->get($query)) {
			$arbre = prestashop_ws_nettoyer_reception($xml, $resource, $iterateur->get_langues());
			return $arbre;
		}
	} catch (PrestaShopWebserviceException $ex) {
		spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage(), 'prestashop.' . _LOG_ERREUR);
		spip_log('Query : ', 'prestashop.' . _LOG_ERREUR);
		spip_log($query, 'prestashop.' . _LOG_ERREUR);
		return [];
	}

	return [];
}

/**
 * Simplifie les données reçues du webservice de prestashop pour les boucles DATA.
 * Crée des balise multis sur certains contenus.
 * @param SimpleXML $xml
 * @param string $resource
 */
function prestashop_ws_nettoyer_reception($xml, $resource, $langues) {
	if (empty($xml->$resource)) {
		return [];
	}
	$arbre = [];
	foreach ($xml->$resource as $group) {
		foreach ($group as $element) {
			$arbre[] = prestashop_ws_nettoyer_value($element, $langues);
		}
		break;
	}
	return $arbre;
}

/**
 * Crée un tableau à partir du xml retourné par le webservice prestashop.
 *
 * On simplifie certaines entrées, notamment celles qui ont une balise language
 * pour un faire une balise 'multi' SPIP.
 *
 * Également on crée un champ 'nn_url' pour les balises 'nn' qui ont l'attribut
 * 'xlink:href'. Ça pourra toujours servir.
 *
 * @param $value
 * @param $langues
 * @return array|string
 */
function prestashop_ws_nettoyer_value($value, $langues) {
	if (!count($value->children())) {
		return (string)$value;
	} else {
		if (isset($value->language)) {
			$t = [];
			foreach ($value->language as $trad) {
				if ($text = (string)$trad) {
					$id = (int)$trad['id'];
					$t[$langues[$id]['code']] = $text;
				}
			}
			if ($t) {
				$multi = '<multi>';
				foreach ($t as $lang => $text) {
					$multi .= '[' . $lang . ']' . (string)$text;
				}
				$multi .= '</multi>';
				return $multi;
			} else {
				return '';
			}
		} else {
			$res = [];
			if (isset($value['nodeType'])) {
				$type = (string)$value['nodeType'];
				$data = [];
				foreach ($value->$type as $k => $v) {
					$data = prestashop_ws_nettoyer_value($v, $langues);
					if ($attr = $v->attributes('xlink', true) and !empty($attr['href'])) {
						$data[$k . '_url'] = (string)$attr['href'];
					}
					$res[] = $data;
				}
			} else {
				foreach ($value as $k => $v) {
					$res[$k] = prestashop_ws_nettoyer_value($v, $langues);
					if ($attr = $v->attributes('xlink', true) and !empty($attr['href'])) {
						$res[$k . '_url'] = (string)$attr['href'];
					}
				}
			}
			return $res;
		}
	}
}

/**
 * Recuperer un critere dans le tableau where selon une contrainte.
 *
 * @return array, un element par valeur trouvee
 **/
function prestashop_ws_critere_valeur($criteres, $cle, $op = '=') {
	$res = array();
	if (!is_array($criteres) OR !$criteres) {
		return $res;
	}
	foreach ($criteres as $c) {
		if (is_array($c) AND $c[0] == $op AND $c[1] == $cle) {
			// enlever les guillemets si presents
			$v = $c[2];
			if ($v !== 'NULL') {
				if (($v[0] == "'") and ($v[ count($v)-1 ] == "'")) {
					$v = substr($v, 1,-1);
				}
				$res[] = $v;
			}
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
function prestashop_ws_recherche_critere($criteres, $cle) {
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
function prestashop_ws_interprete_argument_critere($criteres, $cle, $index) {
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
 * Retourne l'intersection des ids trouvés.
 * Équivalent {...} AND {...}
 */
function prestashop_ws_intersect_ids($anciens, $nouveaux) {
	if ($anciens) {
		return array_intersect($anciens, $nouveaux);
	}
	return $nouveaux;
}