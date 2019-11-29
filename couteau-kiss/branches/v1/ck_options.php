<?php
/*
 * Plugin Couteau KISS
 * (c) 2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (file_exists($f=((defined('_ROOT_CWD')?_ROOT_CWD:'')._DIR_TMP."ck_options.php")))
	include_once $f;

?>