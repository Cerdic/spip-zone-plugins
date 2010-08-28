<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_groupe_auteur_supprimer_dist() {
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'nom')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}


	supprimer_groupe_func(_request('id_groupe'), _request('id_auteur'));
	if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
		supprimer_groupe_zone_func(_request('id_groupe'), _request('id_auteur'));
	}
}


function supprimer_groupe_func($id_groupe, $id_auteur) {
	spip_log("supprimer_groupe_func($id_groupe, $id_auteur)", 'groupes');
	if (autoriser('voir', 'nom')){
		include_spip('base/abstract_sql');
		sql_delete('spip_groupes_auteurs', 'id_groupe='.$id_groupe.' AND id_auteur='.$id_auteur);
	}
}

function supprimer_groupe_zone_func($id_groupe, $id_auteur) {
	include_spip('base/abstract_sql');
	$result = sql_select('id_zone', 'spip_groupes_zones', 'id_groupe='.$id_groupe);
	
	while($r = sql_fetch($result)) {
		sql_delete('spip_zones_auteurs', 'id_auteur='.$id_auteur.' AND id_zone='.$r['id_zone']);
	}
}
?>