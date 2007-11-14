<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : inc/forum_insert.php                               #
#  Authors : Chryjs, 2007 et als                                #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
# [en] Post filtering                                           #
# [fr] Filtrage des post                                        #
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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spipbb');

// ------------------------------------------------------------------------------
// [fr] Quote
// ------------------------------------------------------------------------------
function spipbb_preg_quote($str, $delimiter)
{
	$text = preg_quote($str);
	$text = str_replace($delimiter, '\\' . $delimiter, $text);
	
	return $text;
} // spipbb_preg_quote


// ------------------------------------------------------------------------------
// [fr] Un parametre permet de forcer le statut (exemple: plugin antispam)
// ------------------------------------------------------------------------------
function inc_forum_insert($force_statut = NULL) {

	// On deroute inc/forum_insert
	require_once _DIR_RESTREINT.'inc/forum_insert.php';

	// [fr] Si Spipbb ou le filtrage ne sont pas actifs->traitement classique
	if ( !spipbb_is_configured()
		or $GLOBALS['spipbb']['configure']!='oui'
		or $GLOBALS['spipbb']['config_spam_words']!='oui' ) 
		return inc_forum_insert_dist($force_statut);

	$id_article = intval(_request('id_article'));
	$id_breve = intval(_request('id_breve'));
	$id_forum = intval(_request('id_forum'));
	$id_rubrique = intval(_request('id_rubrique'));
	$id_syndic = intval(_request('id_syndic'));
	$afficher_texte = _request('afficher_texte');
	$ajouter_mot = _request('ajouter_mot');
	$retour_forum = _request('retour_forum');

	$retour_forum = rawurldecode($retour_forum);

	// Antispam : si 'nobot' a ete renseigne, ca ne peut etre qu'un bot
	if (strlen(_request('nobot'))) {
		tracer_erreur_forum('champ interdit (nobot) rempli');
		return $retour_forum; # echec silencieux du POST
	}

	// id_rubrique est parfois passee pour les articles, on n'en veut pas
	if ($id_rubrique > 0 AND ($id_article OR $id_breve OR $id_syndic))
		$id_rubrique = 0;

	$statut = controler_forum($id_article);

	// Ne pas autoriser de changement de nom si forum sur abonnement
	if ($statut == 'abo') {
		controler_forum_abo($retour_forum);
		set_request('auteur', $GLOBALS['auteur_session']['nom']);
		set_request('email_auteur', $GLOBALS['auteur_session']['email']);
	}

	$statut = ($statut == 'non') ? 'off' : (($statut == 'pri') ? 'prop' :
						'publie');

	if (isset($force_statut))
		$statut = $force_statut;

	//
	// Check spam words config
	//

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	$login = _request('auteur') ;

	$spam = check_spam($id_auteur,$login,$id_forum,$id_article,_request('texte'), _request('titre')) ;

	if ($spam) $force_statut = "spam";
	return inc_forum_insert_dist($force_statut);
}

// ------------------------------------------------------------------------------
// [fr] Envoyer un message prive a l'auteur fautif
// [en] Send a private message to spammer
// ------------------------------------------------------------------------------
function insert_pm($id_auteur)
{
	if ( $GLOBALS['spipbb']['sw_send_pm_warning'] == "oui" ) {
		$from_id = $GLOBALS['spipbb']['sw_warning_from_admin'] ;
		$message = $GLOBALS['spipbb']['sw_warning_pm_message'] ;
		$to_auteur = sql_fetsel('email', 'spip_auteurs', "id_auteur=$id_auteur");
		$from_auteur = sql_fetsel('email', 'spip_auteurs', "id_auteur=$from_id");

		// action !!!
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$titre = nettoyer_titre_email($GLOBALS['spipbb']['sw_warning_pm_titre']);
		$envoyer_mail($to_auteur['email'],$titre,$message,$from_auteur['email']);
	}

} // insert_pm

