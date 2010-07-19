<?php
/*
Plugin galuse
réalisation: Thom 2010
Sur la base du plugin de B. Blanzin
Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*/
      function exec_effacer_photo() {
         include_spip("inc/presentation");
      // vérifier les droits
         global $connect_statut;
         global $connect_toutes_rubriques;
         if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
           
             echo _T('avis_non_acces_page');
             
             exit;
         } 
         $url_photo = generer_url_ecrire('photos');
		 $url_effacer_livre =generer_url_ecrire('effacer_photo');
		 $id_photo=$_GET['id_photo'];      
         $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Photos')) ;
        
		  echo gros_titre(_T('Plugin Photos'),'',false);
         
		
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
		 echo gros_titre(_T('Image s&eacute;l&eacute;ctionn&eacute;e'),'',false);
         echo debut_cadre_trait_couleur("plugin-24.gif", true, "", _T('Selectionnez une action'));      
        echo debut_cadre_relief("", false,"");

		 echo' <table width="100%" >
  <tr bgcolor="#D9D7AA">
    <td>Id</td>
    <td>Vignette</td>
    <td>Description</td>
    <td>Date</td>
    <td>Titre</td>
    <td>Action</td>
  </tr>'; echo '<tr>';
  $query=" select * from spip_photos WHERE id_photo='$id_photo'";
  $val = spip_query (${query}) ;
  while ($data = mysql_fetch_assoc($val))
    {
	$mydate=$data['dateheure'];
	//include_spip('livredor_fonctions');
 //$mydate= livredor_convertir_date($mydate); 
    echo "<td>".$data['id_photo']."</td> ";
   echo "<td><img src='"._DIR_PLUGIN_PHOTOS.'vignettes/'.$data['nom_photo']."' width='24%'></td> ";
  echo"<td>".$data['alt_photo']."</td>";
  echo"<td>".$mydate."</td>";
   echo"<td>".$data['nom_photo']."</td>";
 echo "<td><form action='' method='post'><table width='100%' border='0'>
  <tr>";
   echo' <td><input name="sup" type="checkbox" value="'.$id_photo.'"></td>';
   echo' <td></td>';
 echo" </tr></table></td></tr>";

}

echo "</table><input name='go' type='submit' value='Effacer'  onClick=\"return confirm('Voulez-vous supprimer d&eacute;finitivement cette image? Cette image sera conserv&eacute;e dans le repertoire vignette du plugin!');\">";
echo "</form>";
$id= $_POST['sup'];
spip_query("DELETE FROM spip_photos WHERE id_photo='$id'");
 if (isset($_POST['go']) && !empty($_POST['go'])) {echo('<meta http-equiv="refresh" content="0">');}

		  echo fin_cadre_relief(false);
		 echo fin_cadre_trait_couleur(true); 
		 echo debut_boite_info(true);
	echo propre(_T('photo:signature'));	
	echo fin_boite_info(true);
	
         echo fin_gauche(), fin_page();
      }                              
?>
