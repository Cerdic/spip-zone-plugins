<?php

function qrcode($texte,$taille=false,$ecc=false) {
	$taille || ( $taille = lire_config('qrcode/taille') ) || ( $taille = 1 ) ;
	$ecc || ( $ecc = lire_config('qrcode/ecc') ) || ( $ecc = 'L' ) ;
	if ($class = lire_config('qrcode/css')) { $class = ' class="'.$class.'"' ; }
	if ($style = lire_config('qrcode/style')) { $style = ' style="'.$style.'"' ; }
	return "<img$class$style src=\""._DIR_RACINE."?page=qrcode&data=".urlencode($texte)."&size=$taille&level=$ecc\" alt=\"qrcode:$texte\" title=\""._T('qrcode:aide')."\"/>" ;
}

?>
