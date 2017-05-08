<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
} // securiser

# donner un exemple d'url pour le formulaire de choix
define('URLS_ARBOPOLY_EXEMPLE', '/article/titre');
# specifier le form de config utilise pour ces urls
define('URLS_ARBOPOLY_CONFIG', 'arbo');

// TODO: une interface permettant de verifier qu'on veut effectivment modifier
// une adresse existante
defined('CONFIRMER_MODIFIER_URL') || define('CONFIRMER_MODIFIER_URL', false);

include_spip('urls/arbo');

/**
 * Renseigner les infos les plus recentes de l'url d'un objet
 * et de quoi la (re)construire si besoin
 *
 * @param string $type
 * @param int $id_objet
 * @param array $contexte
 *   id_parent : rubrique parent
 * @return bool|null|array
 */
function renseigner_url_arbopoly($type, $id_objet, $contexte = array()) {
	$urls = array();
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table(table_objet($type));
	$table = $desc['table'];
	$col_id = @$desc['key']["PRIMARY KEY"];
	if (!$col_id) {
		return false;
	} // Quand $type ne reference pas une table
	$id_objet = intval($id_objet);

	$id_parent = (isset($contexte['id_parent'])?$contexte['id_parent']:null);

	$champ_titre = $desc['titre'] ? $desc['titre'] : 'titre';

	// parent
	$champ_parent = url_arbo_parent($type);
	$sel_parent = ', 0 as parent';
	$order_by_parent = "";
	if ($champ_parent) {
		// si un parent est fourni est qu'il est polyhierarchique, on recherche une URL pour ce parent
		if ($id_parent
			and $type_parent = end($champ_parent)
			and $url_verifier_parent_objet = charger_fonction('url_verifier_parent_objet', 'inc', true)
			and $url_verifier_parent_objet($type, $id_objet, $type_parent, $id_parent)) {
			$sel_parent = ", ".intval($id_parent) . ' as parent';
			// trouver l'url qui matche le parent en premier
			$order_by_parent = "U.id_parent=".intval($id_parent)." DESC, ";
		}
		// sinon on prend son parent direct fourni par $champ_parent
		else {
			$sel_parent = ", O." . reset($champ_parent) . ' as parent';
			// trouver l'url qui matche le parent en premier
			$order_by_parent = "O." . reset($champ_parent) . "=U.id_parent DESC, ";
		}
	}
	//  Recuperer une URL propre correspondant a l'objet.
	$row = sql_fetsel("U.url, U.date, U.id_parent, U.perma, $champ_titre $sel_parent",
		"$table AS O LEFT JOIN spip_urls AS U ON (U.type='$type' AND U.id_objet=O.$col_id)",
		"O.$col_id=$id_objet",
		'',
		$order_by_parent . 'U.perma DESC, U.date DESC', 1);
	if ($row) {
		$urls[$type][$id_objet] = $row;
		$urls[$type][$id_objet]['type_parent'] = $champ_parent ? end($champ_parent) : '';
	}

	return isset($urls[$type][$id_objet]) ? $urls[$type][$id_objet] : null;
}

/**
 * Retrouver/Calculer l'ensemble des segments d'url d'un objet
 *
 * @param string $type
 * @param int $id_objet
 * @param array $contexte
 *   id_parent : rubrique parent
 * @return string
 */
