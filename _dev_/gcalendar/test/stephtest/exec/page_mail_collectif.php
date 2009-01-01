<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
charger_generer_url();

function exec_page_mail_collectif()
{
	
	global $connect_statut;
  if ($connect_statut != "0minirezo" ) {
	 echo "<p><strong>"._T('acces_a_la_page')."</strong></p>";
	 fin_page();
	 exit;
  }

	$message=traiter_post();
	

 	pipeline('exec_init',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_articles_page'), "naviguer", "articles");

	debut_gauche();
		debut_boite_info();
	echo "<strong>"._T('cotes:bonjour').$GLOBALS['auteur_session']['nom']."</strong><br />";
	echo _T('cotes:intro_page_mail_collectif');
	fin_boite_info();

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	
	creer_colonne_droite();
	
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();

$id_classe=$_GET['id_classe'];
$id_exercice=$_GET['id_exercice'];

gros_titre(_T('cotes:page_mail_collectif'));
if ($message) {
echo "<p class=\"message\">".$message."</p>";
}
		
		// cherche le nom de la classe
		$resultat=spip_query("SELECT * FROM spip_cotes_classes WHERE id_classe=".$id_classe);
		if ($resultat) {
			$row=spip_fetch_array($resultat);
		}
		
		$resultat2=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_exercice=".$id_exercice);
		if ($resultat2) {
			$row2=spip_fetch_array($resultat2);
		}
		
		$rac = icone_horizontale(_T('cotes:retour_page_classe'), generer_url_ecrire("page_principale"), _DIR_PLUGIN_COTES."/img_pack/edit-undo.png", '',false);
		$rac .= icone_horizontale(_T('cotes:gerer_les_etudiants_classe'), generer_url_ecrire("page_etudiants", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		
		$rac .="<hr />";
		
		// liste des exercices
		$resultat2=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_classe=".$id_classe);
		if ($resultat2) {
			while ($row2=spip_fetch_array($resultat2)) {
			$rac .=icone_horizontale(_T('cotes:coter').$row2['titre'], generer_url_ecrire("page_cotes", "id_exercice=".$row2['id_exercice']."&id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/accessories-text-editor.png", '',false);	
			}
		}
		
		echo bloc_des_raccourcis($rac);
		echo "<br />";
	// liste des etudiants
	echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/folder-new-small.png', true, "", _T('cotes:composer_mail').$row['nom']);
	
	echo "<form action='".generer_url_ecrire('page_mail_collectif')."&id_classe=$id_classe' method='post' class='verdana1'>\n";
	echo "<strong>"._T('cotes:mail_retour')."</strong> <input type='text' name='mail_retour' class='forml' value='".$GLOBALS['auteur_session']['nom']." <".$GLOBALS['auteur_session']['email'].">' />\n";
	echo "<strong>"._T('cotes:sujet_et_texte')."</strong><br /><input type='text' name='mail_sujet' class='formo' />\n";
	echo "<textarea rows='8' cols='20' class='formo' name='corps_mail'></textarea>\n";
	echo "<input type='hidden' name='action' value='envoyer_mail_collectif' />\n";
	echo "<input type='hidden' name='id_classe' value='".$id_classe."' />\n";
	echo "<input type='submit' value='"._T("cotes:envoyer_mail")."' class='fondo' />\n";
	echo "</form>\n";
	echo fin_cadre_couleur(true);
		
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_page'),'data'=>''));

	echo fin_gauche(), fin_page();
}

function traiter_post() {
	//print_r($_POST);
	// dans classe : le nom de la classe
	

	// traitement de tous les mails
	if ($_POST['action']=='envoyer_mail_collectif' && isset($_POST['id_classe'])) {
	
	$classe=(int) $_POST['id_classe'];
	
	$resultat=spip_query("SELECT * FROM spip_cotes_classes WHERE id_classe=".$classe);
	$info_classe=spip_fetch_array($resultat);
	$from=addslashes($_POST['mail_retour']);
	$sujet=addslashes($_POST['mail_sujet']);
	$lecorps=htmlspecialchars($_POST['corps_mail']);
	
	include_spip("inc/mail");	
		
	$message="<strong>Mail envoyé à :</strong><br />"; 
	$liste_class=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_classe=".$classe);
	while ($row=spip_fetch_array($liste_class)) {
	
		if ($row['mail']) {
		$to=$row['prenom']." ".$row['nom']." <".$row['mail'].">";
		$go=envoyer_mail($to,$sujet,$lecorps,$from);
		$message .="".$row['prenom']." ".$row['nom']." (".$row['mail'].")<br />"; 
		}
		
	}
	
	}	
	return $message;

}

?>
