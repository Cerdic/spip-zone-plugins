<?php
/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package plugins
 * @subpackage odt2spip
 * @category import
 *
 *
 */

/**
 * retailler une image : (ne gère que les images GIF, JPG et PNG)
 *
 * @internal Gestion de la transparence des PNG : code de matt1walsh@gmail.com sur {@link http://fr2.php.net/manual/fr/function.imagecopyresampled.php}
 * @param string $img_ini Le fichier à retailler
 * @param int $l Largeur max de l'image finale
 * @param int $h Hauteur max
 * @return string Le message d'erreur en cas de problème
 */
function inc_odt2spip_retailler_img($img_ini, $l = 0, $h = 400){
	if (!file_exists($img_ini)) {
		return 'Le fichier ' . $img_ini . ' n\'existe pas';
	}
	// determiner le type de fonction de creation d'image a utiliser 
	$param_img = getimagesize($img_ini);
	$type_img = $param_img[2];
	switch ($type_img) {
		case 1 :
			$fct_creation_ext = 'imagecreatefromgif';
			$fct_ecrire = 'imagegif';
		break;
		case 2 :
			$fct_creation_ext = 'imagecreatefromjpeg';
			$fct_ecrire = 'imagejpeg';
		break;
		case 3 :
			$fct_creation_ext = 'imagecreatefrompng';
			$fct_ecrire = 'imagepng';
		break;
		default :
			return;
		break;
	} 
	// calculer le ratio a appliquer aux dimensions initiales
	$l_ini = $param_img[0];
	$h_ini = $param_img[1];
	$ratio = ($l != 0 ? (abs($l_ini - $l) >= abs($h_ini - $h) ? $l / $l_ini : $h / $h_ini) : $h / $h_ini);
	$img_nv = imagecreatetruecolor($l_ini * $ratio, $h_ini * $ratio); 
	$img_acopier = $fct_creation_ext($img_ini);

	// gerer la transparence pour les images PNG (le mec qui a trouve ce code est genial! :-)
	if ($type_img == 3) {
		imagecolortransparent($img_nv, imagecolorallocate($img_nv, 0, 0, 0));
		imagealphablending($img_nv, false);
		imagesavealpha($img_nv, true);
	}
	imagecopyresampled($img_nv, $img_acopier, 0, 0, 0, 0, $l_ini * $ratio, $h_ini * $ratio, $l_ini, $h_ini);					 
	// sauvegarder l'image et eventuellement detruire le fichier image initial
	$fct_ecrire($img_nv, $img_ini);
	imagedestroy($img_nv);
	imagedestroy($img_acopier);
}

?>