function declarer_url_arbopoly($type, $id_objet, $contexte = array()) {
	static $urls = array();
	// utiliser un cache memoire pour aller plus vite
	if (!is_null($C = Cache())) {
		return $C;
	}
	ksort($contexte);
	$hash = json_encode($contexte);

	// Se contenter de cette URL si elle existe ;
	// sauf si on invoque par "voir en ligne" avec droit de modifier l'url

	// l'autorisation est verifiee apres avoir calcule la nouvelle url propre
	// car si elle ne change pas, cela ne sert a rien de verifier les autorisations
	// qui requetent en base
	$modifier_url = (defined('_VAR_URLS') and _VAR_URLS);

	if (!isset($urls[$type][$id_objet][$hash]) or $modifier_url) {
		$r = renseigner_url_arbopoly($type, $id_objet, $contexte);
		// Quand $type ne reference pas une table
		if ($r === false) {
			return false;
		}

		if (!is_null($r)) {
			$urls[$type][$id_objet][$hash] = $r;
		}
	}

	if (!isset($urls[$type][$id_objet][$hash])) {
		return "";
	} # objet inexistant

	$u = &$urls[$type][$id_objet][$hash];
	$url_propre = $u['url'];

	// si on a trouve l'url
	// et que le parent est bon
	// et (permanente ou pas de demande de modif)
	if (!is_null($url_propre)
		and $u['id_parent'] == $u['parent']
		and ($u['perma'] or !$modifier_url)
	) {
		return declarer_url_arbo_rec($url_propre, $type,
			isset($u['parent']) ? $u['parent'] : 0,
			isset($u['type_parent']) ? $u['type_parent'] : null);
	}

	// Si URL inconnue ou maj forcee sur une url non permanente, recreer une url
	$url = $url_propre;
	if (is_null($url_propre) or ($modifier_url and !$u['perma'])) {
		$url = pipeline('arbo_creer_chaine_url',
			array(
				'data' => $url_propre,  // le vieux url_propre
				'objet' => array_merge($u, array('type' => $type, 'id_objet' => $id_objet)
				)
			)
		);

		// Eviter de tamponner les URLs a l'ancienne (cas d'un article
		// intitule "auteur2")
		include_spip('inc/urls');
		$objets = urls_liste_objets();
		if (preg_match(',^(' . $objets . ')[0-9]*$,', $url, $r)
			and $r[1] != $type
		) {
			$url = $url . _url_arbo_sep_id . $id_objet;
		}
	}


	// Pas de changement d'url ni de parent
	if ($url == $url_propre
		and $u['id_parent'] == $u['parent']
	) {
		return declarer_url_arbo_rec($url_propre, $type, $u['parent'], $u['type_parent']);
	}

	// verifier l'autorisation, maintenant qu'on est sur qu'on va agir
	if ($modifier_url) {
		include_spip('inc/autoriser');
		$modifier_url = autoriser('modifierurl', $type, $id_objet);
	}
	// Verifier si l'utilisateur veut effectivement changer l'URL
	if ($modifier_url
		and CONFIRMER_MODIFIER_URL
		and $url_propre
		// on essaye pas de regenerer une url en -xxx (suffixe id anti collision)
		and $url != preg_replace('/' . preg_quote(_url_propres_sep_id, '/') . '.*/', '', $url_propre)
	) {
		$confirmer = true;
	} else {
		$confirmer = false;
	}

	if ($confirmer and !_request('ok')) {
		die("vous changez d'url ? $url_propre -&gt; $url");
	}

	$set = array(
		'url' => $url,
		'type' => $type,
		'id_objet' => $id_objet,
		'id_parent' => $u['parent'],
		'perma' => intval($u['perma'])
	);
	include_spip('action/editer_url');
	if (url_insert($set, $confirmer, _url_arbo_sep_id)) {
		$u['url'] = $set['url'];
		$u['id_parent'] = $set['id_parent'];
	} else {
		// l'insertion a echoue,
		//serveur out ? retourner au mieux
		$u['url'] = $url_propre;
	}

	return declarer_url_arbo_rec($u['url'], $type, $u['parent'], $u['type_parent']);
}

/**
 * Generer l'url arbo complete constituee des segments + debut + fin
 *
 * @param string $type
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @return string
 */
function _generer_url_arbopoly($type, $id, $args = '', $ancre = '') {

	if ($generer_url_externe = charger_fonction("generer_url_$type", 'urls', true)) {
		$url = $generer_url_externe($id, $args, $ancre);
		if (null != $url) {
			return $url;
		}
	}

	// Mode propre
	$c = array();
	$propre = declarer_url_arbopoly($type, $id, $c);
	parse_str($args, $contexte);
	$champ_parent = url_arbo_parent($type);
	if ($champ_parent
	  and $champ_parent = reset($champ_parent)
	  and isset($contexte[$champ_parent]) and $contexte[$champ_parent]) {
		$c['id_parent'] = $contexte[$champ_parent];
		$propre_contexte = declarer_url_arbopoly($type, $id, $c);
		if ($propre_contexte !== $propre) {
			$propre = $propre_contexte;
			unset($contexte[$champ_parent]);
			$args = http_build_query($contexte);
		}
	}


	if ($propre === false) {
		return '';
	} // objet inconnu. raccourci ?

	if ($propre) {
		$url = _debut_urls_arbo
			. rtrim($propre, '/')
			. url_arbo_terminaison($type);
	} else {

		// objet connu mais sans possibilite d'URL lisible, revenir au defaut
		include_spip('base/connect_sql');
		$id_type = id_table_objet($type);
		$url = get_spip_script('./') . "?" . _SPIP_PAGE . "=$type&$id_type=$id";
	}

	// Ajouter les args
	if ($args) {
		$url .= ((strpos($url, '?') === false) ? '?' : '&') . $args;
	}

	// Ajouter l'ancre
	if ($ancre) {
		$url .= "#$ancre";
	}

	return _DIR_RACINE . $url;
}


