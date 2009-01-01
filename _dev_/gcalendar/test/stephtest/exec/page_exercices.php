<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
charger_generer_url();



function exec_page_exercices()
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
	echo $commencer_page(_T('titre_page_articles_page'), "naviguer", "articles");

	debut_gauche();
	debut_boite_info();
	echo "<strong>"._T('cotes:bonjour').$GLOBALS['auteur_session']['nom']."</strong><br />";
	echo _T('cotes:intro_page_exercices');
	fin_boite_info();
	
	$id_classe=$_GET['id_classe'];

// formulaire de création d'exercice
		echo "<br />";
		echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/document-new.png', true, "", _T('cotes:titre_creer_exercice'));

		echo "<form name='creer_classe' action='".generer_url_ecrire('page_exercices', "id_classe=$id_classe")."' method='post' class='verdana1'>";
		echo "<strong>"._T('cotes:titre_et_descriptif')."</strong><input type='text' name='titre' class='formo' value='' /><br />";
		echo "<input type='hidden' name='action' value='creer_exercice' />";
   		echo "<textarea rows='4' class='formo' name='description'></textarea><br />";
   		
   		echo "<strong>"._T('cotes:cote_maximale')."</strong><input type='text' name='cote_max' class='formo' value='' /><br />";
		echo "<strong>"._T('cotes:facteur')."</strong><input type='text' name='facteur' class='formo' value='' /><br />";
		echo "<input type='hidden' name='id_classe' value='".$id_classe."' />";

   		echo "<input type='submit' value='"._T("cotes:ajouter")."' class='fondo' />";
		echo "</form>";
		echo fin_cadre_couleur(true);
			
	creer_colonne_droite();
	
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();

