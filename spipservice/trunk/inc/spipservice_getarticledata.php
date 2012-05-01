<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getarticledata_dist($format, $service, $data){

	$documents = (isset($data) && isset($data['documents'])) ? ($data['documents']=='true') ? true : ($data['documents']=='1') ? true : false : false; // [true,false]
	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;

	return getArrayResponse(getArticleData($id, $documents));
}

?>