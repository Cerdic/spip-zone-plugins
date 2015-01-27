<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt


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
		
	 // la fonction version_svn_courante() etait non dans inc/filtres mais dans inc/minipres
	 // avant le changeset 9189 (05/06/2007). On fait donc un backport si c'est utile
	include_spip('inc/filtres');
	if (is_callable('version_svn_courante'))
		$r = version_svn_courante(_DIR_ACS);
	else {
		include_spip('inc/minipres');
		$r = version_svn_courante(_DIR_ACS);
	}
	return $r;
}
?>