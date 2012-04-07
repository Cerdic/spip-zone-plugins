<?php 
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
	
	if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * CP-20081112: script obsolete
 * 
 */
 
 
/*
/* Ajax, renvoie le contenu de la console SPIPLISTES
/**/
function action_spiplistes_lire_console_dist () {
	global $auteur_session;

	if($auteur_session['statut'] != "0minirezo") {
		include_spip('inc/spiplistes_api');
		die (spiplistes_terminer_page_non_autorisee());
		return(false);
	}

	include_spip('inc/spiplistes_api_presentation');
	
	$result = ""
		. "<div style='margin-top:0.5em;'>"
		. debut_cadre_relief("", true, "", "Logs")
		. "<pre style='width:98%;overflow:auto'>".spiplistes_console_lit_log("spiplistes")."</pre>\n"
		. fin_cadre_relief(true)
		. "</div>\n"
		;
		
	echo($result);
	return(true);

}

/*
/**/
function spiplistes_console_lit_log ($logname) {

	$files = preg_files(defined('_DIR_TMP') ? _DIR_TMP : _DIR_SESSION, "$logname\.log(\.[0-9])?");
	krsort($files);

	$log = "";
	foreach($files as $nom){
		if (lire_fichier($nom, $contenu))
			$log .= $contenu;
	}
	$contenu = explode("\n", $contenu);
	
	$result = "";
	$maxlines = 40;
	while ($contenu && $maxlines){
		$ii = trim(array_pop($contenu));
		if(strlen($ii)) { 
			$result .= $ii."\n";
			$maxlines--;
		}
	}
	
	$result = "<pre style='margin-top:1em'>".$result."</pre>\n";
	
	return($result);
}

?>