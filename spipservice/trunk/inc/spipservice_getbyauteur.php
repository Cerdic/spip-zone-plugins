<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getbyauteur_dist($format, $service, $data){

	$types = (isset($data) && isset($data['types'])) ? $data['types'] : null;
	$auteur = (isset($data) && isset($data['auteur'])) ? $data['auteur'] : null;

	return getArrayResponse(getByAuteur($auteur, $types));
}

?>