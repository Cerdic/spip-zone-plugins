<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
$z = explode('/',end($p));
define('_DIR_PLUGIN_COTES',(_DIR_PLUGINS.stripslashes($z[0])));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
charger_generer_url();

function exec_page_cotes()
{
	
	global $connect_statut;
  if ($connect_statut != "0minirezo" ) {
	 echo "<p><strong>"._T('acces_a_la_page')."</strong></p>";
	 fin_page();
	 exit;
  }

	$message=traiter_post();
	
	$id_classe=$_GET['id_classe'];
	$id_exercice=$_GET['id_exercice'];
	$id_coteur=$GLOBALS['auteur_session']['id_auteur'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gestion_exercices'), "naviguer", "articles");

	debut_gauche();
	
	debut_boite_info();
	// bloc de listage des stats de cotes
	echo "<strong>"._T('cotes:bonjour').$GLOBALS['auteur_session']['nom']."</strong><br />";
	echo _T('cotes:intro_page_cotes');
	fin_boite_info();
	echo "<br />";
	debut_boite_info();
	echo "<strong>"._T('cotes:etudiants_non_cotes')."</strong><br /><br />";
	$toutcote=0;
	$etudiants=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_classe=".$id_classe." ORDER BY nom");
	$nb_etudiants=spip_num_rows($etudiants);
	$liste_etudiants="";
	if ($etudiants) { 
		// verifier si chaque etudiant a une cote
		while ($etudiant=spip_fetch_array($etudiants)) {
			$lacote=spip_query("SELECT * FROM spip_cotes_cotes WHERE id_exercice=".$id_exercice." AND id_etudiant=".$etudiant['id_etudiant']);
			
			
			if ($lacote){
			// cote est entree, verifier que cote et comment sont vides
			$lacote=spip_fetch_array($lacote);
			if($lacote['cote']==0 && $lacote['commentaire']=="") {
			$liste_etudiants .= "- ".$etudiant['nom']." ".$etudiant['prenom']."<br />\n";
			$toutcote++;
			}
			} else {
			// rien dans la db, c'est clair
			$liste_etudiants .= "- ".$etudiant['nom']." ".$etudiant['prenom']."<br />\n";
			$toutcote++;
			}
			
		}
		if ($toutcote == 0) { echo _T('cotes:tout_cote'." (".$toutcote."/".$nb_etudiants.")"); }
		if ($toutcote == $nb_etudiants) { echo _T('cotes:personne_cote'); } else { echo $liste_etudiants."<br />A coter : ".$toutcote."/".$nb_etudiants; }
	}
	
	
	fin_boite_info();

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'articles_page'),'data'=>''));
	
	creer_colonne_droite();
	
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();
gros_titre(_T('cotes:page_cotes'));

