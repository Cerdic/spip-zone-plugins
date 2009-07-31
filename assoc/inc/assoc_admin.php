<?php

function creer_relation($pour,$avec,$edit=true){
	include_spip('inc/cfg_config');
	ecrire_config("php::assoc/$pour/$avec","ok");
	if ($edit) echo get_liste_association();
}


function supprimer_relation($pour,$avec,$edit=true){
	include_spip('inc/cfg_config');
	effacer_config("php::assoc/$pour/$avec");
	if ($edit) echo get_liste_association();
}


function creer_type($nom){
	include_spip('inc/cfg_config');
	ecrire_config("php::type_assoc/$nom","ok");
}

function supprimer_type($nom){
	include_spip('inc/cfg_config');
	effacer_config("php::type_assoc/$nom");
}


function get_liste_association(){
	include_spip('inc/cfg_config');
	$tab = lire_config("php::assoc");
	if (count($tab)==0)return;
	$retour = "";
	foreach ($tab as $cle=>$val) {
		$pour = $cle;
		foreach ($val as $var=>$va) {
			$retour .="<li id='$pour$var'>$pour - $var  <span onclick='delete_relation(\"$pour\",\"$var\")' class='delete_relation'>&nbsp;&nbsp;X</span></li>";
		}
	}
	return $retour;
}

function get_liste_type_association(){
	include_spip('inc/cfg_config');
	$tab = lire_config("php::type_assoc");
	if (count($tab)==0)return;
	$retour = "";
	foreach ($tab as $cle=>$val) $retour .="<option value='$cle'>$cle</option>";
	$retour .= "@".get_liste_association();
	return $retour;
}

?>