<?php
session_start();
header('Content-Type: text/html; charset='.$_POST['char']);
include_once('../fonction/fonction.php');
if(0!=($autorite=salon($_POST['id_auteur'],$_POST['id_salon'],'autorite',$_POST['char'])))
{ $timer=time();//Avant l'attribution des variables de session une verification des permissions d'accès au salon.
	$p=salon($_POST['id_auteur'],$_POST['id_salon'],'',$_POST['char']);
	$_SESSION['catchattime'] = $timer;
	$_SESSION['catchatidsalon'] = $_POST['id_salon'];
	$_SESSION['catchatlogin'] =  $_POST['nom'];
	$_SESSION['catchatplugin'] = $_POST['url'];
	$_SESSION['spipcatchatstart'] = true;
	$_SESSION['spipcatchatcode'] = $p['code'];
	$_SESSION['spipcatchatautorite'] = $autorite;
	$_SESSION['spipcatchatnomsalon'] = $p['nom'];
	$_SESSION['spipcatchatstatut'] = 3;
	unset($_SESSION['catchatcache'.$p['code']]);
		if($statut=onlineChat('id'.$_POST['id_auteur'].'_'.$_POST['nom'],$p['code'],'statut')) 
		{ $_SESSION['spipcatchatstatut'] = statut($statut).'_';}//On récupére l'ancien statut de l'auteur de moins de 4 heure.
		else
		{ $_SESSION['spipcatchatstatut'] = '3_';}// Si pas de statut on l'impose en -> online code 3
if(false!=($tabfile=file_get_contents('../db_catchat/'.$p['code'].'/'.$p['code'].'line.js'))){$tableau=json_decode($tabfile,true);}
if(is_array($tableau))// Test : On obtient l'autorisation pour cet auteur
{	foreach($tableau as $key => $value)
	{	if($timer-14400>=chatdate($value))
		{ onlineChat($key,$p['code'],'del'); }	// On supprime les auteurs qui ne sont pas sur le chat depuis plus de 4h // donc qui n'ont pas actualisé automatiquement ce fichier récemment
}	}
onlineChat($_POST['id_auteur'].'_'.$_POST['nom'],$p['code'],'record',$_SESSION['spipcatchatstatut'].$timer);
} echo json_encode($autorite);
?>