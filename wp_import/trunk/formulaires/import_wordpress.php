<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/wp_import_api');





// A utiliser en DEV pour importer uniquement les NB_ARTICLES
// define("NB_ARTICLES", 20);

function formulaires_import_wordpress_charger_dist() {

$valeurs['mes_saisies'] = array(
	
	array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'document_xml',
			'label' => _T('wp_import:document_xml_label'),
			'explication' => _T('wp_import:document_xml_explication'),
			'obligatoire' => 'oui'
		)),
	array(
		'saisie' => 'case',
		'options' => array(
			'nom' => 'auteurs',
			'label' => _T('wp_import:auteurs_label'),
			'explication' => _T('wp_import:auteurs_explication')
		)),
	// array(
	// 	'saisie' => 'case',
	// 	'options' => array(
	// 		'nom' => 'rubriques',
	// 		'label' => _T('wp_import:rubriques_label'),
	// 		'explication' => _T('wp_import:rubriques_explication')
	// 	)),
	// array(
	// 	'saisie' => 'input',
	// 	'options' => array(
	// 		'nom' => 'id_parent',
	// 		'label' => _T('wp_import:id_parent_label'),
	// 		'explication' => _T('wp_import:id_parent_explication'),
	// 		'defaut' => 0
	// 	)),
	array(
		'saisie' => 'case',
		'options' => array(
			'nom' => 'documents',
			'label' => _T('wp_import:documents_label'),
			'explication' => _T('wp_import:documents_explication')
		)),
	array(
		'saisie' => 'case',
		'options' => array(
			'nom' => 'articles',
			'label' => _T('wp_import:articles_label'),
			'explication' => _T('wp_import:articles_explication')
		)),
	array(
		'saisie' => 'case',
		'options' => array(
			'nom' => 'forum',
			'label' => _T('wp_import:forum_label'),
			'explication' => _T('wp_import:forum_explication')
		)),
	// array(
	// 	'saisie' => 'radio',
	// 	'options' => array(
	// 		'nom' => 'forum_config',
	// 		'label' => _T('wp_import:forum_config_label'),
	// 		'explication' => _T('wp_import:forum_config_explication'),
	// 		'afficher_si' =>  '@forum@=="on"'
	// 	)),
	
	// array(
	// 	'saisie' => 'case',
	// 	'options' => array(
	// 		'nom' => 'motcle',
	// 		'label' => _T('wp_import:motcle_label'),
	// 		'explication' => _T('wp_import:motcle_explication')
	// 	))
    );

    return $valeurs;
}


// function formulaires_import_wordpress_verifier_dist() {
// 	$erreurs = array();
// 	$fichier = _DIR_TMP . _request('document_xml');

// 	$fichiers = "../tmp/afap.wordpress.2018-04-06.xml";
// 	if (_request('document_xml')) {
// 		if (!file_exists($fichier)) {
// 			$erreurs['document_xml'] = _T('wp_import:erreur_fichier', array('fichier' => "$fichier"));
// 			$erreurs['message_erreur'] = _T('wp_import:erreur_generale');
// 		}
// 	} 
// 	else {
// 		$erreurs['document_xml'] = _T('wp_import:erreur_fichier_vide');
// 	}
// 	return $erreurs;
// }


// https://code.spip.net/@inc_editer_mot_dist
function formulaires_import_wordpress_traiter_dist() {

	// Lancer l'importation
    list($message, $erreurs) = wp_import_import_wordpress();

    $retour['editable'] = true;
    if (count($erreurs) == 0) {
        $retour['message_ok'] = $message;
    }
    else {
        $retour['message_erreur'] = implode('<br />', $erreurs);
    }
    return $retour;
}

