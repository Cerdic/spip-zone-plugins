<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deletedocumentarticle_dist($format, $service, $data){

	$id_document = (isset($data) && isset($data['id_document'])) ? $data['id_document'] : null;
	$id_article = (isset($data) && isset($data['id_article'])) ? $data['id_article'] : null;

	return getBooleanResponse(deleteDocument($id_document, $id_article, 'article'));
}

?>