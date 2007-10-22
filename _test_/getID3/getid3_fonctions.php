<?php

function recuperer_id3_doc($id_document){
include_spip('inc/recuperer_id3');

$result = spip_query("SELECT fichier FROM spip_documents WHERE id_document = " . intval($id_document));

	if(spip_num_rows($result)>0){
	$document=spip_fetch_array($result);
	$fichier = $document['fichier'];
	$fichier =ereg_replace(" ","%20",$fichier);
	return recuperer_id3($fichier);
	}

}

?>