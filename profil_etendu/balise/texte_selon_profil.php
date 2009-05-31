<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

function balise_TEXTE_SELON_PROFIL ($p) {
  return calculer_balise_dynamique($p,'TEXTE_SELON_PROFIL',array());
}

function balise_TEXTE_SELON_PROFIL_stat($args, $filtres) {
	return $args;
}
function balise_TEXTE_SELON_PROFIL_dyn($champ='',$valeur='',$texte='',$texte_autres='',$profil='etendu') {
	
	if (!$GLOBALS['auteur_session']) return $texte_autres;
	$id_auteur=$GLOBALS['auteur_session']['id_auteur'];
	$q="SELECT id_auteur FROM spip_profil_".addslashes($profil)." " .
	"WHERE ".addslashes($champ)."='".addslashes($valeur)."' " .
	"AND id_auteur=".$id_auteur;
	$r=spip_query($q);
	if (spip_num_rows($r)>0) return $texte;
	else return $texte_autres;
}
?>