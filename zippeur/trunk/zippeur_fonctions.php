<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function zippeur_chemin_dossier_local(){

	if (!isset($chemin)) {
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
function zippeur_dynamique($dossier,$date, $cmd,$dynamiques=array(),$statiques=array(),$sanspath=array(),$delai=0, $extension='zip'){
	if ($date == '') {
		$date = date("Y-m-d H:i:s",time());
	}
	$chemin = zippeur_chemin_dossier_local().$dossier;
	function_exists('supprimer_repertoire') ?  supprimer_repertoire($chemin) : spip_log("Version de SPIP < 3, possibilité de mélange dans un repertoire dynamique",'zippeur');
	sous_repertoire($chemin);

	// création des fichiers dynamiques
	if (is_array($dynamiques)) {
		foreach ($dynamiques as $dyn){
			if ($dyn[1] == '') { 	// si le 2 argument est vide, alors pas de souci, on prend le chemin tel quel
				$dyn[1] = $dyn[0];
			}
			zippeur_creer_fichier($dyn[0],$dossier.'/'.$dyn[1],$dyn[2]);
		}
	}
	// Les fichiers statiques
	if (is_array($statiques)) {
		foreach ($statiques as $stat) {
			if ($stat[1] == '') {		// si le 2 argument est vide, alors pas de souci, on prend le chemin tel quel
				$stat[1] = $stat[0];
			}

			if (is_dir(find_in_path($stat[0])))
				zippeur_copier_dossier($stat[0],$dossier.'/'.$stat[1]);
			else
				zippeur_copier_fichier($stat[0],$dossier.'/'.$stat[1]);
		}
	}
	// Et ceux où la notion de chemin ne s'applique pas
	if (is_array($sanspath)) {
		foreach ($sanspath as $sp) {
			defined('_DIR_SITE') ? $base = _DIR_SITE: $base = _DIR_RACINE;
			if (stripos($sp[0],'http://') === 0 or stripos($sp[0],'https://')) {		   // On peut passer une url
				include_spip('inc/distant');
				$url = str_replace('&amp;','&',$sp[0]);
				if ($sp[1]){

					$chemin_fichier_recup = zippeur_chemin_dossier_local().$dossier.'/'.$sp[1];
					zippeur_creer_arbo($dossier.'/'.$sp[1],'oui');
					copie_locale($url,'force',$chemin_fichier_recup);
				}

			}
			else {// pas url ?
				if (stripos($sp[0],$base) === false){//vérifier que la personne n'a pas passé le chemin complet avant de modifier $sp[0]
					$sp[0] = $base.$sp[0];
				}
				$p = $sp[0];
				if ($sp[1]==''){			// si le 2 argument est vide, alors pas de souci, on prend le chemin tel quel
					$sp[1] = $sp[0];
				}
				zippeur_copier_fichier($p, $dossier.'/'.$sp[1],false);
			}
		}
	}
	return zippeur(array($chemin),$date,$cmd,$dossier,zippeur_chemin_dossier_local().$dossier,$delai,$extension);
}

function zippeur($array,$date='',$cmd='',$nom='',$plat='oui',$delai='0',$extension='zip'){
	if ($date == '') {
		$date = date("Y-m-d H:i:s",time());
	}
	$delai = valeur_numerique($delai);
	$nom == '' ? $nom = md5(serialize($array)) : $nom = $nom;
	$cmd =='' ? $cmd = lire_config('zippeur/zippeur_cmd'):$cmd=$cmd;

	$chemin = zippeur_chemin_dossier_local().'cache-zip/'.$nom.'.'.$extension;
	include_spip('inc/flock');
	$enbase = sql_fetsel('id_zip,fichiers,date_modif','spip_zippeur',"`nom`='$nom' and `extension`='$extension'");
	/* On vérifie si le zip existe*/
	if (count(preg_files($chemin))==0 or!$enbase['id_zip'] or $enbase['date_modif']!=$date or count($array)!=$enbase['fichiers'] or (defined('_NO_CACHE') and _NO_CACHE!=0 and !defined('_NO_CACHE_SAUF_ZIPPEUR'))){

		if (zippeur_zipper($chemin,$array,$cmd,$plat)) {
			spip_log("Zippage de $nom.$extension avec cmd=$cmd","zippeur");
			if ($enbase['id_zip']) {
				sql_updateq("spip_zippeur",array("delai_suppression"=>$delai,"date_modif"=>$date,'date_zip'=>date('Y-m-d H-i-s'),'fichiers'=>count($array)),"id_zip=".$enbase['id_zip']);
			} else{
				sql_insertq("spip_zippeur",array("delai_suppression"=>$delai,"nom"=>$nom,'extension' => $extension,"date_modif"=>$date,'date_zip'=>date('Y-m-d H-i-s'),'fichiers'=>count($array)));
			}
		}
	}
	;

	return $chemin;
}

function zippeur_zipper($chemin,$array,$cmd,$plat) {
	$temps_un=explode(" ",microtime());
	if($cmd=='PclZip'){include_spip('inc/pclzip');}
	sous_repertoire(zippeur_chemin_dossier_local(),'cache-zip');
	supprimer_fichier($chemin);
	$fichiers = 0;
	$fichier_liste = '';
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
			spip_log("$chemin".$zip->errorInfo(true),"zippeur_erreur"._LOG_ERREUR);

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
			spip_log($fichier_liste." -- code d'erreur 7z: ".$result,"zippeur_erreur"._LOG_ERREUR);
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
			spip_log($fichier_liste." -- code d'erreur zip: ".$result,"zippeur_erreur"._LOG_ERREUR);
		}
		else{
			//$fichiers++;
		}
	}
	if ($fichiers !=count($array)){
		spip_log("$chemin : $fichiers fichiers présents mais ".count($array)." prévus",'zippeur_erreur'._LOG_ERREUR);
		return false;
	}else{
		$temps_deux=explode(" ",microtime());
		spip_log('zipper en '.($temps_deux[1]-$temps_un[1]).'sec avec '.$cmd,'zippeur');
		return true;
	}
}
