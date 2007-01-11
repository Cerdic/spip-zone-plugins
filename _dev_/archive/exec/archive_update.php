<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');   // for spip presentation functions
	include_spip('base/abstract_sql'); 	// for sql request
	include_spip('inc/utils');          // for _request function
	include_spip('inc/plugin');         // xml function
	include_spip('inc/distant');         // xml function

	$archive_action = _request('archive_action'); 	//determine le type d'action à faire = update 
	$archive_submit = _request('submit'); 
	$archiver = _request('archiver'); 				//determine l'état demandé = {oui,non}
	$id_article = _request('id_article'); 			//determine l'article concerné
	$etat_precedent = _request('etat_precedent');	//demande l'état precdedent du statut archive
		
	//si demande d'archivage, mis à jour de la base 
	if (($archiver != $etat_precedent) && ($archive_action == 'update')) {
		//défini la mise à jour à faire
		switch ($archiver) {
			case true:
				$sql = "UPDATE spip_articles SET archive = ".$archiver.",archive_date = NOW() WHERE id_article=".$id_article;
				break;
			default :
				$sql = "UPDATE spip_articles SET archive = NULL, archive_date = NULL WHERE id_article=".$id_article;
		}
		//execute la requete de mise à jour
		$n = spip_query($sql);
			if (!$n) die('UPDATE');						
		//met à jour les index de la table articles
		//if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip('inc/indexation');
			marquer_indexer('spip_articles', $id_article);
		//}
		ecrire_acces();
	}
	
	//relance la page appelante
	$url = generer_url_ecrire("articles", "id_article=$id_article", true, true);
	$url = "http://".$_SERVER['HTTP_HOST']."/ecrire/".$url;
	header("Location: ".$url);
	//echo recuperer_page($url);
	
?>
