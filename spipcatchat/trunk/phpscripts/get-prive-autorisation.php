<?php
if(isset($_POST['char'])){$utf=trim($_POST['char']);}else{$utf='utf-8';}
session_start();
header('Content-Type: text/html; charset='.$_POST['char']);
include_once('../fonction/fonction.php'); //--Les fonctions--//
if(is_readable('../db_catchat/catchat.xml')){
		if($_POST['admin']) //Pas très élégant l'attribution du code du salon de l'espace privé, mais le projet n'est pas fini (ajout de la fonction de création de salons plus sélecteur)
	{   $p='0471910101112141518996354';   echo 1;   }
	else
	{   $p='3345895214586785231548974';   echo 0;   }
	start_prive($p);
	require('../obj/salon.class.php');//------Les fichiers à inclure-----//
	if(!file_exists('../db_catchat/')) // --> Si le dossier DB_CATCHAT n'existe pas on le crée
	{   if(false!=mkdir('../db_catchat/',0777)){
		$salon = new salon('../db_catchat/',$utf);
		$salon->execute('add','',array('Bienvenue','true','null')); start_prive($p);}
		}
	elseif(!file_exists('../db_catchat/catchat.xml'))
	{	$salon = new salon('../db_catchat/',$utf);
		$salon->execute('add','',array('Bienvenue','true','null')); start_prive($p);}
	 $timer=time();
	 	$_SESSION['catchathistorique']=trim($_POST['historique']);
		$_SESSION['catchatprivetime']=$timer;
		$_SESSION['catchatprivelogin']=trim($_POST['nom']);
		$_SESSION['catchatpriveplugin']=trim($_POST['url']);
		$_SESSION['spipcatchatprivestart']=true;
		$_SESSION['spipcatchatprivecode']=trim($p);
		$_SESSION['spipcatchatprivestatut']=3;
		unset($_SESSION['catchatcache'.$p]);
			if($statut=onlineChat('id'.$_POST['id_auteur'].'_'.$_POST['nom'],$p,'statut')) 
			{ $_SESSION['spipcatchatprivestatut'] = statut($statut).'_';}//On récupére l'ancien statut de l'auteur de moins de 4 heure.
			else
			{ $_SESSION['spipcatchatprivestatut'] = '3_';}// Si pas de statut on l'impose en -> online code 3
	if(false!=($tabfile=file_get_contents('../db_catchat/'.$p.'/'.$p.'line.js'))){$tableau=json_decode($tabfile,true);}
	if(is_array($tableau))// Test : On obtient l'autorisation pour cet auteur
	{	foreach($tableau as $key => $value)
		{	if($timer-1440>=chatdate($value))
			{ onlineChat($key,$Online,'del'); }	// On supprime les auteurs qui ne sont pas sur le chat depuis plus de 4h // donc qui n'ont pas actualisé automatiquement ce fichier récemment
	}	}
	onlineChat($_POST['id_auteur'].'_'.$_POST['nom'],$p,'record',$_SESSION['spipcatchatprivestatut'].$timer);
}
else{echo 3;}
?>