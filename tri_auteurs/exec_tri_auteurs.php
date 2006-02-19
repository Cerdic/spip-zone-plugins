<?php
   
   $table_pref = 'spip';
   if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
   
   $id_article = intval($_POST['id_article']);
   $id_rubriqe = intval($_POST['id_rubrique']);

   if($order = $_REQUEST['o']) {
   for($i=0;$i<count($order);$i++) {  									
								   spip_query("UPDATE ".$table_pref."_auteur_article SET rang = $i WHERE id_article=$id_article AND id_auteur=".intval($order[$i]));
	}
  }

?>
