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

function saveauto_ajouter_boutons($boutons_admin) {
    		// si on est admin
    		if ($GLOBALS['connect_statut'] == "0minirezo") {    		
					$boutons_admin['configuration']->sousmenu['saveauto_admin']= new Bouton('../'._DIR_PLUGIN_SAVEAUTO.'/img_pack/saveauto-24.png', _T('saveauto:saveauto') );
    		} 
    		return $boutons_admin;				 
}

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
        			   include (_DIR_PLUGIN_SAVEAUTO."/inc/saveauto_conf.php");
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

// lancement du processus de sauvegarde
 saveauto_go();
?>
