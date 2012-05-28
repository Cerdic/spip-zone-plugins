<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

if(!defined('_DIR_LIB_IMGAREASELECT'))
	define('_DIR_LIB_IMGAREASELECT',_DIR_RACINE.'lib/jquery.imgareaselect-0.9.8/');

if(!defined('_IMG_GD_QUALITE')){
	include_spip('inc/config');
	define('_IMG_GD_QUALITE', lire_config('photospip/compression_rendu',100));
}
?>