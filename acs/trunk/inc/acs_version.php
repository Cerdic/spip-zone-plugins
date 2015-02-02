<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

// Version - Lue dans la variable meta que spip a écrit
function acs_version() {
	static $v;
	if ($v)
		return $v;
	$v = acs_get_from_active_plugin('ACS', 'version');
	return $v;
}

// On lit la release avec la fonction SPIP
function acs_release() {
	global $spip_version_code;
	static $r;
	
	if ($r)
		return $r;
	
	include_spip('inc/filtres');
	if (is_callable('version_svn_courante')) {
		//la fonction SPIP retrourne 0 en cas de bug ou paquet fait main
		$r = version_svn_courante(_DIR_ACS);
	}
	return $r;
}
?>