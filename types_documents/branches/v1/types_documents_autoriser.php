<?php
/**
 * Plugin Types de documents
 *
 */

/* Pour que le pipeline de rale pas ! */
function types_documents_autoriser(){}


function autoriser_types_document_modifier_dist($faire, $type, $id, $qui, $opt){
	if ($qui['statut'] == '0minirezo'
	AND !$qui['restreint'])
		return true;
}

function autoriser_types_document_creer_dist($faire, $type, $id, $qui, $opt){
	if ($qui['statut'] == '0minirezo'
	AND !$qui['restreint'])
		return true;
}

function autoriser_types_document_voir_dist($faire, $type, $id, $qui, $opt){
	if ($qui['statut'] == '0minirezo'
	AND !$qui['restreint'])
		return true;
}