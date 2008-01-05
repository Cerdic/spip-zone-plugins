<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/date");
include_spip ("inc/texte");
include_spip ("inc/meta");
include_spip ("inc/session");
include_spip ("inc/filtres");
include_spip ("inc/acces");
include_spip ("inc/documents");
include_spip ("inc/ajouter_documents");
include_spip ("inc/getdocument");
include_spip('inc/barre');
include_spip('base/abstract_sql');

function balise_FORMULAIRE_ARTICLE ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_ARTICLE', array('id_rubrique'));
}

function balise_FORMULAIRE_ARTICLE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_ARTICLE',
					'motif' => 'RUBRIQUES')), '');
		list ($id_rubrique,$url) = $args;
		return array($id_rubrique,$url);
}

function balise_FORMULAIRE_ARTICLE_dyn($id_rubrique,$url) {
	global $_FILES, $_HTTP_POST_FILES;
	
	if ($url){
		$retour = str_replace('&amp;', '&', $url);
	}

	$article = _request('article');
	
	if (!$article) {
			$s = spip_query("SELECT id_article FROM spip_articles WHERE statut='prepa' LIMIT 1");
			if ($r = spip_fetch_array($s)) {
				$article = $r['id_article'];
			}
	}
	
	//recuperation des champs
	$surtitre= stripslashes(_request('surtitre'));
	$titre= stripslashes(_request('titre'));
	$soustitre= stripslashes(_request('soustitre'));
	$descriptif= _request('descriptif');
	$chapeau= _request('chapeau');
	$texte= _request('texte');
	$ps= _request('ps');

	$var_lang = _request('var_lang');
	$date1 = _request('date1');
	$date2 = _request('date2');
	$heure_d = _request('heure_d');
	$minute_d = _request('minute_d');
	$heure_f = _request('heure_f');
	$minute_f = _request('minute_f');
	$date_aujd = date('Y-m-d H:i:s');
	$date_modif = _request('date_modif');

	if ($GLOBALS["auteur_session"]) {
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
		$nom_auteur_session = $GLOBALS['auteur_session']['nom'];
		$email_auteur_session = $GLOBALS['auteur_session']['email'];
	}

	if ($date1 == $date2){
		$minute_f = $minute_d;
		$heure_f = $heure_d;
	}

//Creation du formulaire de l'heure de debut
	$heure_debut = afficher_heure($heure_d, "name='heure_d' size='1' class='fondl'", true);
	$minute_debut = afficher_minute($minute_d, "name='minute_d' size='1' class='fondl'", true);

//Creation du formulaire de l'heure de fin
	$heure_fin = afficher_heure($heure_f, "name='heure_f' size='1' class='fondl'", true);
	$minute_fin = afficher_minute($minute_f, "name='minute_f' size='1' class='fondl'", true);

//Creation des deux dates qui servent a l'insertion dans la base
	$date = $date1." ".$heure_d.":".$minute_d.":00";
	$date_redac= $date2." ".$heure_f.":".$minute_f.":00";

//Creation du champ de langue
	$lang = _request('var_lang');
	$nom = 'changer_lang';
	lang_select();
	$langues = liste_options_langues($nom, $lang);
	
	$s = spip_query("SELECT id_secteur, lang FROM spip_rubriques WHERE id_rubrique = '$id_rubrique' ");
	if ($r = spip_fetch_array($s)) {
		$id_secteur = $r["id_secteur"];
		$lang = $r["lang"];
	}

	$previsualiser = _request('previsualiser');
	$valider = _request('valider');
	
	$media=_request('media');
	// donnees pour formulaire document
	$formulaire_documents = stripslashes(_request('formulaire_documents'));
	$documents_actifs = array();
	$doc = stripslashes(_request('doc'));
	$type_doc 	= stripslashes(_request('type'));

	$previsu = '';
	$bouton= '';
	$erreur='';

	if (!$id_auteur_session) {
		return;
	}
	// statut de l'article, et formulaire de login en fonction de la configuration choisie
	if (($articles_publics == "abo") && (!$GLOBALS["auteur_session"])) {
		return array('formulaire_login_article', 0, array());
	}
	
	$statut= lire_config('FormulaireArticle/statut_article');	

	if (($previsualiser) || ($valider) || ($media)) {
		// Verifier si pas d'erreurs		
		// Sur le texte
		if (!$article) {
				$article = op_request_new_id($id_auteur_session);
		}
	}
	
	if($valider){
		if ((lire_config('FormulaireArticle/textearticle') == 'on') && (strlen($texte) < lire_config('FormulaireArticle/textetaille'))){
			$erreur .= _T('formulairearticle:forum_attention_texte_court');
			$erreur .= "<br />" ._T('formulairearticle:nb_caracteres_minimum'). lire_config('FormulaireArticle/textetaille');
		}
		// Si une erreur
		if ($erreur){
			return array('formulaires/formulaire_article', 0,
				array(
					'formulaire_document' => $formulaire_document,
					'erreur' => $erreur,
					'article' => $article,
					'langues' => $langues,
					'erreur' => $erreur,
					'date1' => $date1,
					'date2' => $date2,
					'heure_debut' => $heure_debut,
					'minute_debut' => $minute_debut,
					'heure_fin' => $heure_fin,
					'minute_fin' => $minute_fin,
					'previsu' => $previsu,
					'titre' => interdire_scripts(typo($titre)),
					'chapeau' => $chapeau,
					'texte' => $texte
			));
		}
		
		// Sinon integrer a la base de donnee
		spip_query("UPDATE spip_articles SET titre = "._q($titre).", id_rubrique = '$id_rubrique', texte = "._q($texte).", chapo = "._q($chapo).", statut = '$statut', lang = '$lang', id_secteur = '$id_secteur', date = '$date_modif', date_redac = '$date_redac', date_modif = '$date_modif', lang = '$var_lang' WHERE id_article = '$article'" );
		spip_log("insert article : -> $titre");

		// On shoot le cache
		include_spip ("inc/invalideur");
		suivre_invalideur("1");

		// On recharge la page
		if (!$retour){
			$retour =	generer_url_article($article);
		}
		return header("Location: $retour");
	}

	else{
		if($previsualiser){

		if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}

		$previsu = inclure_balise_dynamique(
			array(
				'formulaires/formulaire_article_previsu',
				0,
			array(
				'formulaire_document' => $formulaire_document,
				'article' => $article,
				'date' => $date,
				'date_redac' => $date_redac,
				'titre' => interdire_scripts(typo($titre)),
				'chapeau' => propre($chapeau),
				'texte' => propre($texte),
				'erreur' => $erreur,
				'id_rubrique' => $id_rubrique,
				'retour' => $retour,
				'bouton' => $bouton,
				)
			), false);
				$previsu = preg_replace("@<(/?)f(orm[>[:space:]])@ism",
				"<\\1no-f\\2", $previsu);
		}

		if($media) {
		
			// compatibilite php < 4.1
		
			if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
				
			// recuperation des variables
		
			$fichier = $_FILES['doc']['name'];
			$size = $_FILES['doc']['size'];
			$tmp = $_FILES['doc']['tmp_name'];
			$type = $_FILES['doc']['type'];
			$error = $_FILES['doc']['error'];
				
			// verification si upload OK
		
			if( !is_uploaded_file($tmp) ) {
				$error = _T('formulaire_article:erreur_upload');
				$erreur_document = 1;
			}
			else {
				$ok = 1;
		
				// ajout du document
		
				if ($ok==1) {
					inc_ajouter_documents_dist ($tmp, $fichier, "article", $article, $type_doc, $id_document, $documents_actifs);
				}
				else { // sinon, erreur
					$error = _T('opconfig:erreur_extension');
					$erreur_document = 1;
				}
			}
			$invalider = false;

			// supprimer des documents ?
			if (is_array(_request('supprimer')))
			foreach (_request('supprimer') as $supprimer) {
				if ($supprimer = intval($supprimer)
				AND $s = spip_query("SELECT * FROM spip_documents_articles WHERE id_article="._q($id_article)." AND id_document="._q($supprimer))
				AND $t = spip_fetch_array($s)) {
					include_spip('inc/documents');
					$s = spip_query("SELECT * FROM spip_documents WHERE id_document="._q($supprimer));
					$t = spip_fetch_array($s);
					unlink(copie_locale($t['fichier']));
					spip_query("DELETE FROM spip_documents_$articles WHERE id_document="._q($supprimer));
					spip_query("DELETE FROM spip_documents WHERE id_document="._q($supprimer));
					$invalider = true;
					spip_log("supprimer document (article)".$supprimer, 'upload');
				}
			}
			$retour = self();
	
			if ($invalider) {
				include_spip('inc/invalideur');
				suivre_invalideur("0",true);
				spip_log('invalider', 'upload');
			}
			
			// Gestion des documents
			$bouton_docs = _T('formulairearticle:add_docs');
			$formulaire_documents = inclure_balise_dynamique(
			array('formulaires/formulaire_documents',	0,
				array(
					'id_article' => $article,
					'bouton' => $bouton_docs,
				)
			), false);
			
		return array('formulaires/formulaire_article', 0,
		array(
				'formulaire_documents' => $formulaire_documents,
				'article' => $article,
				'langues' => $langues,
				'erreur' => $erreur,
				'date1' => $date1,
				'date2' => $date2,
				'heure_debut' => $heure_debut,
				'minute_debut' => $minute_debut,
				'heure_fin' => $heure_fin,
				'minute_fin' => $minute_fin,
				'previsu' => $previsu,
				'titre' => interdire_scripts(typo($titre)),
				'chapeau' => $chapeau,
				'retour' => $retour,
				'texte' => $texte
		));
		}
			
		// Gestion des documents
			$bouton_docs = _T('formulairearticle:add_docs');
			$formulaire_documents = inclure_balise_dynamique(
			array('formulaires/formulaire_documents',	0,
				array(
					'id_article' => $article,
					'bouton' => $bouton_docs,
				)
			), false);
			
		return array('formulaires/formulaire_article', 0,
		array(
				'formulaire_documents' => $formulaire_documents,
				'article' => $article,
				'langues' => $langues,
				'erreur' => $erreur,
				'date1' => $date1,
				'date2' => $date2,
				'heure_debut' => $heure_debut,
				'minute_debut' => $minute_debut,
				'heure_fin' => $heure_fin,
				'minute_fin' => $minute_fin,
				'previsu' => $previsu,
				'titre' => interdire_scripts(typo($titre)),
				'chapeau' => $chapeau,
				'retour' => $retour,
				'texte' => $texte
		));
	}
}

// Fonction qui permet de creer un faux nouvel article qui nous donne ensuite la valeur de l'id_article si pas dispo
function op_request_new_id($id_auteur_session){
	
	// Le statut sera en propose
    $statut_nouv='prepa';
	// Savoir si on autorise les forums sur l'article
    $forums_publics = substr(lire_meta('forums_publics'),0,3);
	// On crŽe un faux article avec la date de maintenant
    spip_query("INSERT INTO spip_articles (statut, date, accepter_forum) VALUES ( '$statut_nouv', NOW(), '$forums_publics')");
    $article = mysql_insert_id();
    spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur_session, $article)");

    return $article;
}
?>