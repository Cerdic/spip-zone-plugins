<?php
/**
* saveauto : plugin de sauvegarde automatique de la base de données de SPIP
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
$sauver_base = false;
$fin_sauvegarde_base = false;

function saveauto_body_prive($flux) {
    $flux .= saveauto_go();
    return $flux;
}

function saveauto_go() {
    global $fin_sauvegarde_base, $sauver_base, $saveauto_msg, $connect_statut;
    $saveauto_msg = '';
    if (($connect_statut == "0minirezo") OR ($connect_statut == "1comite" AND (lire_config('saveauto/acces_redac') == 'true'))) {
        if (!isset($_COOKIE["spip_saveauto"]) OR empty($_COOKIE["spip_saveauto"]))	{
          //sauver la base
            include_spip('inc/saveauto_fonctions');
            saveauto_sauvegarde();
            if ($fin_sauvegarde_base) {
                include_spip('inc/cookie');
                spip_setcookie("spip_saveauto","ok");
            }
            if ($sauver_base) {
                if (!$fin_sauvegarde_base) {
                    $saveauto_msg = _T('saveauto:probleme_sauve_base').$base."<br />";
                }
                if ((lire_config('saveauto/ecrire_succes') == 'true') && $fin_sauvegarde_base) {
                    $saveauto_msg = "<script language=\"javascript\">alert(\""._T('saveauto:sauvegarde_ok')."\", \""._T('saveauto:maintenance')."\");</script>";
                }
            }
        }
    }
    return $saveauto_msg;
}

// Pipeline "mes_fichiers_a_sauver" permettant de rajouter des fichiers ˆ sauvegarder dans le plugin Mes Fichiers 2
function saveauto_mes_fichiers_a_sauver($flux){
    // Determination du repertoire de sauvegarde
    $tmp_dump = defined('_DIR_DUMP') ? _DIR_DUMP: _DIR_TMP.'dump/';
    $rep_save = lire_config('saveauto/rep_bases');
    $rep_save = $rep_save ? _DIR_RACINE.$rep_save : $tmp_dump;
    // le dernier fichier de dump de la base cree par saveauto
    $dump = preg_files($rep_save);
    $fichier_dump = '';
    $mtime = 0;
    foreach ($dump as $_fichier_dump) {
        if (($_mtime = filemtime($_fichier_dump)) > $mtime) {
            $fichier_dump = $_fichier_dump;
            $mtime = $_mtime;
        }
    }
    if ($fichier_dump)
        $flux[] = $fichier_dump;
    
    spip_log('*** saveauto_mes_fichiers_a_sauver ***');
    spip_log($flux);
    return $flux;
}

?>
