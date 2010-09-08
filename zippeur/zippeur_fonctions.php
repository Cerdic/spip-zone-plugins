<?php

function zippeur($array,$date,$nom=''){
	$nom == '' ? $nom = md5(serialize($array)) : $nom = $nom;
	$chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.'cache-zip/'.$nom.".zip";
	include_spip('inc/flock');
	$enbase = sql_fetsel('id_zip','spip_zippeur',"`nom`='$nom' AND date_modif='$date'");
	/* On vérifie si le zip existe*/
	if (count(preg_files(_DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES.'cache-zip/',$nom.".zip"))==0  or !$enbase['id_zip']){
		
		$enbase = sql_fetsel('id_zip','spip_zippeur',"`nom`='$nom'");
		zippeur_zipper($chemin,$array);
		spip_log("Zippage de $nom.zip","zippeur");
		if ($enbase['id_zip']){
			sql_updateq("spip_zippeur",array("date_modif"=>$date),"id_zip=".$enbase['id_zip']);	
		}
		else{
			sql_insertq("spip_zippeur",array("nom"=>$nom,"date_modif"=>$date));	
		}
		
	}
;
	
	return $chemin;
}

function zippeur_zipper($chemin,$array){
	include_spip('inc/pclzip');
	sous_repertoire(_DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES,'cache-zip');
	supprimer_fichier($chemin);
	
	$zip = new PclZip($chemin);
	foreach ($array as $fichier){
		$erreur = $zip->add($fichier,PCLZIP_OPT_REMOVE_ALL_PATH);
		if ($erreur == 0){
			spip_log("$chemin".$zip->errorInfo(true),"zippeur_erreur");
			
		}
	}
}

?>