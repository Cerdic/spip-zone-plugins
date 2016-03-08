<?php
/**
 * Gestion du génie Publication par email
 *
 * @plugin Publication par email
 * @license GPL
 * @package SPIP\Emailtospip\Genie
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_emailtospip_dist($t){
	// chargement configuration
	include_spip('inc/config');

	$email = lire_config('emailtospip/email');
	$email_pwd = lire_config('emailtospip/email_pwd');
	$hote_imap = lire_config('emailtospip/hote_imap');
	$hote_port = lire_config('emailtospip/hote_port');
	$hote_inbox = lire_config('emailtospip/inbox');
	$pwd = lire_config('emailtospip/pwd');

	if (lire_config('emailtospip/import_statut')=="publie") $import_statut = "publie";  else $import_statut = "prop";
	$id_rubrique = intval(lire_config('emailtospip/id_rubrique'));
	$id_secteur  = sql_getfetsel("id_secteur", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
	$lang = lire_meta("langue_site");


	$limit = 20; // max d'emails à traiter en une passe (pour éviter le timeout)
	$pagination = 50; // nb emails à examiner (pour ne pas consulter toute la boite)

	if ($hote_imap!="") {
			// connection or die
			$connection = '{'.$hote_imap.':'.$hote_port.'}'.$hote_inbox;
			$mbox = @imap_open($connection, $email, $email_pwd);

			if (FALSE === $mbox) {
				spip_log("connection $connection impossible","emailtospip");
				return false;
			} else {
				// lecture boite
				$info = imap_check($mbox);
				if (FALSE === $info) {
					spip_log("Impossible de lire le contenu de la boite mail","emailtospip");
					return false;
				} else {
					// lire des derniers msgs
					$nbMessages = $info->Nmsgs;
					$nbMessagesMin =  max(1,$nbMessages- $pagination);
					$mails = imap_fetch_overview($mbox, "$nbMessagesMin:$nbMessages", 0);

					$i=0;
					foreach ($mails as $mail) {
						$sujet = imap_utf8_fix($mail->subject);
						$uid = $mail->uid; 
						$msgno = $mail->msgno;
						if (preg_match_all("#<(.*?)>#ims",$mail->from, $matches,PREG_SET_ORDER))    // buzz <buzz@buzz.org> ->  buzz@buzz.org
									$email_from = $matches[0][1];
							else  $email_from = $mail->to;
						//echo "<h1>NEW EMAIL</h1>- $i ($msgno/$uid) : $sujet ";    // debug

						// en mode mot de passe, ne selectionner que les emails avec le mot titre
						if ($pwd!="") {
							if (substr($sujet,0,strlen($pwd)) == $pwd)  {
								$sujet = substr($sujet,strlen($pwd));
								$import = true;
							} else  {
								$import = false;
							}
						} else {
							$import = true;
						}

						if ($import) {
							if ($i++<$limit)
								emailtospip_mail($uid,$mbox,$sujet,$email_from,$import_statut,$id_rubrique,$id_secteur,$lang);
						}
					}  #foreach
					imap_close($mbox,CL_EXPUNGE);
					return true; 
				}
			}
	}

	return 1;
}


// bug de casse sur les vieilles versions PHP 
// http://docs.php.net/manual/fr/function.imap-utf8.php
// https://bugs.php.net/bug.php?id=44098
// http://svn.php.net/viewvc/?view=revision&revision=294699
function imap_utf8_fix($string) {
	if (version_compare(phpversion(), '5.3.3', '>=')) {
			spip_log("decodage sujet (5.3+):".$string,"emailtospip");
			return $string;
	}     else   {
			spip_log("decodage sujet (5.3-):".$string." : ".iconv_mime_decode($string,2,"UTF-8") ,"emailtospip");
			return iconv_mime_decode($string,0,"UTF-8");
	}
}

//
// import un email en tant qu'article spip
//        puis efface l'email de la boite
// 
// @uid   uid de l'email
// @mbox  connection imap
// @sujet sujet de l'email
// @email email de l'expediteur
// @.... 
function emailtospip_mail($uid,$mbox,$sujet,$email,$import_statut,$id_rubrique,$id_secteur,$lang) {
	include_spip('inc/texte'); // pour safehtml

	// lecture de l'email    
	$headerText = imap_fetchHeader($mbox, $uid, FT_UID);
	$header = imap_rfc822_parse_headers($headerText);

	// REM: Attention s'il y a plusieurs sections
	$structure = imap_fetchstructure($mbox, $uid, FT_UID);

	//$corps = imap_fetchbody($mbox, $uid, 2, FT_UID);  // 1: plain text 2: html
	//$corps = imap_body($mbox, $uid, FT_UID);     // pas assez precis ex. gmail alternative txt et html melange

	// HTML disponible   ?
	if ($corps = imap_fetchbody($mbox, $uid, 2, FT_UID)) {
			$corps = quoted_printable_decode($corps); 
			// si le html contient  un <html><body> on essaie de virer pas regex
			$pattern = "#<body[^>]*>(.*?)<\/body>#ims";
			if (preg_match_all($pattern, $corps, $matches,PREG_SET_ORDER))  {
				$corps = $matches[0][1];
				spip_log("email $sujet (type: ".$structure->subtype.") HTML avec body regex","emailtospip");
			} else {
			// cas gmail, on fait rien ... on garde le corps sans regex
				spip_log("email $sujet (type: ".$structure->subtype.") HTML sans body","emailtospip");
			}
	} else {
			// pas HTML disponible, on prend le PLAIN TXT
			$corps = imap_fetchbody($mbox, $uid, 1, FT_UID);
			$corps = quoted_printable_decode($corps);
			spip_log("email $sujet (type: ".$structure->subtype.") TXT","emailtospip");
	};

	// ....dans la table articles 
	$date =  date('Y-m-d H:i:s',time());
	$id_nouvel_article = sql_insertq("spip_articles",array(
												'lang' => $lang,
												'titre' => safehtml($sujet),
												'id_rubrique' => $id_rubrique,
												'id_secteur' => $id_secteur,
												'texte' => safehtml($corps),       // utiliser une filtrage genre sale ?
												'statut' => $import_statut,
												'accepter_forum' => 'non',
												'date' => $date
												));
	// ... l'auteur est connu ?
	if ($id_auteur  = sql_getfetsel("id_auteur", "spip_auteurs", "email='$email'")) {
			sql_insertq("spip_auteurs_liens",array(
												'id_auteur' => $id_auteur,
												'id_objet' => $id_nouvel_article,
												'objet' => 'article',
												'vu' => 'non'
												));
	}

	// on supprime l'email  
	imap_delete($mbox, $uid, FT_UID);

	return true;
}

?>