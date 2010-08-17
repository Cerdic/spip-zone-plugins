<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : spipbb_fonctions - fonctions communes         #
#  Authors : Scoty, Gurdil, Booz, Chryjs 2007 et           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

# requis
if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
include_spip('inc/spipbb_util');
include_spip('inc/traiter_imagerie');
//include_spip('inc/spipbb_inc_formpost'); // tout_de_selectionner

// ------------------------------------------------------------------------------
// Filtre : insere_texte_alerter
// Scoty 11/08/07 - GAF 0.5
// Insere texte alerte-abus dans corps message pour webmaster
// ------------------------------------------------------------------------------
function insere_texte_alerter($texte,$insere)
{
	if (!$premiere_passe = _request('valide')) {
		if(_request('alerter')=='oui') {
			$origine=explode('-',_request('orig'));
			#$insere = _T('spipbb:alerter_texte');
			$lien_forum = generer_url_public('voirsujet',"id_forum=".$origine[0]."#forum".$origine[1],true);
			$texte = $insere."\n".$lien_forum."\n\n";
		}
	}
	return $texte;
} // insere_texte_alerter

// ------------------------------------------------------------------------------
//	Filtre : insere_sujet_alerter
//	Scoty 11/08/07 - GAF 0.5
//	Insere texte alerte-abus dans sujet message pour webmaster
// ------------------------------------------------------------------------------
function insere_sujet_alerter($sujet,$insere)
{
	if (!$premiere_passe = _request('valide')) {
		if(_request('alerter')=='oui') {
			#$insere = _T('spipbb:alerter_sujet');
			$sujet = $insere;
		}
	}
	return $sujet;
} // insere_sujet_alerter


// ------------------------------------------------------------------------------
// filtre :
// explode() !!
// scoty 26/10/07 - GAF v.0.6
// ------------------------------------------------------------------------------
function chaine2array($chaine,$sep='')
{
	$chaine=trim($chaine);
	if(!$sep) $sep = ',';
	if($chaine=='') { $chaine=array(); }
	else { $chaine = explode($sep,$chaine); }
	return $chaine;
} // chaine2array


// ------------------------------------------------------------------------------
//	Filtre : spipbb_maintenance ex gaf_maintenance
//	scoty 26/09/07 - GAF v.0.6
//	Sur balise id_article.
//	Signaler une maintenance (donc ferme temporaire)
// ------------------------------------------------------------------------------
function spipbb_maintenance($id_article)
{
	if ($ds = @opendir(_DIR_SESSIONS)) {
		while (($file = @readdir($ds)) !== false) {
			if (preg_match('/^gafart_([0-9]+)-([0-9]+)\.lck$/', $file, $match)) {
				if($match[1] == $id_article) { return "1"; }
			}
		}
	}
} // spipbb_maintenance



//+---------------------------------------------+
//Filtre : Nombre de messages
//base : BoOz Email:booz@bloog.net
//Compte le nombre de messages d'un auteur
//Appel dans squellette : [(#ID_AUTEUR|spipbb_nb_messages)]
//+---------------------------------------------+
## h.
# a renommer plus simple ! => |nombre_post ??
##
function spipbb_nb_messages($id_auteur){
	if (empty($id_auteur)) return ;
	$nb_mess = "";
	if (lire_config('spipbb/activer_spipbb', '')=='on'
		AND lire_config('spipbb/secteur_spipbb') > 0 ) {
			spip_log('ok', 'spipbb_cfg');
		$id_secteur = lire_config('spipbb/secteur_spipbb');
		$result_auteurs = sql_select('id_auteur',
							"spip_forum AS sf, spip_articles AS sa", // FROM
							array("id_auteur=$id_auteur",
									"sf.id_article=sa.id_article",
									"( sa.id_rubrique=".$id_secteur." OR sa.id_secteur=".$id_secteur." )"
									) //WHERE
							);
		}
	else {
		$result_auteurs = sql_select('auteur','spip_forum',"id_auteur=$id_auteur");
	}
	$nb_mess = sql_count($result_auteurs);
	return $nb_mess;
} // spipbb_nb_messages



