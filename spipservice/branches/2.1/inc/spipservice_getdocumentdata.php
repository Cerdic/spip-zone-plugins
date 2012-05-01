<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getdocumentdata_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;

	return getArrayResponse(getDocumentData($id));
}

?>