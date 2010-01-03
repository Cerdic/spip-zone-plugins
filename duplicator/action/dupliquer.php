<?php
/***************************************************************************\
 * Plugin Duplicator pour Spip 2.0
 * Licence GPL (c) 2010 - Apsulis
 * Duplication de rubriques et d'articles
 *
\***************************************************************************/

/**
 * Duplique un article dans la rubrique cible
 * - Conserve le contenu de l'article source
 * - Conserve le statut de publication de l'article source
 */
function dupliquer_article($article,$rubrique){
	include_spip('action/editer_article');
	include_spip('inc/modifier_article');

	// On lit l'article qui va etre dupliqué
	$champs = array('*');
	$from = 'spip_articles';
	$where = array( 
		"id_article=".$article
	);
	$infos = sql_allfetsel($champs, $from, $where);
	// On choisi les champs que l'on veut conserver
	$champs_dupliques = array(
		'surtitre','titre','soustitre','descriptif','chapo','texte','ps','accepter_forum','lang','langue_choisie','nom_site','url_site'
	);
	foreach ($champs_dupliques as $key => $value) {
		$infos_de_l_article[$value] = $infos[0][$value];
	}
	
	// On cherche ses mots clefs
	$champs = array('id_mot');
	$from = 'spip_mots_articles';
	$where = array( 
		"id_article=".$article
	);
	$mots_clefs_de_l_article = sql_allfetsel($champs, $from, $where);

	/*
	 * On duplique !
	 */
	// On le clone, il sera NON publié par défaut
	$id_article = insert_article($rubrique);
	revision_article($id_article, $infos_de_l_article);
	
	// On lui rend ses infos
	$maj_statut_article = sql_updateq("spip_articles", array('statut' => $infos[0]['statut']), "id_article=".$id_article);
	
	// On lui remet ses mots clefs
	foreach($mots_clefs_de_l_article as $champ => $valeur){
		$n = sql_insertq(
			'spip_mots_articles',
			array(
				'id_mot' => $valeur['id_mot'],
				'id_article' => $id_article
			)
		);
	}
	
	return $id_article;
}

/**
 * Duplique une rubrique dans la rubrique qui la contient
 * - Conserve le contenu de la rubrique source
 * - Conserve les mots clefs de la rubrique source
 * - Conserve les articles de la rubrique source
 */
function dupliquer_rubrique($rubrique){
	include_spip('action/editer_rubrique');

	/*
	 * Pré traitement
	 * On prépare les données
	 */
		// On lit la rubrique qui va etre dupliquee
		$champs = array('titre', 'texte', 'descriptif', 'id_parent');
		$from = 'spip_rubriques';
		$where = array( 
			"id_rubrique=".$rubrique
		);
		$infos = sql_allfetsel($champs, $from, $where);
		$infos_de_la_rubrique = array(
			'titre'=>$infos[0]['titre'].' (copie)',
			'texte'=>$infos[0]['texte'],
			'descriptif'=>$infos[0]['descriptif'],
			'id_parent'=>$infos[0]['id_parent']
		);

		// On cherche ses mots clefs
		$champs = array('id_mot');
		$from = 'spip_mots_rubriques';
		$where = array( 
			"id_rubrique=".$rubrique
		);
		$mots_clefs_de_la_rubrique = sql_allfetsel($champs, $from, $where);

		// On cherche ses articles
		$champs = array('id_article');
		$from = 'spip_articles';
		$where = array( 
			"id_rubrique=".$rubrique
		);
		$articles_de_la_rubrique = sql_allfetsel($champs, $from, $where);

	/*
	 * Traitement
	 * On duplique les données
	 */
		// On la duplique !
		$id_nouvelle_rubrique = insert_rubrique($infos_de_la_rubrique['id_parent']);
		revisions_rubriques($id_nouvelle_rubrique,$infos_de_la_rubrique);
		// On la publie (pour activer l'aperçu)
		$maj_statut_rubrique = sql_updateq("spip_rubriques", array('statut' => 'publie'), "id_rubrique=".$id_nouvelle_rubrique);

		// On lui remet ses mots clefs
		foreach($mots_clefs_de_la_rubrique as $champ => $valeur){
			$n = sql_insertq(
				'spip_mots_rubriques',
				array(
					'id_mot' => $valeur['id_mot'],
					'id_rubrique' => $id_nouvelle_rubrique
				)
			);
		}

		// On lui remet ses articles
		foreach($articles_de_la_rubrique as $champ => $valeur){
			$id_article = dupliquer_article($valeur['id_article'],$id_nouvelle_rubrique);
		}
	
	return $id_nouvelle_rubrique;
}

/**
 * Duplique une rubrique dans la rubrique passée en cible
 * - Conserve le contenu de la rubrique source
 * - Conserve les mots clefs de la rubrique source
 * - Conserve les articles de la rubrique source
 */
function dupliquer_sous_rubrique($rubrique,$cible){
	include_spip('action/editer_rubrique');

	/*
	 * Pré traitement
	 * On prépare les données
	 */
		// On lit la rubrique qui va etre dupliquee
		$champs = array('titre', 'texte', 'descriptif');
		$from = 'spip_rubriques';
		$where = array( 
			"id_rubrique=".$rubrique
		);
		$infos = sql_allfetsel($champs, $from, $where);
		$infos_de_la_rubrique = array(
			'titre'=>$infos[0]['titre'],
			'texte'=>$infos[0]['texte'],
			'descriptif'=>$infos[0]['descriptif'],
		);

		// On cherche ses mots clefs
		$champs = array('id_mot');
		$from = 'spip_mots_rubriques';
		$where = array( 
			"id_rubrique=".$rubrique
		);
		$mots_clefs_de_la_rubrique = sql_allfetsel($champs, $from, $where);

		// On cherche ses articles
		$champs = array('id_article');
		$from = 'spip_articles';
		$where = array( 
			"id_rubrique=".$rubrique
		);
		$articles_de_la_rubrique = sql_allfetsel($champs, $from, $where);

	/*
	 * Traitement
	 * On duplique les données
	 */
		// On la duplique !
		$id_nouvelle_rubrique = insert_rubrique($cible);
		revisions_rubriques($id_nouvelle_rubrique,$infos_de_la_rubrique);
		// On la publie (pour activer l'aperçu)
		$maj_statut_rubrique = sql_updateq("spip_rubriques", array('statut' => 'publie'), "id_rubrique=".$id_nouvelle_rubrique);

		// On lui remet ses mots clefs
		foreach($mots_clefs_de_la_rubrique as $champ => $valeur){
			$n = sql_insertq(
				'spip_mots_rubriques',
				array(
					'id_mot' => $valeur['id_mot'],
					'id_rubrique' => $id_nouvelle_rubrique
				)
			);
		}
		
		// On lui remet ses articles
		foreach($articles_de_la_rubrique as $champ => $valeur){
			$id_article = dupliquer_article($valeur['id_article'],$id_nouvelle_rubrique);
		}
	
	return $id_nouvelle_rubrique;
}
