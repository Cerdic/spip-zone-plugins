<?php
/**
 * Fonctions utiles au plugin Webservice Prestashop
 *
 * @plugin     Webservice Prestashop
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Wsps\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// charger les fonctions pour le compilateur SPIP
// boucles (PRESTASHOP:PRODUCTS) ...
include_spip('public/prestashop');


/**
 * Retourne l'URL de prestashop ou d'un élément de Prestashop.
 *
 * L'URL de base du prestashop peut dépendre de la langue.
 * On peut forcer une langue en utilisant le 3è argument.
 *
 * Par défaut, on souhaite URL "propre" (réécrite), et cela
 * nécessite un appel du serveur vers le Prestashop pour déduire
 * cette jolie URL. Cette correspondance URL => URL propre est mise
 * en cache, ce qui peut poser problème avec certains éléments.
 *
 * Particulièrement pour le controlleur 'my-account', qui
 * renvoie sur l'URL de la page de connexion (même si le visiteur est
 * identifié sur Prestashop, vu que c'est le serveur qui demande l'URL).
 * Pour ces cas particuliers, on peut demander à ne pas utiliser/calculer
 * l'URL propre, et laisser l'URL d'appel du controlleur donc, en utilisant
 * l'étoile.
 *
 * @example
 *     ```
 *     #URL_PRESTASHOP
 *     #URL_PRESTASHOP{product,51}
 *     #URL_PRESTASHOP{category,3}
 *     #URL_PRESTASHOP{product,51,en}
 *     #URL_PRESTASHOP{'','',en}
 *
 *     #URL_PRESTASHOP{cart} <-- URL réécrite
 *     #URL_PRESTASHOP*{my-account} <-- URL non réécrite
 *     ```
 *
 * @uses prestashop_calculer_url_propre()
 * @uses prestashop_calculer_url_controlleur()
 *
 * @param $p
 * @return mixed
 */
function balise_URL_PRESTASHOP_dist($p) {
	if (!$type = interprete_argument_balise(1, $p)) {
		$type = "''";
	}
	if (!$id = interprete_argument_balise(2, $p)) {
		$id = "''";
	}
	if (!$lang = interprete_argument_balise(3, $p)) {
		$lang = "''";
	}
	if (!$p->etoile) {
		$p->code = "prestashop_calculer_url_propre($type, $id, $lang)";
	} else {
		$p->code = "prestashop_calculer_url_controlleur($type, $id, $lang)";
	}
	return $p;
}

/**
 * Retourne une URL appelant un controlleur de Prestashop.
 *
 * @uses prestashop_ws_list_shops_by_lang()
 *
 * @param string $type
 *     Type d'objet ou page de prestashop
 * @param int $id
 *     Identifiant d'objet de prestashop
 * @param string $lang
 *     Langue désirée.
 * @return string
 *     URL vers le controlleur, tel que `https://domaine.tld/boutique/index.php?controller=truc&param=n`
 */
function prestashop_calculer_url_controlleur($type = '', $id = '', $lang = '') {

	$url_prestashop = rtrim(prestashop_ws_list_shops_by_lang($lang), '/');
	if (!$type) {
		return $url_prestashop;
	}

	$url = $url_prestashop . '/index.php';
	$url = parametre_url($url, 'controller', $type, '&');
	if ($id) {
		$url = parametre_url($url, 'id_' . $type, $id, '&');
	}

	return $url;
}

/**
 * Retourne une URL «propre» pour Prestashop
 *
 * Tente de calculer l'URL réécrite d'un élément de prestashop,
 * en attrapant l'URL de redirection que renvoie le controlleur.
 *
 * On met en cache pour éviter de trop nombreux appels.
 *
 * @note
 *     Il faut faire attention aux redirections d'URL qui seraient différentes
 *     entre un visiteur connecté et non connecté. Le serveur appel toujours
 *     en tant que non connecté donc, et met en cache cette URL.
 *     Typiquement appeler ici la page 'my-account' pose problème par exemple.
 *
 * @uses prestashop_calculer_url_controlleur()
 * @uses prestashop_ws_cache_update()
 *
 * @param string $type
 *     Type d'objet ou page de prestashop
 * @param int $id
 *     Identifiant d'objet de prestashop
 * @param string $lang
 *     Langue désirée.
 * @return string
 *     l'URL réécrite par Prestashop, du genre `https://domaine.tld/boutique/truc-n`
 */
function prestashop_calculer_url_propre($type = '', $id = '', $lang = '') {
	static $urls = [];

	$url = prestashop_calculer_url_controlleur($type, $id, $lang);
	if (!$type) {
		return $url;
	}

	if (isset($urls[$url])) {
		return $urls[$url];
	}

	// Pour éviter des cURL à tout bout de champ… on cache
	if (cache_exists($key = 'url_presta_' . $url) and !prestashop_ws_cache_update()) {
		$_url = cache_get($key);
		return $urls[$url] = $_url;
	}

	// Calculer l'URL de redirection qu'utilise prestashop pour obtenir une belle URL.
	// http://stackoverflow.com/questions/22633395/get-product-url-using-prestashop-api
	// C'est franchement pas ce qu'on fait de mieux…
	$ch = curl_init($url);
	curl_exec($ch);
	if ($_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL)) {
		$url = $_url;
	}
	curl_close($ch);
	cache_set($key, $url);
	return $url;
}




/**
 * Retourne l'URL l'image principale d'un élément de Prestashop.
 *
 * L'URL de base du prestashop peut dépendre de la langue.
 * On peut forcer une langue en utilisant le 3è argument.
 *
 * @example
 *     ```
 *     #URL_IMAGE_PRESTASHOP{product,51}
 *     #URL_IMAGE_PRESTASHOP{category,3}
 *     #URL_IMAGE_PRESTASHOP{category,3,en}
 *     ```
 *
 * @param $p
 * @return mixed
 */
function balise_URL_IMAGE_PRESTASHOP_dist($p) {
	$type = interprete_argument_balise(1, $p);
	$id = interprete_argument_balise(2, $p);
	if (!$type OR !$id) {
		$err_b_s_a = array('zbug_balise_sans_argument', array('balise' => 'URL_IMAGE_PRESTASHOP'));
		erreur_squelette($err_b_s_a, $p);
	} else {
		if (!$lang = interprete_argument_balise(3, $p)) {
			$lang = "''";
		}
		$p->code = "prestashop_image($type, $id, $lang)";
	}
	return $p;
}


/**
 * Retourne l'URL théorique d'une image d'un objet prestashop (produit, category)
 *
 * @param int $id
 * @param string $objet
 * @param string $lang
 *     Langue désirée.
 * @return mixed
 */
function prestashop_image($objet, $id, $lang = '') {
	$url = prestashop_calculer_url_controlleur() . '/img/';
	$id = trim($id);
	$objet = trim($objet);
	switch ($objet) {
		case 'product':
		case 'products':
			$url .= 'p/' . implode('/', str_split($id)) . '/' . $id . '.jpg';
			break;

		case 'category':
		case 'categories':
			$url .= 'c/' . $id . '.jpg';
			break;

		default:
			$url = '';
			break;
	}
	#$img = '<img src="' . $url . '" alt="" />';
	return $url;
}