<?php
// retourne un array avec la liste les polices disponibles dans le dossier /polices du plugin ou du squelette
function choix_police_polices_disponibles($chemin) {
    $Tpolices = array();
    if (is_dir($chemin) AND $pointeur = opendir($chemin)) {
        while (false !== ($fich = readdir($pointeur))) {
            if ($fich != "." AND $fich != ".." ) { 
                $Tnom = explode('.',$fich);
                if (in_array($Tnom[1], array('ttf','ott')))
                	$Tpolices[$Tnom[0]] = trim($fich);
            }
        }
        closedir($pointeur);
    }
    return $Tpolices;
}

// supprimer le # dans une chaמne (cf couleurs pour image_typo
function choix_police_suprime_diese($txt) {
    return str_replace('#', '', $txt);
}

// si $config est א "on", remplace les caractטres accentuיs par leur יquivalent pas accentuי
function choix_police_remplacer_accents($string, $config='on'){ 
    if (trim($config) == 'on')
  	    $string = strtr(utf8_decode($string), "ְֱֲֳִֵאבגדהוׂ׃װױײ״עףפץצרָֹÊֻטיךכַחּֽ־ֿלםמןÙÚÛÜשתûüÿׁס","AAAAAAaaaaaaooooooooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn");
    return $string; 
} 

?>