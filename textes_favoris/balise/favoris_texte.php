<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
//session_start();
function balise_FAVORIS_TEXTE($p) {
    return calculer_balise_dynamique($p, 'FAVORIS_TEXTE', array());
	
}

function balise_FAVORIS_TEXTE_dyn() {
return array('formulaires/favoris_texte', 0, 
		array(
			//'captcha' => ($lien ? $lien : generer_url_public('captcha')),
		
			
		));
}
 $id_texte=$_POST['ajouter']; 
   $id_auth= $_POST['id_zoteur']; 
    if(($_POST['yes_x']) AND ( $id_auth!=0))
	{
	spip_query( "INSERT INTO spip_favtextes (id_auth, id_texte) VALUES ("._q($id_auth).", "._q($id_texte)." )" );
	//$req="INSERT into spip_favtextes (id_auth, id_texte) values ('$id_auth', '$id_texte')";
 //mysql_query($req);
  //echo " Ce texte a été ajouté à vos favoris.";
   // header('Location:../../?article'.$id_texte);
   }
   elseif ($_POST['no_x']) {  $sql="DELETE  FROM spip_favtextes where id_auth='$id_auth' and id_texte='$id_texte'";
   $req= mysql_query($sql);

//mysql_close();
  //  echo " Supprimé de vos favoris";
   }
	// print "<meta http-equiv='refresh' content=\"0;URL=../../?article".$id_texte."\">";
	// header('Location:../../?article'.$id_texte);



?>
