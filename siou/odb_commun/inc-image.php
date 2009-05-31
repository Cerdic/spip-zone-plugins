<?php
header ("Content-type: image/png");
$texte=$_GET['text'];
$x=$_GET['x'];
$y=$_GET['y'];
$taille=$_GET['taille'];

$image = imagecreate($x,$y);

$orange = imagecolorallocate($image, 255, 128, 0); // Le fond est orange (car c'est la première couleur)
$bleu = imagecolorallocate($image, 0, 0, 255);
$bleuclair = imagecolorallocate($image, 156, 227, 254);
$noir = imagecolorallocate($image, 0, 0, 0);
$blanc = imagecolorallocate($image, 255, 255, 255);

imagestringup($image, $taille, round($x/3,0), $y, $texte , $noir);
imagecolortransparent($image, $orange); // On rend le fond orange transparent
imagepng($image);
?>
