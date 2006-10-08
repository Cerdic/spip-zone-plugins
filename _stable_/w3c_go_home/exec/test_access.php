<?php
/*
 * valide_site
 *
 * outil de validation w3c et accessibilite du site
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */

function exec_test_access(){
	global $connect_statut,$erreur1,$erreur2,$erreur3;
	$url=urldecode(_request('url'));
	if ($connect_statut != '0minirezo') {
		exit;
	}
	$compliance = isset($GLOBALS['meta']['xhtml_access_compliance'])?unserialize($GLOBALS['meta']['xhtml_access_compliance']):false;
	if (!$compliance)
		$compliance = array();
	$total_erreurs=0;
	foreach($compliance as $url_verif=>$result)
		$total_erreurs+=$result[0];
	if (($total_erreurs>5)&&(!isset($compliance[$url])))
		exit;

	// validation accessibilité
	$_GET['urlAVerif']=$url;
	include_spip('exec/test_apinc');
	ob_start();
	exec_test_apinc();
	ob_end_clean();

	$ok = false;
	$couleur="cc0000";
	$texte = "Erreurs : $erreur1/$erreur2/$erreur3";
	if(($erreur1+$erreur2+$erreur3)==0){
		$ok = true;
		$couleur="00cc00";
		$texte = "OK";
	}
	
	// enregistrer dans la meta
	// on recharge d'abord car il y a pu avoir des validations concourantes
	lire_metas();
	$compliance = isset($GLOBALS['meta']['xhtml_access_compliance'])?unserialize($GLOBALS['meta']['xhtml_access_compliance']):false;
	if (!$compliance)
		$compliance = array();
	$compliance[$url]=array($erreur1+$erreur2+$erreur3,time());
	ecrire_meta('xhtml_access_compliance',serialize($compliance));
	ecrire_metas();
	
	include_spip('inc/filtres');
	$img = image_typo($texte, "police=dustismo.ttf","couleur=$couleur", "taille=12");
	$img = extraire_attribut($img,'src');
	header('Content-Type: image/png');
	header('Content-Length: '.filesize($img));
	readfile($img);

}