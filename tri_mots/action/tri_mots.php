<?php

function tri_mots() {
  global $redirect;
  global $hash, $id_auteur;
  global $order;
  global $id_mot;
  global $id_table;
  global $table;
  
  $id_mot = intval($id_mot);
  $id_table = addslashes($id_table);
  $table = addslashes($table);

  var_dump("tri_mots $table $id_table $id_mot");

  include_ecrire("inc_session");
  if (!verifier_action_auteur("tri_mots $table $id_table $id_mot", $hash, $id_auteur)) {
	include_ecrire('inc_minipres');
	minipres(_T('info_acces_interdit'));
  }
  
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];

  /************************************************************************/
  /* insertion */
  /************************************************************************/
  //o[]=118&o=120&o[]=128
  if($order) {
	$order = split('&',$order);
	for($i=0;$i<count($order);$i++) {
	  spip_query("UPDATE ".$table_pref."_mots_$table SET rang = $i WHERE id_mot=$id_mot AND $id_table=".intval(substr($order[$i],4)));
	}
  }
  
  if(!$_REQUEST['ajax']) 	redirige_par_entete(urldecode($redirect));
}
?>
   
