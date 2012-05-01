<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deletelogobreve_dist($format, $service, $data){

	$id_breve = (isset($data) && isset($data['id_breve'])) ? $data['id_breve'] : null;

	return getBooleanResponse(deleteLogo($id_breve, 'breve'));
}

?>