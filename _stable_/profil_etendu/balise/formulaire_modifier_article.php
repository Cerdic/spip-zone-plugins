<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
//include_spip("inc/logos");


function balise_FORMULAIRE_MODIFIER_ARTICLE($p) {
  return calculer_balise_dynamique($p,'FORMULAIRE_MODIFIER_ARTICLE',array('id_article','id_rubrique','statut'));
}

function balise_FORMULAIRE_MODIFIER_ARTICLE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0] && !$args[1])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_MODIFIER_ARTICLE',
					'motif' => 'ARTICLES')), '');

	if ($args[3])
	    return (array($args[3],$args[4],$args[5]));
	else
	    return (array($args[0],$args[1],$args[2]));
}

function balise_FORMULAIRE_MODIFIER_ARTICLE_dyn($id_article, $id_rubrique, $statut_article) {
//$document_root="/var/alternc/dns/c/www.chatignoux.org";
$document_root=$_SERVER['DOCUMENT_ROOT'];
if (!$GLOBALS["auteur_session"]) return '';
$message='';
$page=addslashes(_request('page'));
$id_article_edit=intval(_request('id_article_edit'));
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
	if (!$GLOBALS["auteur_session"]) 
		return array('formulaires/formulaire_afficher_article'.$GLOBALS['profil_etendu_type_formulaire'], 60, 
					array(
						'id_article' => $id_article,
						'message' => $message,
						'statut_article' => 'publie',
						'gerer_surtitre' => $gerer_surtitre?' ':'',
						'gerer_soustitre' => $gerer_soustitre?' ':'',
						'gerer_descriptif' => $gerer_descriptif?' ':'',
						'gerer_urlref' => $gerer_urlref?' ':'',
						'gerer_chapeau' => $gerer_chapeau?' ':'',
						'gerer_ps' => $gerer_ps?' ':'',
						'gerer_lang' => $gerer_lang?' ':'',
						'gerer_redac' => $gerer_redac?' ':'',
						'gerer_mots' => $gerer_mots?' ':'',
				));
	
	global $connect_toutes_rubriques,$connect_id_rubrique;
	$gestion_droits=(lire_meta('gestion_droits')=="oui");
	$auteur_statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session = $GLOBALS['auteur_session']['id_auteur'];
	$flag_auteur = false;
	
/*	include_ecrire("inc_auth.php3");

	if ($gestion_droits) {
		if (($auteur_statut=='0minirezo') && $connect_toutes_rubriques){
				$gestion_droits=false;
				$droit_vue='Tout';
				$droit_modif='Non';
		}
		else{

			$query = "SELECT auteurs.droit_vue,auteurs.droit_modif FROM spip_auteurs_articles as articles,spip_auteurs as auteurs WHERE articles.id_auteur=auteurs.id_auteur AND articles.id_article=".$id_article." AND articles.id_auteur=".$id_auteur_session;
			$result_auteur = spip_query($query);
			$flag_auteur = (spip_num_rows($result_auteur) > 0);
			if ($row=spip_fetch_array($result_auteur)){
				$droit_vue=$row['droit_vue'];
				$droit_modif=$row['droit_modif'];
			}else{
				$droit_vue='Rien';
				$droit_modif='Non';
			}
		}
	}
	else {
*/		$query = "SELECT * FROM spip_auteurs_articles WHERE id_article=".$id_article." AND id_auteur=".$id_auteur_session;
		$result_auteur = spip_query($query);
		$flag_auteur = (spip_num_rows($result_auteur) > 0);
//	}

	$flag_modif= ((($connect_toutes_rubriques OR $connect_id_rubrique[$id_rubrique]) && ($auteur_statut=='0minirezo'))
	OR ($flag_auteur AND ($statut_article == 'prepa' OR $statut_article == 'prop' OR $statut_article == 'poubelle'))
//	OR ($flag_auteur AND ($statut_article == 'publie') and ($droit_modif=='Oui')));
);
	$form_prefixe= 'fma'.$id_article.'_';
	$valider= (_request($form_prefixe.'valider')=='oui');
	$editer= (_request($form_prefixe.'editer')=='oui');
    if($page) $hidden .= "<input type='hidden' name='page' value='".$page."'/>";
	if($id_article_edit) $hidden .= "<input type='hidden' name='id_article_edit' value='".$id_article_edit."'/>";
	else $hidden .= "<input type='hidden' name='id_article' value='".$id_article."'/>";
	if ($flag_modif){ 
//		$hidden .= "<input type='hidden' name='".$form_prefixe."editer' value='oui'/>";
		$hidden .= "<input type='hidden' name='".$form_prefixe."valider' value='oui'/>";
//if ($id_article_edit)
		$fma_form="<form action='".parametre_url(parametre_url(self(),"id_article_edit",""),$form_prefixe."editer","")."' method='POST' enctype='multipart/form-data'>".$hidden;
//else
//		$fma_form="<form action='spip.php?page=".$page."&id_article=".$id_article."' method='POST' enctype='multipart/form-data'>".$hidden;
		$url_edit=parametre_url(self(),$form_prefixe."editer","oui");
//		$url_edit=parametre_url($url_edit,$form_prefixe."valider","oui");
	}
	if (($flag_modif)&&($valider)){ 
		$surtitre= addslashes(stripslashes(_request($form_prefixe.'surtitre')));	
		$titre= addslashes(stripslashes(_request($form_prefixe.'titre')));
		$soustitre= addslashes(stripslashes(_request($form_prefixe.'soustitre')));
		$descriptif= addslashes(stripslashes(_request($form_prefixe.'descriptif')));
		$chapo= addslashes(stripslashes(_request($form_prefixe.'chapo')));
		$texte= addslashes(stripslashes(_request($form_prefixe.'texte')));
		$ps= addslashes(stripslashes(_request($form_prefixe.'ps')));
		$nom_site= addslashes(stripslashes(_request($form_prefixe.'lien_titre')));
		$url_site= addslashes(stripslashes(_request($form_prefixe.'lien_url')));
		$annee= addslashes(stripslashes(_request($form_prefixe.'annee_date')));
		$mois= addslashes(stripslashes(_request($form_prefixe.'mois_date')));
		$jour= addslashes(stripslashes(_request($form_prefixe.'jour_date')));
		$date = format_mysql_date($annee, $mois, $jour);
		$annee_redac= addslashes(stripslashes(_request($form_prefixe.'annee_date_redac')));
		$mois_redac= addslashes(stripslashes(_request($form_prefixe.'mois_date_redac')));
		$jour_redac= addslashes(stripslashes(_request($form_prefixe.'jour_date_redac')));
		$date_redac = format_mysql_date($annee_redac, $mois_redac, $jour_redac);
		$lang = addslashes(stripslashes(_request($form_prefixe.'lang')));	
		// int�grer � la base de donn�es
		$time=time();
		$date_redac=date('Y-m-d H:i:s',$time);
		$oldFile1 = addslashes(stripslashes(_request($form_prefixe.'oldFile1')));	
		$oldFile2 = addslashes(stripslashes(_request($form_prefixe.'oldFile2')));	
		$statut_article = addslashes(stripslashes(_request($form_prefixe.'statut_article')));	
		
		// modifier l'article
		$query="update `spip_articles` SET";
		$query.=" `titre`='".$titre."',";
		if ($gerer_surtitre) $query.=" `surtitre`='".$surtitre."',";
		if ($gerer_soustitre) $query.=" `soustitre`='".$soustitre."',";
		//$query.=" `id_rubrique`=".$id_rubrique.",";
		if ($gerer_descriptif) $query.=" `descriptif`='".$descriptif."',";
		if ($gerer_chapeau) $query.=" `chapo`='".$chapo."',";
		$query.=" `texte`='".$texte."',";
		if ($gerer_ps) $query.=" `ps`='".$ps."',";
		if ($gerer_lang) $query.=" `lang`='".$lang."',";
		$query.=" `date`='".$date."',";
		if ($gerer_redac) $query.=" `date_redac`='".$date_redac."',";
		if ($gerer_urlref) {
			$query.=" `nom_site`='".$nom_site."',";
			$query.=" `url_site`='".$url_site."',";
		}
		if (($statut_article) && (!((($statut_article=='publie')||($statut_article=='publie'))&&($auteur_statut!='0minirezo')))) 
			$query.=" `statut`='".$statut_article."',";
		$query.=" `date_modif`=now()";
		$query.=" where `id_article`=".$id_article;

		spip_query($query);

		if (!is_array($_FILES))
			$_FILES = array();
		foreach ($_FILES as $id => $file) {
			if ($file['error'] == 4 )
				unset ($_FILES[$id]);
		}
		$imglogo1="";
		$imglogo2="";

		if ($_FILES['logo1']['name']){
			$ext1=strrchr($_FILES['logo1']['name'],'.');
			$myFile1 = $document_root."/IMG/arton".$id_article.$ext1;
			if (file_exists($myFile1)) @unlink($myFile1);	
			rename($_FILES['logo1']['tmp_name'], $myFile1);
		}
		if ($_FILES['logo2']['name']){
			$ext2=strrchr($_FILES['logo2']['name'],'.');
			$myFile2 = $document_root."/IMG/artoff".$id_article.$ext2;
			if (file_exists($myFile2)) @unlink($myFile2);	
			rename($_FILES['logo2']['tmp_name'], $myFile2);
		}
				
		include_spip("inc/mail");
		envoyer_mail_proposition($id_article);
		$message.=_T('form_'.$statut_article.'_enregistre');
		
	}
	
	if (($flag_modif)&&($editer)){
		return array('formulaires/formulaire_modifier_article'.$GLOBALS['profil_etendu_type_formulaire'], 0,
			array('id_article' => $id_article,
					'message' => $message,
					'statut_article' => $statut_article,
					'fma_form' => $fma_form,
					'form_prefixe' => $form_prefixe,
					'gerer_surtitre' => $gerer_surtitre?' ':'',
					'gerer_soustitre' => $gerer_soustitre?' ':'',
					'gerer_descriptif' => $gerer_descriptif?' ':'',
					'gerer_urlref' => $gerer_urlref?' ':'',
					'gerer_chapeau' => $gerer_chapeau?' ':'',
					'gerer_ps' => $gerer_ps?' ':'',
					'gerer_lang' => $gerer_lang?' ':'',
					'gerer_redac' => $gerer_redac?' ':'',
					'gerer_mots' => $gerer_mots?' ':'',
					'gerer_modif' => $gerer_modif?' ':'',
					'id_auteur_session'=> $id_auteur_session,
					'auteur_statut' => $auteur_statut,
					'redirect_url'=>$url_edit
			));
	}
	return array('formulaires/formulaire_afficher_article'.$GLOBALS['profil_etendu_type_formulaire'], 0, 
//	return array('formulaires/formulaire_modifier_article', 60, 
					array('id_article' => $id_article,
						'statut_article' => $statut_article,
						'message' => $message,
						'url_modifier_article' => $url_edit,
						'gerer_surtitre' => $gerer_surtitre?' ':'',
						'gerer_soustitre' => $gerer_soustitre?' ':'',
						'gerer_descriptif' => $gerer_descriptif?' ':'',
						'gerer_urlref' => $gerer_urlref?' ':'',
						'gerer_chapeau' => $gerer_chapeau?' ':'',
						'gerer_ps' => $gerer_ps?' ':'',
						'gerer_lang' => $gerer_lang?' ':'',
						'gerer_redac' => $gerer_redac?' ':'',
						'gerer_mots' => $gerer_mots?' ':'',
						'gerer_modif' => $gerer_modif?' ':''
		));

}
function generer_liste_statuts($statut_article,$auteur_statut){
	$returned="<option value=\"prepa\"".(($statut_article=='prepa')?' selected':'')." style='background-color: white'>"._T('texte_statut_en_cours_redaction')."\n";
	$returned.="<option value=\"prop\"".(($statut_article=='prop')?' selected':'')." style='background-color: #FFF1C6'>"._T('texte_statut_propose_evaluation')."\n";
	if ($auteur_statut=='0minirezo')
		$returned.="<option value=\"publie\"".(($statut_article=='publie')?' selected':'')." style='background-color: #B4E8C5'>"._T('texte_statut_publie')."\n";
	$returned.="<option value=\"poubelle\"".(($statut_article=='poubelle')?' selected':'')." style='background: url(ecrire/img_pack/rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n";
	if ($auteur_statut=='0minirezo')
		$returned.="<option value=\"refuse\"".(($statut_article=='refuse')?' selected':'')." style='background-color: #FFA4A4'>"._T('texte_statut_refuse')."\n";
return $returned;
}
?>