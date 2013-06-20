<?php
/**
 * Plugin Adminer pour Spip
 * Licence GPL 3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_adminer_dist(){

	if (intval($GLOBALS['spip_version_branche'])<3)
		$redir = _DIR_PLUGIN_ADMINER."index.php";
	else
		$redir = url_de_base()._DIR_RESTREINT_ABS."prive.php?page=adminer";

	include_spip("inc/headers");
	redirige_par_entete($redir);
}