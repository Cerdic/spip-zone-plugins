<?php
/**
 * retourne une liste contenant les modules
 * trouves dans le repertoire passe en parametre
 * 
 * @param string $rep
 * @return array
 */
function inc_tradlang_getmodules_fics($rep){
	$ret = array();

	// parcourt de l'ensemble des fichiers
	$handle = opendir($rep);  
	while (($fichier = readdir($handle)) != ''){
		// Eviter ".", "..", ".htaccess", etc.
		if ($fichier[0] == '.') continue;
		if ($fichier == 'CVS') continue;
      
		$nom_fichier = $rep."/".$fichier;
		if (is_file($nom_fichier)){
			// cherche un fichier de la forme <nom module>_<langue>.php
			if (preg_match("/^([a-z]*)_([a-z_]*)\.php$/i", $fichier, $match)){
				$nommod = $match[1];
				$langue = $match[2];
	      
				if (tradlang_verif($nom_fichier)){
				// verifie si deja trouve
					if (!isset($ret[$nommod])){
						$ret[$nommod] = array();
						$ret[$nommod]["nomfichier"]=$fichier;
						$ret[$nommod]["nom_mod"]=$nommod;
						$ret[$nommod]["dir_lang"]=$rep;
					}
					$ret[$nommod]["langue_".$langue] = $fichier;
				}
			}
		}
	}
	closedir($handle);

	return $ret;
}
?>