<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */


function zcore_blocs($espace_prive=false) {
	if ($espace_prive)
		return (isset($GLOBALS['z_blocs_ecrire'])?$GLOBALS['z_blocs_ecrire']:array('contenu','navigation','extra','head','hierarchie','top'));
	return (isset($GLOBALS['z_blocs'])?$GLOBALS['z_blocs']:array('contenu','navigation','extra','head','head_js'));
}

if ($z = _request('var_zajax')) {
	if ($z_blocs = zcore_blocs(test_espace_prive())
	  AND in_array($z,$z_blocs)) {
		$GLOBALS['marqueur'] .= "$z:";
		$GLOBALS['flag_preserver'] = true;
	}
	else
		set_request('var_zajax'); // enlever cette demande incongrue
}

?>