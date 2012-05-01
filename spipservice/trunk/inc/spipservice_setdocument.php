<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_setdocument_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;
	$values = (isset($data) && isset($data['values'])) ? $data['values'] : null;

	return getBooleanResponse(setDocument($id, $values));
}

?>