function wp_import_import_wordpress() {

	$chemin_temp = sous_repertoire(_DIR_TMP, 'wordpress');
	//$chemin_fichier = _DIR_TMP . _request('document_xml');
	$chemin_fichier = "../tmp/afap.wordpress.2018-04-06.xml";
	include_spip('inc/getdocument');

	if (file_exists($chemin_fichier)) {

		include_spip('inc/xml');
		include_spip('sale_fonctions');
	

		$tab_document = array();

		$arbre = spip_xml_load($chemin_fichier);
		$arbre = array_shift($arbre);
		$arbre = $arbre[0]['channel'][0];

		// On commence par les auteurs, rubriques, documents, mot-clés
		if (_request('auteurs') or _request('rubriques') or _request('motcle')) {
			foreach ($arbre as $type => $a) {
				// spip_log("Type : $type  ", "wp_import" . _LOG_INFO_IMPORTANTE);

				switch ($type) {

					// Importation des auteurs
					case 'wp:author':
						if (!_request("auteurs")) {
							break; //sortir de suite si l'on n'a pas coché l'importation des auteurs
						}
						wp_import_auteurs($a);
						break;

					// Importation des rubriques
					case "wp:category":
						if (!_request("rubriques")) {
							break; // sortir de suite si l'on n'a pas coché l'importation des rubriques
						}
						wp_import_rubriques($a);
						break;

					case "wp:term":
						//sortir de suite si l'on n'a pas coché l'importation des mots-clés
						if (!_request("motcle")) {
							break;
						}
						wp_import_mots_cles($item);
						break;
					
					// case "item":
					// 	foreach ($a as $item) {
					// 		spip_log("post_type : " . $item['wp:post_type'][0], "wp_import" . _LOG_INFO_IMPORTANTE);
					// 		$objet = wp_import_twp($item['wp:post_type']['0']);

					// 		switch ($objet) {
					// 			case 'attachment':
					// 				wp_import_documents($item);
					// 				break;
					// 		}
					// 	}
					// 	break;
				}
			}
		}

		// On continue avec les articles (puisqu'on a maintenant les documents dans la médiathèque)
		if (_request("articles")) {
		
			foreach ($arbre as $type => $a) {
				// spip_log("Type : $type  ", "wp_import" . _LOG_INFO_IMPORTANTE);

				switch ($type) {
					// Importation des articles, pages, documents, sujets de forums et réponses asscosiés.
					case "item":

						foreach ($a as $item) {
							// spip_log("post_type : " . $item['wp:post_type'][0], "wp_import" . _LOG_INFO_IMPORTANTE);
							$objet = wp_import_twp($item['wp:post_type']['0']);

							switch ($objet) {
								case 'post': 	// articles de blog, mais des fois traiter dans WP comme actus
								//case 'page': 	// articles (peut être transformé en Page Uniques (voir plugin éponyme))
								case 'topic': 	// articles aux sens SPIP, mais en fait ce sont des Sujets de Forum
									wp_import_articles($item, $objet);
									break;
							}
						}
						break;
				}
			}
		}

		// finir avec les forums (puisqu'on a maintenant les articles)
		if (_request("forum")) {
			// faire le tableau des correspondances parent_wp <-> id_article
			// $res = sql_allfetsel('id_article, accepter_forum ', 'spip_articles', "accepter_forum != 'non'");
			// foreach ($res as $key => $value) {
			// 	$correspondance[$value['accepter_forum']] = $value['id_article'];
			// }
			
			// debug($correspondance);

			foreach ($arbre as $type => $a) {
				if ($type == 'item') {
					foreach ($a as $item) {
						$objet = wp_import_twp($item['wp:post_type']['0']);
						if ($objet == 'reply') {
							wp_import_forum($item, $correspondance);
						}
					}
				}
			}
		}
	}

    if (empty($erreurs)) {
        $message = "Le contenu de votre site Wordpress a bien été importé";
    }

    return array($message, $erreurs);
}

/**
 * Import des auteurs (wp_author - auteurs spip)
 * 
 * @authors xml : l'ensemble des authors WP
 * @return string : le nombre d'auteurs enregistrés
 */
function wp_import_auteurs($authors) {
	include_spip('action/editer_auteur');

	foreach ($authors as $auteur) {
		$nom = wp_import_twp($auteur['wp:author_display_name'][0]);
		$nom = empty($nom) ? $auteur['wp:author_login'][0] : $nom;
		$data_auteur = array(
			'login'		=> wp_import_twp($auteur['wp:author_login'][0]),
			'email'		=> wp_import_twp($auteur['wp:author_email'][0]),
			'statut'	=> '1comite',
			'nom'		=> $nom
		);
		$id_auteur = auteur_inserer();
		auteur_modifier($id_auteur, $data_auteur);
		$tab_auteur[$auteur['wp:author_login'][0]] = $id_auteur;
		spip_log("Auteur $id_auteur créé ( " . $auteur['wp:author_login'][0] . " ) ", "wp_import" . _LOG_INFO_IMPORTANTE);
		$cpt_auteurs++;
	}
}

