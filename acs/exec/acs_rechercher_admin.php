<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt
#
# Recherche un admin en ajax

if (!defined("_ECRIRE_INC_VERSION")) return;

# gerer un charset minimaliste en convertissant tout en unicode &#xxx;

// http://doc.spip.org/@exec_rechercher_auteur_dist
function exec_acs_rechercher_admin_dist()
{
	$idom = _request('idom');
	if (!preg_match('/\w+/',$idom)) {
		include_spip('minipres');
		echo minipres();
		exit;
  }

	$where = split("[[:space:]]+", _request('nom'));
	if ($where) {
		foreach ($where as $k => $v)
			$where[$k] = "'%" . substr(str_replace("%","\%", _q($v)),1,-1) . "%'";
		$where= ("(nom LIKE " . join(" AND nom LIKE ", $where) . ")");
	}

	$admid = _request('admid');
	acs_log("exec_acs_rechercher_admin("._request('nom').", $idom, $admid)");
	
	$q = spip_query("SELECT * FROM spip_auteurs WHERE $where AND statut='0minirezo' ORDER BY nom");
	include_spip('inc/acs_selectionner_admin');
	ajax_retour(selectionner_admin_boucle($q, $idom, $admid));
}
?>
