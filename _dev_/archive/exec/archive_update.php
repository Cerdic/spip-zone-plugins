<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip('inc/presentation');   // for spip presentation functions
	include_spip('base/abstract_sql'); 	// for sql request
	include_spip('inc/utils');          // for _request function
	include_spip('inc/plugin');         // xml function
	include_spip('inc/distant');         // xml function

	$archive_action = _request('archive_action'); 	//determine le type d'action à faire = update
	$archive_submit = _request('submit');
	$archiver = _request('archiver'); 				//determine l'‚tat demand‚ = {oui,non}
	$id_objet = _request('id_objet'); 				//determine l'article ou la rubrique concern‚
    $objet_nature = _request('objet_nature');       //determine si c'est une rubrique ou bien un article

	$etat_precedent = _request('etat_precedent');	//demande l'‚tat precdedent du statut archive

        //determine la table … actualiser
        switch($objet_nature) {
            case rubrique:
                $spip_table = "spip_rubriques";
                $spip_id = "id_rubrique";
                break;
            case article:
                $spip_table = "spip_articles";
                $spip_id = "id_article";
                break;
            default :
                return 0;
        }

	//si demande d'archivage, mis … jour de la base
	if (($archiver != $etat_precedent) && ($archive_action == 'update')) {

		//défini la mise à jour à faire
		switch ($archiver) {
			case true:
				$sql = "UPDATE ".$spip_table." SET archive = ".$archiver.",archive_date = NOW() WHERE ".$spip_id."=".$id_objet;
				break;
			default :
				$sql = "UPDATE ".$spip_table." SET archive = NULL, archive_date = NULL WHERE ".$spip_id."=".$id_objet;
		}

		//execute la requete de mise à jour
		$n = spip_query($sql);
		if (!$n) die('UPDATE');
		//met à jour les index de la table articles
		include_spip('inc/indexation');
		//en 193 l'indexation est sortie du core, du coup on teste l'existance de l'indexation avant d'indexer
		if ($GLOBALS['meta']['activer_moteur'] == 'oui' && function_exists('marquer_indexer')) {
			marquer_indexer($spip_table, $id_objet);
		}
	}

	//relance la page appelante
	//$url = generer_url_ecrire("articles", "id_article=$id_article", true, true);
	//$url = "http://".$_SERVER['HTTP_HOST']."/ecrire/".$url;

	header("Location: ".$_SERVER['HTTP_REFERER']);
	//echo recuperer_page($url);
?>
