<?php
include_spip("inc/filtres_images");
if (!function_exists("image_recadre")){
	eval('
function image_recadre($im,$width,$height,$position="center")
{
	$image = image_valeurs_trans($im, "recadre-$width-$height-$position");
	if (!$image) return("");
	
	$x_i = $image["largeur"];
	$y_i = $image["hauteur"];
	
	if ($width==0 OR $width>$x_i) $width==$x_i;
	if ($height==0 OR $height>$y_i) $height==$y_i;
	
	$offset_width = $x_i-$width;
	$offset_height = $y_i-$height;
	$position=strtolower($position);
	if (strpos($position,"left")!==FALSE)
		$offset_width=0;
	elseif (strpos($position,"right")!==FALSE)
		$offset_width=$offset_width;
	else
		$offset_width=intval(ceil($offset_width/2));

	if (strpos($position,"top")!==FALSE)
		$offset_height=0;
	elseif (strpos($position,"bottom")!==FALSE)
		$offset_height=$offset_height;
	else
		$offset_height=intval(ceil($offset_height/2));
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	
	$creer = $image["creer"];
	
	if ($creer) {
		$im = $image["fonction_imagecreatefrom"]($im);
		$im_ = imagecreatetruecolor($width, $height);
		@imagealphablending($im_, false);
		@imagesavealpha($im_,true);
	
		$color_t = ImageColorAllocateAlpha( $im_, 255, 255, 255 , 127 );
		imagefill ($im_, 0, 0, $color_t);
		imagecopy($im_, $im, 0, 0, $offset_width, $offset_height, $width, $height);

		$image["fonction_image"]($im_, "$dest");
		imagedestroy($im_);
		imagedestroy($im);
	}
	
	$class = $image["class"];
	if (strlen($class) > 1) $tags=" class=\'$class\'";
	$tags = "$tags alt=\'".$image["alt"]."\'";
	$style = $image["style"];
	if (strlen($style) > 1) $tags="$tags style=\'$style\'";
	
	return "<img src=\'$dest\'$tags />";
}	
');
}

?>