<?php
/**
 * Plugin Adminer pour Spip
 * Licence GPL 3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_adminer_dist(){

	$redir = _DIR_PLUGIN_ADMINER."index.php";
	include_spip("inc/headers");
	redirige_par_entete($redir);

}