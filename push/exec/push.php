<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_push(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $couleur_claire;
	global $spip_lang_right;

	include_spip('inc/presentation');

	debut_page(_T('push:page_zones_acces'));
	echo "<br /><br /><br />";
	gros_titre(_T('push:titre_config_push'));
	debut_gauche();

	debut_boite_info();
	echo propre(_T('push:info_page'));
	fin_boite_info();

	debut_droite();
	//que les admins ...
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('push:avis_non_acces_page');
		fin_page();
		exit;
	}

	debut_cadre_relief("fiche-perso-24.gif", false, "", _T("push:icone_test_connection_email"));
	echo info_boite_email() ;
	fin_cadre_relief();


	//reponse du formulaire
	if (isset($_POST['exec']) and $_POST['exec']=="push" ) {
		//on stocke les infos dans le meta
		//y compris le mot de passe en clair !!!
		ecrire_meta('push_serveur_pop',$_POST['push_serveur_pop']);
		ecrire_meta('push_user',$_POST['push_user']);
		ecrire_meta('push_password',$_POST['push_password']);
		ecrire_meta('push_port',$_POST['push_port']);
		ecrire_meta('push_supprimer_message_pop3',$_POST['push_supprimer_message_pop3']);
		ecrire_meta('push_expression_reguliere',$_POST['push_expression_reguliere']);
		ecrire_meta('push_email_confirmation',$_POST['push_email_confirmation']);
		ecrire_meta('push_statut_par_defaut',$_POST['push_statut_par_defaut']);
		ecrire_meta('push_rubrique_par_defaut',$_POST['id_parent']);
		ecrire_meta('push_auteur_par_defaut',$_POST['push_auteur_par_defaut']);

		// on ecrit dans le meta
		ecrire_metas();

		//On relit les valeurs pour réafficher le forumaire
		lire_metas();
		echo _T('push:ok_ifo_sauvegardée ');
		echo formulaire_push ();

	}
	else {
		// pas sur qu'il faille faire un lire_metas mais dans le doute
		lire_metas();
		echo formulaire_push ();
	}



	fin_page();
}


function getMailBoxPop3 () {
	

	$mamailBox= get_mailBox(
	$GLOBALS['meta']['push_serveur_pop'] ,
	$GLOBALS['meta']['push_user'] ,
	$GLOBALS['meta']['push_password'],
	$GLOBALS['meta']['push_port']
	);
	return $mamailBox ;
	

}

function nbEmailPop3 ($mbox) {

	// Lecture des mails
	$count=0;
	for ($i = 1; $i <= imap_num_msg($mbox); $i++) {
		//recuperation du header
		$header = imap_headerinfo($mbox, $i,80,80) or die ('probleme de lecture du mail ');
		//Le sujet du mail
		$subject=$header->fetchsubject;

		//Doit respecter l'expression reguliere

		if(strpos($subject,$GLOBALS['meta']['push_expression_reguliere'])!==false) {
			$count++;
		}

	}
	return $count ;
}


function info_boite_email () {

	//test connexion
	$mailBox=getMailBoxPop3() ;
	if ($mailbox) {
		echo "<B>"._T('push:connexion_serveur_pop_ok')."</B><BR>";
		echo "<B>"._T('push:nb_email_pop3')."</B>:".imap_num_msg($mailbox)."<BR>";
		echo "<B>"._T('push:nb_articles_pop3')."</B> :".nbEmailPop3($mailbox)."<BR>";
	}
	else
	{
		echo "<B>"._T('push:connexion_serveur_pop_ko')."</B><BR>";
		echo "<B>"._T('push:nb_email_pop3')."</B> : 0<BR>";
		echo "<B>"._T('push:nb_articles_pop3')."</B><BR>";
	}

	//nb d'email total


	//nb d'emails qui matchent $push_expression_reguliere
}