if ($message) {
echo "<div class='message'>".$message."</div>";
}
		
		// cherche le nom de la classe
		$classe=spip_query("SELECT * FROM spip_cotes_classes WHERE id_classe=".$id_classe);
		if ($classe) {
			$classe=spip_fetch_array($classe);
		}
		
		$exercice=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_exercice=".$id_exercice);
		if ($exercice) {
			$exercice=spip_fetch_array($exercice);
		}
		
		// bloc de raccourcis
		$rac = icone_horizontale(_T('cotes:retour_page_classe'), generer_url_ecrire("page_principale"), _DIR_PLUGIN_COTES."/img_pack/edit-undo.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:gerer_les_etudiants_classe'), generer_url_ecrire("page_etudiants", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:gestion_mail_exercice'), generer_url_ecrire("page_mail", "id_classe=".$id_classe."&id_exercice=".$id_exercice), _DIR_PLUGIN_COTES."/img_pack/contact-new-small.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:envoi_mail_collectif_classe'), generer_url_ecrire("page_mail_collectif", "id_classe=".$id_classe), _DIR_PLUGIN_COTES."/img_pack/document-properties-small.png", '',false);
		
		
		$rac .= "<hr />".icone_horizontale(_T('cotes:voir_page_recapitulative_imprimable'), "../spip.php?page=recapitulatif_exercice&id_classe=".$id_classe."&id_exercice=".$id_exercice, _DIR_PLUGIN_COTES."/img_pack/x-office-document.png", '',false);
		
		$rac .= icone_horizontale(_T('cotes:voir_page_recapitulative'), "../spip.php?page=recapitulatif_cotes&id_classe=".$id_classe."&id_exercice=".$id_exercice, _DIR_PLUGIN_COTES."/img_pack/x-office-document.png", '',false);
		
		echo bloc_des_raccourcis($rac);
		
	
	

		
	// liste des etudiants deroulante pour creation
	
	$etudiants=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_classe=".$id_classe." ORDER BY nom");
	if ($etudiants) { 
	
	echo "<br /><div id='etudiant-selecteur' class='verdana1'>\n";
	echo "<strong>"._T("cotes:selectionner_etudiant")."</strong> <select name='etudiants' id='etudiants'>";
	echo "<option value='none' class='non' > - - - - </option>";
	while ($row=spip_fetch_array($etudiants)) {
		echo "<option value='".$row['id_etudiant']."'>".$row['nom']." ".$row['prenom']."</option>";
	}
	echo "</select>";
	echo "<input type='submit' value='Ajouter' id='ajouter' />";
	echo "</div><br />\n";
		
		// liste des etudiants existants
		//print_r($resultat);
?>
<script type='text/javascript'>
	
$(document).ready(function(){
var ajout=0;

$('#ajouter').click(function(){
ajout=$('#etudiants option:selected').not(".rempli").not(".non").attr("value");
if(ajout.length){
$.ajax({
	  url: "?exec=afficheform&id_exercice=<?php echo $id_exercice; ?>&id_etudiant=" + ajout,
	  cache: false,
	  success: function(html){
	  	$('#form-etudiants').prepend(html);
	  	$('#etudiants option:selected').addClass("rempli");
	  	$('#etudiants option:selected').selected=false;
	  	$('#etudiants option:first').attr("selected","selected");
	  	$('.illu-etudiant' + ajout).click(function(){
	  		var largeur=$(this).children("img").css("width");
	  		if (largeur=="60px"){
	  		$(this).children("img").css("width","250px");
	  		} else {
	  			$(this).children("img").css("width","60px");
	  		}
			});
	  	}
	  });
}
return false;
});

});

function annule(lequel){
	if (confirm("Suppression de cette cotation ?")) { // Clic sur OK
	$("#form-etudiants .listageetudiant[@rel='" + lequel + "']").remove();
	$("#etudiants option[@value='" + lequel + "']").removeClass("rempli");
	}
}	
</script>
		
		<?php
		
		echo debut_cadre_couleur(_DIR_PLUGIN_COTES.'/img_pack/text-x-generic-small.png', true, "", _T('cotes:exercice')." : ".$exercice['titre']);
		
		echo "<form action='".generer_url_ecrire('page_cotes', 'id_exercice='.$id_exercice.'&id_classe='.$id_classe)."' method='post' class='verdana1'>\n";	
		// texte général du mail
	echo "<div>"._T("cotes:commentaire_general")."</div>";
		$general=spip_query("SELECT * FROM spip_cotes_mails WHERE type='commentaire_general' AND id=".$id_exercice);
	if ($general) {
	$general=spip_fetch_array($general);
		$commentaire_general=$general['contenu'];
	} else { $commentaire_general=""; }
			echo "<textarea name='commentaire_general' rows='5' class='forml'>".$commentaire_general."</textarea>";
			echo "<br />\n";
		
		
		// recepteur ajax
		echo "<div id='form-etudiants'></div>";
		
		// reste de l'info
		echo "<input type='hidden' name='cote_action' value='sauver_cotes' />\n";
		echo "<input type='hidden' name='id_classe' value='".$id_classe."' />\n";
		echo "<input type='hidden' name='id_exercice' value='".$id_exercice."' />\n";
		echo "<input type='hidden' name='coteur' value='".$id_coteur."' />";
		echo "<input type='submit' value='"._T("cotes:sauvez_les_cotes")."' class='fondo' />";
		echo "</form>";
	} 
	echo fin_cadre_couleur(true);	

	echo fin_gauche(), fin_page();
}

function traiter_post() {
		//print_r($_POST);
		$message="";
		if ($_POST['cote_action']=='sauver_cotes') {
		$id_exercice=(int) $_POST['id_exercice'];
		$id_classe=(int) $_POST['id_classe'];
		$id_coteur=(int) $_POST['coteur'];
		$cote=$_POST['cote'];
		$commentaire=$_POST['commentaire'];
		
		$commentaire_general=addslashes($_POST['commentaire_general']);
		$date = date("Y-m-d H:m:s");
		// commentaire general
		$controle_exist=spip_query("SELECT * FROM spip_cotes_mails WHERE type='commentaire_general' AND id=$id_exercice");
		if (spip_fetch_array($controle_exist)) {
				spip_query("UPDATE spip_cotes_mails SET contenu ='$commentaire_general' WHERE type = 'commentaire_general' AND id=$id_exercice");	
		} else {
				spip_query("INSERT INTO spip_cotes_mails (type, id, contenu, date) VALUES ('commentaire_general',$id_exercice, '$commentaire_general', '$date')");
		}
		
		//print_r($commentaire);
		foreach($_POST['etudiant'] as $id_etudiant){
			// pour chaque etudiant entre
			$id_etudiant=(int) $id_etudiant;
			$cote[$id_etudiant] = addslashes($cote[$id_etudiant]);
			if (!$cote[$id_etudiant]) { $cote[$id_etudiant]=0; }
			$commentaire[$id_etudiant] = htmlspecialchars($commentaire[$id_etudiant]);
			$date_comment=date("Y-m-d H:m:s");
			// supprimer une éventuelle entree
			
			spip_query("DELETE from spip_cotes_cotes WHERE id_exercice=".$id_exercice." AND id_etudiant=".$id_etudiant);
			spip_query("INSERT INTO spip_cotes_cotes (id_etudiant,id_classe,id_exercice,cote,commentaire,id_coteur,date_cote) VALUES ($id_etudiant,$id_classe,$id_exercice,".$cote[$id_etudiant].",'".$commentaire[$id_etudiant]."',$id_coteur,'$date_comment')");
			
			// message ok
		}
		$message.="Cotes entrées !";
		}
		return $message;
	}


?>
