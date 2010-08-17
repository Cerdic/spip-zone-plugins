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

$table_des_traitements['TITRE'][]= 'supprimer_numero(typo(%s))';

// c: 14/12/8 retour a spip_log suite a l'introduction de la constante _MAX_LOG dans
// http://trac.rezo.net/trac/spip/changeset/13438
define('_MAX_LOG',1000); // Ca devrait suffire comme nombre de lignes

#
# inclus def de champs (voir ci-apres !)
#
include_spip('base/sap_spipbb');


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


	if (is_array($spipbb_meta) AND
		lire_config('spipbb/activer_spipbb', '') == 'on' AND
 		lire_config('spipbb/config_spam_mots', '') == 'on' ) {

		$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');

		include_spip('base/abstract_sql'); // requis quand il n'est pas initialisé
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
} // test_inscription


# h.22/05/07 GAF 0.4 spip 1.9.2
#
# Valider notification
# (voir inc/notifications.php - fonction : notifications_forumvalide_dist() .. )
# Fonction redeclaree dans inc/spipbb_notifications.php
# Tous les participants d'un thread recoivent les nouveaux messages,
# sauf sur threads refuses (voir profil -> refus_suivi_thread)
# Les deux sont exclusifs mutuellement, le premier est prioritaire

define('_SUIVI_FORUM_THREAD', true);
#define('_SUIVI_FORUMS_REPONSES', true);

#
# Definition de tous les extras possibles (voir base/sap_spipbb.php)
#
// lire_config fourni par CFG
// Voir si pas plus simple d'utiliser meta ?
if(function_exists('lire_config')) {

	if (lire_config('spipbb/activer_spipbb', '') == 'on') {

		if (lire_config('spipbb/support_auteurs')=='extra') {
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
	} // spipbb actif

} else {
	spip_log("SpipBB : Debug spipbb_options.php : Pas de lire_config");
}

// autorisations pour spipbb
function spipbb_autoriser() {
	return function_exists('autoriser')
	?autoriser('configurer', 'plugins')
	:$GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"];
} // spipbb_autoriser

?>