function formulaire_push()
{
	echo generer_url_post_ecrire("push");

	echo "<div class='serif'>";

	debut_cadre_relief("fiche-perso-24.gif", false, "", _T("push:icone_informations_config"));

	debut_cadre_enfonce("site-24.gif", false, "", _T('push:info_pop'));
	echo "<B>"._T('push:serveur_pop')."</B><BR>";
	echo "<INPUT TYPE='text' NAME='push_serveur_pop' CLASS='formo' VALUE=\"".entites_html($GLOBALS['meta']['push_serveur_pop'])."\" SIZE='40'><P>\n";

	echo "<B>"._T('push:user')."</B><BR>";
	echo "<INPUT TYPE='text' NAME='push_user' CLASS='formo' VALUE=\"".entites_html($GLOBALS['meta']['push_user'])."\" SIZE='40'><P>\n";

	echo "<B>"._T('push:password')."</B><BR>";
	echo "<INPUT TYPE='password' NAME='push_password' CLASS='formo' VALUE=\"".entites_html($GLOBALS['meta']['push_password'])."\" SIZE='40'><P>\n";

	echo "<B>"._T('push:port')."</B><BR>";
	echo "<INPUT TYPE='text' NAME='push_port' CLASS='formo' VALUE=\"".entites_html($GLOBALS['meta']['push_port'])."\" SIZE='40'><P>\n";

	fin_cadre_enfonce();



	debut_cadre_enfonce("site-24.gif", false, "", _T('push:info_config'));
	echo "<B>"._T('push:supprimer_message_pop3')."</B> &nbsp; &nbsp; &nbsp; ";

	$checked=($GLOBALS['meta']['push_supprimer_message_pop3']=="oui") ? "checked=\"checked\"" : "" ;
	echo "<input type='radio' name='push_supprimer_message_pop3' value='oui' id='label_0' $checked /> ";
	$label=($GLOBALS['meta']['push_supprimer_message_pop3']=="oui") ? "<b>Oui</b>" : "Oui" ;
	echo "<label for='label_0'>$label</label>";
	$checked=($GLOBALS['meta']['push_supprimer_message_pop3']=="non") ? "checked=\"checked\"" : "" ;
	echo "&nbsp; <input type='radio' name='push_supprimer_message_pop3' value='non' id='label_1'  $checked /> ";
	$label=($GLOBALS['meta']['push_supprimer_message_pop3']=="non") ? "<b>Non</b>" : "Non" ;
	echo "<label for='label_1'>$label</label>";


	echo "<br><br><B>"._T('push:email_confirmation')."</B><BR>";
	echo "<INPUT TYPE='text' NAME='push_email_confirmation' CLASS='formo' VALUE=\"".entites_html($GLOBALS['meta']['push_email_confirmation'])."\" SIZE='40'><P>\n";

	echo "<B>"._T('push:expression_reguliere')."</B><BR>";
	echo "<INPUT TYPE='text' NAME='push_expression_reguliere' CLASS='formo' VALUE=\"".entites_html($GLOBALS['meta']['push_expression_reguliere'])."\" SIZE='40'><P>\n";

	fin_cadre_enfonce();



	debut_cadre_enfonce("site-24.gif", false, "", _T('push:info_defaut'));


	echo liste_auteur();

	include_spip('inc/rubriques');
	echo "&nbsp;<B>"._T('push:texte_rubrique_par_defaut')."</B>" ;
	echo selecteur_rubrique_html($GLOBALS['meta']['push_rubrique_par_defaut'], 'rubrique', false);

	echo "<p>&nbsp;".liste_statut ();


	fin_cadre_enfonce();

	echo "<p align='right'><br><INPUT TYPE='submit' VALUE='"._T('bouton_valider')."' CLASS='fondo'>";

	echo "</form>";
	fin_cadre_relief();


}


/* reprise de exec/articles/afficher_statut_articles */
function liste_statut()
{
	$texte="";
	$texte.="<B>"._T('push:texte_statut_par_defaut')."</B>";
	$texte.="\n<SELECT NAME='push_statut_par_defaut' SIZE='1' CLASS='fondl'\n";
	$selected=($GLOBALS['meta']['push_statut_par_defaut']=="prepa") ? "selected" : "" ;
	$texte.="<OPTION $selected VALUE=\"prepa\" style='background-color: white'>"._T('texte_statut_en_cours_redaction')."</OPTION>\n";
	$selected=($GLOBALS['meta']['push_statut_par_defaut']=="prop") ? "selected" : "" ;
	$texte.="<OPTION $selected VALUE=\"prop\" style='background-color: #FFF1C6'>"._T('texte_statut_propose_evaluation')."</OPTION>\n";
	$selected=($GLOBALS['meta']['push_statut_par_defaut']=="publie") ? "selected" : "" ;
	$texte.="<OPTION $selected VALUE=\"publie\" style='background-color: #B4E8C5'>"._T('texte_statut_publie')."</OPTION>\n";
	$texte.="</SELECT>";
	return $texte;
}



/* reprise de exec/articles/ajouter_auteurs_articles*/
function liste_auteur ()
{
	$texte="";
	$result = spip_query("SELECT * FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='6forum' AND statut!='nouveau' ORDER BY statut, nom");

	$num = spip_num_rows($result);
	if ($num) {
		$texte.= "<B>"._T('push:titre_auteur_par_defaut')."&nbsp; </B>\n";
		$texte.= "<SELECT NAME='push_auteur_par_defaut' SIZE='1' STYLE='width:250px;' CLASS='fondl' \">";
		while ($row = spip_fetch_array($result)) {
			$id_auteur = $row["id_auteur"];
			$nom = $row["nom"];
			$email = $row["email"];
			$statut = $row["statut"];

			$statut=str_replace("0minirezo", _T('info_administrateurs'), $statut);
			$statut=str_replace("1comite", _T('info_redacteurs'), $statut);
			$statut=str_replace("6visiteur", _T('info_visiteurs'), $statut);

			$premiere = strtoupper(substr(trim($nom), 0, 1));

			if ($connect_statut != '0minirezo')
			if ($p = strpos($email, '@'))
			$email = substr($email, 0, $p).'@...';
			if ($email)
			$email = " ($email)";

			if ($statut != $statut_old) {
				$texte.= "\n<OPTION VALUE=\"x\">";
				$texte.= "\n<OPTION VALUE=\"x\" style='background-color: $couleur_claire;'> $statut";
			}

			if ($premiere != $premiere_old AND ($statut != _T('info_administrateurs') OR !$premiere_old)) {
				$texte.= "\n<OPTION VALUE=\"x\">";
			}

			$selected=($GLOBALS['meta']['push_auteur_par_defaut']==$id_auteur) ? "selected" : "" ;
			$texte.= "\n<OPTION $selected VALUE=\"$id_auteur\">&nbsp;&nbsp;&nbsp;&nbsp;";
			$texte.=supprimer_tags(couper(typo("$nom$email"), 200));
			$statut_old = $statut;
			$premiere_old = $premiere;
		}

		$texte.= "</SELECT>";
	}
	$texte.= "</div>";
	return $texte ;
}


function get_mailBox($server , $user , $passwd , $port=110){
	if (! $mbox= @imap_open("{".$server."/pop3:$port"."}", $user, $passwd) )
	 echo "Probleme : ". imap_last_error();
	return $mbox;
}
?>