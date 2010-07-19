<?php
/*
Plugin galuse
réalisation: Thom 2010
Sur la base du plugin de B. Blanzin
Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*/
      function exec_editer_photo() {
         include_spip("inc/presentation");
      // vérifier les droits
         global $connect_statut;
         global $connect_toutes_rubriques;
         if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
           
             echo _T('avis_non_acces_page');
             
             exit;
         } 
         $url_photo = generer_url_ecrire('photos');
		 $url_effacer_photo =generer_url_ecrire('effacer_photo');
		 $id_photo=$_GET['id_photo'];      
         $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Photos')) ;
        
		  echo gros_titre(_T('G&eacute;rer les images'),'',false);
         
		
		  echo debut_gauche ("",true);
		 echo "<br /><br />";
          echo  debut_cadre_relief("", false, "", $titre = _T('Informations'));
	
		echo"Cette page vous permet de g&eacute;rer les photos de votre site.<br />";$i="0";
	$query="SELECT * FROM spip_photos"; 
	 $val = spip_query (${query}) ;
  while ($data = mysql_fetch_assoc($val))
    { $i++;}
echo "Vous avez :";
echo '<span style="color:red"><strong>';echo $i; echo " photo(s) sur le site.</strong></span>";

 	fin_cadre_relief(false);   
        
	$res=icone_horizontale(_T('retour aux images'), $url_photo,  '../'._DIR_PLUGIN_PHOTOS.'/img_pack/photos.png','rien.gif',false );
		echo bloc_des_raccourcis($res);	
     
		echo fin_boite_info(true);
	
         echo debut_droite("", true);
		 echo gros_titre(_T('Modifier la Photo'),'',false);
         echo debut_cadre_trait_couleur("plugin-24.gif", true, "", _T('Vous avez selectionn&eacute;').$id_livredor);      
        echo debut_cadre_relief("", false,"");

		 
  $query=" select * from spip_photos, spip_auteurs WHERE id_photo='$id_photo' AND spip_photos.id_auteur=spip_auteurs.id_auteur";
   $val = spip_query (${query}) ;
  while ($data = mysql_fetch_assoc($val))
    {
	$mydate=$data['dateheure'];$titre_image=$data['nom_photo'];

   echo "<strong>".(_T('Titre '))."</strong>".$data['nom_photo']; echo "<p><img src='"._DIR_PLUGIN_PHOTOS."vignettes/".$data['nom_photo']."' width='24%'></p>";
	echo "<br /><br />".(_T('Envoy&eacute; par ')).$data['nom']." le: ".$mydate;
		echo '<br /><br /><form action="" method="post"><input name="alt_image" type="text" size="60" value="'.$data['alt_photo'].'"><input name="submit" type="submit" value="ok"></form>';
	echo "<br/><br /><strong>".(_T('Correction '))."</strong><br/> ".$_POST['alt_image'];}$reponse=$_POST['alt_image'];
	if ($reponse!="") { 
	spip_query("UPDATE spip_photos SET alt_photo="._q($reponse)." WHERE id_photo='$id_photo' ");}
 
 echo fin_cadre_relief(false);
		 echo fin_cadre_trait_couleur(true); 
			 
		 echo debut_boite_info(true);
	echo propre(_T('photo:signature'));	
	echo fin_boite_info(true);
	
         echo fin_gauche(), fin_page();
      }                              
?>
