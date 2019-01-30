<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_configurer_metasplus_traiter_dist() {

	include_spip('inc/cvt_configurer');
	//refuser_traiter_formulaire_ajax();

	$retours = array();

	// Enregistrement de l'image
	if ($documents = $_FILES) {
		$mode = 'auto';
		include_spip('action/editer_liens');
		$ajouter_document = charger_fonction('ajouter_documents', 'action');
		if (
			$document = $ajouter_document(0, $documents, null, 0, $mode)
			and $id_document = intval($document[0])
		) {
			;
			sql_updateq('spip_documents',array('statut' => 'publie'), 'id_document='.intval($id_document));
			set_request('id_doc_logo',$id_document);
		}
	}

	// Enregistrement de la configuration
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_metasplus', array());
	$retours['message_ok'] = _T('config_info_enregistree') . $trace;

	// Si on traite le logo, on force une redirection
	if ($id_document) {
		$retours['redirect'] = parametre_url(self(), 'id_document', $id_document, '&');
	}

	$retours['editable'] = true;

	return $retours;
}
