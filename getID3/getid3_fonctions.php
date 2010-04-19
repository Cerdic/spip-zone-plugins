<?php

function recuperer_id3_doc($id_document,$info = "", $mime = "",$retour='oui'){
	include_spip('inc/documents');

	$recuperer_id3 = charger_fonction('getid3_recuperer_infos','inc');
	$id3_content = $recuperer_id3($id_document);

	if($retour == 'oui'){
		$output = '';
		foreach($id3_content as $cle => $val){
			if(preg_match('/cover/',$cle)){
				$output .= ($val) ? '<img src='.$val.' /><br />' : '';
			}else{
				$output .= ($val) ? _T('getid3:'.$cle).' : '.$val.'<br />' : '';
			}
		}
	}
	return $output;
}

?>