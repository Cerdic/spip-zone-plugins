<?php
// Orginal From SPIP-Listes-V :: $Id: aide_spiplistes.php paladin@quesaco.org $
/******************************************************************************************/
/* SPIP-Listes-v est une adaptation de SPIP-Listes.                                       */
/* Copyright (C) 2007 Christian PAULUS  cpaulus@quesaco.org , http://quesaco.org          */
/* Plus d'informations sur le lien donne dans la boite info du plugin.                    */
/*                                                                                        */
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
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_presentation');
include_spip('inc/plugin');

// adaptation de aide_index.php

function aide_spiplistes_erreur() {
	echo minipres(_T('forum_titre_erreur'),
		 '<div>'._T('aide_non_disponible').'<br /></div><div align="right">'.menu_langues('var_lang_ecrire').'</div>');
	exit;
}


function exec_spiplistes_aide () {

	global $spip_lang;
	
	spiplistes_debug_log('exec_spiplistes_aide()');
	
	$var_lang = _request('var_lang');
	if (!changer_langue($var_lang)) {
		$var_lang = $spip_lang;
		changer_langue($var_lang);
	}
		
	$info = spiplistes_plugin_get_infos(spiplistes_get_meta_dir(_SPIPLISTES_PREFIX));
	$nom = typo($info['nom']);
	$version = typo($info['version']);
		
	$f_lang = _DIR_PLUGIN_SPIPLISTES . 'docs/'._SPIPLISTES_EXEC_PREFIX.'aide_'.$var_lang.'html';
	
	$fichier_aide_spiplistes = is_readable($f_lang)
		? $f_lang
		: _DIR_PLUGIN_SPIPLISTES . 'docs/'._SPIPLISTES_EXEC_PREFIX.'aide_fr.html'
		;

	if($content = file_get_contents($fichier_aide_spiplistes)) {
		// corrige les liens images
		$content = str_replace('../img_docs/', _DIR_PLUGIN_SPIPLISTES.'img_docs/', $content);
		// place les vars
		$pattern = array(
			'/@spiplistes_name@/'
			,'/@spiplistes_version@/'
			,'/\$LastChangedDate:/'
			,'/\$EndLastChangedDate/'
			,'/@_aide@/'
			);
		$replacement = array(
			$nom
			, $version
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

