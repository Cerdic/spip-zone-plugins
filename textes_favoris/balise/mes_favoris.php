<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;
//session_start();
function balise_MES_FAVORIS($p) {
    return calculer_balise_dynamique($p, 'MES_FAVORIS', array());
	
}

function balise_MES_FAVORIS_dyn() {
$session=$GLOBALS['visiteur_session']['id_auteur'];

			  $sql=spip_query("SELECT * FROM spip_favtextes ,spip_articles WHERE spip_favtextes.id_texte=spip_articles.id_article AND id_auth=$session");
while($data = sql_fetch($sql)){
    
		$article=$data['titre']; 
		$id_article=$data['id_article'];
		 echo "<a href=?article".$id_article.">".$article."</a><br />";
  }   
}

			 
	
?>