// ------------------------------------------------------------------------------
// [fr] Stocke dans la base l'avertissement de l'auteur
// [en] Store in database the spammer informations
// ------------------------------------------------------------------------------
function warn_user($id_auteur=0)
{
	if (empty($id_auteur)) return;

	// On le recherche
	$is_spammer = sql_fetsel('id_auteur, user_spam_warnings', 'spip_auteurs_spipbb', "id_auteur=$id_auteur");

	if (is_array($is_spammer) and !empty($is_spammer['id_auteur']) )
	{
		$res=sql_updateq('spip_auteurs_spipbb', array( 
					'user_spam_warnings' => $is_spammer['user_spam_warnings']+1),
				"id_auteur=$id_auteur");
	} else {
		$res=sql_insertq('spip_auteurs_spipbb', array( 
					'id_auteur'	=> $id_auteur,
					'user_spam_warnings' => 1)
			);
	}
} // warn_user

// ------------------------------------------------------------------------------
// [fr] Met a jour la log des mots spammes
// [en] Update the spam word log
// ------------------------------------------------------------------------------
function log_spam_word($id_auteur, $login, $id_forum, $id_article, $titre, $message)
{
	$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');

	$req = sql_select('spam_word','spip_spam_words');
	$spam = false;

	while ( $row = sql_fetch($req) )
	{
		$spamword = str_replace('*', '', $row['spam_word']);
		$titre = preg_replace("#$spamword#is", '{{' . $spamword . '}}', $titre);
		$message = preg_replace("#$spamword#is", '{{' . $spamword . '}}', $message);
	}

	$res = sql_insertq('spip_spam_words_log', array (
				'id_auteur' => $id_auteur,
				'login' => $login,
				'ip_auteur' => $user_ip,
				'titre' => $titre,
				'message' => $message,
				'id_forum' => $id_forum,
				'id_article' => $id_article
				)
			);
} // log_spam_word

// ------------------------------------------------------------------------------
// [fr] Bannis le spammeur
// [en] Ban the spammer
// ------------------------------------------------------------------------------
function ban_user($id_auteur)
{
	$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');

	if (empty($id_auteur)) return;

	// On le recherche
	$is_spammer = sql_fetsel('id_auteur', 'spip_auteurs_spipbb', "id_auteur=$id_auteur");
	$infos = sql_fetsel('login, email', 'spip_auteurs', "id_auteur=$id_auteur");

	if (is_array($is_spammer) and !empty($is_spammer['id_auteur']) and $is_spammer['user_spam_warnings'] > $GLOBALS['spipbb']['sw_nb_spam_ban'] ) // parametrage
	{
		@sql_updateq('spip_auteurs_spipbb', array(
					'ip_auteur' => $user_ip,
					'ban' => 'oui'
							),
				"id_auteur=$id_auteur");
		$login = $infos['login'];
		$email = $infos['email'];
		@sql_insertq('spip_ban_liste', array(
					'ban_ip' => $user_ip,
					'ban_login' => $login,
					'ban_email' => $email,
					)
				);

	}
} // ban_user

// ------------------------------------------------------------------------------
// [fr] Verifie s'il s'agit de spam
// [en] Check if it's a  spam post
// ------------------------------------------------------------------------------
function check_spam($id_auteur,$login,$id_forum,$id_article,$message, &$titre)
{
	$is_spammer = sql_fetsel('id_auteur, statut', 'spip_auteurs_spipbb', "id_auteur=$id_auteur");
	if ( $id_auteur==1 AND $GLOBALS['spipbb']['sw_admin_can_spam']=="oui" ) return;
	if ( $is_spammer['statut']=="0minirezo" AND $GLOBALS['spipbb']['sw_modo_can_spam']=="oui" ) return;

	$req = sql_select('spam_word','spip_spam_words');
	$spam = false;

	while ( $row = sql_fetch($req) )
	{
		if (preg_match('#\b(' . str_replace('\*', '\w*?', spipbb_preg_quote($row['spam_word'], '#')) . ')\b#i', $message)
		or  preg_match('#\b(' . str_replace('\*', '\w*?', spipbb_preg_quote($row['spam_word'], '#')) . ')\b#i', $titre)) {
			log_spam_word($id_auteur, $login, $id_forum, $id_article, $titre, $message);
			warn_user($id_auteur);
			ban_user($id_auteur);
			// insert_pm($id_auteur);
			$spam = true;
			break;
		}
	}
	return $spam;
} // check_spam

?>
