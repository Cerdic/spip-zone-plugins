<?php
/**
 * Pack langue allemand
 * 
 * @package spiplistes
 */
 // $LastChangedRevision: 47066 $
 // $LastChangedBy: paladin@quesaco.org $
 // $LastChangedDate: 2011-04-25 19:54:15 +0200 (Lun 25 avr 2011) $
 
$GLOBALS['i18n_spiplistes_de'] = array(


//_ 
'_aide' => '<p>Mit SPIP-Listes k&ouml;nnen Sie Mails und automatisierte Nachrichten an Abonnenten senden.</p> <p>Sie k&ouml;nnen einen einfachen Text schreiben, Ihre Nachricht als HTML gestalten oder sie mit einer "Vorlage" formatieren und erg&auml;nzen.</p>
<p>Mit Hilfe eines &ouml;ffentlichen Formulars k&ouml;nnen die Abonnenten ihren Status selber &auml;ndern und bestimmen, ob sie Nachrichten im Text- oder HTML-Format erhalten wollen.</p><p>Die Mails werden automatisch in das Text-Format umgewandelt, wenn ein Abonnent diese Versandform gew&auml;hlt hat.</p><p><b>Anmerkung :</b><br />
Der Versand der Mails kann einige Minuten in Anspruch nehmen: Die Empf&auml;nger werden nach und nach abgearbeitet, wenn Besucher die Website aufrufen. Sie k&ouml;nnen den Versand auch manuell ausl&ouml;sen, indem Sie auf den Link "Versand verwalten" klicken.</p>',

// A
'abo_1_lettre' => 'Newsletter',
'abonnement_0' => 'Abonnement',
'abonnement'=>'Sie m&ouml;chten Ihr Abonnement des Newsletters bearbeiten',
'abonnement_bouton'=>'Ihr Abonnement bearbeiten',
'abonnement_cdt' => '<a href=\'http://bloog.net/spip-listes/\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'Sie k&ouml;nnen das Format, in dem Sie den Newsletter erhalten &auml;ndern: ',
'modif_abonnement_text' => 'Um Ihr Abonnement zu bearbeiten, gehen Sie bitte zu dieser Adresse',
'abonnement_mail_passcookie' => '(Dieses ist eine automatische Nachricht)
Um Ihr Abonnement der Website: @nom_site_spip@ (@adresse_site@) zu bearbeiten,
gehen Sie bitte zu dieser Adresse:

    @adresse_site@/spip.php?page=abonnement&d=@cookie@

Sie k&ouml;nnen dann die &auml;nderung Ihres Abonnements best&auml;tigen.',
'abonnement_modifie'=>'Ihre &auml;nderungen wurden gespeichert',
'abonnement_nouveau_format'=>'Sie haben dieses Format f&uuml;r Ihr Abonnement gew&auml;hlt: ',
'abonnement_titre_mail'=>'Ihr Abonnement &auml;ndern',
'abonnement_texte_mail'=>'Bitte geben Sie hier die E-Mail Adresse an, mit der der Sie sich angemeldet haben. Sie erhalten dann eine Mail, die Ihnen erlaubt, auf die Webseite zur &auml;nderung Ihres Abonnements zuzugreifen.',
'abonner' => 'Anmeldung',
'actualiser' => 'Aktualisieren',
'adresse' => 'Geben Sie hier die Adresse f&uuml;r Antworten (reply-to) ein (Wenn Sie keine Angabe machen, wird die Adresse des Webmasters verwendet): ',
'adresses_importees' => 'Adressen importiert',
'Courriers_en_cours_de_redaction' => 'Nachricht in Bearbeitung',
'Courriers_en_cours_denvoi' => 'Nachricht wird gesendet',
'aff_envoye' => 'Gesendete Nachrichten',
'aff_lettre_auto' => 'Gesendete Newsletter',
'aff_envoye' => 'Gesendete Nachrichten',
'alerte_edit' => 'Achtung: Diese Nachricht kann von allen Administratoren der Website ge&auml;ndert werden und wird an alle Abonnenten gschickt. Verwenden Sie den Newsletter nur f&uuml;r wichtige Ereignisse der Website.',
'alerte_modif' => '<b>Nachdem Ihre Nachricht angezeigt wurde, k&ouml;nnen Sie ihren Inhalt bearbeiten</b>',
'annuler_envoi' => 'Versand abbrechen',

//B

//C
'Cette_liste_est' => 'Diese Liste ist',
'charger_patron' => 'Vorlage f&uuml;r die Nachricht w&auml;hlen',
'charger_le_patron' => 'Nachricht erzeugen',
'Configuration' => 'Konfiguration',
'courriers' => 'Nachrichten',

//D
'definir_squel' => 'W&auml;hlen Sie eine Vorlage f&uuml;r die Vorschau der Nachricht',
'definir_squel_choix' => 'Wenn Sie eine neue Nachricht verfassen, k&ouml;nnen Sie eine Vorlage laden. Wenn Sie die Schaltfl&auml;che anklicken, wird der Inhalt einer Vorlage aus dem Verzeichnis <b>/patrons</b> (das sich im Wurzelverzeichnis Ihrer SPIP-Insatllation befindet) ihn Ihre Nachricht geladen. <p><b>Sie k&ouml;nnen diese Vorlagen nach Belieben bearbeiten und &auml;ndern.</b></p> <ul><li>Diese Vorlagen k&ouml;nnen klassisches HTML enthalten</li>
<li>Diese Vorlage kann SPIP-Code enthalten</li>
<li>Nachdem eine Vorlage geladen wurde, k&ouml;nnen Sie den Text Ihrer Nachricht vor dem Senden erneut bearbeiten.</li>
</ul><p>Die Funktion "Vorlage laden" erlaubt, individuelle HTML-Templates mit SPIP-Schleifen, die bestimmte Inhalte einf&uuml;gen, f&uuml;r Ihre Nachrichten und Newsletter &uuml;ber bestimmte Themen zu verwenden.</p><p>Achtung: Diese Templates d&uuml;rfen auf keinen Fall die Tags body, head oder html enthalten, sondern ausschlie&szlig;lich sonstigen HTML- und SPIP-Code.</p>',
'definir_squel_texte' => 'Wenn Sie &uuml;ber einen FTP-Zugang zu dieser Website verf&uuml;gen, k&ouml;nnen Sie SPIP-Templates in das Verzeichnis /patrons (im Wurzelverzeichnis der SPIP-Installation) hochladen.',
'devenir_redac'=>'Anmeldung als Redakteur der Website',
'devenir_abonne'=>'Anmeldung bei der Website',
'desabonnement_valid'=>'Diese Adresse hat den Newsletter nicht mehr abonniert.' ,
'pass_recevoir_mail'=>'Sie erhalten eine E-Mail mit Informationen zur &auml;nderung Ihres Abonnements. ',
'desabonnement_confirm'=>'Sie sind dabei, Ihr Abonnement des Newsletters zu beenden',
'date_depuis'=>'seit @delai@', 


//E
'email' => 'E-Mail',
'envoi' => 'Sendung :',
'envoi_nouv' => 'Sendung der Neuigkeiten',
'envoi_program' => 'Programmierte Sendung',
'envoi_texte' => 'Wenn Sie mit der Nachricht zufrieden sind, k&ouml;nnen Sie sie absenden',
'exporter' => 'Liste der Abonnenten exportieren',

//F
'faq' => 'FAQ',
'forum' => 'Forum',
'ferme' => 'Diese Diskussion ist geschlossen',
'form_forum_identifiants' => 'Best&auml;tigung',
'form_forum_identifiant_confirm'=>'Ihr Abonnement wurde gespeichert. Sie erhalten nun eine Best&auml;tigungsmail.',
'format' => 'Format',

//H
'Historique_des_envois' => 'Verlauf der Sendungen',

//I
'info_auto' => 'SPIP-Listes kann Ihren Abonnenten regelm&auml;&szlig;ig Informationen &uuml;ber die neuesten Inhalte (k&uuml;rzlich ver&ouml;ffentlichte Artikel und Meldungen) Ihrer Website senden.',
'info_heberg' => 'Manche Provider verhindern den automatischen Mailversand &uuml;ber ihre Server. In diesem Fall wird SPIP-Listes nicht funktionieren.',
'info_nouv' => 'Sie haben den Versand der Neuigkeiten aktiviert.',
'info_nouv_texte' => 'N&auml;chter Versand der Neuigkeiten in @proch@ Tagen',
'inscription_mail_forum' => 'Hier Ihre Zugngsdaten f&uuml;r die Website @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'Hier Ihre Zugngsdaten f&uuml;r die Website @nom_site_spip@ (@adresse_site@) und f&uuml;r das Redaktionssystem (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'Ihre Anmeldung erm&ouml;glicht Ihnen den Zugang zu den gesch&uuml;tzten Bereichen der Website, das Verfassen von Beitr&auml;gen in Foren f&uuml;r eingetragene Leser und den Erhalt der Newsletter.' ,
'inscription_redacteurs' =>'Das Redaktionssystem dieser Website ist f&uuml;r eingetragene Besucher zug&auml;nglich. Nach der Anmeldung k&ouml;nnen Sie Artikel vor ihrer Ver&ouml;ffentlichung kommentieren, eigen Artikel schreiben und vorschlagen und an allen Foren teilnehmen. Mit der Anmeldung erhalten Sie ebenfalls Zugang zu den pa&szlig;wortgesch&uuml;tzten Bereichen der Website und k&ouml;nnen die Newsletter empfangen.',
'import_export' => 'Import / Export',

//J
'jours' => 'Tage',

//L
'listes_de_diffusion_' => 'Newsletter',
'login' => 'Login',
'logout' => 'Abmelden',
'lot_suivant' => 'N&auml;chten Stapel abschicken',
'lieu' => 'Ort',
'lettre_d_information' => 'Newsletter',

//M
'mail_format' => 'Sie haben den Newsletter der Website @nom_site_spip@ im folgenden Format abonniert',
'mail_non' => 'Sie haben den Newsletter der Website @nom_site_spip@ nicht abonniert.',
'message_arch' => 'Nachricht archiviert',
'messages_auto' => 'Automatische Nachrichten',
'messages_auto_texte' => '<p>Das Standard-Template erm&ouml;glicht, automatisch eine Liste der seit dem letzten automatisch gesendeten Newsletter neu erschienenen Artikel und Meldungen Ihrer Website zu verschicken.</p><p>Sie k&ouml;nnen diesen Newsletter anpassen, indem Sie den URL eines Logos und eines Hintergrundbilds f&uuml;r Abschnitts&uuml;berschriften in der Datei <b>"nouveautes.html"</b> eintragen. Sie befindet sich im Wurzelverzeichnis Ihrer SPIP-Installation.</p>',
'message_redac' => 'In Bearbeitung und fertig f&uuml;r den Versand',
'message_en_cours' => 'Nachricht wird gesendet',
'message_type' => 'E-Mail',
'membres_liste' => 'Mitgliederliste',
'membres_groupes' => 'Benutzergruppe',
'membres_profil' => 'Profil',
'membres_messages_deconnecte' => 'Melden Sie sich an, um Ihre privaten Nachrichten zu lesen',
'membres_sans_messages_connecte' => 'Sie haben keine neuen Nachrichten',
'membres_avec_messages_connecte' => 'Sie haben @nombres@ neue Nachricht(en)',
'message' => 'Nachricht: ',
'message_date' => 'Gesendet am',
'message_sujet' => 'Betreff ',
'messages' => 'Nachrichten',
'Messages_automatiques' => 'Programmierte Nachrichten',
'messages_derniers' => 'Letzte Nachrichten',
'messages_forum_clos' => 'Forum desaktiviert',
'messages_nouveaux' => 'Neue Nachrichten',
'messages_pas_nouveaux' => 'Keine neuen Nachrichten',
'messages_non_lus_grand' => 'Keine neuen Nachrichten',
'messages_repondre' => 'Ne�e Antwort',
'messages_voir_dernier' => 'Letzte Nachricht lesen',
'moderateurs' => 'Moderator(en)',
'modifier' => '&auml;ndern',
'mis_a_jour' => 'Updated', // FIXME

//N
'nom' => 'Benutzername',
'Nouveau_courrier' => 'Neue Nachricht',
'nouveaux_messages' => 'Neue Nachrichten',
'Nouvelle_liste_de_diffusion' => 'Neuer Newsletter',

//P
'par_date' => 'Nach Anmeldedatum',
'patron_disponibles' => 'Verf&uuml;gbare Vorlagen',
'Patrons' => 'Vorlagen',
'poster' => 'Nachricht senden',

//R
'recherche' => 'Suchen',
'revenir_haut' => 'Nach oben',
'reponse' => 'Antwort auf die Nachricht',
'adresse_mail_retour' => 'E-Mail Adresse des Listenverwalters (reply-to)',

//S
'suivi' => 'Abonnements verwalten',
'Suivi_des_abonnements' => 'Abonnements verwalten',
'sujet_nouveau' => 'Neues Thema',
'sujet_auteur' => 'Autor',
'sujet_visites' => 'Besuche',
'sujets' => 'Themen',
'sujets_aucun' => 'In diesem Forum gibt es kein Thema',
'site' => 'Website',
'sujet_clos_titre' => 'Thema geschlossen',
'sujet_clos_texte' => 'Dieses Thema ist geschlossen, Sie k&ouml;nnen keinen Beitrag dazu schreiben.',
 
 //T
'texte_boite_en_cours' => 'SPIP-Listes verschickt gerade eine Nachricht. <p> Sie k&ouml;nnen den Versand beschleunigen, indem Sie auf den Link klicken.</p> <p>Dieser Kasten wird nicht mehr angezeigt, wenn die Nachricht vollst&auml;ndig gesendet wurde.</p>',
'texte_lettre_information' => 'Hier ist der Newsletter von ',
'Tous les @s@' => 'Alle @s@',

//V
'voir' => 'ansehen',
'vous_pouvez_egalement' => 'Sie k&ouml;nnen',
'vous_inscrire_auteur' => 'sich als Reakteur anmelden',

// ====================== spip_listes.php3 ======================
'abon' => 'ABONNENTEN',
'abon_ajouter' => 'ABONNENTEN hinzuf&uuml;gen&nbsp; ',
'abonees' => 'alle Abonnenten',
'abonne_listes' => 'Diese Adresse hat folgende Listen abonniert',
'abonne_aucune_liste' => 'Keine Liste abonniert',
'abonnement_simple' => '<b>Einfaches Abonnieren: </b><br /><i>Die Abonnenten erhalten eine Best&auml;tigungsmail nach dem Abonnieren.</i>',
'abonnement_code_acces' => '<b>Abonnement mit Zugangsdaten: </b><br /><i>Die Abonnenten erhalten au&szlig;erdem einen Benutzernamen und ein Pa&szlig;wort, mit denen sie sich bei der Website anmelden k&ouml;nnen. </i>',
'abonnement_newsletter' => '<b>Abonnement des Newsletters</b>',
'acces_a_la_page' => 'Sie k&ouml;nnen diese Seite nicht &ouml;ffnen.',
'adresse_deja_inclus' => 'Die Adresse ist bereits bekannt',
'autorisation_inscription' => 'SPIP-Listes hat die Anmeldefunktion f&uuml;r Besucher der Website aktiviert.',

'choisir' => 'Ausw&auml;hlen',
'Choisir_cette_liste' => 'Diese Liste ausw&auml;hlen',
'confirme_envoi' => 'Bitte best&auml;tigen Sie den Versand',

'date_act' => 'Daten aktualisiert',
'date_ref' => 'Vergleichsdatum',
'desabo' => 'Abmeldung',
'desabonnement' => 'Abonnement&nbsp;k&uuml;ndigen&nbsp;',
'desabonnes' => 'Abgemeldet',
'desole' => 'Entschuldigung',
'destinataire' => 'Empf&auml;nger',
'destinataires' => 'Empf&auml;nger',

'efface' => 'hat die Listen aus der Datenbank gel&ouml;scht',
'efface_base' => 'wurde aus den Listen der der Datenbak gel&ouml;scht',
'email_adresse' => 'Test-E-Mail',
'email_collec' => 'Kollektive E-Mail',
'email_test' => 'Testmail senden',
'email_test_liste' => 'An eine Mailing-Liste senden',
'email_tester' => 'Per E-Mail testen',
'env_esquel' => 'Programmierter Versand der Vorlage',
'env_maint' => 'Jetzt senden',
'envoyer' => 'Mail senden',
'envoyer_a' => 'Senden an',
'erreur' => 'Fehler',
'erreur_import' => 'Die Importdatei hat einen Fehler in Zeile',

'format_date' => 'Y/m/d',

'html' => 'HTML',

'importer' => 'Abonnentenliste importieren',
'importer_fichier' => 'Importer un fichier',
'importer_fichier_txt' => '<p><b>Ihre Abonnetenliste mu&szlig; eine einfache Textdatei sein, die eine E-Mail Adresse pro Zeile enth&auml;lt</b></p>',
'importer_preciser' => '<p>Geben Sie die Liste und das Format an, das die Abonnenten bevorzugen</p>',
'inconnu' => 'ist nicht mehr Abonnent der Liste',
'liste_diff_publiques' => '&ouml;ffentliche Listen<br /><i>Die &ouml;ffentliche Seite zur Anmeldung erm&ouml;glicht, diese Listen zu abonnieren.</i>',
'liste_sans_titre' => 'Listen ohne Titel',
'listes_internes' => 'Interne Listen<br /><i>Beim Senden einer Nachricht k&ouml;nnen diese Listen als Empf&auml;nger gew&auml;hlt werden.</i>',
'listes_poubelle' => 'Ihre gel&ouml;schten Listen',
'lock' => 'Gesperrt: ',
'liste_numero' => 'LISTE NUMMER',

'mail_a_envoyer' => 'Anzahl der zu sendenden Mails: ',
'mail_tache_courante' => 'F&uuml;r den aktuellen Task gesendete Mails: ',
'messages_auto_envoye' => 'Automatisch gesendete Nachrichten',
'message_en_cours' => 'Diese Nachricht ist in Bearbeitung',
'message_presque_envoye' =>'Diese Nachricht wird unverz&uuml;glich gesendet',
'mode_inscription' => 'Einschreibemodus der Besucher einstellen',
'modif_envoi' => 'Sie k&ouml;nnen sie bearbeiten oder den Versand beauftragen',
'modifier_liste' => 'Diese Liste bearbeiten',

'nb_abonnes' => 'In den Listen: ',
'nb_inscrits' => 'Auf der Site:  ',
'nb_listes' => 'Bei allen Listen anmelden: ',
'pas_denvoi_auto_programme' => 'F&uuml;r diese Liste gibt es keinen programmierten Versand.',
'nouvelle_abonne' => 'Der folgende Abonnent wurde der Liste hinzugef&uuml;gt',
'pas_acces' => 'Sie d&uuml;rfen diese Seite nicht &ouml;ffnen.',
'plus_abonne' => ' ist nicht mehr Abonnent dieser Liste ',
'prochain_envoi_aujd' => 'N&auml;chste Sendung f&uuml;r heute vorgesehen',
'prochain_envoi_prevu' => 'N&auml;chste Senden vorgesehen',
'prochain_envoi_prevu_dans' => 'N&auml;chste Sendung vorgesehen in',
'prog_env' => 'Automatische Sendung programmieren',
'prog_env_non' => 'Keinen Versand programmieren',
'program' => 'Programmaierung der automatischen Sendungen',
'plein_ecran' => '(Vollbild)',

'reinitialiser' => 'neu initialisieren',
'remplir_tout' => 'Alle Felder m&uuml;ssen ausgef&uuml;llt werden',
'repartition' => 'Verteilung',
'retour_link' => 'Zur&uuml;ck',

'Erreur_Adresse_email_inconnue' => 'Achtung, die angegebene E-Mail entspricht keinem Abonnenten<br />der Versand kann nicht ausgef&uuml;hrt werden. Bitte wiederholen Sie den Vorgang<br /><br />',
'squel' => 'Vorlage: &nbsp;',
'statut_interne' => 'Intern',
'statut_publique' => '&ouml;ffentlich',
'suivi_envois' => 'Versand verwalten',
'supprime_contact' => 'Diese Adresse endg&uuml;ltig l&ouml;schen',
'supprime_contact_base' => 'Endg&uuml;ltig aus der Datenbank l&ouml;schen',

'tableau_bord' => 'Steuerung',
'texte' => 'Text',
'toutes' => 'Alle Abonnenten',
'txt_abonnement' => '(Geben Sie hier den Text zum Abonnieren der Liste an, der auf der &ouml;ffnetlichen Seite angezeigt werden soll)',
'txt_inscription' => 'Anmeldetext : ',

'une_inscription' => 'Ein Abonnent gefunden',

'val_texte' => 'Text',
'version' => 'Version',
'voir_historique' => 'Verlauf der Sendungen anzeigen',



// ====================== inscription-listes.php3 / abonnement.php3 ======================

'abo_listes' => 'Abonnement',
'acces_refuse' => 'Sie haben keinen Zugriff auf diese Website',

'confirmation_format' => ' im Format ',
'confirmation_liste_unique_1' => 'Sie haben den Newsletter dieser Website abonniert',
'confirmation_liste_unique_2' =>'Sie haben sich f&uuml;r den Empfang von Nachrichten der folgenden Liste eingetragen:',
'confirmation_listes_multiples_1' => 'Sie haben die Newsletter der Website abonniert ',
'confirmation_listes_multiples_2' => 'Sie haben folgende Listen abonniert:',

'Erreur_Adresse_email_invalide' => 'Fehler: Sie haben eine ung&uuml;ltige E-Mail Adresse angegeben',

'infos_liste' => 'Informationen &uuml;ber diese Liste',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'Anzahl der Adressen',
'contacts_lot' => 'Adressen dieses Stapels',
'editeur' => 'Herausgeber : ',
'envoi_en_cours' => 'Versand in Arbeit',
'envoi_tous' => 'Versand an alle Abonnenten',
'envoi_listes' => 'Versand an alle Abonnenten diese Liste : ',
'envoi_erreur' => 'Fehler : SPIP-Listes findet keine Empf&auml;nger f&uuml;r diese Nachricht',
'email_reponse' => 'Antwortadresse : ',
'envoi_annule' => 'Versand gestoppt',
'envoi_fini' => 'Versand beendet',
'erreur_destinataire' => 'Empf&auml;ngerfehler: Kein Versand',
'erreur_sans_destinataire' => 'Fehler: F&uuml;r diese Nachricht kann wird kein Empf&auml;nger gefunden',
'erreur_mail' => 'Fehler: Versand nicht m&ouml;glich (&uuml;berpr&uuml;fen Sie, ob die PHP-Funktion mail() zur Verf&uuml;gung steht)',

'forcer_lot' => 'N&auml;chsten Stapel senden',

'non_courrier' => 'Keine Mails (mehr) zu senden',
'non_html' => 'Ihr Mailprogramm kann offensichtlich die grafische (HTML) Version dieser E-Mail nicht anzeigen',
'sans_adresse' => 'Mail wurde nicht gesendet -> Bitte tragen Sie eine ANzwortadresse (reply-to) ein',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Best&auml;tigen',

'lettre_info' => 'Newsletter der Website',

'patron_erreur' => 'Die gew&auml;hlte Vorlage generiert mit den gew&auml;hlten Parametern keine Daten',



// ====================== listes.html ======================

'abonees_titre' => 'Abonnenten',


// ====================== inc-presentation.php3 ======================

'listes_emails' => 'Newsletter',


// ====================== mes-options.php3 ======================


'options' => 'radio|brut|Format :|HTML,Text,Abbestellen|html,texte,non',

// ====================== mes-options.php3 ======================

'bonjour' => 'Hallo,',

'inscription_reponse_s' => 'Sie haben den Newsletter dieser Website abonniert @s@\n',
'inscription_reponses_s' => 'Sie haben die Newsletter dieser Website abonniert @s@\n',
'inscription_liste_f' => 'Sie haben sich f&uuml;r den Empfang von Nachrichten der folgenden Liste eingetragen im Format @f@: ',
'inscription_listes_f' => 'Sie haben folgende Listen abonniert im Format @f@: '

);

?>