<?php
/**
 * Fonctions utiles au plugin Évaluations
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Identique au filtre `thumbshot` du plugin thumbsites si présent,
 * sinon retourne vide.
 *
 * @note
 *     Filtre présent pour éviter une erreur de squelette en l'absence
 *     du plugin thubsites.
 * 
 * @param string $url
 *     URL du site dont on veut l'image distante
 * @return string
 *     Chemin de l'image ou rien
**/
function evaluations_thumbshot($url) {
	if ($thumbshot = chercher_filtre('thumbshot'))
		return $thumbshot($url);
	else
		return '';
}


?>
