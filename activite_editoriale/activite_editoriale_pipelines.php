<?php

function activite_editoriale_taches_generales_cron($taches_generales) {
	$taches_generales['activite_editoriale_alerte'] = 24*3600; // tous les jours
  	return $taches_generales;
}

function activite_editoriale_boite_infos($flux){
	$type = $flux['args']['type'];
	if (function_exists('lire_config')){
        $config_stats_rubriques = lire_config('activite_editoriale/stats_rubriques','0');
		$config_mots_cles = lire_config('activite_editoriale/mots_cles','0');
    }

	if(autoriser("webmestre")){
		if (($id = intval($flux['args']['id'])) && ($type=='rubrique'))
		{
			if($config_stats_rubriques) {
				$flux['data'] .= recuperer_fond('fonds/stat_rubrique',array('id_rubrique' => $flux['args']['id']));
				$flux['data'] .= icone_horizontale(_T('activite_editoriale:icone_statistics'), generer_url_ecrire('activite_editoriale_statistics',"id_objet=$id&type=$type"), "", _DIR_PLUGIN_ACTIVITE_EDITORIALE."/images/statistics.png", false);
			}
			if($config_mots_cles)
			{
				$flux['data'] .= recuperer_fond('fonds/mots_cles',array('id_rubrique' => $flux['args']['id']));
				$flux['data'] .= icone_horizontale(_T('activite_editoriale:icone_mots_cles'), generer_url_ecrire('activite_editoriale_mots_cles',"id_objet=$id&type=$type"), "", _DIR_PLUGIN_ACTIVITE_EDITORIALE."/images/mot-cle.gif", false);
			}
		}
	}
	return $flux;
}