<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getrubriquedata_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;
	$documents = (isset($data) && isset($data['documents'])) ? ($data['documents']=='true') ? true : ($data['documents']=='1') ? true : false : false; // [true,false]

	return getArrayResponse(getRubriqueData($id, $documents));
}

?>