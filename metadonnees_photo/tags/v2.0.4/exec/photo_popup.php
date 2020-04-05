<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_photo_popup() {
	include_spip("inc/utils");
	include_spip("inc/autoriser");

	if (!$id_document = _request('id_document')
		AND $fichier = _request('fichier')) {
		if (strncmp($fichier,_DIR_IMG,strlen(_DIR_IMG))==0)
			$fichier = substr($fichier,strlen(_DIR_IMG));

		$id_document = sql_getfetsel('id_document','spip_documents','fichier='.sql_quote($fichier));
	}

	include_spip('inc/actions');
	ajax_retour(recuperer_fond("prive/squelettes/inclure/popup_document", array('id_document'=>$id_document)));
}		


?>