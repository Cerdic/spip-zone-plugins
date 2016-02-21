<?php
session_start(); //--On démarre la session
if($_SESSION['spipcatchatautorite']==2 || $_SESSION['spipcatchatautorite']==3)
	{ 		if(false!=($MembreSalon=file_get_contents('../db_catchat/'.$_SESSION['spipcatchatcode'].'/'.$_SESSION['spipcatchatcode'].'.js'))){
			$mo=json_decode($MembreSalon,true);
			if(is_array($mo) && count($mo)>1)
			{// Affichage de la variable tableau json
				 echo $MembreSalon;
			}
			else
			{// Encodage de la variable tableau json et affichage
				echo json_encode("0");
			}
		}
    } ?>