/**
 * Import des category (wp_category -> rubriques spip)
 * Attention : cette logique [category = rubrique] n'est pas toujours respectée
 * 
 * @authors xml : l'ensemble des authors WP
 * @return string : le nombre d'auteurs enregistrés
 */
function wp_import_rubriques($category) {
	include_spip('action/editer_rubrique');
	$id_parent_rubrique = _request("id_parent");
	foreach ($a as &$cat) {
		$data_rub = array(
		'titre' => wp_import_twp($cat['wp:cat_name'][0]),
		'id_parent' => "$id_parent_rubrique"
		);
		$id_rub = rubrique_inserer($id_parent_rubrique);
		spip_log("Création rubrique $id_rub (id_parent : $id_parent_rubrique) (" . wp_import_twp($cat['wp:cat_name'][0]) . ") ", "wp_import" . _LOG_INFO_IMPORTANTE);
		$cat["id"] = $id_rub;
		$tab_cat[$cat['wp:category_nicename'][0]] = $id_rub;
		rubrique_modifier($id_rub, $data_rub);
	}
	foreach ($a as $cat) {
		$id_parent = $tab_cat[wp_import_twp($cat['wp:category_nicename'][0])] + 0;
		spip_log("Modif rubrique parent $id_parent ) ", "wp_import" . _LOG_INFO_IMPORTANTE);

		$data_rub = array('id_parent' => $id_parent);
		rubrique_modifier($cat["id"], $data_rub);
	}
}

/**
 * Import des articles (item[post, page, topic] -> article spip)
 * 
 * @article_wp xml : un article WP
 * @objet string : soit un post, une page, un topic
 * @return string : le nombre d'auteurs enregistrés
 */
function wp_import_articles($article_wp, $objet) {
	include_spip('action/editer_article');
	include_spip('action/editer_auteur');

	static $compteur_post = 0;
	static $compteur_page = 0;
	static $compteur_topic = 0;

	// récuperation des données
	$set_article = array(
		'titre' 		=> $article_wp['title'][0],
		'descriptif' 	=> wp_import_twp($article_wp['description'][0]),
		'texte' 		=> sale(wp_import_twp($article_wp['content:encoded'][0]), $tab_document),
		'date' 			=> wp_import_twp($article_wp['wp:post_date'][0]),
		'date_modif' 	=> wp_import_twp($article_wp['wp:post_date'][0]),
		'accepter_forum' => 'non',
		'statut' => 'prepa',
	);

	// Gestion du rubriquage
	switch ($objet) {
		case 'post':
			$set_article['id_rubrique'] = 1;
			$set_article['id_secteur'] = 1;
			$compteur_post++;
			break;
		case 'page':
			$set_article['id_rubrique'] = 2;
			$set_article['id_secteur'] = 2;
			$compteur_page++;
			break;
		case 'topic':
			$set_article['id_rubrique'] = 3;
			$set_article['id_secteur'] = 3;
			$compteur_topic++;
			break;
	}
	
	// gestion du statut
	$status_wp = wp_import_twp($article_wp['wp:status'][0]);
	if ($status_wp == 'publish') {
		$set_article['statut'] = 'publie';
	}
	else {
		$set_article['statut'] = 'prepa';
	}

	// On insère…
	$id_article = sql_insertq('spip_articles', $set_article);
	//article_modifier($id_article, $set_article);

	// gestion des forums : creer un table de correspondance si récupération des forums demandée
	global $tab_forum;
	if ($objet == 'topic') {
		$id_wp_parent = $article_wp['wp:post_id'][0];
		$tab_forum[$id_wp_parent] = $id_article;
	}
	
	// Lier l'auteur : on se base sur le login car avec SPIP, on est sûr de l'unicité de ce champ
	$login_auteur = wp_import_twp($article_wp['dc:creator'][0]);
	$id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'login='.sql_quote($login_auteur ));
	$res = auteur_associer($id_auteur, array('article' => $id_article));

	$message_retour = 'à faire';
	return $message_retour;
	
}

/**
 * Import des réponses aux sujets posés (item[reply] -> forum spip)
 * Il s'agit ici de lier les réponses aux articles identifiés comme étant des sujets
 * 
 * @reply_wp : un item[reply] WP
 * 
 * @return string : le nombre de reply enregistré
 */
