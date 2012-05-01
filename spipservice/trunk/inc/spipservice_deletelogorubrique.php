<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deletelogorubrique_dist($format, $service, $data){

	$id_rubrique = (isset($data) && isset($data['id_rubrique'])) ? $data['id_rubrique'] : null;

	return getBooleanResponse(deleteLogo($id_rubrique, 'rubrique'));
}

?>