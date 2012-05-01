<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_adddocumentbreve_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;
	$filename = (isset($data) && isset($data['file_name'])) ? $data['file_name'] : null;
	$binaryBase64 = (isset($data) && isset($data['binary_base_64'])) ? $data['binary_base_64'] : null; // format binaire en base64

	return getBooleanResponse(addDocumentBreve($id, $filename, $binaryBase64));
}

?>