<?php
function genie_fulltext_index_document_dist() {
	// TODO : rendre paramétrable cette limite de 5 docs par passe
	if ($docLists = sql_select("*", "spip_documents", "indexe = 'non'", "", "maj", "0,5")) {
		while($row = sql_fetch($docLists)) {
			$extension = $row['extension'];
			$doc = $row['fichier'];
			spip_log('-------------------------------------', 'fulltext');
      spip_log('Indexation de '.$doc, 'fulltext');
			global $extracteur;
			include_spip('extract/'.$extension);
			if (function_exists($lire = $extracteur[$extension])) {
				include_spip('inc/distant');
				include_spip('inc/documents');
				if (!$fichier = copie_locale(get_spip_doc($row['fichier']), 'test')) {
					spip_log("pas de copie locale de '$fichier'", "fulltext");
					return;
				}
				// par defaut, on pense que l'extracteur va retourner ce charset
				$charset = 'iso-8859-1';
				// lire le contenu
				$contenu = $lire(_DIR_RACINE.$fichier, $charset);
				if (!$contenu) {
					spip_log('Echec de l\'extraction de '.$fichier, 'fulltext');
          sql_updateq("spip_documents", array('contenu' => '', 'indexe' => 'err'), "id_document=".intval($row['id_document']));
				} else {
					// Ne retenir que les 50 premiers ko
					if(defined('_FULLTEXT_TAILLE')){
						$size = _FULLTEXT_TAILLE;
					} else {
						$size = 50000;
					}
					$contenu = substr($contenu, 0, $size);
					// importer le charset
					include_spip('inc/charsets');
					$contenu = importer_charset($contenu, $charset);
					sql_updateq("spip_documents", array('contenu' => $contenu, 'indexe' => 'oui'), "id_document=".intval($row['id_document']));
				}
			}
		}
	}
	return 0;
}
?>