<?php
/**
 * Fonctions utiles au plugin Centre image
 *
 * @plugin     Centre image
 * @copyright  2015
 * @author     ARNO*
 * @licence    GNU/GPL
 * @package    SPIP\Centre_image\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Retourne le positionnement à appliquer lors de la découpe d'une image
 *
 * @uses centre_image_x()
 * @uses centre_image_y()
 *
 * @param string $img
 *     Chemin de l'image ou balise html `<img src=... />`.
 * @param int $width
 *     Largeur finale de l'image
 * @param int $height
 *     Hauteur finale de l'image
 * @return string
 *     Positionnement tel qu'attendu par image_recadre, par exemple `top=50 left=12`
**/
function inc_image_positionner_par_focus_center_dist($img, $width, $height, $zoom = 1) {
	$positionner = charger_fonction("image_positionner_par_focus", "inc");
	return $positionner($img, $width, $height, "focus-center", $zoom);
}

