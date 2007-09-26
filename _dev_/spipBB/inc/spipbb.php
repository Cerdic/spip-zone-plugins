<?php
// spipbb
// (c) chryjs 2007

$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
function spipbb_init_metas($id_rubrique=0) {
	spipbb_delete_metas(); // [fr] Nettoyage des traces [en] remove old metas
	unset($spipbb_meta);
	$spipbb_meta=array();
	$id_rubrique=intval($id_rubrique);
	if (empty($id_rubrique)) {
		$row = spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 ORDER by 0+titre,titre LIMIT 1")); // SELECT the first rubrique met
		$spipbb_meta['spipbb_id_rubrique']=  $row['id_rubrique'];
	}
	else $spipbb_meta['spipbb_id_rubrique']= $id_rubrique;

	$spipbb_meta['spipbb_squelette_groupeforum']= "groupeforum";
	$spipbb_meta['spipbb_squelette_filforum']= "filforum";

	// les mots cles specifiques
	$row = spip_fetch_array(spip_query("SELECT id_groupe FROM spip_groupes_mots WHERE titre='spipbb' LIMIT 1"));
	$spipbb_meta['spipbb_id_groupe_mot']=intval($row['id_groupe']);
	if (empty($spipbb_meta['spipbb_id_groupe_mot'])) $spipbb_meta = spipbb_creer_groupe_mot($spipbb_meta);
	else {
		$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='ferme' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		$spipbb_meta['spipbb_id_mot_ferme']=intval($row['id_mot']);
		$row = spip_fetch_array(spip_query("SELECT id_mot FROM spip_mots WHERE titre='annonce' AND id_groupe='".$spipbb_meta['spipbb_id_groupe_mot']."'"));
		$spipbb_meta['spipbb_id_mot_annonce']=intval($row['id_mot']);
	}

	// chemin icones et smileys ?

	// final - sauver

	if ($spipbb_meta!= $GLOBALS['meta']['spipbb']) {
		include_spip('inc/meta');
		ecrire_meta('spipbb', serialize($spipbb_meta));
		ecrire_metas();
		$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
		spip_log('spipbb : init_metas OK');
	}

}

// [fr] Supprimer les metas du plugin (desinstallation)
// [en] Delete plugin metas
function spipbb_delete_metas() {
	if (isset($GLOBALS['meta']['spipbb'])) {
		include_spip('inc/meta');
		effacer_meta('spipbb');
		ecrire_metas();
		spip_log('spipbb : delete_metas OK');
	}
}

function spipbb_creer_groupe_mot($l_meta) {
	$res = spip_query("INSERT INTO spip_groupes_mots SET titre='spipbb'");
	$l_meta['spipbb_id_groupe_mot']= spip_insert_id();
	$res = spip_query("INSERT INTO spip_mots SET titre='ferme', id_groupe='".$l_meta['spipbb_id_groupe_mot']."'");
	$l_meta['spipbb_id_mot_ferme'] = spip_insert_id();
	$res = spip_query("INSERT INTO spip_mots SET titre='annonce', id_groupe='".$l_meta['spipbb_id_groupe_mot']."'");
	$l_meta['spipbb_id_mot_annonce'] = spip_insert_id();

	return $l_meta;
}

?>
