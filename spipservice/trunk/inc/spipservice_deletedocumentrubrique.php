<?php 

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function inc_spipservice_deletedocumentrubrique_dist($format, $service, $data){

	$id_document = (isset($data) && isset($data['id_document'])) ? $data['id_document'] : null;
	$id_rubrique = (isset($data) && isset($data['id_rubrique'])) ? $data['id_rubrique'] : null;

	return getBooleanResponse(deleteDocument($id_document, $id_rubrique, 'rubrique'));
}

?>