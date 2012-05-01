<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deletedocumentbreve_dist($format, $service, $data){

	$id_document = (isset($data) && isset($data['id_document'])) ? $data['id_document'] : null;
	$id_breve = (isset($data) && isset($data['id_breve'])) ? $data['id_breve'] : null;

	return getBooleanResponse(deleteDocument($id_document, $id_breve, 'breve'));
}

?>