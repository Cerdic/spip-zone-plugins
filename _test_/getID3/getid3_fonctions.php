<?php

function recuperer_id3_doc($id_document,$info = "", $mime = ""){
include_spip('inc/documents');

	$fichier = sql_getfetsel("fichier","spip_documents","id_document = " .sql_quote($id_document));
	$recuperer_id3 = charger_fonction('recuperer_id3','inc');
	$fichier = get_spip_doc($fichier);
	$fichier = ereg_replace(" ","%20",$fichier);
	$id3_content = $recuperer_id3($fichier,$info,$mime);
	
	$output = '';
	foreach($id3_content as $cle => $val){
		$output .= ($val) ? _T('getid3:'.$cle).' : '.$val.'<br />' : '';
	}
	
	return $output;
}

?>