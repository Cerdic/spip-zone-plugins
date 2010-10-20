<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */


if ($z = _request('var_zajax')) {
	incude_spip('public/styliser_par_z');
	if ($z_blocs = zcore_blocs(test_espace_prive())
	  AND in_array($z,$z_blocs)) {
		$GLOBALS['marqueur'] .= "$z:";
		$GLOBALS['flag_preserver'] = true;
	}
	else
		set_request('var_zajax'); // enlever cette demande incongrue
}

?>