gros_titre(_T('cotes:page_exercices'));
		
		// cherche le nom de la classe
		$resultat=spip_query("SELECT * FROM spip_cotes_classes WHERE id_classe=".$id_classe);
		if ($resultat) {
			$row=spip_fetch_array($resultat);
			echo "<p>Exercice de la classe ".$row['nom']."</p>";
		}
		
		$rac = icone_horizontale(_T('cotes:retour_page_classe'), generer_url_ecrire("page_principale"), _DIR_PLUGIN_COTES."/img_pack/edit-undo.png", '',false);
		$rac .= icone_horizontale(_T('cotes:gerer_les_etudiants_classe'), generer_url_ecrire("page_etudiants", "id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:envoi_mail_collectif_classe'), generer_url_ecrire("page_mail_collectif", "id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);

			echo bloc_des_raccourcis($rac);
	// liste des classes
	
	$resultat=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_classe=".$id_classe);
	if ($resultat) { 
		echo "<br />";
		
		echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/text-x-generic-small.png', true, "", _T('cotes:liste_des_exercices'));
		
		// liste des exercices existants
		echo "<script type='text/javascript'>function confirmer_supprimer(form) { if (confirm( '"._T('cotes:confirmer_suppression')."' )) { return true; } else { return false; } } </script>";
			

		while ($row=spip_fetch_array($resultat)) {
			echo debut_cadre_relief("", true, "", $row['titre']);
			echo "<div class='verdana1'>".$row['description']."<br />";
			echo "<strong>"._T('cotes:cote_maximale')." : </strong>".$row['cote_max']."<br />";
			echo "<strong>"._T('cotes:facteur')." : </strong>".$row['facteur']."</div>";
			
		
		// boite des raccourcis
		$res = icone_horizontale(_T('cotes:gerer_les_cotes_exercice'), generer_url_ecrire("page_cotes", "id_classe=$id_classe&id_exercice=".$row['id_exercice']), _DIR_PLUGIN_COTES."/img_pack/accessories-text-editor.png", '',false);		
		$res .= icone_horizontale(_T('cotes:gestion_mail_exercice'), generer_url_ecrire("page_mail", "id_classe=$id_classe&id_exercice=".$row['id_exercice']), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		
		echo bloc_des_raccourcis($res);
		
		$texte = "<form action='".generer_url_ecrire('page_exercices', "id_classe=$id_classe")."' method='post' class='verdana1'>\n"
  		."<input type='hidden' name='action' value='modifier_exercice' />"
		."<input type='hidden' name='id_classe' value='".$row['id_classe']."' />"
		."<input type='hidden' name='id_exercice' value='".$row['id_exercice']."' />"
		."<p><strong>"._T('cotes:titre_et_descriptif')."</strong><input type='text' name='titre' class='forml' value='".htmlspecialchars($row['titre'])."' /><br />"
   		."<textarea rows='4' class='forml' name='description'>".htmlspecialchars($row['description'])."</textarea><br />"
   		
   		."<strong>"._T('cotes:cote_maximale')."</strong><input type='text' name='cote_max' class='forml' value='".$row['cote_max']."' /><br />"
		."<strong>"._T('cotes:facteur')."</strong><input type='text' name='facteur' class='forml' value='".$row['facteur']."' />"
   		
   		."<br /><input type='submit' value='"._T("cotes:modifier_cet_exercice")."' class='fondo' />"
  		."</p></form>"
  		
  		.debut_boite_alerte(_DIR_PLUGIN_COTES.'/img_pack/folder-open-small.png', true, "", $row['titre'])
  		."<p><strong>"._T('cotes:supprimer_exercice')."</strong></ p>"
  		."<p>"._T('cotes:supprimer_exercice_avertissement')."</ p>"
  		."<form action='".generer_url_ecrire('page_exercices', "id_classe=$id_classe")."' method='post' onsubmit='return confirmer_supprimer(this);'>"
		."<input type='hidden' name='action' value='supprimer_exercice' />"
		."<input type='hidden' name='id_exercice' value='".$row['id_exercice']."' />"
		."<br /><input type='submit' value='"._T("cotes:supprimer_exercice")."' class='fondo' />"
		.fin_boite_alerte(true)
		."</form>";
  		
		echo block_parfois_visible("exercice".$row['id_exercice'], _T('cotes:modifier_supprimer_cet_exercice'), $texte, '', FALSE);


			echo fin_cadre_relief(true);

		} 
		echo fin_cadre_couleur(true);
	} 

	echo fin_gauche(), fin_page();
}

function traiter_post() {


	// creer un exercice
	if ($_POST['action']=='creer_exercice' && isset($_POST['titre'])) {
		
		$nom=trim(addslashes($_POST['titre']));
		$descriptif=trim(addslashes($_POST['description']));
		$id_classe=(int) $_POST['id_classe'];
		$facteur=(int) $_POST['facteur'];
		$cote_max=(int) $_POST['cote_max'];

		
		spip_query("INSERT INTO spip_cotes_exercices (titre, description, id_classe, cote_max, facteur) VALUES ('$nom', '$descriptif', $id_classe, $cote_max, $facteur)");
		
	}
	
	// modifier un exercice
	if ($_POST['action']=='modifier_exercice' && isset($_POST['titre'])) {
		//print_r($_POST);
		$nom=trim(addslashes($_POST['titre']));
		$descriptif=trim(addslashes($_POST['description']));
		$id_classe=(int) $_POST['id_classe'];
		$facteur=(int) $_POST['facteur'];
		$cote_max=(int) $_POST['cote_max'];
		$id_exercice=(int) $_POST['id_exercice'];

		spip_query("UPDATE spip_cotes_exercices SET titre = '$nom', description ='$descriptif', id_classe = $id_classe, facteur = $facteur, cote_max = $cote_max WHERE id_exercice = ".$id_exercice);
		
	}
	
	// supprimer un exercice
	if ($_POST['action']=='supprimer_exercice' && isset($_POST['id_exercice'])) {
		$id_exercice=(int) $_POST['id_exercice'];
		spip_query("DELETE FROM spip_cotes_exercices WHERE id_exercice = ".$id_exercice);
	}

}


?>
