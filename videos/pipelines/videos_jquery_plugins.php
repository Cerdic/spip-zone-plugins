<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
	TODO Tester si insertion est activée dans CONFIG, pas la peine d'insérer des scripts sur les pages qui contiennent pas de vidéos
*/
function videos_jquery_plugins($scripts){
	$scripts[] = "lib/html5media-1.1.4/api/html5media.min.js";
    return $scripts;
}
?>