/**
 * API : retourner l'url d'un objet si i est numerique
 * ou decoder cette url si c'est une chaine
 * array([contexte],[type],[url_redirect],[fond]) : url decodee
 *
 * https://code.spip.net/@urls_arbo_dist
 *
 * @param string|int $i
 * @param string $entite
 * @param string|array $args
 * @param string $ancre
 * @return array|string
 */
function urls_arbopoly_dist($i, $entite, $args = '', $ancre = '') {
	if (is_numeric($i)) {
		return _generer_url_arbopoly($entite, $i, $args, $ancre);
	}

	// traiter les injections du type domaine.org/spip.php/cestnimportequoi/ou/encore/plus/rubrique23
	if ($GLOBALS['profondeur_url'] > 0 and $entite == 'sommaire') {
		$entite = 'type_urls';
	}

	// recuperer les &debut_xx;
	if (is_array($args)) {
		$contexte = $args;
	} else {
		parse_str($args, $contexte);
	}

	$url = $i;
	$id_objet = $type = 0;
	$url_redirect = null;

	// Migration depuis anciennes URLs ?
	// traiter les injections domain.tld/spip.php/n/importe/quoi/rubrique23
	if ($GLOBALS['profondeur_url'] <= 0
		and $_SERVER['REQUEST_METHOD'] != 'POST'
	) {
		include_spip('inc/urls');
		$r = nettoyer_url_page($i, $contexte);
		if ($r) {
			list($contexte, $type, , , $suite) = $r;
			$_id = id_table_objet($type);
			$id_objet = $contexte[$_id];
			$url_propre = generer_url_entite($id_objet, $type);
			if (strlen($url_propre)
				and !strstr($url, $url_propre)
			) {
				list(, $hash) = array_pad(explode('#', $url_propre), 2, null);
				$args = array();
				foreach (array_filter(explode('&', $suite)) as $fragment) {
					if ($fragment != "$_id=$id_objet") {
						$args[] = $fragment;
					}
				}
				$url_redirect = generer_url_entite($id_objet, $type, join('&', array_filter($args)), $hash);

				return array($contexte, $type, $url_redirect, $type);
			}
		}
	}
	/* Fin compatibilite anciennes urls */

	// Chercher les valeurs d'environnement qui indiquent l'url-propre
	$url_propre = preg_replace(',[?].*,', '', $url);

	// Mode Query-String ?
	if (!$url_propre
		and preg_match(',[?]([^=/?&]+)(&.*)?$,', $url, $r)
	) {
		$url_propre = $r[1];
	}

	if (!$url_propre
		or $url_propre == _DIR_RESTREINT_ABS
		or $url_propre == _SPIP_SCRIPT
	) {
		return;
	} // qu'est-ce qu'il veut ???


	include_spip('base/abstract_sql'); // chercher dans la table des URLS

	// Revenir en utf-8 si encodage type %D8%A7 (farsi)
	$url_propre = rawurldecode($url_propre);

	// Compatibilite avec .htm/.html et autres terminaisons
	$t = array_diff(array_unique(array_merge(array('.html', '.htm', '/'), url_arbo_terminaison(''))), array(''));
	if (count($t)) {
		$url_propre = preg_replace('{('
			. implode('|', array_map('preg_quote', $t)) . ')$}i', '', $url_propre);
	}

	if (strlen($url_propre) and !preg_match(',^[^/]*[.]php,', $url_propre)) {
		$parents_vus = array();

		// recuperer tous les objets de larbo xxx/article/yyy/mot/zzzz
		// on parcourt les segments de gauche a droite
		// pour pouvoir contextualiser un segment par son parent
		$url_arbo = explode('/', $url_propre);
		$url_arbo_new = array();
		$dernier_parent_vu = false;
		$objet_segments = 0;
		while (count($url_arbo) > 0) {
			$type = null;
			if (count($url_arbo) > 1) {
				$type = array_shift($url_arbo);
			}
			$url_segment = array_shift($url_arbo);
			// Rechercher le segment de candidat
			// si on est dans un contexte de parent, donne par le segment precedent,
			// prefixer le segment recherche avec ce contexte
			$cp = "0"; // par defaut : parent racine, id=0
			if ($dernier_parent_vu) {
				$cp = $parents_vus[$dernier_parent_vu];
			}
			// d'abord recherche avec prefixe parent, en une requete car aucun risque de colision
			$row = sql_fetsel('id_objet, type, url',
				'spip_urls',
				is_null($type)
					? "url=" . sql_quote($url_segment, '', 'TEXT')
					: sql_in('url', array("$type/$url_segment", $type)),
				'',
				// en priorite celui qui a le bon parent et les deux segments
				// puis le bon parent avec 1 segment
				// puis un parent indefini (le 0 de preference) et les deux segments
				// puis un parent indefini (le 0 de preference) et 1 segment
				(intval($cp) ? "id_parent=" . intval($cp) . " DESC, " : "id_parent>=0 DESC, ") . "segments DESC, id_parent"
			);
			if ($row) {
				if (!is_null($type) and $row['url'] == $type) {
					array_unshift($url_arbo, $url_segment);
					$url_segment = $type;
					$type = null;
				}
				$type = $row['type'];
				$col_id = id_table_objet($type);

				// le plus a droite l'emporte pour des objets presents plusieurs fois dans l'url (ie rubrique)
				$contexte[$col_id] = $row['id_objet'];

				$type_parent = '';
				if ($p = url_arbo_parent($type)) {
					$type_parent = end($p);
				}
				// l'entite la plus a droite l'emporte, si le type de son parent a ete vu
				// sinon c'est un segment contextuel supplementaire a ignorer
				// ex : rub1/article/art1/mot1 : il faut ignorer le mot1, la vrai url est celle de l'article
				if (!$entite
					or $dernier_parent_vu == $type_parent
				) {
					if ($objet_segments == 0) {
						$entite = $type;
					}
				} // sinon on change d'objet concerne
				else {
					$objet_segments++;
				}

				$url_arbo_new[$objet_segments]['id_objet'] = $row['id_objet'];
				$url_arbo_new[$objet_segments]['objet'] = $type;
				$url_arbo_new[$objet_segments]['segment'][] = $row['url'];

				// on note le dernier parent vu de chaque type
				$parents_vus[$dernier_parent_vu = $type] = $row['id_objet'];
			} else {
				// un segment est inconnu
				if ($entite == '' or $entite == 'type_urls') {
					// on genere une 404 comme il faut si on ne sait pas ou aller
					return array(array(), '404');
				}
				// ici on a bien reconnu un segment en amont, mais le segment en cours est inconnu
				// on pourrait renvoyer sur le dernier segment identifie
				// mais de fait l'url entiere est inconnu : 404 aussi
				// mais conserver le contexte qui peut contenir un fond d'ou venait peut etre $entite (reecriture urls)
				return array($contexte, '404');
			}
		}

		if (count($url_arbo_new)) {
			$caller = debug_backtrace();
			$caller = $caller[1]['function'];
			// si on est appele par un autre module d'url c'est du decodage d'une ancienne URL
			// ne pas regenerer des segments arbo, mais rediriger vers la nouvelle URL
			// dans la nouvelle forme
			if (strncmp($caller, "urls_", 5) == 0 and $caller !== "urls_decoder_url") {
				// en absolue, car assembler ne gere pas ce cas particulier
				include_spip('inc/filtres_mini');
				$col_id = id_table_objet($entite);
				$url_new = generer_url_entite($contexte[$col_id], $entite);
				// securite contre redirection infinie
				if ($url_new !== $url_propre
					and rtrim($url_new, "/") !== rtrim($url_propre, "/")
				) {
					$url_redirect = url_absolue($url_new);
				}
			} else {
				foreach ($url_arbo_new as $k => $o) {
					$c = array();
					if (isset($parents_vus['rubrique'])) {
						$c['id_parent'] = $parents_vus['rubrique'];
					}
					if ($s = declarer_url_arbopoly($o['objet'], $o['id_objet'], $c)) {
						$url_arbo_new[$k] = $s;
					} else {
						$url_arbo_new[$k] = implode('/', $o['segment']);
					}
				}
				$url_arbo_new = ltrim(implode('/', $url_arbo_new), '/');

				if ($url_arbo_new !== $url_propre) {
					$url_redirect = $url_arbo_new;
					// en absolue, car assembler ne gere pas ce cas particulier
					include_spip('inc/filtres_mini');
					$url_redirect = url_absolue($url_redirect);
				}
			}
		}

		// gerer le retour depuis des urls propres
		if (($entite == '' or $entite == 'type_urls')
			and $GLOBALS['profondeur_url'] <= 0
		) {
			$urls_anciennes = charger_fonction('propres', 'urls');

			return $urls_anciennes($url_propre, $entite, $contexte);
		}
	}
	if ($entite == '' or $entite == 'type_urls' /* compat .htaccess 2.0 */) {
		if ($type) {
			$entite = objet_type($type);
		} else {
			// Si ca ressemble a une URL d'objet, ce n'est pas la home
			// et on provoque un 404
			if (preg_match(',^[^\.]+(\.html)?$,', $url)) {
				$entite = '404';
				$contexte['erreur'] = ''; // qu'afficher ici ?  l'url n'existe pas... on ne sait plus dire de quel type d'objet il s'agit
			}
		}
	}
	define('_SET_HTML_BASE', 1);

	return array($contexte, $entite, $url_redirect, null);
}
