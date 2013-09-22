<?php
if (!defined("_ECRIRE_INC_VERSION")) return;



function zippeur_creer_arbo($chemin,$fichier='oui'){
	$arbo = explode('/',$chemin);
	
	$chemin = zippeur_chemin_dossier_local();
	if ($fichier == 'oui'){
		array_pop($arbo);
	}
	foreach ($arbo as $rep){
		$chemin = $chemin.'/'.$rep;
	
		sous_repertoire($chemin);		
	}
}
function zippeur_creer_fichier($squel,$chemin,$options=array()){
	zippeur_creer_arbo($chemin);
	$chemin = zippeur_chemin_dossier_local().$chemin ; 
	$contenu = recuperer_fond($squel,$options);
	ecrire_fichier($chemin,$contenu);
}

function zippeur_copier_fichier($orig,$dest,$find_in_path=True){
	zippeur_creer_arbo($dest);
	$chemin = zippeur_chemin_dossier_local() ;
	if ($find_in_path)
	   copy(find_in_path($orig),$chemin.$dest);
	else
	   copy($orig,$chemin.$dest);
}

function zippeur_copier_dossier($orig,$dest){
    zippeur_creer_arbo($dest,'non');
    
    $chemin = zippeur_chemin_dossier_local() ;
    $path = find_in_path($orig);
    $fichiers=preg_files($path);

    foreach ($fichiers as $f){
        $arbo = str_replace($path.'/','',$f);
        zippeur_creer_arbo($arbo);
        copy($f,$chemin.$dest.'/'.basename($f));
    }
       
}
?>