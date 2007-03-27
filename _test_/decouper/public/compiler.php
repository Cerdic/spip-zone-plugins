<?php

include_spip('../ecrire/public/compiler');

function public_compiler($squelette, $nom, $gram, $sourcefile) {
    
	$squelette = str_replace('#TEXTE', "#TEXTE*|en_page|propre", $squelette);
	return public_compiler_dist($squelette, $nom, $gram, $sourcefile);
	
}

?>
