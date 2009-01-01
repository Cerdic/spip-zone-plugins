<?php

function exec_afficheform()
{
	
	global $connect_statut;
  if ($connect_statut != "0minirezo" ) {
	 echo "<p><strong>"._T('acces_a_la_page')."</strong></p>";
	 fin_page();
	 exit;
  }
  
	$id_etudiant=(int) $_GET['id_etudiant'];
	$id_exercice=$_GET['id_exercice'];
  
  // cherche l'exercice
		$etudiant=spip_query("SELECT * FROM spip_cotes_etudiants WHERE id_etudiant=".$id_etudiant);
		if ($etudiant) {
			$etudiant=spip_fetch_array($etudiant);
		}
		
		$exercice=spip_query("SELECT * FROM spip_cotes_exercices WHERE id_exercice=".$id_exercice);
		if ($exercice) {
			$exercice=spip_fetch_array($exercice);
		}
  		
  		$cote_actu=spip_query("SELECT * FROM spip_cotes_cotes WHERE id_etudiant=".$id_etudiant." AND id_exercice=".$id_exercice);
  		if ($cote_actu) {
			$cote_actu=spip_fetch_array($cote_actu);
		}
		
		// image
		$chem=_DIR_DOC."illu-etudiant/";
  		$handle = @opendir($chem); 
  		$logo = false;
  		while($fichier = @readdir($handle)) {
			if (ereg("^illu_etudiant-".$id_etudiant."\.(jpg|png|gif)$", $fichier)) {
			$logo = $fichier;
    		}
  		}
		if ($logo){
		// le nombre aléatoire permet d'éviter que le navigateur affiche la version en cache de l'image.
		$image = "<div class='illu-etudiant illu-etudiant".$id_etudiant."'><img width=\"60\" src=\""._DIR_DOC."illu-etudiant/$logo?".uniqid(rand())."\" /></div>";
  		} else {
  		$image="";
  		}
		
		
		
  		
 		echo "<div class='listageetudiant' rel='".$id_etudiant."'>\n";
 		echo $image."<br />";
 		echo $etudiant['nom']." ".$etudiant['prenom']." <span class=\"listagemail\">(".$etudiant['mail'].")</span>";
 		echo "<br />";
 		if(ereg("\.0$",$cote_actu['cote'],$regs)){
			// enleve le '.0' disgracieux
			$cote_actu['cote'] = ereg_replace("\.0$","",$cote_actu['cote']);
			}
 		echo "<input type='text' name='cote[".$id_etudiant."]' value='".$cote_actu['cote']."' style='width:3em' />";
		echo "<strong> / ".$exercice['cote_max']."</strong><br />\n";
 		echo "<textarea rows='10' cols='20' name='commentaire[".$id_etudiant."]' class='forml'>"
 		.$cote_actu['commentaire']
 		."</textarea>\n";
 		echo "<input type='hidden' name='etudiant[".$id_etudiant."]' value='".$id_etudiant."' />";
 		echo "<a href='javascript:annule(".$id_etudiant.")' class='listageannuler'>Annuler cette cotation</a>";
 		echo "</div>\n";
  
}

?>