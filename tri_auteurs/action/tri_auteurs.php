<?php

define('_DIR_PLUGIN_TRI_AUTEURS',(_DIR_PLUGINS . 'tri_auteurs'));

function tri_auteurs() {
  global $hash, $id_auteur, $id_article, $order;

  $id_article = intval($id_article);
  
  include_ecrire("inc_session");
  if (!verifier_action_auteur("tri_auteurs $id_article", $hash, $id_auteur)) {
	include_ecrire('inc_minipres');
	minipres(_T('info_acces_interdit'));
  }

  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  
  
  if($order = $_REQUEST['o']) {
	for($i=0;$i<count($order);$i++) {  									
	  spip_query("UPDATE ".$table_pref."_auteurs_articles SET rang = $i WHERE id_article=$id_article AND id_auteur=".intval($order[$i]));
	}
  }
}

?>
