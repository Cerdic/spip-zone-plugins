<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/texte");
include_spip ("inc/session");
include_spip ("inc/filtres");
include_spip('base/abstract_sql');
include_spip('inc/article_select');
include_spip ("inc/documents");
include_spip ("inc/ajouter_documents");
include_spip ("inc/getdocument");
spip_connect();

charger_generer_url();

function balise_FORMULAIRE_MODIF_ARTICLE ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_MODIF_ARTICLE', array('id_article'));
}

function balise_FORMULAIRE_MODIF_ARTICLE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_MODIF_ARTICLE',
					'motif' => 'ARTICLES')), '');
	return ($args);
}

function balise_FORMULAIRE_MODIF_ARTICLE_dyn($id_article,$url) {
	global $_FILES, $_HTTP_POST_FILES;
					
	$s = spip_query("SELECT * FROM spip_articles WHERE id_article=$id_article");
	$row = spip_fetch_array($s);
	
	if ($url){
		$retour = str_replace('&amp;', '&', $url);
	}

	$auteur_statut=$GLOBALS["auteur_session"]["statut"];

	$articles_publics = $GLOBALS['articles_publics'];
	
	// url de reference
	$id_article = $row['id_article'];
	$titre = $row['titre'];
	$soustitre = $row['soustitre'];
	$chapo = $row['chapo'];
	$texte = $row['texte'];
	
	$lien_titre= stripslashes(_request('lien_titre'));
	$lien_url= stripslashes(_request('lien_url'));
	
	$var_lang = _request('var_lang');
	$date_aujd = date('Y-m-d H:i:s');

	if ($GLOBALS["auteur_session"]) {
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
		$nom_auteur_session = $GLOBALS['auteur_session']['nom'];
		$email_auteur_session = $GLOBALS['auteur_session']['email'];
	}

//Creation du champ de langue
	$lang = _request('var_lang');
	$nom = 'changer_lang';
	lang_dselect();
	$langues = liste_options_langues($nom, $lang);
	
	// retrouver le secteur et la langue de la rubrique
	$s = spip_query("SELECT id_secteur, lang FROM spip_rubriques WHERE id_rubrique = '$id_rubrique' ");
	if ($r = spip_fetch_array($s)) {
		$id_secteur = $r["id_secteur"];
		$lang = $r["lang"];
		}
	
	$previsualiser= _request('previsualiser');
	$valider= _request('valider');
	
	$previsu = '';
	$bouton= '';
	$erreur='';

	$media=_request('media');
	// donnees pour formulaire document
	$formulaire_documents = stripslashes(_request('formulaire_documents'));
	$documents_actifs = array();
	$doc = stripslashes(_request('doc'));
	$type_doc 	= stripslashes(_request('type'));

	if (!$id_auteur_session) {
		return;
	}
			
	if ($valider || $previsualiser){
		$titre = _request('titre');
		$soustitre = _request('soustitre');
		$chapo = _request('chapo');
		$texte = _request('texte');

		// Verifier si pas d'erreurs		
		// Sur le texte
		if ((lire_config('FormulaireArticle/textearticle') == 'on') && (strlen($texte) < lire_config('FormulaireArticle/textetaille'))){
			$erreur .= _T('formulairearticle:forum_attention_texte_court');
			$erreur .= "<br />" ._T('formulairearticle:nb_caracteres_minimum')."".lire_config('FormulaireArticle/textetaille');
		}
	}
	
	if($valider){
				
		// Si une erreur
		if ($erreur){
			return array('formulaires/formulaire_modif_article', 0,
				array(
					'formulaire_documents' => $formulaire_documents,
					'langues' => $langues,
					'titre' => $titre,
					'erreur' => $erreur,
					'soustitre' => propre($soustitre),
					'retour' => $retour,
					'chapo' => $chapo,
					'texte' => $texte
			));
		}
		// integrer a la base de donnee		
		// ajouter l'article (sans auteur) dans la base: statut: 'prop' ou 'publie'
		spip_query("UPDATE spip_articles SET soustitre = "._q($soustitre).", texte = "._q($texte).", chapo = "._q($chapo)." WHERE id_article = '$id_article'" );

		// On shoot le cache
		include_spip ("inc/invalideur");
		suivre_invalideur("1");

		// On recharge la page
		if (!$retour){
			$retour =	parametre_url(generer_url_public('article'),'id_article',$id_article,'&');
		}
		return header("Location: $retour");
	}

	else{
		if($previsualiser){

			if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}

			$previsu = inclure_balise_dynamique(
				array(
					'formulaires/formulaire_modif_article_previsu',
					0,
				array(
					'formulaire_documents' => $formulaire_documents,
					'titre' => propre($titre),
					'soustitre' => propre($soustitre),
					'chapo' => propre($chapo),
					'texte' => propre($texte),
					'erreur' => $erreur,
					'retour' => $retour,
					'bouton' => $bouton
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
				spip_log("erreur  document (article)".$tmp, 'upload');
			}
			else {
				$ok = 1;
		
				// ajout du document
		
				if ($ok==1) {
					inc_ajouter_documents_dist ($tmp, $fichier, "article", $id_article, $type_doc, $id_document, $documents_actifs);
					spip_log("ajouter document (article)".$id_article, 'upload');
				}
				else { // sinon, erreur
					$error = _T('formulaire_article:erreur_extension');
					$erreur_document = 1;
					spip_log("erreur  document (article)".$error, 'upload');
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
					'id_article' => $id_article,
					'bouton' => $bouton_docs,
				)
			), false);
			
		return array('formulaires/formulaire_modif_article', 0,
		array(
				'formulaire_documents' => $formulaire_documents,
					'langues' => $langues,
					'titre' => $titre,
					'erreur' => $erreur,
					'soustitre' => propre($soustitre),
					'retour' => $retour,
					'chapo' => $chapo,
					'texte' => $texte
		));
		}
		
		// Gestion des documents
		$bouton_docs = _T('formulairearticle:add_docs');
		$formulaire_documents = inclure_balise_dynamique(
		array('formulaires/formulaire_documents',	0,
			array(
				'id_article' => $id_article,
				'bouton' => $bouton_docs,
			)
		), false);
			
		return array('formulaires/formulaire_modif_article', 0,
		array(
				'formulaire_documents' => $formulaire_documents,
				'langues' => $langues,
				'id_article' => $id_article,
				'titre' => $titre,
				'erreur' => $erreur,
				'soustitre' => propre($soustitre),
				'retour' => $retour,
				'chapo' => $chapo,
				'texte' => $texte
		));
	}
}
?>