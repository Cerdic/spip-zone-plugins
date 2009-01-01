<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
charger_generer_url();

function exec_page_mail()
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
	echo _T('cotes:intro_page_mail');
	fin_boite_info();
	
	creer_colonne_droite();
	
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();

$id_classe=$_GET['id_classe'];
$id_exercice=$_GET['id_exercice'];

gros_titre(_T('cotes:page_mail'));
if ($message) {
echo "<p class='message'>".$message."</p>";
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
		$rac .=icone_horizontale(_T('cotes:coter').$row2['titre'], generer_url_ecrire("page_cotes", "id_exercice=".$id_exercice."&id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/accessories-text-editor.png", '',false);
		$rac .= icone_horizontale(_T('cotes:gerer_les_etudiants_classe'), generer_url_ecrire("page_etudiants", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		$rac .= icone_horizontale(_T('cotes:envoi_mail_collectif_classe'), generer_url_ecrire("page_mail_collectif", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);
		
		$rac .="<hr />";
		
		// liste des exercices
		$resultat3=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_classe=".$id_classe);
		if ($resultat3) {
			while ($row3=spip_fetch_array($resultat3)) {
			if($row['id_classe'] != $id_classe) {
			$rac .=icone_horizontale(_T('cotes:coter').$row3['titre'], generer_url_ecrire("page_cotes", "id_exercice=".$row3['id_exercice']."&id_classe=".$row['id_classe']), _DIR_PLUGIN_COTES."/img_pack/accessories-text-editor.png", '',false);	
			}
			}
		}
		// fin de liste des exerices
		
			echo bloc_des_raccourcis($rac);
	// liste des etudiants
	
	?>
	<br />
	
	<script type="text/javascript">
	function selectionne(laclasse){
		$("#mail_selecteur input" + laclasse).attr("checked","true");
	}
	
	function deselectionne() {
		$("#mail_selecteur input").removeAttr("checked");
	}
	
	</script>
	<?php debut_boite_info(); ?>
	<a href="javascript:selectionne('.coteur<?php echo $GLOBALS['auteur_session']['id_auteur']; ?>')">Sélectionner mes cotes</a> - <a href="javascript:selectionne('.nonenvoye')">sélectionner les cotes non envoyées</a> - <a href="javascript:deselectionne()">Tout déselectionner</a>
	<?php fin_boite_info(); ?>
	<br />
	<?php
	
				
		echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/text-x-generic-small.png', true, "", _T('cotes:exercice')." ".$row2['titre']);
		echo "<form action='".generer_url_ecrire('page_mail', 'id_exercice='.$row2['id_exercice']."&id_classe=".$row['id_classe'])."' method='post' id='mail_selecteur'>\n";

		$resultat=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_classe=".$id_classe." ORDER BY nom");
		if ($resultat) { 
			
			// texte général du mail
			$general=spip_query("SELECT * FROM spip_cotes_mails WHERE type='commentaire_general' AND id=".$id_exercice);
			if ($general) {
				$general=spip_fetch_array($general);
				if ($general){
					debut_boite_info();
					echo "<strong>"._T("cotes:commentaire_general")."</strong><br />";
					echo htmlspecialchars($general['contenu']);
					fin_boite_info();
					echo "<br />";
					}
				}
			
			// liste des cotes avec bouton à cocher
			while ($row=spip_fetch_array($resultat)) {
				$class_checkbox="";
				$cote_exist=spip_query("SELECT * FROM spip_cotes_cotes WHERE id_etudiant=".$row['id_etudiant']." AND id_exercice=".$row2['id_exercice']." AND id_classe=".$id_classe);
				if ($cote_exist) {
					$cote_exist=spip_fetch_array($cote_exist);
					$class_checkbox .="coteur".$cote_exist['id_coteur']." ";
					if ($cote_exist['envoi_cote']) {
						$class_checkbox .="envoye";
					} else { $class_checkbox .="nonenvoye"; }
				}
				
				echo debut_cadre_relief("", true, "", "<input type='checkbox' name='etudiant[]' value='".$row['id_etudiant']."' class='".$class_checkbox."' />".$row['nom']." ".$row['prenom']." <span class=\"mailmail\">(".$row['mail'].")</span>");
				// recupere la cote pour l'afficher
				
				if($cote_exist){
					if(ereg("\.0$",$cote_exist['cote'],$regs)){
					// enleve le '.0' disgracieux
					$cote_exist['cote'] = ereg_replace("\.0$","",$cote_exist['cote']);
					}
					$coteur=spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur=".$cote_exist['id_coteur']);
					$coteur=spip_fetch_array($coteur);
					echo "<strong>Coté par : </strong>".$coteur['nom']."\n";
				}
				if($cote_exist['commentaire']){
					$texte="<strong>Cote : </strong>".$cote_exist['cote']."/".$row2['cote_max']."\n"
					."<br /><strong>Commentaire : </strong>".stripslashes(nl2br($cote_exist['commentaire']));
					echo block_parfois_visible("etudiant".$row['id_etudiant'], "Détails", $texte, '', FALSE);
				}
				
				if ($cote_exist['envoi_cote']) {
					echo "<span><strong>"._T('cotes:dernier_envoi_mail')."</strong> : ".affdate($cote_exist['envoi_cote'])."</span>\n";
				} else { echo "<strong>"._T('cotes:cote_pas_encore_envoyee')."</strong>"; }
				 
				echo fin_cadre_relief(true);
				echo "<br />";
			}
			echo "<input type='hidden' name='id_exercice' value='".$id_exercice."' />\n";
			echo "<input type='hidden' name='id_classe' value='".$id_classe."' />\n";
			echo "<input type='hidden' name='action' value='envoi_cotes_choisies' />\n";
			echo "<input type='submit' value='"._T("cotes:envoyer_mail")."' class='fondo' />";
			echo "</form>";
			} 
		echo fin_cadre_couleur(true);		
		
	echo fin_gauche(), fin_page();
}

function traiter_post() {
	//print_r($_POST);
	
	// securite
		$id_exercice=(int) $_POST['id_exercice'];
		$id_classe = (int) $_POST['id_classe'];
	
	// dans exercice : intitulé et titre de l'exercice
	$exercice=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_exercice=".$id_exercice);
	$exercice=spip_fetch_array($exercice);
	
	// dans classe : le nom de la classe
	$classe=spip_query("SELECT * FROM spip_cotes_classes WHERE id_classe=".$id_classe);
	$classe=spip_fetch_array($classe);
	
	// commentaire general de l'exercice
	$general=spip_query("SELECT * FROM spip_cotes_mails WHERE type='commentaire_general' AND id=".$_POST['id_exercice']);
	if ($general) {
	$general=spip_fetch_array($general);
		$commentaire_general=$general['contenu'];
	} else { $commentaire_general=""; }
			
	if ($_POST['action']=='envoi_cotes_choisies' && isset($_POST['etudiant']) && isset($_POST['id_exercice'])) {
		$message="";
		include_spip("inc/mail");
		
		foreach($_POST['etudiant'] as $etudiant) {
			$etudiant=(int) $etudiant;
			
			$info_etudiant=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_etudiant=".$etudiant);
			if ($info_etudiant) {
				$info_etudiant=spip_fetch_array($info_etudiant);
			}
			
			$cote_et_comment=spip_query("SELECT * FROM spip_cotes_cotes WHERE id_etudiant=".$etudiant." AND id_exercice=".$id_exercice." AND id_classe=".$id_classe);
			if ($cote_et_comment) {
				$cote_et_comment=spip_fetch_array($cote_et_comment);
				
				// coteur
				$coteur=spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=".$cote_et_comment['id_coteur']);
				$coteur=spip_fetch_array($coteur);
				
				// ok, on a tout, mailons.
				if(ereg("\.0$",$cote_et_comment['cote'],$regs)){
				// enleve le '.0' disgracieux
				$cote_et_comment['cote'] = ereg_replace("\.0$","",$cote_et_comment['cote']);
				}
				
				$to = $info_etudiant['nom']." ".$info_etudiant['prenom']." <".$info_etudiant['mail'].">";
				$from=$coteur['nom']." <".$coteur['email'].">";
				$sujet ="[Soap narration] - ".$exercice['titre'];
				//$lecorps="<div style='font-family:arial,verdana,sans-serif'>";
				$lecorps= _T('cotes:il_concerne')."\n".$exercice['titre']."\n\n";
				$lecorps.=_T("cotes:commentaire_mail_general")." : \n".stripslashes($commentaire_general)."\n\n";
				
				if($exercice['cote_max']){
					if ($cote_et_comment['cote']) {
						if(ereg("\.00$",$cote_et_comment['cote'],$regs)){
						// enleve le '.0' disgracieux
						$cote_et_comment['cote'] = ereg_replace("\.00$","",$cote_et_comment['cote']);
						}
						$lecorps.=_T("cotes:votre_cote")." ".$cote_et_comment['cote']."/".$exercice['cote_max']."\n\n";
					}
				}
				
				$lecorps.= _T("cotes:commentaire_mail")." ".$coteur['nom']." : \n".stripslashes($cote_et_comment['commentaire'])."\n\n";
				
				$lecorps.= _T("cotes:voir_page_recapitulative")." http://www.blogs.erg.be/dramaturgie/spip.php?page=recapitulatif_cotes&id_classe=".$id_classe."&id_exercice=".$id_exercice;
				$lecorps.= "\n\nSi ce mail est une erreur, signalez-le-nous par retour de mail.";
				
				// n'envoie que si le commentaire existe et si la cote n'est pas rien ou zero
				if( intval($cote_et_comment['cote'])!="0" || strlen($cote_et_comment['commentaire']) > 2 ){
				
				$go=envoyer_mail($to,$sujet,$lecorps,$from);
				$message .="Mail envoyé à ".$to.". Expediteur : ".$from."<br />";			
				
				// entrer dans la db
				$date = date("Y-m-d H:m:s");
				spip_query("UPDATE spip_cotes_cotes SET envoi_cote = '".$date."' WHERE id_etudiant=".$etudiant." AND id_exercice=".$id_exercice." AND id_classe=".$id_classe);
				} else { 
					$message .="Mail NON envoyé à ".$to." : pas de cote ou de commentaire<br />";
				}			
				
			}
			
		}
	}
	
	return $message;
}

?>
