<?php
// Orginal From SPIP-Listes-V :: $Id: aide_spiplistes.php paladin@quesaco.org $
/******************************************************************************************/
/* SPIP-Listes-v est une adaptation de SPIP-Listes.                                       */
/* Copyright (C) 2007 Christian PAULUS  cpaulus@quesaco.org , http://quesaco.org          */
/* Plus d'informations sur le lien donné dans la boîte info du plugin.                    */
/*                                                                                        */
/* SPIP-listes est un systeme de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// adaptation de aide_index.php
syslog_trace(_SPIPLISTES_EXEC_AIDE." <<");

if (!defined("_ECRIRE_INC_VERSION")) return;

function aide_spiplistes_erreur() {
	echo minipres(_T('forum_titre_erreur'),
		 "<div>"._T('aide_non_disponible')."<br /></div><div align='right'>".menu_langues('var_lang_ecrire')."</div>");
	exit;
}


function exec_aide_spiplistes () {
	global $spip_lang;
	
	$var_lang = _request('var_lang');
	if (!changer_langue($var_lang)) {
		$var_lang = $spip_lang;
		changer_langue($var_lang);
	}
		
	$fichier_aide_spiplistes = is_readable($f = _DIR_PLUGIN_SPIPLISTES . "docs/aide-".$var_lang."_spiplistes.html")
		? $f
		: _DIR_PLUGIN_SPIPLISTES . "docs/aide-fr_spiplistes.html"
		;

	if($content = @file_get_contents($fichier_aide_spiplistes)) {
		// corrige les liens images
		$content = str_replace("../img_docs/", _DIR_PLUGIN_SPIPLISTES."img_docs/", $content);
		// place les vars
		$pattern = array(
			"/%spiplistes_name%/"
			,"/%spiplistes_version%/"
			,'/\$Date:/'
			,'/\$EndDate/'
			,'/%_aide%/'
			);
		$replacement = array(
			__plugin_dirname()
			, __plugin_meta_version(__plugin_get_real_prefix())
			, ''
			, ''
			, _T('spiplistes:_aide')
			);
		$content = preg_replace($pattern, $replacement, $content);
		
		echo($content);
	}
	else {
		aide_spiplistes_erreur();
	}
}

?>