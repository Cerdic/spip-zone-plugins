<?php

include_spip("inc/extra");
include_spip("inc/layer");

function afficher_manipuler_couches($texte,$action){
	global $compteur_block,$compteur_block_avant;
	if ($compteur_block>$compteur_block_avant)
		return "manipuler_couches('".$action."','',".($compteur_block_avant+1).",".$compteur_block.", 'ecrire/img_pack/');";
	else return $texte;
}
function afficher_swap_couches($dummy){
	global $compteur_block;
	return "swap_couche(".$compteur_block.", '','ecrire/img_pack/',0);";
}
function init_manipuler_couches($texte){
	global $compteur_block,$compteur_block_avant;
	if (!$compteur_block) $compteur_block=0;
	$compteur_block_avant=$compteur_block;
	return $texte;
}

function logoauteur($id_auteur, $formats = array ('gif', 'jpg', 'png')) {
	reset($formats);
	while (list(, $format) = each($formats)) {
		$d = _DIR_IMG . "auton$id_auteur.$format";
		if (@file_exists($d)) return $d;
	}
	return  '';
}

function calculer_hash($id_auteur, $action) {
	return calculer_action_auteur($action,$id_auteur);
}

function afficher_jour_mois_annee($date, $suffixe='')
{
  return 
    afficher_jour(jour($date), "name='jour$suffixe' size='1' class='forml verdana1'") .
    afficher_mois(mois($date), "name='mois$suffixe' size='1' class='forml verdana1'") .
    afficher_annee(annee($date), "name='annee$suffixe' size='1' class='forml verdana1'", date('Y')-1);
}
function str_cat($texte1, $texte2=''){
	return $texte1.$texte2;
}


function barre_article($texte,$name='texte')
{
	include_ecrire('inc_layer.php3');

	if (!$GLOBALS['browser_barre'])
		return "<textarea name='texte' rows='12' class='forml' cols='40'>$texte</textarea>";
	static $num_formulaire = 0;
	$num_formulaire++;
	include_ecrire('inc_barre.php3');
	return afficher_barre("document.getElementById('formulaire_$num_formulaire')", true) .
	  "
<textarea name='$name' rows='12' class='forml' cols='40'
id='formulaire_$num_formulaire'
onselect='storeCaret(this);'
onclick='storeCaret(this);'
onkeyup='storeCaret(this);'
ondbclick='storeCaret(this);'>$texte</textarea>";
}

?>