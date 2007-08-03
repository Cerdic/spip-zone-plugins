<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/texte");
include_spip ("inc/session");
include_spip ("inc/filtres");
include_spip('base/abstract_sql');
include_spip('inc/article_select');
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

function balise_FORMULAIRE_FORUM_stat($args, $filtres) {

	// Note : ceci n'est pas documente !!
	// $filtres[0] peut contenir l'url sur lequel faire tourner le formulaire
	// exemple dans un squelette article.html : [(#FORMULAIRE_FORUM|forum)]
	// ou encore [(#FORMULAIRE_FORUM|forumspip.php)]

	// le denier arg peut contenir l'url sur lequel faire le retour
	// exemple dans un squelette article.html : [(#FORMULAIRE_FORUM{#SELF})]

	// recuperer les donnees du forum auquel on repond.
	$id_article = article_select($id_article);
	$s = spip_query("SELECT * FROM spip_articles WHERE id_article=$id_article");
	$row = spip_fetch_array($s);

	return
		$row;
}

function balise_FORMULAIRE_MODIF_ARTICLE_dyn($row) {
	$s = spip_query("SELECT * FROM spip_articles WHERE id_article=$row");
	$row = spip_fetch_array($s);

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

	// statut de l'article, et formulaire de login en fonction de la configuration choisie
	if (($articles_publics == "abo") && (!$GLOBALS["auteur_session"])) {
		return header("Location: ".parametre_url(generer_url_public('article'),'id_article',$id_article,'&'));
	}
	elseif ($articles_publics == "pos") {
		$statut= "publie";
	}
	else{
		$statut= "publie";
	}

	if ($valider || $previsualiser){
		$titre = _request('titre');
		$soustitre = _request('soustitre');
		$chapo = _request('chapo');
		$texte = _request('texte');
	}
	if($valider){
		// integrer a la base de donnee		
		// ajouter l'article (sans auteur) dans la base: statut: 'prop' ou 'publie'
		spip_query("UPDATE spip_articles SET soustitre = "._q($soustitre).", texte = "._q($texte).", chapo = "._q($chapo)." WHERE id_article = '$id_article'" );

		// On shoot le cache
		include_spip ("inc/invalideur");
		suivre_invalideur("1");

		// On recharge la page
		return header("Location: ".parametre_url(generer_url_public('article'),'id_article',$id_article,'&'));
	}

	else{
		if($previsualiser)
		{

		if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}

		$previsu = inclure_balise_dynamique(
			array(
				'formulaire_modif_article_previsu',
				0,
			array(
				'titre' => propre($titre),
				'soustitre' => propre($soustitre),
				'chapo' => propre($chapo),
				'texte' => propre($texte),
				'erreur' => $erreur,
				'bouton' => $bouton
				)
			), false);
				$previsu = preg_replace("@<(/?)f(orm[>[:space:]])@ism",
				"<\\1no-f\\2", $previsu);
		}

		return array('formulaire_modif_article', 0,
		array(
				'langues' => $langues,
				'previsu' => $previsu,
				'titre' => $titre,
				'soustitre' => propre($soustitre),
				'chapo' => $chapo,
				'texte' => $texte
		));
	}
}

function propre_article ($texte) {
	return ereg_replace('"','&nbsp;', $texte);
}


?>