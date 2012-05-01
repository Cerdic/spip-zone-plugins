<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getbystatut_dist($format, $service, $data){

	$statut = (isset($data) && isset($data['statut'])) ? $data['statut'] : null;
	$types = (isset($data) && isset($data['types'])) ? $data['types'] : null;

	return getArrayResponse(getByStatut($statut, $types));
}

?>