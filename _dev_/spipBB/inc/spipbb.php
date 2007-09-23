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

?>
