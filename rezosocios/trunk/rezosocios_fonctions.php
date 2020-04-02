<?php
/**
 * Fonctions du plugin Rezosocios
 *
 * @plugin     Rezosocios
 * @copyright  2015
 * @author     kent1
 * @licence    GPL 3
 * @package    SPIP\Rezosocios\Fonctions
 */

 // Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Renvoie le nom d'un type de réseau social
 *
 * @param string $type
 *     Type : facebook, twitter, etc.
 * @return string|null
 */
function rezosocios_nom($type) {
	include_spip('inc/rezosocios');

	$rezosocios = rezosocios_liste();

	if (isset($rezosocios[$type])) {
		$nom = $rezosocios[$type]['nom'];
	}

	return $nom;
}

/**
 * Renvoie l'URL d'après le type et un identifiant de compte
 *
 * @param string $type
 *     Type : facebook, twitter, etc.
 * @param string $compte
 *     Identifiant du compte
 * @return string|boolean
 */
function rezosocios_url($type, $compte) {
	include_spip('inc/rezosocios');

	$rezosocios = rezosocios_liste();

	// Si c'est direct une URL, on prend telle quelle
	if (substr($compte, 0, 4) === 'http') {
		$url = $compte;
	// Sinon c'est juste l'identifiant du compte, on ajoute l'URL de base
	} elseif (!empty($rezosocios[$type]['url'])) {
		$url = $rezosocios[$type]['url'] . $compte;
	} else {
		$url = false;
	}

	return $url;
}

/**
 * Renvoie la bonne classe socicon pour un type de réseau social
 *
 * @param string $type
 *     Type : facebook, twitter, etc.
 * @return string
 */
function rezosicos_classe_socicon($type) {
	$classe = $type;
	$exceptions = array(
		'youtube_channel'  => 'youtube',
		'linkedin_company' => 'linkedin',
		'twitter_hashtag'  => 'twitter',
	);
	if (!empty($exceptions[$type])) {
		$classe = $exceptions[$type];
	}
	return $classe;
}
