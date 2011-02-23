<?php

function zippeur($array,$date,$cmd,$nom=''){
	$nom == '' ? $nom = md5(serialize($array)) : $nom = $nom;
	$cmd =='' ? $cmd = lire_config('zippeur/zippeur_cmd'):$cmd=$cmd;
	
	defined('_DIR_SITE') ? $chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.'cache-zip/'.$nom.".zip" : $chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES.'cache-zip/'.$nom.".zip";
	include_spip('inc/flock');
	$enbase = sql_fetsel('id_zip,fichiers,date_modif','spip_zippeur',"`nom`='$nom'");
	/* On vérifie si le zip existe*/
	if (count(preg_files($chemin))==0 or!$enbase['id_zip'] or $enbase['date_modif']!=$date or count($array)!=$enbase['fichiers']){
		
		if(zippeur_zipper($chemin,$array,$cmd))
		{
			spip_log("Zippage de $nom.zip avec cmd=$cmd","zippeur");
			if ($enbase['id_zip']){
				sql_updateq("spip_zippeur",array("date_modif"=>$date,'fichiers'=>count($array)),"id_zip=".$enbase['id_zip']);	
			}
			else{
				sql_insertq("spip_zippeur",array("nom"=>$nom,"date_modif"=>$date,'fichiers'=>count($array)));	
			}
		}
	}
;
	
	return $chemin;
}

function zippeur_zipper($chemin,$array,$cmd){
	if($cmd=='PclZip'){include_spip('inc/pclzip');}
	defined('_DIR_SITE') ? sous_repertoire(_DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES,'cache-zip') : sous_repertoire(_DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES,'cache-zip');
	supprimer_fichier($chemin);
	$fichiers = 0;
	if($cmd=='PclZip')
	{
		$zip = new PclZip($chemin);
		foreach ($array as $fichier){
			if (test_espace_prive()){
				$fichier = '../'.$fichier;
			}
			$erreur = $zip->add($fichier,PCLZIP_OPT_REMOVE_ALL_PATH);
			if ($erreur == 0){
				spip_log("$chemin".$zip->errorInfo(true),"zippeur_erreur");
				
			}
			else{
				$fichiers++;	
			}
		}
	}elseif($cmd=='7zip')
	{
		foreach ($array as $fichier){
			if (test_espace_prive()){
				$fichier = '../'.$fichier;
			}
			passthru("7za a -tzip ".$chemin." ".$fichier." -mx5 >/dev/null",$result);
			if($result!=0)
			{
				spip_log($fichier." -- code d'erreur 7z: ".$result,"zippeur_erreur");
			}
			else{
				$fichiers++;
			}
		}
	}elseif($cmd=='zip')
	{
		foreach ($array as $fichier){
			if (test_espace_prive()){
				$fichier = '../'.$fichier;
			}
			passthru("zip -q9 ".$chemin." ".$fichier." >/dev/null",$result);
			if($result!=0)
			{
				spip_log($fichier." -- code d'erreur zip: ".$result,"zippeur_erreur");
			}
			else{
				$fichiers++;
			}
		}
	}
	if ($fichiers !=count($array)){
		spip_log("$chemin : $fichiers fichiers présents mais ".count($array)." prévus",'zippeur_erreur');
		return false;		
	}else{
		return true;
	}
}

?>