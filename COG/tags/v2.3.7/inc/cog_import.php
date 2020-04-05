<?php


function cog_tab_fichier_telecharger($fichiers)
{
$tab_fichier=array();
if(!is_array($fichiers)) {
	$tab_fichier=array($fichiers);
	}
else {
	$tab_fichier=array();
	foreach($fichiers as $fichier){
		if(is_array($fichier))
			$tab_fichier[]=$fichier[0];
		else
			$tab_fichier[]=$fichier;
		}
	}
return $tab_fichier;
}
