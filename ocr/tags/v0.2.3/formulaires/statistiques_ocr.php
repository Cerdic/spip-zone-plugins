<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_statistiques_ocr_traiter_dist(){

	include_spip('ocr_administrations');
	ocr_reinitialiser_totalement_document();

	return array(
		"editable" => false,
		"message_ok" => _T('ocr:statistiques_message_relance'),
	);
	
}
