<?php
/**
 * saveauto : plugin de sauvegarde automatique de la base de donnees de SPIP
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 *
 **/

function saveauto_trouve_table($table, $tableau_tables) {
    $trouve = false;
    foreach ($tableau_tables as $t)	{
        if (strstr($table, $t)) {
            $trouve = true;
            break;
        }
    }
    return $trouve;
}


//fonction originale mail_attachement en utilisation libre
//Auteur : Damien Seguy
//Url : http://www.nexen.net
//modifiee pour plus de souplesse sur les entetes
function saveauto_mail_attachement($to , $sujet , $message , $fichier, $nom, $reply="", $from="") {
	include_spip('inc/envoyer_mail');
    if (!function_exists('mail')) {
        echo _T('saveauto:config_inadaptee').' '._T('saveauto:mail_absent').'<br />';
        return false;
    }
    $from = $reply = lire_config('email_webmaster');

    $limite = "_parties_".md5(uniqid (rand()));

    $mail_mime = "Date: ".date("l j F Y, G:i")."\n";
    $mail_mime .= "MIME-Version: 1.0\n";
    $mail_mime .= "Content-Type: multipart/mixed;\n";
    $mail_mime .= " boundary=\"----=$limite\"\n\n";

    //Le message en texte simple pour les navigateurs qui n'acceptent pas le HTML
    $texte = _T('saveauto:message_MIME')."\n";
    $texte .= "------=$limite\n";
    $texte .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
    $texte .= "Content-Transfer-Encoding: 32bit\n\n";
    $texte .= $message;
    $texte .= "\n\n";

	if((filesize($fichier) / 1000000) < lire_config('saveauto/mail_max_size','2')){
	    //le fichier
	    $attachement = "------=$limite\n";
	    $attachement .= "Content-Type: application/octet-stream; name=\"$nom\"\n";
	    $attachement .= "Content-Transfer-Encoding: base64\n";
	    $attachement .= "Content-Disposition: attachment; filename=\"$nom\"\n\n";

	    $fp = fopen($fichier, "rb");
	    $buff = fread($fp, filesize($fichier));

	    fclose($fp);
	    $attachement .= chunk_split(base64_encode($buff));

	    $attachement .= "\n\n\n------=$limite\n";
	}else{
		$fichier = str_replace(_DIR_RACINE,'',$fichier);
		$texte .= _T('saveauto:mail_fichier_lourd',array('fichier'=>$fichier));
	}

	if (! empty($reply)) $entete = "Reply-to: $reply\n";

	$envoyer_mail = charger_fonction('envoyer_mail','inc');

	$mail = $envoyer_mail($to, $sujet, $texte.$attachement, $from, $entete.$mail_mime);
    return $mail;
}

function saveauto_mysql_version() {
   $result = sql_query('SELECT VERSION() AS version');
   if ($result != FALSE && sql_count($result) > 0) {
      $row = mysql_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else {
      $result = sql_query('SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && sql_count($result) > 0) {
         $row = mysql_fetch_row($result);
         $match = explode('.', $row[1]);
      }
   }

   if (!isset($match) || !isset($match[0])) $match[0] = 3;
   if (!isset($match[1])) $match[1] = 21;
   if (!isset($match[2])) $match[2] = 0;
   return $match[0] . "." . $match[1] . "." . $match[2];
}

?>