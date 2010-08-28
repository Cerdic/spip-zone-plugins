<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_groupe_modifier_dist() {
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'nom')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}


	$zones = json_decode(_request('zones'));
	modifier_groupe_func(_request('nom_groupe'), _request('id_groupe'));
	if(defined('_DIR_PLUGIN_ACCESRESTREINT') && (count($zones)!=0)) {
		modifier_groupe_zone_func(_request('id_groupe'), $zones);
	}
}

function modifier_groupe_func($nom, $id) {
	include_spip('base/abstract_sql');
	if(empty($nom)) {
		echo -1;
	} else {
		sql_updateq('spip_groupes', array('nom'=>$nom), 'id_groupe='.$id);
		echo 0;
	}
}

function modifier_groupe_zone_func($id_groupe, $zones) {
	include_spip('formulaires/ajouter_groupe');
	include_spip('exec/groupe_auteur_supprimer');
	include_spip('formulaires/auteur_ajouter');
	include_spip('base/abstract_sql');
	
	sql_delete('spip_groupes_zones', 'id_groupe='.$id_groupe);
	ajouter_groupe_zones($id_groupe, $zones);
	
	
	//On selectionne les auteurs qui appartienent au groupe puis les desinscris des zones et les reinscris
	$result = sql_select('id_auteur', 'spip_groupes_auteurs', 'id_groupe='.$id_groupe);
	while($r = sql_fetch($result)) {
		supprimer_groupe_zone_func($id_groupe, $r['id_auteur']);
		ajouter_auteur_zone_func($id_groupe, $r['id_auteur']);
	}
}
?>