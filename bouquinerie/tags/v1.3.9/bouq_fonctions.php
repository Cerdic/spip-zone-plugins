<?php
/**
 * Fonctions utiles au plugin Bouquinerie
 *
 * @plugin     Bouquinerie
 * @copyright  2017
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Bouquinerie\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Afficher le prix avec une virgule plutôt qu'un point
 *
 * @param float $prix
 *     Saisie à protéger
 * @return string
 **/

function bouq_prix_virgule($prix) {
	$prix = str_replace('.', ',', $prix);
	return $prix;
}
