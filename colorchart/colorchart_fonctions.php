<?php

function couleurs_extraire($src, $a) {
	for ($i = 1; $i <= $a; $i++)
	for ($j = 1; $j <= $a; $j++) {
		$e = extraire_image_couleur($src, $j*floor(20/($a+1)), $i*floor(20/($a+1)));
		$b[] = couleur_web($e);
	}

	return join('-', $b);
}

function pixellate($img, $a=4) {
	include_spip('images_fonctions');
	include_spip('inc/filtres_images');

	$src = extraire_attribut($img, 'src');

	$k = couleurs_extraire($src, $a);
	$r = "<table>";

	foreach (explode('-', $k) as $i => $c) {
		$c = str_replace(':', 'a', preg_replace(',(.).,e', 'chr(2*ceil(ord("\1")/2))', $c));
		if ($i%$a == 0) $r .= "<tr>";
		$r .= "<td style='background:#$c;' title='$c' /><b> $c </b>";
	}
	$r .= "</table>\n";

	return $r;
}

?>
