<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deleterubrique_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;

	return getBooleanResponse(deleteRubrique($id));
}

?>