// Calcule le nombre de messages par auteur et les classes par ordre decroissant
function spipbb_nb_messages_groupe($id_bidon){
	$aut_nb = array();
	$secteur_spipbb = lire_config('spipbb/secteur_spipbb');
	$result_auteurs = sql_select(
							'sf.id_auteur, sa.nom AS auteur, COUNT(sa.nom) AS total', //SELECT
							'spip_forum AS sf, spip_auteurs AS sa, spip_articles AS sar', // FROM
							"sf.statut='publie' AND sf.id_auteur>0 AND sf.id_auteur=sa.id_auteur AND sa.statut!='5poubelle' AND sar.id_secteur=" . sql_quote($secteur_spipbb) . " AND sar.id_article=sf.id_article AND sf.id_article>0" , // WHERE
							"sf.id_auteur", // GROUPBY
							array("total desc"), // ORDERBY
							"10" // LIMIT
							);
	$compte = 0;
	while ($row = sql_fetch($result_auteurs) AND $compte++<10) {
		# 1/12/07 fct spipbb_auteur_infos() change de nom :
		$infos = spipbb_donnees_auteur($row['id_auteur']);
		if ( ( isset($infos['annuaire_forum'])  AND $infos['annuaire_forum']!='non')
				OR
				( lire_config('spipbb/afficher_membres', '')=='on' AND (!isset($infos['annuaire_forum']) OR  !$infos['annuaire_forum'] ) ) )
		 {
			// Peut apparaitre dans la liste
			$aut_nb[]=$row['auteur']."(".$row['total'].")";
		}
	}

	return join(", ",$aut_nb) ;
} // spipbb_nb_messages_groupe

//+----------------------------------+
//Filtre :  citation
//Base : BoOz
//Modif scoty  29/10/06 .. -> spip 1.9.1/2
//Modif chryjs 9/7/8 .. -> spip 2.0SVN
//Affiche le texte à citer
//+-------------------------------------+
function barre_forum_citer($texte, $lan)
{
	if (!$premiere_passe = rawurldecode(_request('retour_forum'))) {
		if(_request('citer')=='oui'){
			$id_citation = _request('id_forum') ;
			$row = sql_fetsel('auteur,texte','spip_forum',"id_forum=$id_citation");
			$aut_cite=$row['auteur'];
			$text_cite=$row['texte'];
			//ajout de la citation
			$texte="{{ $aut_cite $lan }}\n<quote>\n$text_cite</quote>\n";
		}
	}
	return $texte;
} // barre_forum_citer

// ------------------------------------------------------------------------------
// chryjs : 11/1/8
// fait un join sur un tableau compose de key="nomauteur" value ="nbposts"
// utilise $filtre pour faire ses remplacements ligne par ligne
// ------------------------------------------------------------------------------
function spipbb_join_membre($liste_cnt=array(),$filtre="%NOM% [%TOTAL%]<br />"){
	reset($liste_cnt);
	$res = "";
	while (list($nom,$nb)=each($liste_cnt)) {
		$res .= str_replace(array("%NOM%","%TOTAL%"),array($nom,$nb." "._T('spipbb:message_s')),$filtre)."\n";
	}
	return $res;
} // spipbb_join_membre

// ------------------------------------------------------------------------------
// chryjs : 12/1/8
// Identifie si un (id_)auteur est moderateur de l'article == forum passé en paramètre
// Retourne 'oui' si modo, 'non' dans les autres cas
// Attention en 1.9.2 , $id_auteur _doit_ etre un int sinon -> pas autorise
// ------------------------------------------------------------------------------
function is_modo($id_auteur=0,$id_article=0) {
	if (!function_exists('autoriser')) include_spip('inc/autoriser'); // 1.9.2 surtout
	if (autoriser('modifier','article',$id_article,intval($id_auteur))) return 'oui';
	else return 'non';
} // is_modo

// ------------------------------------------------------------------------------
// chryjs : 14/12/8
// disparition du gros hack sur les visites
// et ajout d'une fonction de calcul !
// ------------------------------------------------------------------------------

