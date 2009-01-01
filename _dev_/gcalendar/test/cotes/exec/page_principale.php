<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
charger_generer_url();


function exec_page_principale()
{
	
	global $connect_statut;
  if ($connect_statut != "0minirezo" ) {
	 echo "<p><strong>"._T('acces_a_la_page')."</strong></p>";
	 fin_page();
	 exit;
  }

	traiter_post();

 	pipeline('exec_init',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('Plugin_cote'), "naviguer", "articles");

	debut_gauche();
	debut_boite_info();
	echo "<strong>"._T('cotes:bonjour').$GLOBALS['auteur_session']['nom']."</strong><br />";
	echo _T('cotes:intro_page_principale');
	fin_boite_info();
	
	// formulaire de création de classe
		echo "<br />";
		echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/folder-new-small.png', true, "", _T('cotes:titre_creer_classe'));

		echo "<form name='creer_classe' action='?exec=page_principale' method='post' class='verdana1'>";
		echo "<strong>"._T('cotes:titre_et_descriptif')."</strong><input type='text' name='nom' value=''  class='formo' />";
		echo "<input type='hidden' name='action' value='creer_classe' />";
   		echo "<textarea rows='4' name='descriptif' class='formo'></textarea>";	
   		echo "<input type='submit' value='"._T("cotes:ajouter")."' class='fondo' />";
		echo "</form>";
		echo fin_cadre_couleur(true);
	

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	
	creer_colonne_droite();
	
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();

gros_titre(_T('cotes:page_principale'));
		
	// liste des classes
	$resultat=spip_query("SELECT * FROM spip_cotes_classes ORDER BY id_classe DESC");
	if ($resultat) { 
		echo "<br />";
		// liste des classes existantes
		//print_r($resultat);
		echo "<script type='text/javascript'>function confirmer_supprimer(form) { if (confirm( '"._T('cotes:confirmer_suppression')."' )) { return true; } else { return false; } } </script>";
			

		while ($row=spip_fetch_array($resultat)) {
			echo debut_cadre_relief(_DIR_PLUGIN_COTES.'/img_pack/folder-open-small.png', true, "", $row['nom']);
			echo "<p>".$row['descriptif']."</p>";
		
		// boite des raccourcis
		$res = icone_horizontale(_T('cotes:gerer_les_etudiants_classe'), generer_url_ecrire("page_etudiants", "id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		$res .= icone_horizontale(_T('cotes:gerer_les_exercices_classe'), generer_url_ecrire("page_exercices", "id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);
		$res .= icone_horizontale(_T('cotes:envoi_mail_collectif_classe'), generer_url_ecrire("page_mail_collectif", "id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);
		
		
		$res .="<hr />";
		
		// liste des exercices
		$resultat2=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_classe=".$row['id_classe']);
		if ($resultat2) {
			while ($row2=spip_fetch_array($resultat2)) {
			$res .=icone_horizontale(_T('cotes:coter').$row2['titre'], generer_url_ecrire("page_cotes", "id_exercice=".$row2['id_exercice']."&id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/accessories-text-editor.png", '',false);	
			}
		}
		// fin de liste des exerices
		
		
		echo bloc_des_raccourcis($res);

		$texte = "<form action='".generer_url_ecrire('page_principale')."' method='post'>\n"
  		."<input type='hidden' name='action' value='modifier_classe' />"
		."<input type='hidden' name='id_classe' value='".$row['id_classe']."' />"
		."<p><strong>"._T('cotes:titre_et_descriptif')."<strong><input type=\"text\" name=\"nom\" style=\"width: 100%\" value=\"".htmlspecialchars($row['nom'])."\" /><br />"
   		."<textarea rows=\"8\" cols=\"20\" style=\"width: 100%\" name=\"descriptif\">".htmlspecialchars($row['descriptif'])."</textarea><br />"
   		."<br /><input type='submit' value='"._T("cotes:modifier_cette_classe")."' class='fondo' />"
  		."</p></form>"
  		
  		
  		.debut_boite_alerte(_DIR_PLUGIN_COTES.'/img_pack/folder-open-small.png', true, "", $row['nom'])
  		."<p><strong>"._T('cotes:supprimer_classe')."</strong></ p>"
  		."<p>"._T('cotes:supprimer_classe_avertissement')."</ p>"
  		."<form action=\"?exec=page_principale\" method=\"post\" onsubmit=\"return confirmer_supprimer(this);\">"
		."<input type='hidden' name='action' value='supprimer_classe' />"
		."<input type='hidden' name='id_classe' value='".$row['id_classe']."' />"
		."<br /><input type='submit' value='"._T("cotes:supprimer_classe")."' class='fondo' />"
		.fin_boite_alerte(true)
		."</form>";
  		
  		
		echo block_parfois_visible("classe".$row['id_classe'], _T('cotes:modifier_supprimer_cette_classe'), $texte, '', FALSE);


			echo fin_cadre_relief(true);

		} 
		
	
	} 
	
		
	

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_page'),'data'=>''));

	echo fin_gauche(), fin_page();
}

function traiter_post() {


	// creer une classe
	if ($_POST['action']=='creer_classe' && isset($_POST['nom'])) {
		
		$nom=trim(addslashes($_POST['nom']));
		$descriptif=trim(addslashes($_POST['descriptif']));
		spip_query("INSERT INTO spip_cotes_classes (nom, descriptif) VALUES ('$nom', '$descriptif')");
		
	}
	
	// modifier une classe
	if ($_POST['action']=='modifier_classe' && isset($_POST['nom'])) {
		$id_classe=(int) $_POST['id_classe'];
		$nom=trim(addslashes($_POST['nom']));
		$descriptif=trim(addslashes($_POST['descriptif']));

		spip_query("UPDATE spip_cotes_classes SET nom = '$nom', descriptif ='$descriptif' WHERE id_classe =".$id_classe);
		
	}
	
	// supprimer une classe
	if ($_POST['action']=='supprimer_classe' && isset($_POST['id_classe'])) {
		$id_classe=(int) $_POST['id_classe'];
		spip_query("DELETE FROM spip_cotes_classes WHERE id_classe = ".$id_classe);
	}

}


?>
