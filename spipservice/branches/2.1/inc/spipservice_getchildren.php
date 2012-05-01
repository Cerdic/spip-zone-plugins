<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getchildren_dist($format, $service, $data){

	$id_parent = (isset($data) && isset($data['id_parent'])) ? $data['id_parent'] : null;
	$recurse = (isset($data) && isset($data['recurse'])) ? ($data['recurse']=='true') ? true : ($data['recurse']=='1') ? true : false : false; // [true,false]
	$documents = (isset($data) && isset($data['documents'])) ? ($data['documents']=='true') ? true : ($data['documents']=='1') ? true : false : false; // [true,false]

	return getArrayResponse(getChildren($id_parent, $recurse, $documents));
}

?>