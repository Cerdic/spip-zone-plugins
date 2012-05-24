<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

// from spip 1.9.2.c

if (!defined("_ECRIRE_INC_VERSION")) return;

// similar to http://doc.spip.org/@exec_editer_auteurs_dist
//
function exec_acs_editer_admins() {
	$type = _request('type');
	if (!preg_match(',^[a-z]+$,',$type)) // securite et a defaut on assure le fonctionnement pour acsadmins
		$type = 'acsadmins';

	$id = intval(_request("id_$type"));
	if (! autoriser('modifier',$type,$id)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	$script = urldecode(_request('script')).(_request('onglet') ? '&onglet='.urldecode(_request('onglet')) : '');
	$titre = ($titre=_request('titre'))?urldecode($titre):$titre;

  acs_log("exec_acs_editer_admins id_$type=".$id.' script='.$script);

	$editer_admins = charger_fonction('acs_editer_admins', 'inc');
	ajax_retour($editer_admins($type, $id, 'ajax', _request('cherche_auteur'), _request('ids'),$titre,$script));
}
?>