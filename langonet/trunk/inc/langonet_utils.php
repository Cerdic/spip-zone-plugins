<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Conversion d'un texte en utf-8
 *
 * @param string	$sujet
 * @return string
 */
function entite2utf($sujet) {
	if (!$sujet OR !is_string($sujet)) return;
	include_spip('inc/charsets');

	return unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $sujet), ENT_NOQUOTES, 'utf-8'));
}


//
/**
 * Calcul du représentant canonique d'une chaine de langue (_L ou <: :>).
 * C'est un transcodage ASCII, reduit aux 32 premiers caractères,
 * les caractères non alphabétiques étant remplacés par un souligné.
 * On élimine les répétitions de mots pour évacuer le cas fréquent truc: @truc@.
 * Si le résultat a plus que 32 caractères, on élimine les mots de moins de 3 lettres.
 * Si cela demeure toujours trop, on coupe au dernier mot complet avant 32 caractères.
 *
 * @param string	$occurrence
 * @return string
 */
function langonet_index_brut($occurrence) {
	$index = textebrut($occurrence);
	$index = preg_replace('/\\\\[nt]/', ' ', $index);
	$index = strtolower(translitteration($index));
	$index = trim(preg_replace('/\W+/', ' ', $index));
	$index = preg_replace('/\b(\w+)\W+\1/', '\1', $index);
	if (strlen($index) > 32) {
	  // trop long: abandonner les petits mots
		$index = preg_replace('/\b\w{1,3}\W/', '', $index);
		if (strlen($index) > 32) {
			// tant pis mais couper proprement si possible
			$index = substr($index, 0, 32);
			if ($n = strrpos($index,' ') OR ($n = strrpos($index,'_')))
				$index = substr($index, 0, $n);
		}
	}
	$index = str_replace(' ', '_', trim($index));

	return $index;
}


/**
 * Calcul du représentation canonique d'une chaine de langue à créer avec traitement d'homonynie.
 * En cas d'homonynmie, le représentant utilisé est le md5.
 *
 * @param string	$occurrence
 * @param array		$item_md5
 * @return string
 */
function langonet_index($occurrence, $item_md5) {
	// Calcul du raccourci brut de l'item de langue
	$index = langonet_index_brut($occurrence);

	// Si cet item existe déjà mais que la chaine diffère par des majuscules, on considère qu'on a à faire
	// au même item. Sinon c'est que le calcul précédent a donné lieu à une collision inattendue de deux items différents :
	// on prend alors son md5 mais qui produira un raccourci illisible
	if (isset($item_md5[$index])) {
		if (strcasecmp($item_md5[$index], $occurrence) != 0)
			$index = md5($occurrence);
	}

	return $index;
}

?>