function wp_import_forum($reply_wp, $correspondance) {
	include_spip('inc/forum');
	global $tab_forum;

	$id_wp_parent = $reply_wp['wp:post_parent'][0];
	$id_article = $tab_forum[$id_wp_parent];

	// debug($id_wp_parent);
	// debug($id_article);


	// récupérer les infos de l'auteur
	$login_auteur = wp_import_twp($reply_wp['dc:creator'][0]);
	$auteur = sql_fetsel('id_auteur, nom, email', 'spip_auteurs', 'login='.sql_quote($login_auteur));

	// recuperer la date
	$date =  wp_import_twp($reply_wp['wp:post_date'][0]);

	// récuperation des données
	$set_forum = array(
		'id_objet' => $id_article,
		'objet' => 'article',
		'date_heure' => $date,
		'date_thread' => $date,
		'titre' => $reply_wp['title'][0],
		'texte' => sale(wp_import_twp($reply_wp['content:encoded'][0]), $tab_document),
		'auteur' => $auteur['nom'],
		'email_auteur' => $auteur['email'],
		'statut' => 'publie',
		'id_auteur' => $auteur['id_auteur'],
	);

	// Insertion des forums
	$id_forum = sql_insertq('spip_forum', $set_forum);

	// mise à jour de la valeur id_thread = id_forum
	$res = sql_updateq('spip_forum', array('id_thread' => $id_forum), 'id_forum='.intval($id_forum));

}


/**
 * Import des documents (images, fichiers pdf, etc.)
 * 
 * @attachement_wp : un item[attachement] WP
 * 
 * @return string : le nombre de document enregistré jusque là
 */
function wp_import_documents($attachement_wp) {
	include_spip("action/ajouter_documents");

	$data_document = array(
		'titre' 		=> $attachement_wp['title'][0],
		'descriptif' 	=> wp_import_twp($attachement_wp['description'][0]),
		'date' 			=> $attachement_wp['post_date'][0]
	);

	$fichier = $attachement_wp['wp:attachment_url'][0];
	$result = array();
	$path_parts = pathinfo($attachement_wp['wp:attachment_url'][0]);
	$e = $path_parts['extension'];
	$mode = strpos($GLOBALS['meta']['formats_graphiques'], $e) === false ? 'document' : 'image';

	$tmp_name 		= basename($attachement_wp['wp:attachment_url'][0]);
	$nom_fichier 	= basename($attachement_wp['wp:attachment_url'][0]);
	$chemin_temp_document = sous_repertoire($chemin_temp, 'uploads');
	if (file_exists($chemin_temp_document . $nom_fichier)) {
		$tmp_name = $chemin_temp_document . $nom_fichier;
	}
	else {
		$tmp_name = $attachement_wp['wp:attachment_url'][0];
	}

	$file = array('tmp_name' => $tmp_name,
		'name' => $nom_fichier,
		'titrer' => true,
		'distant' => false,
		'mode' => 'document'
	);

	$ajouter_un_document = charger_fonction('ajouter_un_document', 'action');
	$id_document = $ajouter_un_document(0, $file, '', 0, 'document');
	document_modifier($id_document, $data_document);

	$tab_document[basename($attachement_wp['wp:attachment_url'][0])] = $id_document;
}

function wp_import_mots_cles($term_wp) {
	include_spip("action/editer_mot");
	include_spip("action/editer_groupe_mots");
	include_spip("action/editer_objet");

	foreach ($a as &$term) {
		$id_groupe = 0;
		$titre_groupe = $term['wp:term_taxonomy'][0];
		if ($sql_groupe_mot = sql_fetsel('id_groupe', "spip_groupes_mots", "titre=" . sql_quote($titre_groupe))) {
			$id_groupe = $sql_groupe_mot['id_groupe'];
		} 
		else {
			//Création du groupe de mot
			$data = array('titre' => $titre_groupe);
			//$id_groupe = groupe_mots_inserer( $data);
			$id_groupe = objet_inserer("groupe_mots");
			groupe_mots_modifier($id_groupe, $data);
			spip_log("Création groupe_mot $id_groupe ( $titre_groupe)  ", "wp_import" . _LOG_INFO_IMPORTANTE);
		}
	}

	//Création du mot
	$data = array(
	'titre' => wp_import_twp($term['wp:term_name'][0]),
	'id_groupe' => $id_groupe);

	$id_mot = mot_inserer($id_groupe);
	mot_modifier($id_mot, $data);
	spip_log("Création mot $id_mot (" . wp_import_twp($term['wp:term_name'][0]) . ")  ", "wp_import" . _LOG_INFO_IMPORTANTE);

}
