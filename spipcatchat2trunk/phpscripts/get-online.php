<?php session_start();//--On démarre la session
include('../fonction/fonction.php');//------Les fichiers à inclure-----//
$url=$_SESSION['spipcatchatcode']; $nom=$_SESSION['catchatlogin']; //------Le nom de l'auteur de la session et le code du chat
if(false!=($tabfile=file_get_contents('../db_catchat/'.$url.'/'.$url.'line.js'))){$tableau=json_decode($tabfile,true);} $i=0; $timer= time();
onlineChat($_GET['auteur'].'_'.$nom,$url,'record',$_SESSION['spipcatchatstatut'].$timer);/* si l'utilisateur n'est pas inscrit, on l'ajoute, et on modifie la date de sa derniere actualisation */
if(is_array($tableau))// Test de l'autorisation pour cet auteur
{	foreach($tableau as $key => $value)
	{	 if($timer-140<=chatdate($value))
			{	$data['statut_line']=statut($value);
				if($data['statut_line'] == '1') { $status = 'inactive'; }
				  elseif($data['statut_line'] == '2') { $status = 'busy';}
				  elseif($data['statut_line'] == '3') { $status = 'active'; }
				$infos["id"] = id($key); // On enregistre dans la colonne [status] du tableau le statut du membre : busy, active ou inactive (occupé, en ligne, absent)
				$infos["status"] = $status;
				$infos["login"] = nom($key);// Et on enregistre dans la colonne [login] le pseudo
				$accounts[$i] = $infos;// Enfin on enregistre le tableau des infos de l'auteur
		$i++;	}	}	}
$count = onlineChat('',$url,'count','');// On compte le nombre d'entrées
$t=onlineChat('',$url,'tableau');
	if($count == 1 && !file_exists('../db_catchat/'.$url.'/mr_propre.txt') && $_SESSION['spipcatchatstart'] && (filesize ('../db_catchat/'.$url.'/'.$url.'.catchat') > 100000) )
	{//--> Nous nous assurons d'être le seul et l'unique membre du salon  //--> On écrase le fichier du salon sur lui même pour plus de rapidité lors de la manipulation de celui-ci. 
	 $_SESSION['spipcatchatstart']=false;
	 @file_put_contents('../db_catchat/'.$url.'/'.$url.'.catchat',''); @file_put_contents('../db_catchat/'.$url.'/mr_propre.txt','');	//On écrase le fichier et l'on indique que le fichier à été écrasé, pour ne pas le ré-écrasé de nouveau 
	}
	if($count > 1 && file_exists('../db_catchat/'.$url.'/mr_propre.txt')) {@unlink('../db_catchat/'.$url.'/mr_propre.txt');}
if(salon($_GET['auteur'],$_SESSION['catchatidsalon'],'autorite','utf-8')!=0 ) //Si l'autorisation est toujours valable, nous continuons, sinon on éject  
{  $json['autorisation']=1; $json['list'] = $accounts; // On enregistre le tableau des comptes dans la colonne [list] de JSON
	}
else{ $json['autorisation']=0;// Il existe une erreur	
} echo json_encode($json); // Encodage de la variable tableau json et affichage
?>