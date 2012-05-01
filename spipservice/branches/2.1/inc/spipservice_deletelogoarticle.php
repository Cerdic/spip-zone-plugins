<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deletelogoarticle_dist($format, $service, $data){

	$id_article = (isset($data) && isset($data['id_article'])) ? $data['id_article'] : null;

	return getBooleanResponse(deleteLogo($id_article, 'article'));
}

?>