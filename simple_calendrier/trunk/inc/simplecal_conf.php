<?php
/**
 * Plugin Simple Calendrier pour Spip 3
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function simplecal_liste_themes($select_name, $choix){
    // Version Php5
    //$dir_theme = _DIR_SIMPLECAL_PRIVE.'css/datepicker/';
    //$dirs = scandir($dir_theme, 0);
    //$dirs = array_slice ($dirs, 2); 

    // Version Php4                    
    $dir_theme = _DIR_SIMPLECAL_PRIVE.'css/datepicker/';
    $dh  = opendir($dir_theme);
    while (false !== ($filename = readdir($dh))) {
        $dirs[] = str_replace(".css", "", $filename);
    }
    sort($dirs);
    $dirs = array_slice($dirs, 2); // retire les 2 premiers dossiers (. et ..)

    // -----
    $s="";
    $s.="\n<select name=\"$select_name\">";
    
    foreach ($dirs as $dir){
        if ($dir == $choix){
            $s.="\n\t<option name=\"$dir\" selected=\"selected\">$dir</option>";
        } else {
            $s.="\n\t<option name=\"$dir\">$dir</option>";
        }
    }   
    
    $s.="\n</select>";
    
    return $s;
}
?>