<?php 

function ajouter_auteur_groupe_func($id_groupe, $id_auteur) {
	spip_log("ajouter_auteur_groupe_func($id_groupe, $id_auteur)", 'groupes');
	include_spip('base/abstract_sql');
	sql_insertq('spip_groupes_auteurs', array('id_groupe'=>$id_groupe, 'id_auteur'=>$id_auteur));
} 

function ajouter_auteur_zone_func($id_groupe, $id_auteur) {
	$result = sql_select('id_zone', 'spip_groupes_zones', 'id_groupe='.$id_groupe);
	$zones = array();
	while($r = sql_fetch($result)) {
		sql_insertq('spip_zones_auteurs', array('id_zone'=>$r['id_zone'], 'id_auteur'=>$id_auteur));
	}
}

function formulaires_auteur_ajouter_traiter_dist() {
	ajouter_auteur_groupe_func(_request('id_groupe'), _request('id_auteur'));
	if(defined('_DIR_PLUGIN_ACCESRESTREINT')) {
		ajouter_auteur_zone_func(_request('id_groupe'), _request('id_auteur'));
	}
}

function formulaires_auteur_ajouter_verifier_dist() {
	spip_log('gerer auteur verifier debut', 'groupes');
	$err = array();
	if(_request('id_groupe') == 0) {
		spip_log('gerer auteur verifier erreur', 'groupes');
		$err['id_groupe'] = "Vous devez remplir le champ";
	} 
	spip_log('gerer auteur verifier fin', 'groupes');
	return $err;
}

function formulaires_auteur_ajouter_charger_dist($nom) {
	return array('id_auteur'=>_request('id_auteur'), 'nom_auteur'=>$nom);
}
?>