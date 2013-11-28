<?php
/**
 * Plugin Séminaires
 * Licence GNU/GPL
 * 
 * @package SPIP\Seminaires\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

function seminaire_post_insertion($flux) {
	if ($flux['args']['table'] == 'spip_evenements') {
		sql_insertq("spip_mots_liens", array(
			'id_mot' => _request('id_mot'),
			'id_article' =>$flux['args']['id_objet'],
			'objet' =>'evenement'));
	}
}

?>