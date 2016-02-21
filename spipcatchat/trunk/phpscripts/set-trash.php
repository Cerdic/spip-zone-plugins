<?php session_start();//--On démarre la session
if($_SESSION['spipcatchatautorite']==2 || $_SESSION['spipcatchatautorite']==3)
{include('../obj/salon.class.php');//------Les fichiers à inclure-----//
$salonTrash = new salon('../db_catchat/',$_POST['char']);
 if($salonTrash->execute('del',$salonTrash->execute('id',$_SESSION['catchatidsalon'],''),'')) //--On instancie l'objet d'edition et gestion du menu des salons
	{$data=1;}
 else
	{$data=0;}
} else //On retourne le résultat en JSON
{$data=0;} echo json_encode($data); ?>