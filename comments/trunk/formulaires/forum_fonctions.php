<?php

include_spip('inc/filtres');
if (!function_exists('barre_typo')){
	function barre_typo(){return '';}
}

ecrire_meta('forums_titre','non'); // forcer l'absence de titre sur les forums

?>
