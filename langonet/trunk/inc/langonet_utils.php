<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


// Conversion d'un texte en utf-8
function entite2utf($sujet) {
	if (!$sujet) return;
	include_spip('inc/charsets');

	return unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $sujet), ENT_NOQUOTES, 'utf-8'));
}


// Calcul du representant canonique d'une chaine de langue (_L ou <: :>)
// C'est un transcodage ASCII, reduits aux 32 premiers caracteres,
// les caracteres non alphabetiques etant remplaces par un souligne.
// On elimine les repetitions de mots pour evacuer le cas frequent truc: @truc@
// Si plus que 32 caracteres, on elimine les mots de moins de 3 lettres.
// Si toujours trop, on coupe au dernier mot complet avant 32 caracteres.
// C'est donc le tableau des chaines de langues manquant.

// @param string $occ
// @return string

function langonet_index_brut($occ) {
	$index = textebrut($occ);
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
 * @param string	$occ
 * @param array		$item_md5
 * @return string
 */
function langonet_index($occurence, $item_md5) {
	// Calcul du raccourci brut de l'item de langue
	$index = langonet_index_brut($occurence);

	// Si cet item existe déjà, on prend son md5 mais qui produira un raccourci illisible
	$index_existe =  (isset($item_md5[$index]) AND strcasecmp($item_md5[$index], $occurence));
	if ($index_existe)
		md5($occurence);

	return $index;
}

?>