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

// on surcharge quelques fonctions simplement
include_spip('urls/arbo');

# donner un exemple d'url pour le formulaire de choix
define('URLS_ARBODOM_EXEMPLE', '/arbo/sans/la/racine/article/titre');
# specifier le form de config utilise pour ces urls
define('URLS_ARBODOM_CONFIG', 'arbo');


/**
 * API : retourner l'url d'un objet si i est numerique
 * ou decoder cette url si c'est une chaine
 *
 * @uses _generer_url_arbodom()
 * @uses urls_arbo_dist()
 *
 * @param string|int $i
 * @param string $entite
 * @param string|array $args
 * @param string $ancre
 * @return array|string
 */
function urls_arbodom_dist($i, $entite, $args = '', $ancre = '') {
	if (is_numeric($i)) {
		return _generer_url_arbodom($entite, $i, $args, $ancre);
	}

	// HACK pour distinguer 2 urls qui collisionnent.
	// Normalement en url arborescentes, on aurait 'fr/contact' et 'en/contact'.
	// Avec ces urls arbodom on a 'domaine.fr/contact' et 'domaine.en/contact',
	// et ici le nom 'contact' n'a du coup pas de parent associé pour retrouver à quelle URL
	// il appartient. En 3.2 on pourra peut être tenter d'utiliser le champ 'langue' de spip_urls
	// pour retrouver le parent…

	// Ici, on considère que si l'URL demandé n'a pas de / , c'est que c'est un article ou une rubrique
	// à la racine d'une rubrique de langue. On prefixe de la langue pour calculer l'URL normalement
	// par les urls arborescentes. Sauf que comme on envoie avec une url 'différente' de celle en cours,
	// les urls arbos considère que l'url envoyée n'est pas la plus à jour et demandent une redirection.
	// Il faut annuler la redirection dans ce cas.

	// Y a encore quelques points à améliorer donc… car si on objet éditorial autre que rubrique / article
	// utilise des urls arborescentes sans le type en préfixe (ie: 'toto' à la place de 'mot/toto' par exemple)
	// alors ce hack plantera.
	$contexte = $args;
	$change = false;
	$i2 = $i;
	if (is_string($contexte)) {
		parse_str($contexte, $contexte);
	}
	if (
		!empty($args['lang'])
		and $url_propre = preg_replace(',[?].*,', '', $i)
		and strpos($url_propre, '/') === false
		and !preg_match(',^.*[.]php,', $url_propre)
	) {
		$i2 = $args['lang'] . '/' . $i;
		$change = true;
	}
	$res = urls_arbo_dist($i2, $entite, $args, $ancre);
	// s'il y a une redirection vers nous-même (l'url en cours)
	// c'est que… bah c'était en fait la bonne url (sans le fr/ qu'on a ajouté)
	if ($change and !empty($res[2])) {
		$url_propre = preg_replace(',[?].*,', '', $i);
		if (url_de_base() . $url_propre == $res[2]) {
			$res[2] = null;
		}
	}
	return $res;
}



/**
 * Generer l'url arbo complete constituee des segments + debut + fin
 *
 * Surcharge de _generer_url_arbo simplement pour appeler
 * declarer_url_arbodom() à la place de declarer_url_arbo()
 *
 * @param string $type
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @return string
 */
