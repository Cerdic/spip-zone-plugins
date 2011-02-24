<?php

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function action_telecharger() 
{	// Id du document
	$id = intval(_request('arg'));
	
	if (!autoriser('document', 'voir', $id)) 
	{	http_status(404);
		include_spip('inc/minipres');
		echo minipres(_T('erreur').' 404',_T('info_document_indisponible'));
		return;
	}
	
	// DEBUG : echo "DATE : ".date("h:i:s")."<br/>";
	
	// Le document
	$doc = sql_fetsel("id_document, fichier, distant", "spip_documents", "id_document='$id'");
	if ($doc)
	{	$url = $doc['fichier'];
		$adr_site= _DIR_IMG;

		// ip du visiteur 
		$ip = $_SERVER["REMOTE_ADDR"];
		// Suprime les anciens clics
		$delai = (isset($GLOBALS['meta']['anaclic_delai']) ? $GLOBALS['meta']['anaclic_delai'] : 3600 );
		if ($delai >= 0)
		{ 	$time = time() -$delai;
			sql_delete ("spip_doc_compteurs_fix", "time < $time");
		}
		// Pas de multi-clic (meme IP sur le meme document dans le laps de temps)
		if (!sql_fetsel ("id_document","spip_doc_compteurs_fix","ip='$ip' AND id_document=$id"))
		{	sql_insertq ("spip_doc_compteurs_fix", array("id_document"=>$id,"ip"=>$ip,"time"=>time()));
			$date = date("Y-m-d");
			// Incrementer le compteur
			if ($row = sql_fetsel ("telechargement","spip_doc_compteurs","id_document=$id AND date='$date'"))
			{	sql_updateq ("spip_doc_compteurs", array("telechargement"=>$row[telechargement]+1), "id_document=$id AND date='$date'");
			}
			// Nouvelle journee
			else
			{	sql_insertq ("spip_doc_compteurs", array("id_document"=>$id, "date"=>$date, "telechargement"=>1) );
			}
		}

		if($distant=='oui') @header ("Location: $url");
		else @header ("Location: $adr_site/$url"); 

		echo "$distant <a href='$adr_site/$url'>$adr_site/$url</a>";
	}
}
?>