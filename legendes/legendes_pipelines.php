<?php

function legendes_insert_head($flux){
	$flux .='<script src="'._DIR_PLUGIN_LEGENDES.'javascript/jquery.annotate.js" type="text/javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_LEGENDES.'legendes.css" type="text/css" media="projection, screen, tv" />';
	return $flux;
}

function legendes_jqueryui_forcer($plugins){
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.mouse";
	$plugins[] = "jquery.ui.draggable";
	$plugins[] = "jquery.ui.resizable";
	return $plugins;
}

function legendes_post_edition($flux){
	// si on tourne un document, tourner les legendes associees
	if($flux['args']['action']=='tourner'){
		$id_document = $flux['args']['id_objet'];
		$angle = $flux['args']['champs']['rotation'];
		$res = sql_select("id_legende", "spip_legendes", "id_document=".intval($id_document));
		while ($row = sql_fetch($res)){
			$id_legende = $row['id_legende'];
			include_spip("action/editer_legende");
			legendes_action_tourner_legende($id_legende,$angle);
		}
		// Invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_document/$id_document'");
	}
	/**
	 * On supprime les légendes de documents supprimés
	 */
	if($flux['args']['operation'] == 'supprimer_document'){
		spip_log('suppression du doc : '.$flux['args']['id_objet'],'test');
		$legendes_documents = sql_select('id_legende','spip_legendes','id_document='.intval($flux['args']['id_objet']));
		include_spip("action/editer_legende");
		while($legende = sql_fetch($legendes_documents)){
			legendes_action_supprime_legende($legende['id_legende']);
		}
	}
	return $flux;
}

?>