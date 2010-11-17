<?php

// Declaration du pipeline permettant d'ajouter des fichiers a la sauvegarde mes_fichiers
$GLOBALS['spip_pipeline']['mes_fichiers_a_sauver'] = '';

if(!isset($GLOBALS['spip_pipeline']['post_sauvegarde'])){
	$GLOBALS['spip_pipeline']['post_sauvegarde'] = '';
}

// Repertoire de stockage des archives creees
define('_DIR_MES_FICHIERS', _DIR_TMP . 'mes_fichiers/');

?>
