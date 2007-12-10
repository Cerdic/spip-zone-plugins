<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : spipbb_options                                     #
#  Contact : chryjs!@!free!.!fr                                 #
#  Authors : Chryjs, 2007 et als                                #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
# [en] admin menus                                              #
# [fr] menus d'administration                                   #
#---------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

# h. pourquoi ??
/*
if (!defined('_DIR_PLUGIN_SPIPBB')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SPIPBB',(_DIR_PLUGINS.end($p))."/");
}
*/

$table_des_traitements['TITRE'][]= 'supprimer_numero(typo(%s))';

//---------------------------------------------------------
// [fr] Concu a partir de balise/formulaire_inscription.php
//---------------------------------------------------------
function test_inscription($mode, $mail, $nom, $id=0)
{
	include_spip('inc/filtres');
	$nom = trim(corriger_caracteres($nom));
	if (!$nom || strlen($nom) > 64)
	    return _T('ecrire:info_login_trop_court');
	if (!$r = email_valide($mail)) return _T('info_email_invalide');

	// Controle de la ban_list
	include_spip('inc/spipbb_192');
	$spipbb_meta = @unserialize($GLOBALS['meta']['spipbb']);

	if (is_array($spipbb_meta) AND
		$spipbb_meta['configure'] == 'oui' AND
 		$spipbb_meta['config_spam_words'] == 'oui' ) {

		$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');

		$res = sql_select('ban_login,ban_ip, ban_email','spip_ban_liste');
		while ( $row = sql_fetch($res) )
		{

			$match_email = str_replace('*', '.*?', $row['ban_email']);
			$match_ip = str_replace('*', '.*?', $row['ban_ip']);
			$match_login = str_replace('*', '.*?', $row['ban_login']);
			if ( 	preg_match('/^' . $match_email . '$/is', $mail) or
				preg_match('/^' . $match_ip . '$/is', $user_ip)	or
				preg_match('/^' . $match_login . '$/is', $nom) ) {
				return _T('spipbb:info_inscription_invalide');
			}
		} // while
	} // Fin du bloc traitement specifique spipbb

	return array('email' => $r, 'nom' => $nom, 'bio' => $mode);
}


# h.22/05/07 GAF 0.4 spip 1.9.2
#
# Valider notification
# (voir inc/notifications.php - fonction : notifications_forumvalide_dist() .. )
# Fonction redeclaree dans inc/spipbb_notifications.php
# Tous les participants d'un thread recoivent les nouveaux messages,
# sauf sur threads refuses (voir profil -> refus_suivi_thread)
#
define('_SUIVI_FORUM_THREAD', "1");


# h. GAF 
# definir repertoire des smileys ;
# permet un repert perso de remplacement : mes_smileys/
#
if (!defined("_DIR_SMILEYS_SPIPBB")) {
	$smilbase = _DIR_PLUGIN_SPIPBB."smileys/";
	$smilperso = _DIR_PLUGIN_SPIPBB."mes_smileys/";
	foreach (creer_chemin() as $dir) {
		if (@is_dir($f = "$dir$smilperso")) {
			$repert = $f;
		}
		elseif (@is_dir($f = "$dir$smilbase")) {
			$repert = $f;
		}
	}
	define('_DIR_SMILEYS_SPIPBB', $repert);
}


#
# inclus def de champs (voir ci-apres !)
#
include_spip("base/sap_spipbb");


#
# Definition de tous les extras possibles (voir base/sap_spipbb.php)
#
/* lire_config fourni par CFG */
/* Voir si pas plus simple d'utiliser meta ?*/
if(function_exists('lire_config')) {
	if(lire_config('spipbb/support_auteurs')=='extra') {
		# champs a creer
		if (!is_array($GLOBALS['champs_extra'])) {
			$GLOBALS['champs_extra'] = Array ();
		}
		foreach($GLOBALS['champs_sap_spipbb'] as $k =>$v) {
			$GLOBALS['champs_extra']['auteurs'][$k]=$v['extra'];
		}
		
		# champs affiches
		if (!is_array($GLOBALS['champs_extra_proposes'])) {
			$GLOBALS['champs_extra_proposes'] = Array ();
		}
		foreach($GLOBALS['champs_sap_spipbb'] as $nom => $c) {
			$les_dest = explode(',',$c['extra_proposes']);
			foreach($les_dest as $dest) {
				if (isset($GLOBALS['champs_extra_proposes']['auteurs'][$dest])) {
					$prim = $GLOBALS['champs_extra_proposes']['auteurs'][$dest];
					$GLOBALS['champs_extra_proposes']['auteurs'][$dest]=$prim."|".$nom;
				}
				else {
					$GLOBALS['champs_extra_proposes']['auteurs'][$dest]=$nom;
				}
			}
		}
	}
} else {
	spip_log("SpipBB : Debug spipbb_options.php : Pas de lire_config");
}

?>
