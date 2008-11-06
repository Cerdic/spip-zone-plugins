<?php

include_spip("inc/getid3");
				
function getid3_editer_contenu_objet($flux){
	$id_document = $flux['args']['id'];
	if($flux['args']['type']=='case_document'){
		$son = array("mp3","ogg","flac");
		$document = sql_fetsel("docs.id_document, docs.extension, L.vu,L.objet,L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$extension = $document['extension'];
		$type = $document['objet'];
		$id = $document['id_objet'];
		if(in_array($extension,$son)){
			$infos_son = charger_fonction('infos_son', 'inc');
			$flux['data'] .= $infos_son($id,$id_document,$type);
		}
	}
	return $flux;
}

function getid3_post_edition($flux){
	$id_document = $flux['args']['id_objet'];
	if($flux['args']['operation'] == 'ajouter_document'){
		$son = array("mp3","ogg","flac");
		$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$extension = $document['extension'];
		if(in_any($extension,$son)){
			$recuperer_infos = charger_fonction('getid3_recuperer_infos','inc');
			$infos = $recuperer_infos($id_document);
		}
		else{
			return;
		}
	}
	else{
		return;
	}
}
?>