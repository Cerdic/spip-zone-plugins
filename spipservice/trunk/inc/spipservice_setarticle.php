<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_setarticle_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;
	$values = (isset($data) && isset($data['values'])) ? $data['values'] : null;

	return getBooleanResponse(setArticle($id, $values));
}

?>