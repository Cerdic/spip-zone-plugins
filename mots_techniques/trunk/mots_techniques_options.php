<?php

/**
 * Actions effectuées à chaque hit
 * 
 * @package Mots_Techniques
**/

// les mots techniques ne sont filtrés que dans l'espace public
// on demande des caches différents pour l'un et l'autre
if (!isset($GLOBALS['marqueur_skel'])) {
	$GLOBALS['marqueur_skel'] = '';
}
$GLOBALS['marqueur_skel'] .= test_espace_prive() ? '1' : '';
?>
