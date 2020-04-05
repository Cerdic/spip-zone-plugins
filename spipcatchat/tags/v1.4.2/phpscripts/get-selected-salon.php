<?php //Construction de la liste de choix des salons //
if(isset($_GET['char'])){$utf=trim($_GET['char']);}else{$utf='utf-8';}
header('Content-Type: text/html; charset='.$utf);
session_start();//--On démarre la session
require('../obj/salon.class.php');//------Les fichiers à inclure-----//
if(!file_exists('../db_catchat/')) // --> Si le dossier DB_CATCHAT n'existe pas on le crée
{   if(false!=mkdir('../db_catchat/',0777)){
	$salon = new salon('../db_catchat/',$utf);
	$salon->execute('add','',array('Bienvenue','true','null'));}}
elseif(!file_exists('../db_catchat/catchat.xml'))
{	$salon = new salon('../db_catchat/',$utf);
	$salon->execute('add','',array('Bienvenue','true','null'));}
else
{$salon = new salon('../db_catchat/',$utf);}
$i=1;$z=0;
$json='<option selected style="margin-left:20%;"> '.$_GET['lang'][0].' </option>';
while(!$z)
{	$obj=$salon->execute('balise',$i,'');
	if($obj=="end")
	{$z=1;} //--> Si le fichier du menu des salons arrive à la balise xml <end></end>, alors on stop la boucle !
	elseif($i>=100)
	{$z=1;}	//--> ou si le fichier du menu des salons arrive à plus de 100 salons, alors on stop la boucle !
		if($obj=="nom")
		{$lr=$salon->execute('texte',$i+1,'');
		if(false!=($fileDB=file_get_contents('../db_catchat/'.$lr.'/'.$lr.'.js'))){$UsersListes=json_decode($fileDB,true);}
				if($salon->execute('texte',$i+2,'')=="true")
				{	//-->le salon est public, on implémante OPTION de la class UNLOCK;
					$lock='class="spipcatchatselecsalonunlock" title="'.$_GET['lang'][1].'"';
					$statut="[&radic;]&nbsp;";
					$json.='<option '. $lock.' value="'.$i.'">'.$statut.$salon->execute('texte',$i,'').'</option>';	
				}
				else
				{ if($salon->execute('texte',$i+3,'')==$_GET['auteur'] || in_array($_GET['auteur'],$UsersListes))
					{	//-->Le salon est privé mais, nous sommes membre, alors on implémante OPTION de la class LOCKADMIN;
						$lock='class="spipcatchatselecsalonunlockadmin" title="'.$_GET['lang'][2].'"';
						$statut="[&#8211;]&nbsp;";
						$json.='<option '. $lock.' value="'.$i.'">'.$statut.$salon->execute('texte',$i,'').'</option>';
					}
					else
					{//-->Le salon est privé on implémante OPTION de la class LOCK
						$lock='class="spipcatchatselecsalonlock" title="'.$_GET['lang'][3].'"';
						$statut="[x]&nbsp;";
						$json.='<option '. $lock.' value="'.$i.'">'.$statut.$salon->execute('texte',$i,'').'</option>';
					}	
				} 	unset($lock,$ln);
		} $i++;
}		echo json_encode($json);//On retourne le résultat en JSON
?>