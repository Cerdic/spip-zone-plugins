<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_fulltext_index_document_dist($t) {
	//Recuperation de la configuration
	$fulltext = @unserialize($GLOBALS['meta']['fulltext']);
	if (is_array($fulltext)) {
		// Ne retenir que les 50 000 premiers caracteres (ou la valeur choisie)
		$taille_index = $fulltext['taille_index'] ? $fulltext['taille_index'] : @define('_FULLTEXT_TAILLE', 50000);
		//Nombre de documents traite par iteration
		$nb_docs = $fulltext['nb_docs'] ? $fulltext['nb_docs'] : @define('_FULLTEXT_NB_DOCS', 5);
		if ($docLists = sql_select('*', 'spip_documents', "extrait = 'non'", '', 'maj', '0,'.intval($nb_docs+1))) {
			while ($nb_docs-- and $row = sql_fetch($docLists)) {
				$extension = $row['extension'];
				$doc = $row['fichier'];
				//On indexe seulement si c'est autorise
				if (($fulltext[$extension.'_index'] == 'on') || (defined('_FULLTEXT_'.strtoupper($extension).'_EXE'))) {
					spip_log('Indexation de '.$doc, 'extract');
					global $extracteur;
					if (include_spip('extract/'.$extension)
						and function_exists($lire = $extracteur[$extension])) {
						include_spip('inc/distant');
						include_spip('inc/documents');
						//Le fichier existe-t-il/est-il accessible ?
						if (!$fichier = copie_locale(get_spip_doc($row['fichier']), 'test')) {
							//Le fichier n'est pas accessible, on log mais on poursuit pour les autres
							spip_log('Pas de copie locale de '.$row['fichier'], 'extract');
							//Et on met le statut en erreur
							sql_updateq('spip_documents', array('extrait' => 'err'), 'id_document=' . intval($row['id_document']));
						} else {
							//Le fichier existe, on indexe
							// par defaut, on pense que l'extracteur va retourner ce charset
							$charset = 'iso-8859-1';
							// lire le contenu
							$contenu = $lire(_DIR_RACINE.$fichier, $charset, $fulltext[$extension.'_bin'], $fulltext[$extension.'_opt']);
							if (!$contenu) {
								spip_log('Echec de l\'extraction de '.$fichier, 'extract');
								sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'err'), 'id_document=' . intval($row['id_document']));
							} else {
								$contenu = substr($contenu, 0, $taille_index);
								// Statut protege
								if ($contenu == 3) {
									sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'ptg'), 'id_document=' . intval($row['id_document']));
								} else {
								// importer le charset
									include_spip('inc/charsets');
									$contenu = importer_charset($contenu, $charset);
									sql_updateq('spip_documents', array('contenu' => $contenu, 'extrait' => 'oui'), 'id_document=' . intval($row['id_document']));
								}
							}
						}
					} else {
						// inutile de parcourir un par un tous les docs avec la meme extension !
						sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'err'), "extrait = 'non' AND extension=" . sql_quote($extension));
						spip_log("Impossible d'indexer tous les .$extension", 'extract');
					}
				} else {
					// si pas autoriser inutile de parcourir un par un tous les docs avec la meme extension !
					sql_updateq('spip_documents', array('contenu' => '', 'extrait' => 'err'), "extrait = 'non' AND extension=" . sql_quote($extension));
					spip_log("Interdiction d'indexer tous les .$extension", 'extract');
				}
			}
			if ($row = sql_fetch($docLists)) {
				spip_log('il reste des docs a indexer...', 'extract');
				return 0-$t; // il y a encore des docs a indexer
			}
		}
	}
	return 0;
}
