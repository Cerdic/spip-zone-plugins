<?php
	/**
	 * saveauto : plugin de sauvegarde automatique de la base de données de SPIP
	 *
	 * Auteur : cy_altern d'après une contrib de Silicium (silicium@japanim.net)
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 *  
	 **/

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SAVEAUTO',(_DIR_PLUGINS.end($p)));


function saveauto_body_prive($flux) {
	global $sauver_base,$saveauto_msg;
	if($sauver_base) $flux .= $saveauto_msg; 	
	return $flux;
}


$sauver_base = false;
$fin_sauvegarde_base = false;


function saveauto_go() {
        global $connect_statut;
				global $fin_sauvegarde_base, $sauver_base,$saveauto_msg;
				if (($connect_statut == "0minirezo") || ($connect_statut == "1comite")) {
        	 if (empty($HTTP_COOKIE_VARS["saveauto"]))	{
        		//sauver la base
							include_spip('inc/saveauto_fonctions');
							saveauto_sauvegarde();
							if ($fin_sauvegarde_base) {
        			   setcookie("saveauto","ok");
        		  }
        		  if ($sauver_base) {
        		  	 //to set the $ecrire_success value
								 if (!$fin_sauvegarde_base) {
        				    $saveauto_msg = _T('saveauto:probleme_sauve_base').$base."<br />";
        			   }
        			   if ($ecrire_succes && $fin_sauvegarde_base) {
        				    $saveauto_msg = "<script language=\"javascript\">alert(\""._T('saveauto:sauvegarde_ok')."\", \""._T('saveauto:maintenance')."\");</script>";
        			   }
        		  }
        	 }
        }
}


// Pipeline "mes_fichiers_a_sauver" permettant de rajouter des fichiers ˆ sauvegarder dans le plugin Mes Fichiers 2
function saveauto_mes_fichiers_a_sauver($flux){

	// Determination du repertoire de sauvegarde
	$tmp_dump = defined('_DIR_DUMP') ? _DIR_DUMP: _DIR_RACINE.'tmp/dump/';
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

// lancement du processus de sauvegarde
 saveauto_go();
?>
