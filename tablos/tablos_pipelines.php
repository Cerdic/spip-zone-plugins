<?php
function tablos_insert_head($texte){
	$texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('prive/spip_style.css').'" media="all" />'."\n";
	return $texte;
}
?>