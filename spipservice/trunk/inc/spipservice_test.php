<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_test_dist($format, $service, $data){

	return getBooleanResponse(test());
}

?>