<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_search_dist($format, $service, $data){

	$types = (isset($data) && isset($data['types'])) ? $data['types'] : null;
	$search = (isset($data) && isset($data['search'])) ? $data['search'] : null;

	return getArrayResponse(search($search, $types));
}

?>