<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/date');
include_spip('inc/texte');

function balise_FORMULAIRE_AJOUTER_ARTICLE($p) {
  return calculer_balise_dynamique($p,'FORMULAIRE_AJOUTER_ARTICLE',array('id_rubrique','id_secteur','lang'));
}

function balise_FORMULAIRE_AJOUTER_ARTICLE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0] && !$args[3])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_AJOUTER_ARTICLE',
					'motif' => 'RUBRIQUES')), '');

	// Verifier que les visisteurs sont autorises a proposer un article
	if ($args[3]){
		// retourver le secteur et la langue de la rubrique
		$s = spip_query("SELECT id_secteur, lang FROM spip_rubriques WHERE id_rubrique =".intval($args[3]));
		if ($r = spip_fetch_array($s)) {
		    return (array($args[3],$r["id_secteur"],$r["lang"]));
		}
		else
			return erreur_squelette(
				_T('zbug_champ_hors_motif',
					array ('champ' => '#FORMULAIRE_AJOUTER_ARTICLE',
						'motif' => 'RUBRIQUES')), '');
		
		
	}
	else
	    return (array($args[0],$args[1],$args[2]));
}

function balise_FORMULAIRE_AJOUTER_ARTICLE_dyn($id_rubrique,$id_secteur,$lang) {
//	global $REMOTE_ADDR, $afficher_texte, $_COOKIE, $_POST;
	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	if ((!$GLOBALS["auteur_session"])
	||(($GLOBALS['profil_etendu_articles_visiteurs']!='oui') && ($auteur_statut != "1comite") && ($auteur_statut != "0minirezo"))
	||(($GLOBALS['profil_etendu_articles_visiteurs']=='oui') && ($auteur_statut != "6forum") && ($auteur_statut != "1comite") 
	&& ($auteur_statut != "0minirezo"))) {
		return '';//array('formulaire_login_abo', 0, array());
	}

	//$document_root="/var/alternc/dns/o/nomdedomaine";
	$document_root=$_SERVER['DOCUMENT_ROOT'];

	$gerer_surtitre = (lire_meta("articles_surtitre")=='oui');
	$gerer_soustitre = (lire_meta("articles_soustitre")=='oui');
	$gerer_descriptif = (lire_meta("articles_descriptif")=='oui');
	$gerer_urlref = (lire_meta("articles_urlref")=='oui');
	$gerer_chapeau = (lire_meta("articles_chapeau")=='oui');
	$gerer_ps = (lire_meta("articles_ps")=='oui');
	$gerer_redac = (lire_meta("articles_redac")=='oui');
	$gerer_mots = (lire_meta("articles_mots")=='oui');
	$gerer_modif = (lire_meta("articles_modif")=='oui');
	$gerer_lang = (lire_meta("multi_articles")=='oui');
	
	// url de reference
		$url = self();

	// Recuperer les variables d'upload
	if (!is_array($_FILES))
		$_FILES = array();
	foreach ($_FILES as $id => $file) {
		if ($file['error'] == 4 /* UPLOAD_ERR_NO_FILE */)
			unset ($_FILES[$id]);
	}
	
	
	if ($gerer_surtitre) $surtitre= stripslashes(_request('surtitre'));	
	$titre= stripslashes(_request('titre'));
	if ($gerer_soustitre) $soustitre= stripslashes(_request('soustitre'));
	if ($gerer_descriptif) $descriptif= stripslashes(_request('descriptif'));
	if ($gerer_chapeau) $chapo= stripslashes(_request('chapo'));
	$texte= stripslashes(_request('texte'));
	if ($gerer_ps) $ps= stripslashes(_request('ps'));
	if ($gerer_urlref) {
		$lien_titre= stripslashes(_request('lien_titre'));
		$lien_url= stripslashes(_request('lien_url'));
	}
	if(_request('jour')||_request('mois')||_request('annee')){
		$annee= stripslashes(_request('annee'));
		$mois= stripslashes(_request('mois'));
		$jour= stripslashes(_request('jour'));
	}else{
		$time=time();
		$annee=date('Y',$time);
		$mois=date('m',$time);
		$jour=date('d',$time);
	}
	if ($GLOBALS["auteur_session"]) {
		$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
		$nom_auteur_session = $GLOBALS['auteur_session']['nom'];
		$email_auteur_session = $GLOBALS['auteur_session']['email'];
	} else {
		$nom_auteur_session= _request('nom_auteur_session');
		$email_auteur_session= _request('email_auteur_session');
	}
	
//	$id_rubrique= _request('id_rubrique');
	
//	$lang = _request('var_lang');	
	$nom = 'changer_lang';
	lang_dselect();
	$langues = liste_options_langues($nom, $lang);
	
	
	$previsualiser= _request('previsualiser');
	$valider= _request('valider');
	
	$previsu = '';
	$bouton= '';

	//TODO statut de l'article, et formulaire de login en fonction de la configuration choisie
	//return $auteur_statut;
		$statut= $GLOBALS['profil_etendu_statut_article'];

	
	$formulaire_date = format_mysql_date($annee, $mois, $jour);
	
	if($valider){
		// int�grer � la base de donn�es
		$time=time();
		$date_redac=date('Y-m-d H:i:s',$time);
		
		$surtitre= addslashes($surtitre);	
		$titre= addslashes($titre);
		$soustitre= addslashes($soustitre);
		$descriptif= addslashes($descriptif);
		$chapo= addslashes($chapo);
		$texte= addslashes($texte);
		$ps= addslashes($ps);
		$lien_titre= addslashes($lien_titre);
		$lien_url= addslashes($lien_url);
		
		
		// ajouter l'article (sans auteur) dans la base: statut: 'prop' ou 'publie'
$id_article=spip_abstract_insert('spip_articles',
					'(surtitre, titre, soustitre, id_rubrique, descriptif, chapo, texte, ps, statut, id_secteur, accepter_forum, date_modif, lang, date, date_redac, id_version, nom_site, url_site)',
					"('$surtitre', '$titre', '$soustitre', '$id_rubrique', '$descriptif', '$chapo', '$texte', '$ps', '$statut', '$id_secteur', 'pos', '$formulaire_date', '$lang',  '$formulaire_date', '$date_redac', '1', '$lien_titre', '$lien_url')");

		// ajouter l'auteur
		if($id_auteur_session != 0){
			spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($id_auteur_session, $id_article)");
		}
		//renomer les logos provisoires de l'auteur en logos de l'article
		if(_request("fic_logo1")) 
			rename($document_root."/"._request("fic_logo1"), $document_root."/IMG/arton".$id_article.strrchr(_request("fic_logo1"),'.'));
		if(_request("fic_logo2")) 
			rename($document_root."/"._request("fic_logo2"), $document_root."/IMG/artoff".$id_article.strrchr(_request("fic_logo2"),'.'));
				
		include_ecrire("inc_mail.php3");
		envoyer_mail_proposition($id_article);
		return  _T('form_prop_enregistre');
	} else {
		if($previsualiser)
		{
		$imglogo1="";
		$imglogo2="";
		if ($_FILES['logo1']['name']){
			$ext1=strrchr($_FILES['logo1']['name'],'.');
			$myFile1 = $document_root."/IMG/logo1".$id_auteur_session.$ext1;
			if (file_exists($myFile1)) @unlink($myFile1);	
//			spip_log($_FILES['logo1']['tmp_name']."=>".$myFile1);
			rename($_FILES['logo1']['tmp_name'], $myFile1);
			$imglogo1="IMG/logo1".$id_auteur_session.$ext1;
		}
		if ($_FILES['logo2']['name']){
			$ext2=strrchr($_FILES['logo2']['name'],'.');
			$myFile2 = $document_root."/IMG/logo2".$id_auteur_session.$ext2;
			if (file_exists($myFile2)) @unlink($myFile2);	
//			spip_log($_FILES['logo2']['tmp_name']."=>".$myFile2);
			rename($_FILES['logo2']['tmp_name'], $myFile2);
			$imglogo2="IMG/logo2".$id_auteur_session.$ext2;
		}

		if (strlen($titre) < 3){$erreur .= _T('forum_attention_trois_caracteres');}
		if(!$erreur){$bouton= _T('form_prop_confirmer_envoi');}

		if ($imglogo1=="")$imglogo1=_request("fic_logo1");
		if ($imglogo2=="")$imglogo2=_request("fic_logo2");
//		$img_logo1="<img src=\"".$imglogo1."\" />";
//		$img_logo2="<img src=\"".$imglogo2."\" />";
		$img_logo="<img src=\"".$imglogo1."\" alt='".addslashes($titre)."' width=\"105\" height=\"105\" onmouseover=\"this.src='".$imglogo2."'\" onmouseout=\"this.src='".$imglogo1."'\" style=\"border-width: 0px;\" align=\"left\" class=\"spip_logos\" />";
		$input_logo1="<input name=\"fic_logo1\" type=\"hidden\" value=\"".$imglogo1."\" />";
		$input_logo2="<input name=\"fic_logo2\" type=\"hidden\" value=\"".$imglogo2."\" />";
//		spip_log("previsu :".$img_logo);
		$previsu = inclure_balise_dynamique(
			array(
				'formulaires/formulaire_ajouter_article_previsu'.$GLOBALS['profil_etendu_type_formulaire'],
				0,
				array(
					'surtitre' => interdire_scripts(typo($surtitre)),
					'titre' => interdire_scripts(typo($titre)),
					'soustitre' => interdire_scripts(typo($soustitre)),
					'descriptif' => propre($descriptif),
					'chapo' => propre($chapo),
					'texte' => propre($texte),
					'ps' => propre($ps),
					'lien_titre' => $lien_titre,
					'lien_url' => $lien_url,
					'erreur' => $erreur,
					'bouton' => $bouton,
					'date_redac' => $formulaire_date, 
					'auteur' => $nom_auteur_session,
					'logo' => $img_logo,
				)
			), false);
				$previsu = preg_replace("@<(/?)f(orm[>[:space:]])@ism",
				"<\\1no-f\\2", $previsu);
		}

		return array('formulaires/formulaire_ajouter_article'.$GLOBALS['profil_etendu_type_formulaire'], 0,
		array(
					'logo1'=>formlogo('logo1'),
					'logo2'=>formlogo('logo2'),
					'imglogo1'=>$input_logo1,
					'imglogo2'=>$input_logo2,
					'annee'=>$annee,
					'mois'=>$mois,
					'jour'=>$jour,
					'url' =>  $url,
					'langues' => $langues,
					'previsu' => $previsu,
					'surtitre' => $surtitre,
					'titre' => $titre,
					'soustitre' => $soustitre,
					'descriptif' => $descriptif,
					'chapo' => $chapo,
					'texte' => $texte,
					'ps' => $ps,
					'lien_titre' => $lien_titre,
					'lien_url' => $lien_url,
					'id_rubrique' => $id_rubrique,
					'id_secteur' => $id_secteur,
					'id_auteur_session' => $id_auteur_session,
					'nom_auteur_session' => $nom_auteur_session,
					'email_auteur_session' => $email_auteur_session,
					'gerer_surtitre' => $gerer_surtitre?' ':'',
					'gerer_soustitre' => $gerer_soustitre?' ':'',
					'gerer_descriptif' => $gerer_descriptif?' ':'',
					'gerer_urlref' => $gerer_urlref?' ':'',
					'gerer_chapeau' => $gerer_chapeau?' ':'',
					'gerer_ps' => $gerer_ps?' ':'',
					'gerer_lang' => $gerer_lang?' ':'',
					'gerer_redac' => $gerer_redac?' ':''
//					'gerer_mots' => $gerer_mots?' ':'',
			));
	}

}



function formlogo($name='image')
{
		$returned.="\n<input id='$name' name='$name' type='file' class='forml' SIZE=15>";
		return $returned;	
}

?>
