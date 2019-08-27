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
 * @note
 *     Code repris de image_proportions du plugin Image Responsive
 * 
 * @param string $img
 *     Chemin de l'image ou balise html `<img src=... />`.
 * @param int $largeur
 *     Largeur finale de l'image
 * @param int $hauteur
 *     Hauteur finale de l'image
 * @param int $zoom
 *     ?
 * @return string
 *     Positionnement tel qu'attendu par image_recadre, par exemple `top=50 left=12`
**/
function inc_image_positionner_par_focus_dist($img, $largeur, $hauteur, $type = "focus", $zoom = 1) {

	if (!$img) {
		return "";
	}

	// Avec "focus", point d'intérêt reste décentré
	// Avec "focus-center", point d'intérêt aussi centré que possible
	if (!in_array($type, array("focus", "focus-center"))) {
		return "";
	}

	$l_img = largeur($img);
	$h_img = hauteur($img);

	if ($largeur == 0 OR $hauteur == 0) {
		$largeur = $l_img;
		$hauteur = $h_img;
	}

	if ($l_img == 0 OR $h_img == 0) {
		return "";
	}

	$r_img = $h_img / $l_img;
	$r = $hauteur / $largeur;

	if ($r_img < $r) {
		$l_dest = $h_img/$r;
		$h_dest = $h_img;
	} else if ($r_img > $r) {
		$l_dest = $l_img;
		$h_dest = $l_img*$r;
	}

	$res = centre_image($img);
	$dx = $res['x'];
	$dy = $res['y'];

	if ($r_img > $r) {
		$h_dest = round(($l_img * $r) / $zoom);
		$l_dest = round($l_img / $zoom);
	} else {
		$h_dest = round($h_img / $zoom);
		$l_dest = round(($h_img / $r) / $zoom);
	}

	$h_centre = $h_img * $dy;
	$l_centre = $l_img * $dx;

	if ($type == "focus-center") {
		$top  = round($h_centre - ($h_dest*0.5));
		$left = round($l_centre - ($l_dest*0.5));
	} else {
		// ici on n'applique pas *$dy directement, car effet exagéré, alors on pondère 
		$top  = round($h_centre - ($h_dest*((2*$dy+0.5)/3)));
		$left = round($l_centre - ($l_dest*((2*$dx+0.5)/3)));
	}

	if ($top < 0) {
		$top = 0;
	}
	if ($top + $h_dest > $h_img ) {
		$top = $h_img - $h_dest;
	}
	if ($left < 0) {
		$left = 0;
	}
	if ($left + $l_dest > $l_img ) {
		$left = $l_img - $l_dest;
	}

	// echo "<li>$dx x $dy - $l_img x $h_img - $l_dest x $h_dest - $l_centre x $h_centre - $left x $top</li>"; 
	$align = "top=$top, left=$left";

	return $align;
}

