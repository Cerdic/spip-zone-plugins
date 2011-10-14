<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * saveauto : plugin de sauvegarde automatique de la base de donnees de SPIP
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 *
 */
$sauver_base = false;
$fin_sauvegarde_base = false;

/**
 * Insertion dans le pipeline "mes_fichiers_a_sauver"
 * Permettre de rajouter des fichiers a sauvegarder dans le plugin Mes Fichiers 2
 */
function saveauto_mes_fichiers_a_sauver($flux){
	if(defined('_DIR_SITE')){
		$racine = _DIR_SITE;
	}else{
		$racine = _DIR_RACINE;
	}
    /**
     * Determination du repertoire de sauvegarde et du prefixe
     */
    $tmp_dump = defined('_DIR_DUMP') ? _DIR_DUMP: _DIR_TMP.'dump/';
    $rep_save = lire_config('saveauto/rep_bases','');
    $prefixe = lire_config('saveauto/prefixe_save','');
    $rep_save = $rep_save ? $racine.$rep_save : $tmp_dump;

    /**
     * le dernier fichier de dump de la base cree par saveauto
     * - commence par le prefixe de la configuration
     * - a pour extension zip ou sql
     * - on ne conserve que le dernier en date
     */
    $dump = preg_files($rep_save,"$prefixe.+[.](zip|sql)$");
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

    return $flux;
}

/**
 * On s'insère dans le cron de SPIP
 * Par défaut une fois par jour (peut être modifié dans la conf)
 *
 * @param array $taches_generales
 */
function saveauto_taches_generales_cron($taches_generales){
	if ($cfg = @unserialize($GLOBALS['meta']['saveauto'])){
		$taches_generales['saveauto'] = $cfg['frequence_maj']*24*3600;
		//$taches_generales['saveauto'] = $cfg['frequence_maj']*60; #pour debug
	}else{
		$taches_generales['saveauto'] = 24*3600;
	}
	return $taches_generales;
}

?>
