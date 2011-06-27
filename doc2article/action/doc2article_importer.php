<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_doc2article_importer_dist($arg=null){

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (!$doc2article = sql_fetsel('*','spip_doc2article','id_doc2article='.intval($arg))) {
		return false;
	}
	
	// charger crud
	if ($crud = charger_fonction('crud','action')) {
		
		//extraire le nom du fichier
		$filename = basename($doc2article['fichier']);
		preg_match(",^(.*)\.([^.]+)$,", $filename, $match);
		@list(,$titre,$ext) = $match;
		
		// préparer les valeurs de l'article
		$titre = preg_replace(',_,',' ',$titre);
		$valeurs_article = array(
			'titre'=>$titre,
			'id_rubrique'=>$doc2article['id_rubrique'],
			'statut'=>'publie'
		);

		// envoyer aux plugins
		$valeurs_article = pipeline('doc2article_preparer_article',
			array(
				'args' => array(
					'id_doc2article' => $doc2article['id_doc2article'],
					'fichier' => $doc2article['fichier'],
				),
				'data' => $valeurs_article
			)
		);

		// creation de l'article
		$article = $crud('create','articles',null,$valeurs_article);
		
		// corriger l'id_auteur attribué automatiquement par action/editer_article
		sql_delete('spip_auteurs_articles', 'id_article='.$article['result']['id']);
		sql_insertq('spip_auteurs_articles', array('id_auteur' => $doc2article['id_auteur'], 'id_article' => $article['result']['id']));
		
		if(!$article['success']) {
			$err = _T('doc2article:erreur_creation_article');
		} else {
			spip_log("création de l'article ". $article['result']['id'],"doc2article");
			spip_log($valeurs_article,"doc2article");
						
			// ajout du doc à l'article
			$doc = $crud('create','documents',null,
				array(
					'id_document'=> 'non',
					'type' => 'article',
					'id_objet' => $article['result']['id'],
					'mode' => 'document',
					'source' => $doc2article['fichier'],
					'titre' => $titre
				)
			);
			
			if(!$doc['success']) {
				$err = _T('doc2article:erreur_creation_document');
			} else {
				spip_log("ajout du document à l'article ". $article['result']['id'],"doc2article");
				// suppression du doc de la file d'attente
				sql_delete('spip_doc2article','id_doc2article='.$doc2article['id_doc2article']);
				// supression du fichier du dossier d'import
				spip_log(_DIR_TMP."upload/$filename","doc2article");
				$suppitem = supprimer_fichier(_DIR_TMP."upload/$filename");
				if($suppitem){
					spip_log("suppression du fichier ". $doc2article['fichier'],"doc2article");
				}else{
					$err = _T('doc2article:erreur_suppression_impossible',array('fichier'=>$doc2article['fichier']));
					spip_log("erreur lors de la suppression du fichier ". $doc2article['fichier'],"doc2article");

				}
			}
		}
	}

	return array($id_doc2article,$err);
}

?>