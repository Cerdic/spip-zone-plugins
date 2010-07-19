<?php
/*
Plugin galuse
réalisation: Thom 2010
Sur la base du plugin de B. Blanzin
Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*/
      function exec_photos() {
         include_spip("inc/presentation");
      // vérifier les droits
         global $connect_statut;
         global $connect_toutes_rubriques;
         if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
           
             echo _T('avis_non_acces_page');
             
             exit;
         } 
         $url_editer_photo = generer_url_ecrire('editer_photo');
		 $url_effacer_photo =generer_url_ecrire('effacer_photo');      
         $commencer_page = charger_fonction('commencer_page', 'inc');
		
		echo $commencer_page(_T('balises_sso')) ;
        
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
        
	
     
		echo fin_boite_info(true);
         echo debut_droite("", true);
		 echo gros_titre(_T('Liste des images'),'',false);
         echo debut_cadre_trait_couleur("plugin-24.gif", true, "", _T('Selectionnez une action'));      
        echo debut_cadre_relief("", false,"");
	        
		 echo' <table width="100%" >
  <tr bgcolor="#D9D7AA">
    <td>Id</td>
    <td>Nom de l\'auteur</td>
    <td>Description</td>
    <td>Date</td>
    <td>Vignettes</td>
	<td>Action</td>
    
  </tr>';
   
  $query="SELECT * FROM spip_photos, spip_auteurs WHERE spip_photos.id_auteur= spip_auteurs.id_auteur";
  $val = spip_query (${query}) ;
  while ($data = mysql_fetch_assoc($val))
    {
	$mydate=$data['dateheure'];
	
   echo '<tr>'; 
   echo "<td>".$data['id_photo']."</td> ";
   echo "<td>".$data['nom']."</td> ";
  echo"<td>".$data['alt_photo']."</td>";
  echo"<td>".$mydate."</td>";
  echo'<td> 
<a href="'._DIR_PLUGIN_PHOTOS.'vignettes/'.$data['nom_photo'].'" onClick="window.open(this.href, \'exemple\', \'height=600, width=600, top=100, left=100, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=yes, status=no\'); return false;"/><img src="'._DIR_PLUGIN_PHOTOS.'vignettes/'.$data['nom_photo'].'" width="24%"></a></td>';
 echo "<td><table width='100%' border='0'><tr>"; 

   echo' <td><a href="'.$url_effacer_photo.'&id_photo='.$data['id_photo'].'"><img src="'._DIR_PLUGIN_PHOTOS.'img_pack/corbeille.gif" title="'._T('effacer la photo').'"></a></td>';
   echo' <td><a href="'.$url_editer_photo.'&id_photo='.$data['id_photo'].'"><img src="'._DIR_PLUGIN_PHOTOS.'img_pack/repondre.gif" title="'._T('editer la photo').'"></a></td>';
 echo" </tr></table></td>";
echo'</td></tr>';
}
echo "</table>";




		  echo fin_cadre_relief(false);
		 echo fin_cadre_trait_couleur(true); 
		 echo debut_boite_info(true);
	echo propre(_T('photo:signature'));	
	echo fin_boite_info(true);
	
         echo fin_gauche(), fin_page();
      }                                
?>
