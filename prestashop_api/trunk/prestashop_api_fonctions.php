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
 * @example
 *     ```
 *     #URL_PRESTASHOP
 *     #URL_PRESTASHOP{product,51}
 *     #URL_PRESTASHOP{category,3}
 *     #URL_PRESTASHOP{product,51,en}
 *     #URL_PRESTASHOP{'','',en}
 *     ```
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
	$p->code="calculer_url_prestashop($type, $id, $lang)";
	return $p;
}

/**
 * Retourne une URL pour Prestashop.
 *
 * @param string $type
 *     Type d'objet de prestashop
 * @param int $id
 *     Identifiant d'objet de prestashop
 * @param string $lang
 *     Langue désirée.
 * @param string $lang
 */
function calculer_url_prestashop($type = '', $id = '', $lang = '') {
	$url_prestashop = rtrim(prestashop_ws_list_shops_by_lang($lang), '/');
	if (!$type) {
		return $url_prestashop;
	}
	$url = $url_prestashop . '/index.php';
	$url = parametre_url($url, 'controller', $type, '&');
	if ($id) {
		$url = parametre_url($url, 'id_' . $type, $id, '&');
	}

	// Pour éviter des cURL à tout bout de champ… on cache
	if ($W = cache_get($key = 'url_presta_' . $url)) {
		return $W;
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
	$url = calculer_url_prestashop() . '/img/';
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