<?php

function photo_infos_pave($args) {
	$type = $args["args"]["type"];
	
	if ($type == "case_document") {
		$data = $args["data"];
		$id_document = $args["args"]["id"];		
			
			
		include_spip("base/abstract_sql");
			
			
		$query = sql_select("fichier", "spip_documents", "id_document=$id_document AND extension='jpg'");

		if ($row = sql_fetch($query)) {
			$fichier = _DIR_IMG.$row["fichier"];
			
			include_spip("inc/utils");
			$contexte = array('fichier'=>$fichier);

			$page = recuperer_fond("pave_exif", $contexte);
			$ret .= $page;
		}
		
		
		$args["data"] = $data . $ret;
	}


	return $args;
}

?>
