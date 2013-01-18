<?php
/*
 * Plugin Zpip
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

if ($z = _request('var_zajax')) {
	if ($z_blocs = isset($GLOBALS['z_blocs'])?$GLOBALS['z_blocs']:array('contenu','navigation','extra','head')
	  AND in_array($z,$z_blocs)) {
		$GLOBALS['marqueur'] .= "$z:";
		$GLOBALS['flag_preserver'] = true;
	}
	else
		set_request('var_zajax'); // enlever cette demande incongrue
}

?>