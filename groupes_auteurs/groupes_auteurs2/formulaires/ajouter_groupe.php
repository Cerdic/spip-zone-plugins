<?php 
function ajouter_groupe_func($nom) {
	include_spip('base/abstract_sql');
	return sql_insertq('spip_groupes', array('nom'=>$nom));		
}

function ajouter_groupe_zones($id_groupe, $zones ) {
	$zones = array_unique($zones);
	
	foreach($zones as $zone) {
		if($zone!=0) {
			sql_insertq('spip_groupes_zones', array('id_groupe'=>$id_groupe, 'id_zone'=>$zone));
		}	
	}
}

function formulaires_ajouter_groupe_traiter() {
	$id_groupe = ajouter_groupe_func(_request('nom_groupe'));
	
	if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
		if(count(_request('zones'))!=0)
			ajouter_groupe_zones($id_groupe, _request('zones'));
	}
}

function formulaires_ajouter_groupe_verifier() {
	$err = array();
	if(_request('nom_groupe') == "") {
		$err['nom_groupe'] = "Vous devez remplir le champ";
	} 
	return $err;
}
?>