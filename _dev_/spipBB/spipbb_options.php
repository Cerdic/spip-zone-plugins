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

/*************************************************************************************/
// Les lignes qui suivent servent à définir les champs extra
/************************************************************************************/
$GLOBALS['champs_extra'] = Array (
	'auteurs' => Array (
		"Localisation" => "ligne|propre|Localisation",
		"Emploi" => "ligne|propre|Centres d'int&eacute;r&eacute;t",
		"Loisirs" => "ligne|propre|Loisirs",
		"Numero_ICQ" => "ligne|propre|Contact chat (ICQ)",
		"Nom_AIM" => "ligne|propre|Contact chat (AIM)",
		"Nom_Yahoo" => "ligne|propre|Contact chat (Yahoo)",
		"Nom_MSNM" => "ligne|propre|Contact chat (MSNM)",
		"avatar" => "ligne|propre|URL de votre avatar",
		"signature" => "bloc|brut|Votre signature"
		)
	);
	
$GLOBALS['champs_extra_proposes'] = Array (
	'auteurs' => Array (
		// tous : par defaut
		'tous' =>  'Localisation|Emploi|Loisirs|Numero_ICQ|Nom_AIM|Nom_Yahoo|Nom_MSNM|signature',
		// les inscrits non admin ont de quoi se faire un avatar, équivalent au logo des auteurs.
		'6forum' => 'Localisation|Emploi|Loisirs|Numero_ICQ|Nom_AIM|Nom_Yahoo|Nom_MSNM|avatar|signature'
		)
);

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

?>