function spipbb_calc_visites($id_forum=NULL) {
	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (preg_match(
	',google|yahoo|msnbot|crawl|lycos|voila|slurp|jeeves|teoma,i',
	$_SERVER['HTTP_USER_AGENT']))
		return;

	// Identification du client
	$client_id = substr(md5(
		$GLOBALS['ip'] . $_SERVER['HTTP_USER_AGENT']
//		. $_SERVER['HTTP_ACCEPT'] # HTTP_ACCEPT peut etre present ou non selon que l'on est dans la requete initiale, ou dans les hits associes
		. $_SERVER['HTTP_ACCEPT_LANGUAGE']
		. $_SERVER['HTTP_ACCEPT_ENCODING']
	), 0,10);

	//
	// stockage sous forme de fichier tmp/spipbb-visites
	//

	spipbb_log("calcule les stats ".$id_forum,3,__FILE__);

	// 1. Chercher s'il existe deja une session pour ce numero IP.
	$content = array();
	$fichier = sous_repertoire(_DIR_TMP, 'spipbb-visites') . $client_id;
	if (lire_fichier($fichier, $content)) {
		spipbb_log("Contenu stats:".serialize($content),3,__FILE__);
		$content = @unserialize($content);
	}

	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {

	// Identification de l'element
	// Attention il s'agit bien des $GLOBALS, regles (dans le cas des urls
	// personnalises), par la carte d'identite de la page... ne pas utiliser
	// _request() ici !
		if ($id_forum)
			$log_type = "forum";
		else
			$log_type = "";

		if ($log_type)
			$log_type .= "\t" . intval($GLOBALS["id_$log_type"]);
		else    $log_type = "autre\t0";

		if (isset($content[$log_type])) {
			$content[$log_type]++;
		}
		else	$content[$log_type] = 1; // bienvenue au club

		spipbb_log("Enregis stats:".serialize($content),3,__FILE__);
		ecrire_fichier($fichier, serialize($content));
	}

} // spipbb_calc_visites

function spipbb_styliser($flux){
	if (spipbb_is_configured()) {
		// si article ou rubrique
		if (($fond = $flux['args']['fond'])
		AND in_array($fond, array('article','rubrique'))) {
			$ext = $flux['args']['ext'];
			// [...]
			if ($id_rubrique = $flux['args']['id_rubrique'] AND lire_config('spipbb/utiliser_styliser', 'on')=='on') {
				// calcul du secteur
				$id_secteur = sql_getfetsel('id_secteur', 'spip_rubriques', 'id_rubrique=' . intval($id_rubrique));
				// comparaison du secteur avec la config de spipBB
				if (lire_config('spipbb/secteur_spipbb') == $id_secteur) {
					// si un squelette $fond_spipbb existe
					if ($squelette = test_squelette_spipBB($fond, $ext)) {
						$flux['data'] = $squelette;
					}
				}
			}
		}
	}
	return $flux;
}
// retourne un squelette s'il existe
function test_squelette_spipBB($fond, $ext) {
	switch($fond) {
		case 'article' :
			if ($squelette = find_in_path(lire_config('spipbb/filforum', 'filforum').'.'.$ext)) {
				return substr($squelette, 0, -strlen(".$ext"));
			}
		break;
		case 'rubrique' :
			if ($squelette = find_in_path(lire_config('spipbb/groupeforum', 'groupeforum').'.'.$ext)) {
				return substr($squelette, 0, -strlen(".$ext"));
			}
		break;
	}
	return false;
}

function spipbb_pre_edition($flux){
	if (spipbb_is_configured() AND (lire_config('spipbb/config_spam_words', '') == 'on')) {
		if ($flux['args']['table']=='spip_forum' AND $flux['args']['action']=='instituer'){
			$spam = check_spam($id_auteur,$login,$id_forum,$id_article,_request('texte'), _request('titre'));
			if ($spam)
				$flux['data']['statut']='spam';
		}
	}
	return $flux;
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
		@sql_updateq('spip_auteurs_spipbb', array(
					'user_spam_warnings' => $is_spammer['user_spam_warnings']+1),
				"id_auteur=$id_auteur");
	} else {
		@sql_insertq('spip_auteurs_spipbb', array(
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
	$is_spammer = sql_fetsel('id_auteur, statut', 'spip_auteurs', "id_auteur=$id_auteur");
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