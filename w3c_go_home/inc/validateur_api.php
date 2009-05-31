<?php

function validateur_liste(){
	$liste = find_all_in_path("validateur/","[.]php$");
	foreach (array_keys($liste) as $nom)
		$validateur[] = basename($nom,'.php');
	return $validateur;
}

function validateur_infos($nom){
	$validateur = charger_fonction($nom,'validateur');
	return $validateur('infos');
}

function validateur_reset_tests($nom){
	effacer_meta("w3cgh_$nom");
	ecrire_metas();
}

function validateur_test_valide($nom,$url,$last_modif_time){
	if ($r = validateur_test_resultat($nom,$url,$last_modif_time))
		return $r['time'];
	return false;
}

function validateur_test_resultat($nom,$url,$last_modif_time){
	static $compliance = array();
	if (!isset($compliance[$nom]))
		// regarder si le resultat est en cache meta
		$compliance[$nom] = isset($GLOBALS['meta']["w3cgh_$nom"])?unserialize($GLOBALS['meta']["w3cgh_$nom"]):array();
	if (isset($compliance[$nom][$url])
	  &&($compliance[$nom][$url]['res'][0])
	  &&($compliance[$nom][$url]['time']>$last_modif_time) )
	  	return $compliance[$nom][$url];
	 else 
	 	return false;
}


function validateur_test($nom,$url){
	$validateur = charger_fonction($nom,'validateur');
	$res = $validateur('test',$url);

	// enregistrer dans la meta
	// on recharge d'abord car il y a pu avoir des validations concourantes
	lire_metas();
	$compliance = isset($GLOBALS['meta']["w3cgh_$nom"])?unserialize($GLOBALS['meta']["w3cgh_$nom"]):false;
	if (!$compliance)
		$compliance = array();
	$compliance[$url]=array('res'=>$res,'time'=>time());
	ecrire_meta("w3cgh_$nom",serialize($compliance));
	ecrire_metas();
	return $compliance[$url];
}

function validateur_affiche($nom,$url){
	$validateur = charger_fonction($nom,'validateur');
	return $validateur('visu',$url);
}

?>