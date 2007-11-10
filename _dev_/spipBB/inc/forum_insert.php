<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spipbb');

//
function spipbb_preg_quote($str, $delimiter)
{
	$text = preg_quote($str);
	$text = str_replace($delimiter, '\\' . $delimiter, $text);
	
	return $text;
} // spipbb_preg_quote


// Un parametre permet de forcer le statut (exemple: plugin antispam)
function inc_forum_insert($force_statut = NULL) {

	// On deroute inc/forum_insert pour verifier d'abord chez akismet
	require_once _DIR_RESTREINT.'inc/forum_insert.php';

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

	$spam = check_spam($id_auteur,_request('auteur'),$id_forum,_request('texte'), _request('titre')) ;

	if ($spam) return inc_forum_insert_dist("spam");
	else return inc_forum_insert_dist($force_statut);
}


// [fr] Envoyer un message prive a l'auteur fautif
// [en] Send a private message to spammer
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
// table spip_auteurs_spipbb
// id_auteur bigint(21) not null primary key
// spam_warnings unsigned int not null default 0
// ip_auteur varchar(16)
// ban_date timestamp
// ban 'oui' 'non' default 'non'

// [fr] Stocke dans la base l'avertissement de l'auteur
// [en] Store in database the spammer informations
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

// table spip_spam_words_log
// id_spam_log BIGINT(21) not null primary key autoincrement
// id_auteur bigint(21) not null primary key
// ip_auteur varchar(16)
// login varchar(255)
// log_date timestamp
// titre text
// message mediumtext
// id_forum bigint(21)

// [fr] Met a jour la log des mots spammes
// [en] Update the spam word log
function log_spam_word($id_auteur, $login, $id_forum, &$titre, &$message)
{
	$current_time = time();
	$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');

	$res = sql_insertq('spip_spam_words_log', array (
				'id_auteur' => $id_auteur,
				'login' => $login,
				'ip_auteur' => $user_ip,
				'log_date' => $current_time,
				'titre' => $titre,
				'message' => $message,
				'id_forum' => $id_forum )
			);
} // log_spam_word

// [fr] Bannis le spammeur
// [en] Ban the spammer
function ban_user($id_auteur)
{
	$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
	$current_time = time();

	if (empty($id_auteur)) return;

	// On le recherche
	$is_spammer = sql_fetsel('id_auteur', 'spip_auteurs_spipbb', "id_auteur=$id_auteur");

	if (is_array($is_spammer) and !empty($is_spammer['id_auteur']) and $is_spammer['user_spam_warnings'] > $GLOBALS['spipbb']['sw_nb_spam_ban'] ) // parametrage
	{
		$res=sql_updateq('spip_auteurs_spipbb', array(
					'ip_auteur' => $user_ip,
					'ban_date' => $current_time,
					'ban' => 'oui'
							),
				"id_auteur=$id_auteur");
	}
} // ban_user

function ban_ip()
{
	$user_ip = (isset($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
}

// table spip_spam_words
// id_spam_word BIGINT(21) not null primary key autoincrement
// spam_word varchar(255)
// [fr] Verifie s'il s'agit de spam
// [en] Check if it's a  spam post
function check_spam($id_auteur,$login,$id_forum,$message, &$titre)
{
	if ($GLOBALS['spipbb']['disable_sw']=="oui") return false;
	$is_spammer = sql_fetsel('id_auteur, statut', 'spip_auteurs_spipbb', "id_auteur=$id_auteur");
	if ( $id_auteur==1 AND $GLOBALS['spipbb']['sw_admin_can_spam']=="oui" ) return;
	if ( $is_spammer['statut']=="0minirezo" AND $GLOBALS['spipbb']['sw_modo_can_spam']=="oui" ) return;

	$req = sql_select('spam_word','spip_spam_words');

	$message = preg_replace("#\[.{12,16}\]#i", '', $message);
	$message = preg_replace('/\[url\]|\[\/url\]/si', '', $message);
	$message = preg_replace('#\[url=|\]|\[/url\]#si', '', $message);
	$spam = false;

	while ( $row = sql_fetch($req) )
	{
		if (preg_match('#\b(' . str_replace('\*', '\w*?', spipbb_preg_quote($row['spam_word'], '#')) . ')\b#i', $message)
		or  preg_match('#\b(' . str_replace('\*', '\w*?', spipbb_preg_quote($row['spam_word'], '#')) . ')\b#i', $titre)) {
			log_spam_word($id_auteur, $login, $id_forum, $titre, $message);
			warn_user($id_auteur);
			ban_user($id_auteur);
			ban_ip();
			// insert_pm($id_auteur);
			$spam = true;
			break;
		}
	}
	return $spam;
} // check_spam

?>
