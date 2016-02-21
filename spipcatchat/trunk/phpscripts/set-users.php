<?php
session_start();//--On démarre la session
if($_SESSION['spipcatchatautorite']==2 || $_SESSION['spipcatchatautorite']==3)
	{ $chemin='../db_catchat/'.$_SESSION['spipcatchatcode'].'/'.$_SESSION['spipcatchatcode'].'.js';
	if(false!=($userDB=file_get_contents($chemin))){
	$listUsers=json_decode($userDB,true);
	$id=trim($_POST['id']); 
	 if(trim($_POST['stat'])=="plus" && !in_array($id,$listUsers))
		{ array_push($listUsers,$id);
		file_put_contents($chemin,json_encode($listUsers));
		}
	 elseif(trim($_POST['stat'])=="moins" && in_array($id,$listUsers) && $id!=$_SESSION['catchatid'])
		{ $liste=array();
		  foreach($listUsers as $i=>$value)
			{
				if($value!=$id){array_push($liste,$value);}
			}			file_put_contents($chemin,json_encode($liste));
		}
	  }
	} ?>