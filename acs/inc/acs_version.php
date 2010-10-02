<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2010
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
	static $r;
	if ($r)
		return $r;
	include_spip('inc/filtres');
	$r = version_svn_courante(_DIR_ACS);
	return $r;
}
?>