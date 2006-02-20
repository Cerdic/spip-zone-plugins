<?php
function TriAuteurs_verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

function TriAuteurs_verifier_admin_restreint($id_rubrique) {  
	global $connect_id_rubrique;
	global $connect_statut;

	return ($connect_statut == "0minirezo" AND $connect_id_rubrique[$id_rubrique]);
}

function TriAuteurs_verifier_auteur($id_article) {
  global $connect_id_auteur;
  $select = array('id_auteur');
  
  $from =  array('spip_auteurs_articles');
  
  $where = array("id_auteur = $connect_id_auteur", "id_article = $id_article");
  
  $result = spip_abstract_select($select,$from,$where);
  
  if (spip_abstract_count($result) > 0) {
	spip_abstract_free($result);
	return true;
  }
  spip_abstract_free($result);
  return false;
}
?>
