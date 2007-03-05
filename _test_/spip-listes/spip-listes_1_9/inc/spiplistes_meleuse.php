<?php

/******************************************************************************************/
/* SPIP-Listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, +tats-Unis.                   */
/******************************************************************************************/

// Trouver un message à envoyer

include_spip('inc/meta');
include_spip('inc/texte');
include_spip('inc/filtres');
include_spip('inc/acces');

include_spip('spiplistes_boutons');
include_once(_DIR_PLUGIN_SPIPLISTES.'/inc/spiplistes_mail.inc.php');

#$charset=lire_meta('charset');
$GLOBALS['meta']['spiplistes_charset_envoi'] = $charset = 'iso-8859-1';

global $table_prefix;
$query_message = "SELECT * FROM ".$table_prefix."_messages AS messages WHERE statut='encour' AND (TYPE='auto' OR TYPE='nl') ORDER BY date_heure ASC LIMIT 0,1";

$result_pile = spip_query($query_message);
$message_pile = spip_num_rows($result_pile);

if ($message_pile > 0){
//locker
	$meta_liste = lire_meta('lock');
	$meta_liste = "oui" ;
	ecrire_meta('lock' , $meta_liste);
	ecrire_metas();

	// Message
	$row = spip_fetch_array($result_pile);
  	$texte = $row["texte"];
 	$texte_original = $texte ; // pour les envois de test
	
	$texte = stripslashes($texte);
	$titre = typo($row["titre"]);
	
	$type = $row["type"];
	$id_message = $row["id_message"];

	// Determiner le destinataire ou la liste destinataire
	eregi("^__bLg__[0-9@\.A-Z_-]+__bLg__", $texte, $res );
	$destinataires = str_replace("__bLg__","",$res[0]);
	
		//est-ce un mail de test ?
		if( eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$",$destinataires) ){
		spip_log( _T('spiplistes:email_test')." : ".$destinataires);
		$test = 'oui';
		$mail_collectif = 'non' ;
		} else {
			//est-ce un mail collectif ?
			if($destinataires == 'tous'){
			$mail_collectif = 'oui' ;
			spip_log(_T('spiplistes:envoi_tous')) ;
			} else {
			//c'est un mail pour une liste alors ?
			$mail_collectif = 'non' ;
			$query_d = "SELECT * FROM spip_articles WHERE id_article=$destinataires";
			$result_d = spip_query($query_d);
		
				if(spip_num_rows($result_d)>0){
					while($ro = spip_fetch_array($result_d)) {
					$titre_liste = $ro["titre"];
					$id = $ro["id_article"];
					$mail_envoi = get_extra($id,"article") ;
					$email_liste = $mail_envoi['email_envoi'];
					spip_log(_T('spiplistes:envoi_listes').$titre_liste);
					}
				}else{			//erreur
				   spip_log(_T('spiplistes:envoi_erreur'));
				}
			}
		}
	
	
	$email_webmaster = lire_meta("email_webmaster");
	$from = email_valide_bloog($email_liste) ? $email_liste : $email_webmaster;

	// virer les destinataires du texte du message
	$texte = eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte);
            
    // préparer le message pour l'envoi
	
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
  	if (isset($style_reg[0])) $style_str = $style_reg[0]; 
                         else $style_str = "";
  	
	
	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);

	$texte = propre($texte); // pb: enleve aussi <style>...  
	$texte = propre_bloog($texte);

 	$texte = ereg_replace("__STYLE__", $style_str, $texte);

	$nomsite=lire_meta("nom_site");
	$urlsite=lire_meta("adresse_site");
	srand((double)microtime()*1000000);
	$boundary = md5(uniqid(rand()));

	$objet= filtrer_entites($titre);
 	if ($charset <> 'utf-8') {
 	   $objet = str_replace("&#8217;", "'", $objet);
	   $objet = str_replace("&#8220;", "\"", $objet);
	   $objet = str_replace("&#8221;", "\"", $objet);
 	}
	$objet = unicode2charset(charset2unicode($objet),$charset);
	include_spip('inc/filtres');
	$texte = liens_absolus($texte);
	
	// on prépare la version texte
	
	if ($charset <> 'utf-8') {
		$texte = str_replace("&#8217;", "'", $texte);
		$texte = str_replace("&#8220;", "\"", $texte);
		$texte = str_replace("&#8221;", "\"", $texte);
	}
	
  
    $page_ = version_texte($texte);

	$page_.="\n\n________________________________________________________________________"  ;
	$page_.="\n\n"._T('spiplistes:editeur').$nomsite."\n"  ;
	$page_.=$urlsite."\n";
	$page_.="________________________________________________________________________"  ;
	
	
	// on prépare la version html


	$pageh = "<html>\n\n<body>\n\n".$texte."\n\n";
	// la fin de la version html sera générée pour chaque destinataire
  
	// Envoi par lot
	// Compter les inscrits

	$query = ''; 
	
	if($test == 'oui'){
		global $table_prefix;
		$query = "SELECT id_auteur FROM ".$table_prefix."_auteurs WHERE email = '$destinataires' ORDER BY id_auteur ASC ";
	}	else{
		if($mail_collectif == 'non'){
			global $table_prefix;
			$query = "SELECT id_auteur FROM ".$table_prefix."_auteurs_articles WHERE id_article = '$destinataires' ORDER BY id_auteur ASC ";
		}elseif($mail_collectif == 'oui'){
			// attention aux adresse à la poubelle
			global $table_prefix;
			$query = "SELECT nom FROM ".$table_prefix."_auteurs ORDER BY nom ASC";
		}
	}

	$result_inscrits = spip_query($query);
	$nb_inscrits = spip_num_rows($result_inscrits);

	ecrire_meta('total_auteurs' , $nb_inscrits);
	ecrire_metas();

	spip_log(_T('spiplistes:email_reponse').$from."\n"._T('spiplistes:contacts')." : ".$nb_inscrits) ;

	if($test == 'oui' && $nb_inscrits == 0){
  	// à tester avant d arriver la normalement
  	spip_log(_T('spiplistes:sans_envoi')) ;
	}
	
	if($nb_inscrits > 0){
	
	// initialiser le compteur 
	if(!$meta_liste = lire_meta('debut')){
	$meta_liste = 0 ;
	ecrire_meta('debut' , $meta_liste);
	ecrire_metas();
	}
	
	spip_log(_T('spiplistes:message'). $titre);
	
		//Envoi par lots
		$debut = lire_meta('debut') ;
		$limit=20; // nombre de messages envoyés par boucles.	
		if($test== 'oui'){
			global $table_prefix;
			$query = "SELECT id_auteur FROM ".$table_prefix."_auteurs WHERE email = '$destinataires' ORDER BY id_auteur ASC ";
		}	else{
			if($mail_collectif == 'oui'){
				$query = "SELECT nom, id_auteur, email, extra FROM ".$table_prefix."_auteurs ORDER BY nom ASC LIMIT $debut,$limit";
			} elseif ($mail_collectif == 'non'){
				$query = "SELECT id_auteur FROM ".$table_prefix."_auteurs_articles WHERE id_article = '$destinataires' ORDER BY id_auteur ASC LIMIT $debut,$limit";
			} else {
        $query='';
      }
		}
	
		$result_inscrits = spip_query($query);
		$liste_abonnes = spip_num_rows($result_inscrits);
	
		if($liste_abonnes > 0){
	// on modifie le cran du compteur avant d'envoyer les mails pour éviter les doublons en cas d'erreur pendant l'envoi.
	// du coup, on peut perdre des envois si ca plante...	 
		  $debut = $debut+$limit;
	
			if ($debut>=$nb_inscrits) {
				if ($test== 'oui') {				
  				$texte_original = eregi_replace("__bLg__[0-9@\.A-Z_-]+__bLg__","",$texte_original);
  				$texte_original = addslashes($texte_original);
  			spip_query("UPDATE ".$table_prefix."_messages SET statut='redac', texte='$texte_original' WHERE id_message='$id_message'");
  				ecrire_meta('debut', 0 ) ;
  				ecrire_meta('total_auteurs', 0 ) ;
				} else {
  				// archiver
  				$texte = addslashes($texte) ;
  				spip_query("UPDATE ".$table_prefix."_messages SET statut='publie' WHERE id_message='$id_message'");
  				ecrire_meta('debut', 0 ) ;
  				ecrire_meta('total_auteurs', 0 ) ;
				}	
			//attention si on interrompt
			} else {
			 ecrire_meta('debut', $debut ) ;
			}	
		ecrire_metas();			
	
		while ($row2 = spip_fetch_array($result_inscrits)) {
			  $str_temp = " ";
				$id_auteur = $row2['id_auteur'] ;
				$query = "SELECT nom, id_auteur, email, extra FROM ".$table_prefix."_auteurs WHERE id_auteur = $id_auteur ";
				$res = spip_query($query);
				$row3 = spip_fetch_array($res);
		
				$nom_auteur = $row3["nom"];
				$extra = unserialize ($row3["extra"]);
				
				$str_temp .= $nom_auteur." format : ".$extra['abo'];

				$email = $row3["email"];
				$id = $row3["id_auteur"];
				$total=$total+1;
				unset ($cookie);
				
				if (($extra["abo"] == 'texte') OR ($extra["abo"] == 'html')) $abo = true;
				                                                       else $abo = false;				

				
				if ($abo) {
					$cookie = creer_uniqid();
					spip_query("UPDATE spip_auteurs SET cookie_oubli = '$cookie' WHERE email ='$email'");				
		
					// pied de page texte			
					$pagem = $page_."\n\n"  ;
					$pagem.= filtrer_entites(_T('spiplistes:abonnement_mail'))."\n" ;
					$pagem.= filtrer_entites(generer_url_public('abonnement','d='.$cookie))."\n\n"  ;
					include_spip('inc/charsets');
					$pagem = unicode2charset(charset2unicode($pagem),$charset);
				
				if ($extra["abo"] == 'texte'){    // email TXT -----------------------

					// fin du pied de page texte					
						$email_a_envoyer = new phpMail($email, $objet, '',$pagem);
						if (email_valide_bloog($from)){	
						$email_a_envoyer->From = $from ;
						$email_a_envoyer->AddCustomHeader("Errors-To: ".$from);
						$email_a_envoyer->AddCustomHeader("Reply-To: ".$from);
						$email_a_envoyer->AddCustomHeader("Return-Path: ".$from);
	
							if ($email_a_envoyer->send()) {
							     $str_temp .= "->ok";
							     $cmpt++;
              } else {
                  $str_temp .= _T('spiplistes:erreur_mail');
             }
            } else { 
              $str_temp .= _T('spiplistes:sans_adresse');
            }
		
					} else if ($extra["abo"] == 'html') {  // email HTML ------------------
		
						$pagehm = $pageh."<hr style=\"noshade color:#000;size:1px;\" />"._T('spiplistes:editeur')."<a href=\"".$urlsite."\">".$nomsite."</a><br /><a href=\"".$urlsite."\">".$urlsite."</a><hr style=\"noshade color:#000;size:1px;\"/>
						<a href=\"".generer_url_public('abonnement','d='.$cookie)."\">"._T('spiplistes:abonnement_mail')."</a>\n\n</body></html>";
						$pagehm = unicode2charset(charset2unicode($pagehm),$charset);
						
		
						// fin du pied de page HTML
		$email_a_envoyer = new phpMail($email, $objet, $pagehm, $pagem);
						if (email_valide_bloog($from)){	
						$email_a_envoyer->From = $from ;
						$email_a_envoyer->AddCustomHeader("Errors-To: ".$from);
						$email_a_envoyer->AddCustomHeader("Reply-To: ".$from);
						$email_a_envoyer->AddCustomHeader("Return-Path: ".$from);

							if ($email_a_envoyer->send()){
							     $str_temp .= "->ok";
							     $cmpt++;
              } else {
                  $str_temp .= _T('spiplistes:erreur_mail');
              }							
						} else { 
              $str_temp .= _T('spiplistes:sans_adresse') ;
            }
		
					}    // email fin TXT /HTML  -----------------------------------------
				
					
				spip_log($str_temp);								
				$total_abo = $total_abo + 1;			
				} /* fin abo*/
				   
		
			}      /* fin while */
		}    /* fin liste abonnés */	
	
	} else {
	//aucun destinataire connu pour ce message
	spip_log(_T('spiplistes:erreur_sans_destinataire')."---"._T('spiplistes:envoi_annule'));
		 
	spip_query("UPDATE ".$table_prefix."_messages SET titre='"._T('spiplistes:erreur_destinataire')."', statut='publie' WHERE id_message='$id_message'"); 
	ecrire_meta('debut', 0 ) ;
  	ecrire_meta('total_auteurs', 0 ) ;
	ecrire_metas();
	}
	
	//delocker
	$meta_liste = lire_meta('lock');
	$meta_liste = "non" ;
	ecrire_meta('lock' , $meta_liste);
	ecrire_metas();
	

} else {

	spip_log(_T('spiplistes:envoi_fini'))   ;
	//delocker
	$meta_liste = lire_meta('lock');
	$meta_liste = "non" ;
	ecrire_meta('lock' , $meta_liste);
	ecrire_metas();

}	/* flag pile*/

?>
