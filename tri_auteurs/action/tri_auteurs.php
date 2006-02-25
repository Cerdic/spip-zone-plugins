<?php

define('_DIR_PLUGIN_TRI_AUTEURS',(_DIR_PLUGINS . 'tri_auteurs'));

include(_DIR_PLUGIN_TRI_AUTEURS.'/tri_auteurs_utils.php');
$id_article = intval($_POST['id_article']);
$id_rubrique = intval($_POST['id_rubrique']);

if(TriAuteurs_verifier_admin() OR TriAuteurs_verifier_admin_restreint($id_rubrique) 
   OR TriAuteurs_verifier_auteur($id_article)) {
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  
  
  if($order = $_REQUEST['o']) {
	for($i=0;$i<count($order);$i++) {  									
	  spip_query("UPDATE ".$table_pref."_auteurs_articles SET rang = $i WHERE id_article=$id_article AND id_auteur=".intval($order[$i]));
	}
  }
}

?>
