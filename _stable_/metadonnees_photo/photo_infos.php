<?php

function photo_infos_pave($args) {
	$type = $args["args"]["type"];
	
	if ($type == "case_document") {
		$data = $args["data"];
		$id_document = $args["args"]["id"];		
			
		$query = sql_query("SELECT fichier FROM spip_documents WHERE id_document=$id_document AND extension='jpg'");
		if ($row = sql_fetch($query)) {
			$fichier = _DIR_IMG.$row["fichier"];
			
			include_spip("inc/utils");
			$contexte = array('fichier'=>$fichier);

			$p = evaluer_fond("pave_exif", $contexte);
			$ret .= $p["texte"];
		}
		
		
		$args["data"] = $data . $ret;
	}


	return $args;
}

?>
