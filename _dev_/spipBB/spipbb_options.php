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
	include_spip('inc/spipbb');
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
# valider notification (voir inc/notifications.php - fonction : notifications_forumvalide_dist() .. )
# Tous les participants d'un thread recoivent les nouveaux messages
define('_SUIVI_FORUM_THREAD', "1");

# GAF 0.6 - 30/09/07
# definir repertoire des smileys ; recherche dans l'arbo
if (!defined("_DIR_SMILEYS_SPIPBB")) {
	$smilbase = _DIR_PLUGIN_SPIPBB."chatons/";
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

/*************************************************************************************/
// Les lignes qui suivent servent à définir les champs extra ou leur equivalent en table
/************************************************************************************/

# def des champs supplementaire pour ce plugin
# nom de variable generique : champs_sap_[prefix_plugin]
# sur champ de type radio, mettre valeur par defaut en premier !

$GLOBALS['champs_sap_spipbb'] = array(
	"date_crea_spipbb" => array(
		"info" => _L('date de premiere saisie profil SpipBB'), ## petit texte infos pour SAP
		"sql" => "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"filtres_recup" => "", ## filtrage dans fichier (balise/) recup saisie
		#"form_milieu" => "hidden", ## type input, sur auteur_infos.php
		"extra" => "hidden|brut|"._T('spipbb:avatar_saisie_url'), ## pour usage Extra et form prive
		"extra_proposes" => "tous,6forum"
	),
	"avatar" => array(
		"info" => _L('URL de l\'avatar du visiteur'),
		"sql" => "VARCHAR(255) NOT NULL",
		"filtres_recup" => "corriger_caracteres",
		#"form_milieu" => "text",
		"extra" => "ligne|propre|"._T('spipbb:avatar_saisie_url'),
		"extra_proposes" => "6forum"
	),
	"signature_post" => array(
	 	"info" => _L('Court texte de signature des messages'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:signature_saisie_texte'),
	 	"extra_proposes" => "tous,6forum"
	 ),
	"annuaire_forum" => array(
		"info" => _L('Permet de refuser l\'affichage dans l\'annuaire des inscrits en zone public'),
	 	"sql" => "ENUM('non', 'oui') DEFAULT 'oui' NOT NULL",
	 	"filtres_recup" => "",
	 	#"form_milieu" => "radio",
	 	"extra" => "radio|brut|"._T('spipbb:visible_annuaire')."|"._T('non').","._T('oui')."|non,oui",
	 	"extra_proposes" => "tous,6forum"
	),
	"Localisation" => array(
		"info" => _L('Localisation du visiteur'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:localisation'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Emploi" => array(
		"info" => _L('Emploi du visiteur'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:emploi'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Loisirs" => array(
		"info" => _L('Loisirs du visiteur'),
	 	"sql" => "VARCHAR(255) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:loisirs'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Numero_ICQ" => array(
		"info" => _L('Numero ICQ du visiteur'),
	 	"sql" => "VARCHAR(14) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:numero_icq'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Nom_AIM" => array(
		"info" => _L('Nom AIM du visiteur'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:nom_aim'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Nom_Yahoo" => array(
		"info" => _L('Nom Yahoo du visiteur'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:nom_yahoo'),
	 	"extra_proposes" => "tous,6forum"
	),
	"Nom_MSNM" => array(
		"info" => _L('Nom MSNM du visiteur'),
	 	"sql" => "VARCHAR(128) NOT NULL",
	 	"filtres_recup" => "corriger_caracteres",
	 	#"form_milieu" => "text",
	 	"extra" => "ligne|propre|"._T('spipbb:nom_msnm'),
	 	"extra_proposes" => "tous,6forum"
	),
	"refus_suivi_thread" => array(
		"info" => _L('Liste des threads pour lesquels on ne souhaite plus recevoir de notification'),
		"sql" => "TEXT DEFAULT '' NOT NULL",
		"filtres_recup" => "",
		#"form_milieu" => "hidden",
		"extra" => "hidden|brut|"._T('spipbb:refus_suivi_thread'),
		"extra_proposes" => "tous,6forum"
	)
);

#
# Definition de tous les extras possibles (voir base/sap_gaf.php)
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
