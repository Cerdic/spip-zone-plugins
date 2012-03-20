<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * retourne une liste contenant les modules
 * trouves dans le repertoire passe en parametre
 * 
 * @param string $rep
 * @return array
 */
function inc_tradlang_getmodules_fics($rep,$nom_mod){
	$ret = array();
	// parcourt de l'ensemble des fichiers
	$liste_fic_lang= glob($rep.'/'.$nom_mod."_*.php");
	if(is_array($liste_fic_lang) AND count($liste_fic_lang) > 0){
		$ret = array($nom_mod => array(
				'nom_mod' => $nom_mod
			)
		);
		foreach($liste_fic_lang as $fichier){
			$fich = basename($fichier,".php");
			$fich = str_replace($nom_mod,'',$fich);
			list(,$lang)=explode("_",$fich,2);
			if (tradlang_verif($fichier)){
				$ret[$nom_mod]['langue_'.$lang] = basename($fichier);
			}
		}
		return $ret;
	}
	else{
		return false;
	}
}

// verifie si le fichier passe en param
// est bien un fichier de langue
function tradlang_verif($fic){
	include($fic);
	spip_log($fic,'test');
	// verifie si c'est un fichier langue
	if (is_array($GLOBALS[$GLOBALS['idx_lang']])){
		unset($GLOBALS[$GLOBALS['idx_lang']]);
		return true;
	}
	return false;
}
?>