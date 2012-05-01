<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_getauteurloggedin_dist($format, $service, $data){
	return getArrayResponse(getAuteurLoggedIn());
}

?>