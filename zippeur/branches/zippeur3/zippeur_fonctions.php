<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function zippeur_chemin_dossier_local(){
	
	if (!$chemin) {
		static $chemin = '';
		if (defined('_DIR_SITE')) {
			$chemin = _DIR_SITE._NOM_TEMPORAIRES_ACCESSIBLES;
			}
		else{
			$chemin = _DIR_RACINE._NOM_TEMPORAIRES_ACCESSIBLES;
		}
	}
	return $chemin;
}

include_spip('inc/zippeur_dynamique');
function zippeur_dynamique($dossier,$date, $cmd,$dynamiques=array(),$statiques=array(),$sanspath=array(),$delai=0){
	if ($date == '') {
		$date = date("Y-m-d H:i:s",time());
	}
	$chemin = zippeur_chemin_dossier_local().$dossier;
	function_exists('supprimer_repertoire') ?  supprimer_repertoire($chemin) : spip_log("Version de SPIP < 3, possibilité de mélange dans un repertoire dynamique",'zippeur');
	sous_repertoire($chemin);
	
	// création des fichiers dynamiques	
	foreach ($dynamiques as $dyn){
		if ($dyn[1]==''){ 	// si le 2 argument est vide, alors pas de souci, on prend le chemin tel quel
			$dyn[1] = $dyn[0];
		}
		zippeur_creer_fichier($dyn[0],$dossier.'/'.$dyn[1],$dyn[2]);	
	}
	// Les fichiers statiques
	foreach ($statiques as $stat){
		if ($stat[1]==''){		// si le 2 argument est vide, alors pas de souci, on prend le chemin tel quel
			$stat[1] = $stat[0]; 
		}
		
		if (is_dir(find_in_path($stat[0])))
		  zippeur_copier_dossier($stat[0],$dossier.'/'.$stat[1]);
		else
		  zippeur_copier_fichier($stat[0],$dossier.'/'.$stat[1]);
	}
	// Et ceux où la notion de chemin ne s'applique pas
	foreach ($sanspath as $sp){
	   defined('_DIR_SITE') ? $p = _DIR_SITE.$sp[0] : $p = _DIR_RACINE.$sp[0];
	   if ($sp[1]==''){			// si le 2 argument est vide, alors pas de souci, on prend le chemin tel quel
			$sp[1] = $sp[0];
	   }
	   
	   zippeur_copier_fichier($p, $dossier.'/'.$sp[1]);
	}
	return zippeur(array($chemin),$date,$cmd,$dossier,$chemin);
}

function zippeur($array,$date,$cmd='',$nom='',$plat='oui',$delai='0'){
	$delai = valeur_numerique($delai);
	$nom == '' ? $nom = md5(serialize($array)) : $nom = $nom;
	$cmd =='' ? $cmd = lire_config('zippeur/zippeur_cmd'):$cmd=$cmd;
	
	$chemin = zippeur_chemin_dossier_local().'cache-zip/'.$nom.".zip" ;
	include_spip('inc/flock');
	$enbase = sql_fetsel('id_zip,fichiers,date_modif','spip_zippeur',"`nom`='$nom'");
	/* On vérifie si le zip existe*/
	if (count(preg_files($chemin))==0 or!$enbase['id_zip'] or $enbase['date_modif']!=$date or count($array)!=$enbase['fichiers'] or _NO_CACHE!=0){
		
		if(zippeur_zipper($chemin,$array,$cmd,$plat))
		{
			spip_log("Zippage de $nom.zip avec cmd=$cmd","zippeur");
			if ($enbase['id_zip']){
				sql_updateq("spip_zippeur",array("delai_suppression"=>$delai,"date_modif"=>$date,'date_zip'=>date('Y-m-d H-i-s'),'fichiers'=>count($array)),"id_zip=".$enbase['id_zip']);	
			}
			else{
				sql_insertq("spip_zippeur",array("delai_suppression"=>$delai,"nom"=>$nom,"date_modif"=>$date,'date_zip'=>date('Y-m-d H-i-s'),'fichiers'=>count($array)));	
			}
		}
	}
;
	
	return $chemin;
}

function zippeur_zipper($chemin,$array,$cmd,$plat){
	$temps_un=explode(" ",microtime());
	if($cmd=='PclZip'){include_spip('inc/pclzip');}
	sous_repertoire(zippeur_chemin_dossier_local(),'cache-zip'); 
	supprimer_fichier($chemin);
	$fichiers = 0;
	if($cmd=='PclZip')
	{
		$zip = new PclZip($chemin);
		$i = 0;
		foreach ($array as $fichier){
			
			if (test_espace_prive()){
				$array[$i] = '../'.$fichier;
			}
			$i++;
			
			
			
		}
		if ($plat=='oui')
			$erreur = $zip->add($array,PCLZIP_OPT_REMOVE_ALL_PATH);
		else
			$erreur = $zip->add($array,PCLZIP_OPT_REMOVE_PATH, $plat);
		if ($erreur == 0){
				spip_log("$chemin".$zip->errorInfo(true),"zippeur_erreur");
				
			}
		$fichiers  =count($array) ;
	}elseif($cmd=='7zip')
	{
		foreach ($array as $fichier){
			if (test_espace_prive()){
				$fichier_liste .= ' ../'.$fichier;
			}else{
				$fichier_liste .= ' '.$fichier;
				}
				$fichiers++;
		}
			passthru("7za a -tzip ".$chemin." ".$fichier_liste." -mx5 >/dev/null",$result);
			if($result!=0)
			{
				spip_log($fichier_liste." -- code d'erreur 7z: ".$result,"zippeur_erreur");
			}
			else{
				//$fichiers++;
			}
	}elseif($cmd=='zip')
	{
		foreach ($array as $fichier){
			if (test_espace_prive()){
				$fichier_liste .= ' ../'.$fichier;
			}else{
				$fichier_liste .= ' '.$fichier;
				}
				$fichiers++;
		}
			passthru("zip -jq9 ".$chemin." ".$fichier_liste." >/dev/null",$result);
			if($result!=0)
			{
				spip_log($fichier_liste." -- code d'erreur zip: ".$result,"zippeur_erreur");
			}
			else{
				//$fichiers++;
			}
	}
	if ($fichiers !=count($array)){
		spip_log("$chemin : $fichiers fichiers présents mais ".count($array)." prévus",'zippeur_erreur');
		return false;		
	}else{
		$temps_deux=explode(" ",microtime());
		spip_log('zipper en '.($temps_deux[1]-$temps_un[1]).'sec avec '.$cmd,'zippeur');
		return true;
	}
}

?>
