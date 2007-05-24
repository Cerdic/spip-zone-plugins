<?php

function balise_DEBUT_TEXTE_HEAD($p) {
	if(verifie_debut_fin_texte_head())
    $p->code = "'<!-- spip_debut_texte_head'.\$Pile[0]['cle_head'].'-->'";
  else {
    $p->code = "''";
  }
	return $p;
}

function balise_FIN_TEXTE_HEAD($p) {
  if(verifie_debut_fin_texte_head(true))
    $p->code = "'<!-- spip_fin_texte_head'.\$Pile[0]['cle_head'].'-->'";
  else {
    $p->code = "''";
  }    
	return $p;
}

function verifie_debut_fin_texte_head($fin = false) {
  static $debut = false;
  if(!$fin && !$debut) {
    $debut = true;
    return true;
  } 
  if($fin && $debut) {
    $debut = false;
    return true;
  }
  return false;   
}

?>
