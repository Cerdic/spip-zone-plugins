<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/getdocument');
include_spip('inc/upload-image-cotes');
charger_generer_url();



function exec_page_etudiants()
{
	
	global $connect_statut;
  if ($connect_statut != "0minirezo" ) {
	 echo "<p><strong>"._T('acces_a_la_page')."</strong></p>";
	 fin_page();
	 exit;
  }

	traiter_post();
	
	$id_classe=$_GET['id_classe'];
	
 	pipeline('exec_init',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_articles_page'), "naviguer", "articles");

	debut_gauche();
	
	debut_boite_info();
	echo "<strong>"._T('cotes:bonjour').$GLOBALS['auteur_session']['nom']."</strong><br />";
	echo _T('cotes:intro_page_etudiants');
	fin_boite_info();
	echo "<br />";
	// recuperation d'après champ multiligne
	echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/contact-new-small.png', true, "", _T('cotes:entree_multiple'));
	echo "<p class='verdana1'>"._T('cotes:intro_entree_multiple_intro')."</p>";
	
	echo "<form action='".generer_url_ecrire("page_etudiants", "id_classe=$id_classe")."' method='post' class='verdana1'>\n";
	
	echo "<input type='hidden' name='id_classe' value='".$id_classe."' />";
	
	echo "<textarea rows='8' class='formo' name='donnees'></textarea><br />";
	echo "<input type='hidden' name='action' value='ajout_multiple' />";
	echo "<input type='submit' value='"._T('cotes:ajouter')."' class='fondo' /><br />";
	echo "</form>\n";
	echo fin_cadre_couleur(true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	
	creer_colonne_droite();
	
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();

	$resultat=spip_query("SELECT * FROM spip_cotes_classes WHERE id_classe=".$id_classe);
		if ($resultat) {
			$row=spip_fetch_array($resultat);
		}

gros_titre(_T('cotes:page_etudiants')." ".$row['nom']);		
		// cherche le nom de la classe
		
		
		$rac = icone_horizontale(_T('cotes:retour_page_classe'), generer_url_ecrire("page_principale"), _DIR_PLUGIN_COTES."/img_pack/edit-undo.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:gerer_les_exercices_classe'), generer_url_ecrire("page_exercices", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:envoi_mail_collectif_classe'), generer_url_ecrire("page_mail_collectif", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);
		
		$rac .="<hr />";
		
		// liste des exercices
		$resultat2=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_classe=".$id_classe);
		if ($resultat2) {
			while ($row2=spip_fetch_array($resultat2)) {
			$rac .=icone_horizontale(_T('cotes:coter').$row2['titre'], generer_url_ecrire("page_cotes", "id_exercice=".$row2['id_exercice']."&id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/accessories-text-editor.png", '',false);	
			}
		}
		// fin de liste des exerices
		

			echo bloc_des_raccourcis($rac);
	// liste des classes
	
	$resultat=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_classe=".$id_classe." ORDER BY nom");
	if ($resultat) { 
		echo "<br />";
		// liste des etudiants existants
		//print_r($resultat);
		echo "<script type='text/javascript'>function confirmer_supprimer(form) { if (confirm( '"._T('cotes:confirmer_suppression')."' )) { return true; } else { return false; } } </script>";

	echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/contact-new-small.png', true, "", _T('cotes:page_etudiants'));
	
	while ($row=spip_fetch_array($resultat)) {
	echo debut_cadre_relief("", true, "", " ".$row['nom']." ".$row['prenom']);
	// affichage du logo etudiant
	$chem = creer_repertoire_documents("illu-etudiant");
  	$handle = @opendir($chem); 
  	$logo = false;
  	while($fichier = @readdir($handle)) {
  	
    if (ereg("^illu_etudiant-".$row['id_etudiant']."\.(jpg|png|gif)$", $fichier)) {
      $logo = $fichier;
    }
  }
  if ($logo){
    // le nombre aléatoire permet d'éviter que le navigateur affiche la version en cache de l'image.
  	echo "<div style='padding-right:5px;float:right'><img width=\"190\" src=\""._DIR_DOC."illu-etudiant/$logo?".uniqid(rand())."\" />";
  	
  	echo "<form style='display:inline;' action='".generer_url_ecrire('page_etudiants', "id_classe=$id_classe")."' method='post'>\n"
		."<input type='hidden' name='action' value='supprimer_illu' />"
		."<input type='hidden' name='id_etudiant' value='".$row['id_etudiant']."' />"
		."<input type='submit' title='"._T("cotes:supprimer_illu")."' value='X' class='fondo' style='position:relative; border-color:red; background-color:red; left:-30px; font-size:10px;' /><br /><br />"
		."</form>";
  	
  	echo "</div>";
  	 
  } 
			echo "<p class='verdana1'>".$row['commentaire']."</p>";
			echo "<p class='verdana1'><strong>"._T('cotes:email')." : </strong>".$row['mail']."</p>";			
			
		// modifier l'etudiant, formulaire
		$texte = "<form action='".generer_url_ecrire('page_etudiants', "id_classe=$id_classe")."' method='post' enctype='multipart/form-data' style='clear:right;'  class='verdana1' >\n"
		
		."<strong>"._T('cotes:nom')."</strong><input type='text' name='nom' value='".htmlspecialchars($row['nom'])."' class='forml' /><br />"
		."<strong>"._T('cotes:prenom')."</strong><input type='text' name='prenom' class='forml' value='".htmlspecialchars($row['prenom'])."' /><br />"
   		."<strong>"._T('cotes:commentaire')."</strong><textarea rows='4' name='commentaire' class='forml'>".htmlspecialchars($row['commentaire'])."</textarea><br />"
   		."<strong>"._T('cotes:mail')."</strong><input type='text' name='mail' value='".htmlspecialchars($row['mail'])."' class='forml' /><br />"
		."<input type='hidden' name='id_etudiant' value='".$row['id_etudiant']."' />"
		."<input type='hidden' name='id_classe' value='".$id_classe."' />"

		."<input type='hidden' name='action' value='modifier_etudiant' />"		
		
		."<strong>"._T('cotes:bloc_image')."</strong><br />"
		."<input type='file' name='illu_etudiant' size='45' class='forml' />"

   		."<br /><input type='submit' value='"._T("cotes:modifier_cet_etudiant")."' class='fondo' />"
  		."</p></form>"
  		."<br /><br />"
  		
  		.debut_boite_alerte(_DIR_PLUGIN_COTES.'/img_pack/folder-open-small.png', true, "", $row['titre'])
  		."<p><strong>"._T('cotes:supprimer_etudiant')."</strong></ p>"
  		."<p>"._T('cotes:supprimer_etudiant_avertissement')."</ p>"
  		."<form action=\"".generer_url_ecrire('page_etudiants', "id_classe=$id_classe")."\" method=\"post\" onsubmit=\"return confirmer_supprimer(this);\">"
		."<input type='hidden' name='action' value='supprimer_etudiant' />"
		."<input type='hidden' name='id_etudiant' value='".$row['id_etudiant']."' />"
		."<br /><input type='submit' value='"._T("cotes:supprimer_etudiant")."' class='fondo' />"
		.fin_boite_alerte(true)
		."</form>";
  		
		echo block_parfois_visible("etudiant".$row['id_etudiant'], _T('cotes:modifier_supprimer_cet_etudiant'), $texte, '', FALSE);

		echo fin_cadre_relief(true);

		} 
		
	echo fin_cadre_couleur(true);
	} 
	
		// formulaire de création d'etudiant
		echo "<br />";
		echo debut_cadre_relief(_DIR_PLUGIN_COTES.'/img_pack/contact-new-small.png', true, "", _T('cotes:titre_creer_etudiant'));

		echo "<form name='creer_classe' action='".generer_url_ecrire('page_etudiants', "id_classe=$id_classe")."' method='post' enctype='multipart/form-data' class='verdana1'>";
		echo _T('cotes:nom')."<input type='text' name='nom' value='' class='forml' /><br />";
		echo _T('cotes:prenom')."<input type='text' name='prenom' class='forml' value='' /><br />";
   		echo _T('cotes:commentaire')."<textarea rows='4' class='forml' name='commentaire'></textarea><br />";
   		
   		echo _T('cotes:mail')."<input type='text' name='mail' class='forml' value='' /><br />";
		echo "<input type='hidden' name='id_classe' value='".$id_classe."' />";
		
	 echo _T('cotes:bloc_image')."<br />";

  echo "<input type='file' name='illu_etudiant' class='forml' /><br />";
		echo "<input type='hidden' name='action' value='creer_etudiant' />";
   		echo "<br /><input type='submit' value='"._T("cotes:ajouter")."' class='fondo' />";
		echo "</form>";
		echo fin_cadre_relief(true);
	

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_page'),'data'=>''));

	echo fin_gauche(), fin_page();
}

function traiter_post() {

	if ($_POST['action']=='ajout_multiple' && isset($_POST['id_classe']) ) {
	// envoi massif d'etudiants
	$id_classe=addslashes($_POST['id_classe']);
	
	$mecs=explode("\n",$_POST['donnees']);
	foreach($mecs as $mec) {
		$mec=explode(",",$mec);
		if ($mec[0] AND $mec[1]) {
		$nom=trim(addslashes($mec[0]));
		$prenom=trim(addslashes($mec[1]));
		$mail=trim(addslashes($mec[2]));
			spip_query("INSERT INTO spip_cotes_etudiants (nom, prenom, id_classe, mail) VALUES ('$nom', '$prenom', $id_classe, '$mail')");
		}
	}
	
	
	}


	// creer un etudiant
	if ($_POST['action']=='creer_etudiant' && isset($_POST['nom']) && isset($_POST['prenom'])) {
		
		
		$nom=trim(addslashes($_POST['nom']));
		$prenom=trim(addslashes($_POST['prenom']));
		$commentaire=trim(addslashes($_POST['commentaire']));
		$mail=trim(addslashes($_POST['mail']));

		$id_classe=(int) $_POST['id_classe'];
		
		spip_query("INSERT INTO spip_cotes_etudiants (nom, prenom, id_classe, commentaire, mail) VALUES ('$nom', '$prenom', $id_classe, '$commentaire', '$mail')");
		
		$result = spip_query("SELECT LAST_INSERT_ID();");
			 if ($result){
      				$arr = spip_fetch_array($result);
      				$id = (int) current($arr);
    				
    			if (!is_nan($id)){
      				$message_upload = traiter_upload_image_cotes('illu_etudiant','illu-etudiant',$id);
					}
				}	
		
	}
	
	// modifier un etudiant
	if ($_POST['action']=='modifier_etudiant' && isset($_POST['nom']) && isset($_POST['prenom'])) {
		//print_r($_POST);
		$nom=trim(addslashes($_POST['nom']));
		$prenom=trim(addslashes($_POST['prenom']));
		$commentaire=trim(addslashes($_POST['commentaire']));
		$mail=trim(addslashes($_POST['mail']));
		$id_etudiant=(int) $_POST['id_etudiant'];
		$message_upload = traiter_upload_image_cotes('illu_etudiant','illu-etudiant',$id_etudiant);
		spip_query("UPDATE spip_cotes_etudiants SET nom = '$nom', prenom ='$prenom', commentaire = '$commentaire', mail = '$mail' WHERE id_etudiant = ".$id_etudiant);
		
	}
	
	// supprimer un etudiant
	if ($_POST['action']=='supprimer_etudiant' && isset($_POST['id_etudiant'])) {
		$id_etudiant=(int) $_POST['id_etudiant'];
		spip_query("DELETE FROM spip_cotes_etudiants WHERE id_etudiant = ".$id_etudiant);
		traiter_suppression_illu_cotes($id_etudiant);

	}
	
	// supprimer l'illu de l'etudiant
	if ($_POST['action']=='supprimer_illu' && isset($_POST['id_etudiant'])) {
		$id_etudiant=(int) $_POST['id_etudiant'];
		traiter_suppression_illu_cotes($id_etudiant);
	}

}


?>
