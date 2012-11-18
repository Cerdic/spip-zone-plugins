<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

if(!isset($GLOBALS['spip_pipeline']['post_sauvegarde'])){
	$GLOBALS['spip_pipeline']['post_sauvegarde'] = '';
}

// Repertoire de stockage des archives creees
if (!defined('_DIR_MES_FICHIERS')) define('_DIR_MES_FICHIERS', _DIR_TMP . 'mes_fichiers/');
define('PCLZIP_TEMPORARY_DIR', _DIR_TMP);

?>