function _generer_url_arbodom($type, $id, $args = '', $ancre = '') {

	if ($generer_url_externe = charger_fonction("generer_url_$type", 'urls', true)) {
		$url = $generer_url_externe($id, $args, $ancre);
		if (null != $url) {
			return $url;
		}
	}

	// Mode propre
	$propre = declarer_url_arbodom($type, $id);

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
 * Retrouver/Calculer l'ensemble des segments d'url d'un objet
 *
 * Surcharge de declarer_url_arbo() simplement pour appeler
 * declarer_url_arbodom_rec() à la place de declarer_url_arbo_rec()
 *
 * @param string $type
 * @param int $id_objet
 * @return string
 */
function declarer_url_arbodom($type, $id_objet) {
	static $urls = array();
	// utiliser un cache memoire pour aller plus vite
	if (!is_null($C = Cache())) {
		return $C;
	}

	// Se contenter de cette URL si elle existe ;
	// sauf si on invoque par "voir en ligne" avec droit de modifier l'url

	// l'autorisation est verifiee apres avoir calcule la nouvelle url propre
	// car si elle ne change pas, cela ne sert a rien de verifier les autorisations
	// qui requetent en base
	$modifier_url = (defined('_VAR_URLS') and _VAR_URLS);

	if (!isset($urls[$type][$id_objet]) or $modifier_url) {
		$r = renseigner_url_arbo($type, $id_objet);
		// Quand $type ne reference pas une table
		if ($r === false) {
			return false;
		}

		if (!is_null($r)) {
			$urls[$type][$id_objet] = $r;
		}
	}

	if (!isset($urls[$type][$id_objet])) {
		return "";
	} # objet inexistant

	$url_propre = $urls[$type][$id_objet]['url'];

	// si on a trouve l'url
	// et que le parent est bon
	// et (permanente ou pas de demande de modif)
	if (!is_null($url_propre)
		and $urls[$type][$id_objet]['id_parent'] == $urls[$type][$id_objet]['parent']
		and ($urls[$type][$id_objet]['perma'] or !$modifier_url)
	) {
		return declarer_url_arbodom_rec($url_propre, $type,
			isset($urls[$type][$id_objet]['parent']) ? $urls[$type][$id_objet]['parent'] : 0,
			isset($urls[$type][$id_objet]['type_parent']) ? $urls[$type][$id_objet]['type_parent'] : null);
	}

	// Si URL inconnue ou maj forcee sur une url non permanente, recreer une url
	$url = $url_propre;
	if (is_null($url_propre) or ($modifier_url and !$urls[$type][$id_objet]['perma'])) {
		$url = pipeline('arbo_creer_chaine_url',
			array(
				'data' => $url_propre,  // le vieux url_propre
				'objet' => array_merge($urls[$type][$id_objet],
					array('type' => $type, 'id_objet' => $id_objet)
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
		and $urls[$type][$id_objet]['id_parent'] == $urls[$type][$id_objet]['parent']
	) {
		return declarer_url_arbodom_rec($url_propre, $type, $urls[$type][$id_objet]['parent'],
			$urls[$type][$id_objet]['type_parent']);
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
		'id_parent' => $urls[$type][$id_objet]['parent'],
		'perma' => intval($urls[$type][$id_objet]['perma']),
	);
	include_spip('action/editer_url');
	if (url_insert($set, $confirmer, _url_arbo_sep_id)) {
		$urls[$type][$id_objet]['url'] = $set['url'];
		$urls[$type][$id_objet]['id_parent'] = $set['id_parent'];
	} else {
		// l'insertion a echoue,
		//serveur out ? retourner au mieux
		$urls[$type][$id_objet]['url'] = $url_propre;
	}

	return declarer_url_arbodom_rec($urls[$type][$id_objet]['url'], $type, $urls[$type][$id_objet]['parent'],
		$urls[$type][$id_objet]['type_parent']);
}


/**
 * Boucler sur le parent pour construire l'url complete a partir des segments
 *
 * Presque comme urls arbos, mais on n'inclut pas dans l'URL le secteur de langue.
 *
 * Surcharge de declarer_url_arbo_rec() pour gérer cette spécificité.
 *
 * @see declarer_url_arbo_rec()
 * @param string $url
 * @param string $type
 * @param string $parent
 * @param string $type_parent
 * @return string
 */
function declarer_url_arbodom_rec($url, $type, $parent, $type_parent) {
	if (is_null($parent)) {
		return $url;
	}
	// Si pas de parent ou si son URL est vide, on ne renvoit que l'URL de l'objet en court
	if ($parent == 0 or !($url_parent = declarer_url_arbodom($type_parent ? $type_parent : 'rubrique', $parent))) {
		if ($type_parent == 'rubrique' and $parent == 0) {
			return '';
		}
		return rtrim($url, '/');
	} // Sinon on renvoit l'URL de l'objet concaténée avec celle du parent
	else {
		if ($type_parent == 'rubrique' and $parent == 0) {
			return rtrim($url, '/');
		}
		return rtrim($url_parent, '/') . '/' . rtrim($url, '/');
	}
}