<?php
function image_adaptee ($fichier, $largeur = 0, $hauteur = 0, $alt= '', $class = '', $style = '', $rel = '', $color_back = 'FFFFFF')
{
	include_spip('inc/filtres_images');

	if (!$fichier) return '';

	if ($largeur==0 && $hauteur==0)
		$src = $fichier;
	else
		$src = extraire_attribut(image_recadre(image_passe_partout($fichier,$largeur,$hauteur),$largeur,$hauteur,'center',$color_back),'src');
	
	$image_size = getimagesize($src);
	$largeur = $image_size[0];
	$hauteur = $image_size[1];

	return '<img src="'.$src.'"'.' alt="'.$alt.'"'.(($class) ? ' class="'.$class.'"': '').(($class) ? ' style="'.$style.'"': '').(($class) ? ' rel="'.$rel.'"': '').' height="'.$hauteur.'" width="'.$largeur.'" />';
}
?>