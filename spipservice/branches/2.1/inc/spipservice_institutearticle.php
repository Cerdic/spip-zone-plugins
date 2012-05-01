<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_institutearticle_dist($format, $service, $data){

	$id = (isset($data) && isset($data['id'])) ? $data['id'] : null;
	$statut = (isset($data) && isset($data['statut'])) ? $data['statut'] : null;
	$date = (isset($data) && isset($data['date'])) ? $data['date'] : null;

	return getBooleanResponse(instituteArticle($id, $statut, $date));
}

?>