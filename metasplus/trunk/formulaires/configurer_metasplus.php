<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_metasplus_traiter_dist() {
	include_spip('inc/cvt_configurer');
	if ($documents = $_FILES) {
		$mode = 'auto';
		include_spip('action/editer_liens');
		$ajouter_document = charger_fonction('ajouter_documents', 'action');
		$Tid_doc = $ajouter_document(0, $documents, null, 0 ,$mode);
		$id_document = $Tid_doc[0];

		if ($id_document) {
			sql_updateq('spip_documents',array('statut' => 'publie'), 'id_document='.intval($id_document));
			set_request('id_doc_logo',$id_document);
		}
	}

	$retours = array();

	// On enregistre la nouvelle configuration
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_metasplus', array());

	$retours['message_ok'] = _T('config_info_enregistree') . $trace;
	$retours['editable'] = true;

	return $retours;
}
