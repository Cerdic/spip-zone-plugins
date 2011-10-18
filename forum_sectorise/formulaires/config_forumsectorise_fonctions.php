<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_config_forumsectorise_pre_traiter(&$cfg){
	$tab_secteur = &$cfg->val['id_secteur'];
	$type = &$cfg->val['type'];
	$option = &$cfg->val['option'];
	$conf_forumsectorise = lire_config('forumsectorise');
	
	if ($tab_secteur != $conf_forumsectorise['id_secteur']) {
		include_spip('inc/invalideur');
		purger_repertoire(_DIR_SKELS);
	}

	// Appliquer les changements de moderation forum
	// option : futur, saufnon, tous
	if (in_array($option,array('tous', 'saufnon')) && count($tab_secteur)) {
		$where1 = ($option == 'saufnon') ? "accepter_forum != 'non'" : '';
		$where2 = sql_in('id_secteur',$tab_secteur) ;
		if(($where1!= '') && ($where2 != '')) {
			$where = $where1 . ' AND ' . $where2 ;
		} else {
			$where = $where1 . $where2 ;
		}
		sql_updateq('spip_articles', array('accepter_forum'=>$type), $where);
	}
}


?>
