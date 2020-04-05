<?php session_start();//--On démarre la session
if(isset($_POST['char'])){$utf=trim($_POST['char']);}else{$utf='utf-8';} $ref=($_POST['ref']-0.5);
header('Content-Type: text/html; charset='.$utf);
 include('../fonction/fonction.php');//------Les fichiers à inclure-----// 
$p=$_SESSION['spipcatchatprivecode']; $l='../db_catchat/'.$p.'/'.$p.'.catchat'; 
	if(isset($_POST['message']) AND !empty($_POST['message'])) {	
		if(!preg_match("#^[-. ]+$#", $_POST['message'])) {	/* On teste si le message ne contient qu'un ou plusieurs points et qu'un ou plusieurs espaces, ou s'il est vide. ^ -> début de la chaine - $ -> fin de la chaine [-. ] -> espace, rien ou point + -> une ou plusieurs fois*/
			 do{ //On n'ouvre le fichier Data Base
				if(false!=($data_base=fopen($l,'r+')))
				{ $i=0;
			while(!feof($data_base))
			{ $ArrayDernierMessage[$i]=json_decode(fgets($data_base),true); $i++; } $zonetime=time();
			 if(empty($ArrayDernierMessage[0]))
				{//--Le dernier message--//
				$putsMessages=json_encode(array($_POST['auteur'],$_SESSION['catchatprivelogin'],$zonetime,$_POST['message']));}
				else//--Le message--/
				{				if($zonetime-$ref >= $ArrayDernierMessage[$i-1][2]) 
								{ $putsMessages.="\n".json_encode(array($_POST['auteur'],$_SESSION['catchatprivelogin'],$zonetime,$_POST['message']));}																							
				}		
				$locked=fwrite($data_base,$putsMessages);
				fflush($data_base);
				fclose($data_base); //On ferme le fichier
				} 
			}while(true!=$locked);//Si il est impossible d'ecrire dans le fichier nous recommençons.			
		}
	} 	?>