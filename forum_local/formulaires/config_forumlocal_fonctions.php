<?php


function cfg_config_forumlocal_pre_traiter(&$cfg){
	$id_secteur = &$cfg->val['id_secteur'];
	$type = &$cfg->val['type'];
	$option = &$cfg->val['option'];

	if ($id_secteur != $conf_forumlocal['id_secteur']) {
		include_spip('inc/invalideur');
		purger_repertoire(_DIR_SKELS);
	}

	// Appliquer les changements de moderation forum
	// option : futur, saufnon, tous
	if (in_array($option,array('tous', 'saufnon'))) {
		$where = ($option == 'saufnon') ? "type != 'non'" : '';
		$where .= ($id_secteur > 0) ? "id_secteur = '$id_secteur'" : '' ;
		sql_updateq('spip_articles', array('accepter_forum'=>$type), $where);
	}
}


?>
