<?php

function recuperer_id3_doc($id_document,$info,$mime){
include_spip('inc/recuperer_id3');
include_spip('inc/documents');

	$result = sql_select("fichier","spip_documents","id_document = " .sql_quote($id_document));

	if(sql_num_rows($result)>0){
		$document=sql_fetch($result);
		$fichier = get_spip_doc($document['fichier']);
		$fichier = ereg_replace(" ","%20",$fichier);
		return recuperer_id3($fichier,$info,$mime);
	}
}

?>