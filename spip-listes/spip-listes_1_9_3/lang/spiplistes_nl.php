<?php
/**
 * Pack langue neerlandais
 * 
 * @package spiplistes
 */
 // $LastChangedRevision: 47066 $
 // $LastChangedBy: paladin@quesaco.org $
 // $LastChangedDate: 2011-04-25 19:54:15 +0200 (Lun 25 avr 2011) $

$GLOBALS['i18n_spiplistes_nl'] = array(

// CP-20081126: classement par scripts
// action/spiplistes_agenda.php
// action/spiplistes_changer_statut_abonne.php
// action/spiplistes_envoi_lot.php
// action/spiplistes_journal.php
// action/spiplistes_lire_console.php
// action/spiplistes_liste_des_abonnes.php
// action/spiplistes_listes_abonner_auteur.php
// action/spiplistes_moderateurs_gerer.php
'voir_historique' => 'De historiek van de verzendingen bekijken'
, 'pas_de_liste_prog' => "Geen lijst geprogrammeerd."

// action/spiplistes_supprimer_abonne.php
// balise/formulaire_abonnement.php
, 'inscription_liste_f' => 'U heeft er voor gekozen om de berichten te ontvangen voor de volgende lijst @f@: '
, 'inscription_listes_f' => 'U heeft er voor gekozen om de berichten te ontvangen voor de volgende lijsten @f@: '
, 'inscription_reponse_s' => 'U bent geabonneerd op de nieuwslijst van de site @s@'
, 'inscription_reponses_s' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site @s@'
, 'vous_abonne_aucune_liste' => "Vous n'&ecirc;tes pas abonn&eacute; &agrave; une liste de diffusion"
, 'liste_dispo_site_' => "Beschikbare mailing lijst van de site : "
, 'listes_dispos_site_' => "Listes de diffusion disponibles sur ce site : "
, 'desole_pas_de_liste' => "D&#233;sol&#233;, il n&#39;y a pas de liste de diffusion disponible pour le moment."
, 'pour_vous_abonner' => "Pour vous abonner aux listes de diffusion"
// obsolete
, 'abonnement_mail_passcookie' => "
	<br />
	Om uw abonnement op onze nieuwsbrief <strong>@nom_site_spip@</strong> (@adresse_site@) te wijzigen ,
	Gelieve zich naar volgend adres te wenden: <br /><br />
	<a href='@adresse_site@/spip.php?page=abonnement&d=@cookie@'>@adresse_site@/spip.php?page=abonnement&d=@cookie@</a><br /><br />
	Op die manier kan u de aanpassing vanuw abonnement bevestigen.
	<br/>"	
, 'bienvenue_sur_la_liste_' => "Bienvenue sur les listes de diffusion du site "
, 'vos_abos_sur_le_site_' => "Vos abonnements sur le site "
, 'votre_format_de_reception_' => "Votre format de r&#233;ception "
, '_cliquez_lien_formulaire' => "cliquez sur ce lien pour acc&#233;der au formulaire pr&#233;sent sur le site"
, 'pour_modifier_votre_abo_' => "Pour modifier votre abonnement "
, 'abonnement_presentation' => "
	Entrez votre adresse email dans le champ ci-dessous.
	Vous recevrez &#224; cette adresse un courrier de confirmation d&#39;inscription et un lien.
	Ce lien vous permettra de s&#233;lectionner les listes de diffusion publi&#233;es ici.
	"
, 'confirmation_inscription' => "Uw inschrijving is bevestigd"
, 'souhait_modifier_abo'=>'Vous souhaitez modifier votre abonnement &agrave; la lettre d\'information'
, 'suspendre_abonnement_' => "Mijn abonnement afzeggen "
, 'vous_etes_redact' => "Vous &#234;tes inscrit en tant que r&#233;dacteur."
, 'vous_etes_membre' => "Vous &#234;tes membre abonn&#233; aux listes de diffusion de ce site.
	Il est parfois n&#233;cessaire de s&#39;authentifier pour avoir acc&#232;s &#224; ces listes."

// balise/formulaire_modif_abonnement.php
, 'abonnement_modifie' => 'Uw wijzigingen werden opgeslagen'
, 'abonnement_nouveau_format' => 'De vorm van uw nieuwsbrief is vanaf nu: '

// base/spiplistes_init.php
, 'autorisation_inscription' => 'SPIP-lijsten gaf net de toestemming op zich in te schrijven bij de bezoekers van de site'

// base/spiplistes_tables.php
// base/spiplistes_upgrade.php
// docs/spiplistes_aide_fr.html
// exec/spiplistes_abonne_edit.php
, 'adresse_mail_obligatoire' => "Adresse mail manquante. Abonnement impossible."
, 'abonne_sans_format' => "Ce compte est actuellement d&eacute;sabonn&eacute;. Aucun format de courrier n'est 
	d&eacute;fini. Il ne peut pas recevoir de courrier. D&eacute;finissez un format 
	de r&eacute;ception pour ce compte afin de valider son abonnement."
, 'Desabonner_temporaire' => "tijdelijke uitschrijving van dit account."
, 'Desabonner_definitif' => "D&eacute;sabonner ce compte de toutes les listes de diffusion."
, 'export_etendu_' => "Export &eacute;tendu "
, 'exporter_statut' => "Exporter le statut (invit&eacute;, r&eacute;dacteur, etc.)"
, 'editer_fiche_abonne' => "Editer la fiche de l'abonn&eacute;"
, 'edition_dun_abonne' => "Edition d'un abonn&eacute;"
, 'format_de_reception' => "Format de r&eacute;ception" // + formulaire
, 'format_reception' => "Format de r&eacute;ception :"
, 'format_de_reception_desc' => "Vous pouvez choisir un format global de r&eacute;ception des courriers pour  
   cet abonn&eacute;.<br /><br />
   Vous pouvez &eacute;galement d&eacute;sabonner temporairement ce contact. 
   Il reste inscrit dans les listes en tant que destinataire, mais les courriers 
   ne lui seront pas envoy&eacute;s tant que vous ne lui aurez pas d&eacute;fini un format de r&eacute;ception de courriers."
, 'mettre_a_jour' => '<h3>SPIP-listes va mettre &agrave; jour</h3>'
, 'regulariser' => 'regulariser les desabonnes avec listes...<br />'
, 'Supprimer_ce_contact' => "Supprimer ce contact"
, 'abonne_listes' => "Ce contact est abonn&eacute; aux listes suivantes"
, 'n_duplicata_mail' => "@n@ duplicata(s)"
, 'n_incorrect_mail' => "@n@ incorrect(s)"

// exec/spiplistes_abonnes_tous.php
, 'repartition_abonnes' => "R&eacute;partition des abonn&eacute;s"
, 'abonnes_titre' => 'Abonn&eacute;s'
, 'chercher_un_auteur' => "Chercher un auteur"
, 'une_inscription' => 'Un abonn&eacute; trouv&eacute;'
, 'suivi' => 'Suivi des abonnements' // + presentation
, 'abonne_aucune_liste' => 'Abonn&eacute;s &agrave; aucune liste'
, 'format_aucun' => "Aucun"
, 'repartition_formats' => "R&eacute;partition des formats"

// exec/spiplistes_aide.php
// exec/spiplistes_autocron.php
// exec/spiplistes_config.php
, 'personnaliser_le_courrier' => "Personnaliser le courrier"
, 'personnaliser_le_courrier_desc' => 
	"Vous pouvez personnaliser le courrier pour chaque abonn&eacute; en ins&eacute;rant 
   dans votre patron les tags n&eacute;cessaires. Par exemple, pour ins&eacute;rer 
   le nom de votre abonn&eacute; dans son courrier lors de l'envoi, placez dans 
   votre patron _AUTEUR_NOM_ (notez le tiret bas en d&eacute;but et fin de tag)."
, 'utiliser_smtp' => "Utiliser SMTP"
, 'requiert_identification' => "Vereist een identificatie"
, 'adresse_smtp' => "Email adres van <em>sender</em> SMTP"
, '_aide_install' => "<p>Bienvenue dans le monde de SPIP-Listes.</p>
	<p class='verdana2'>Par d&eacute;faut, &agrave; l'installation, SPIP-Listes est en mode <em>simulation 
	d'envoi</em> afin de vous permettre de d&eacute;couvrir les fonctionnalit&eacute;s 
	et d'effectuer vos premiers tests.</p>
	<p class='verdana2'>Pour valider les diff&eacute;rentes options de SPIP-Listes, rendez-vous <a href='@url_config@'>sur 
	la page de configuration</a>.</p>"
, 'adresse_envoi_defaut' => "Standaard email adres"
, 'adresse_on_error_defaut' => "Adresse de retour par d&eacute;faut pour les erreurs"
, 'pas_sur' => '<p>Indien u niet zeker bent, kies de mail functie van PHP.</p>'
, 'Complement_des_courriers' => "Compl&eacute;ment des courriers"
, 'Complement_lien_en_tete' => "Lien sur le courrier"
, 'Complement_ajouter_lien_en_tete' => "Ajouter un lien en en-t&ecirc;te du courrier"
, 'Complement_lien_en_tete_desc' => "Cette option vous permet de rajouter en t&ecirc;te du courrier HTML envoy&eacute; le lien 
   du courrier original pr&eacute;sent sur votre site."
, 'Complement_tampon_editeur' => "Ajouter le tampon Editeur"
, 'Complement_tampon_editeur_desc' => "Cette option vous permet de rajouter le tampon de l'&eacute;diteur en fin de courrier. "
, 'Complement_tampon_editeur_label' => "Ajouter le tampon Editeur en fin de courrier"
, 'Envoi_des_courriers' => "Envoi des courriers"
, 'log_console' => "Console"
, 'log_details_console' => "D&eacute;tails de la console"
, 'log_voir_destinataire' => "Lister les adresses email des destinataires dans la console lors de l'envoi."
, 'log_console_syslog_desc' => "Vous &ecirc;tes sur un r&eacute;seau local (@IP_LAN@). Si besoin, vous pouvez activer la console sur syslog au lieu des journaux SPIP (conseill&eacute; sous unix)."
, 'log_console_syslog_texte' => "Activer les journaux syst&egrave;mes (renvoi sur syslog)"
, 'log_console_syslog' => "Console syslog"
, 'log_voir_le_journal' => "Voir le journal de SPIP-Listes"
, 'recharger_journal' => "Recharger le journal"
, 'fermer_journal' => "Fermer le journal"
, 'methode_envoi' => 'M&eacute;thode d\'envoi'
, 'mode_suspendre_trieuse' => "Suspendre le traitement des envois des listes de diffusion"
, 'Suspendre_le_tri_des_listes' => "Cette option vous permet - en cas d'engorgement - de suspendre le traitement des 
	listes de diffusion programm&eacute;es et de red&eacute;finir les param&egrave;tres 
	d'envoi. D&eacute;sactivez ensuite cette option pour reprendre le traitement des 
	listes de diffusion programm&eacute;es."
, 'mode_suspendre_meleuse' => "Suspendre l'envoi des courriers"
, 'suspendre_lenvoi_des_courriers' => "Cette option vous permet - en cas d'engorgement 
	- d'annuler l'envoi des courriers. D&eacute;sactivez ensuite cette option pour 
	reprendre les exp&eacute;ditions en cours. "
, 'nombre_lot' => 'Nombre d\'envois par lot'
, 'php_mail' => 'Utiliser la fonction mail() de PHP'
, 'patron_du_tampon_' => "Patron du tampon : "
, 'Patron_de_pied_' => "Patron de pied "
, 'personnaliser_le_courrier_label' => "Activer la personnalisation du courrier"
, 'parametrer_la_meleuse' => "Param&eacute;trer la meleuse"
, 'smtp_hote' => 'H&ocirc;te'
, 'smtp_port' => 'Port'
, 'simulation_desactive' => "Mode simulation d&eacute;sactiv&eacute;."
, 'simuler_les_envois' => "Simuler les envois de courriers"
, 'abonnement_simple' => '<strong>Abonnement simple : </strong><br /><em>Les abonn&eacute;s re&ccedil;oivent un message 
	de confirmation apr&egrave;s leur abonnement</em>'
, 'abonnement_code_acces' => '<strong>Abonnement avec codes d\'acc&egrave;s : </strong><br /><i>Les abonn&eacute;s 
	re&ccedil;oivent en plus un login et un mot de passe qui leur permettront de s\'identifier sur le site. </i>'
, 'mode_inscription' => 'Param&eacute;trer le mode d\'inscription des visiteurs'

// exec/spiplistes_courrier_edit.php
, 'Generer_le_contenu' => "G&eacute;n&eacute;rer le contenu"
, 'Langue_du_courrier_' => "Langue du courrier :"
, 'generer_Apercu' => "G&eacute;n&eacute;rer et Aper&ccedil;u"
, 'a_partir_de_patron' => "A partir d'un patron"
, 'avec_introduction' => "Avec texte d'introduction"
, 'calcul_patron_attention' => "Certains patrons ins&egrave;rent dans leur r&eacute;sultat le texte ci-dessous (Texte du courrier). 
	Si vous faites une mise &agrave; jour de votre courrier, pensez &agrave; vider cette boîte avant de g&eacute;n&eacute;rer le contenu."
, 'charger_patron' => 'Choisir un patron pour le courrier'
, 'Courrier_numero_' => "Courrier num&eacute;ro :" // + _gerer
, 'Creer_un_courrier_' => "Cr&eacute;er un courrier :"
, 'choisir_un_patron_' => "Choisir un patron "
, 'Courrier_edit_desc' => 'Vous pouvez choisir de g&eacute;n&eacute;rer automatiquement le contenu du courrier
	ou r&eacute;diger simplement votre courrier dans la bo&icirc;te <strong>texte du courrier</strong>.'
, 'Contenu_a_partir_de_date_' => "Contenu &agrave; partir de cette date "
, 'Cliquez_Generer_desc' => "Cliquez sur <strong>@titre_bouton@</strong> pour injecter le r&eacute;sultat 
	dans la bo&icirc;te @titre_champ_texte@."
, 'Lister_articles_de_rubrique' => "Et lister les articles de la rubrique "
, 'Lister_articles_mot_cle' => "Et lister les articles du mot-cl&eacute; "
, 'edition_du_courrier' => "Edition du courrier" // + gerer
, 'generer_un_sommaire' => "G&eacute;n&eacute;rer un sommaire"
, 'generer_patron_' => "G&eacute;n&eacute;rer le patron "
, 'generer_patron_avant' => "avant le sommaire"
, 'generer_patron_apres' => "apr&egrave;s le sommaire."
, 'introduction_du_courrier_' => "Introduction &agrave; votre courrier, avant le contenu issu du site "
, 'Modifier_un_courrier__' => "Modifier un courrier :"
, 'Modifier_ce_courrier' => "Modifier ce courrier"
, 'sujet_courrier' => '<strong>Onderwerp van bericht</strong> [verplicht]'
, 'texte_courrier' => '<strong>berichttekst</strong> (HTML toegestaan)'
, 'avec_patron_pied__' => "Avec le patron de pied : "

// exec/spiplistes_courrier_gerer.php
, 'Erreur_Adresse_email_invalide' => 'Error, het email-adres dat u opgaf, is  ongeldig'
, 'langue_' => '<strong>Taal:</strong>&nbsp;'
, 'calcul_patron' => 'Indruk met het patroon tekstversie'
, 'calcul_html' => 'Indruk met HTML versie'
, 'dupliquer_ce_courrier' => "Dupliquer ce courrier"
, 'destinataire_sans_format_alert' => "Destinataire sans format de r&eacute;ception.
	Appliquez un format de r&eacute;ception (texte ou html) pour ce compte ou s&eacute;lectionnez un autre destinataire."
, 'envoi_date' => 'Datum van het versturen: '
, 'envoi_debut' => 'Begin van het versturen: '
, 'envoi_fin' => 'Einde van het versturen: '
, 'erreur_envoi' => 'Aantal foutieve verzendingen: '
, 'Erreur_liste_vide' => "Erreur: cette liste n'a pas d'abonn&eacute;s."
, 'Erreur_courrier_introuvable' => "Erreur: ce courrier n'existe pas." // + previsu
, 'Envoyer_ce_courrier' => "Envoyer ce courrier"
, 'format_html__n' => "html vorm: @n@"
, 'format_texte__n' => "tekst vorm: @n@"
, 'message_arch' => 'Opgeslagen berichten'
, 'message_en_cours' => 'Berichten worden verstuurd'
, 'message_type' => 'Electronische post'
, 'sur_liste' => 'op de lijst' 
, 'Supprimer_ce_courrier' => "Supprimer ce courrier"
, 'email_adresse' => 'een test emailadres' // + liste
, 'email_test' => 'Een testemail verzenden'
, 'Erreur_courrier_titre_vide' => "Erreur: votre courrier n'a pas de titre."
, 'message_en_cours' => 'Het bericht wordt momenteel geschreven'
, 'modif_envoi' => 'U kan het wijzigen of de verzending vragen'
, 'message_presque_envoye' =>'Het bericht staat op het punt verzonden te worden'
, 'Erreur_Adresse_email_inconnue' => 'Opgelet, het test-emailadres dat u heeft opgegeven komt met geen enkele geabonneerde overeen. 
	<br />De verzending werd onderbroken. Gelieve de procedure opnieuw te starten.<br /><br />'

// exec/spiplistes_courrier_previsu.php
, 'lettre_info' => 'De nieuwsbrief van de site'

// exec/spiplistes_courriers_casier.php
// exec/spiplistes_import_export.php
, 'Exporter_une_liste_d_abonnes' => "Exporter une liste d'abonn&eacute;s"
, 'Exporter_une_liste_de_non_abonnes' => "Exporter une liste de non abonn&eacute;s"
, '_aide_import' => "Vous pouvez importer ici une liste d'abonn&eacute;s &agrave; partir de votre 
   ordinateur.<br />
	Cette liste d'abonn&eacute;s doit &ecirc;tre au format texte seul, une ligne 
   par abonn&eacute;. Chaque ligne doit &ecirc;tre compos&eacute;e ainsi :<br />
	<tt style='display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;'>adresse@mail<span style='color:#f66'>[separateur]</span>login<span style='color:#f66'>[separateur]</span>nom</tt>
	<tt style='color:#f66'>[separateur]</tt> est un caract&egrave;re de tabulation ou un point-virgule.<br /><br />
	L'adresse email doit &ecirc;tre unique, ainsi que le login. Si cette adresse 
   email ou ce login existent dans la base du site, la ligne sera rejet&eacute;e.<br />
	Le premier champ adresse@mail est obligatoire. Les deux autres champs peuvent 
   &ecirc;tre ignor&eacute;s (vous pouvez importer des listes issues des anciennes versions de SPIP-Listes)."
, 'annuler_envoi' => "Annuler l&#39;envoi" // + _gerer
, 'envoi_patron' => 'Envoi du patron'
, 'import_export' => 'Import / Export'
, 'incorrect_ou_dupli' => " (incorrect ou dupli)"
, 'membres_liste' => 'Liste des Membres'
, 'Messages_automatiques' => 'Courriers automatiques programm&eacute;s'
, 'Pas_de_liste_pour_import' => "Vous devez cr&eacute;er au moins une liste de destination afin de pouvoir importer 
	vos abonn&eacute;s."
, 'Resultat_import' => "R&eacute;sultat import"
, 'Selectionnez_une_liste_pour_import' => "Vous devez s&eacute;lectionner au moins une liste de diffusion pour pouvoir importer 
	les abonn&eacute;s."
, 'Selectionnez_une_liste_de_destination' => "S&eacute;lectionnez une ou plusieurs listes de destination pour vos abonn&eacute;s."
, 'Tous_les_s' => "Alle @s@"
, 'Toutes_les_semaines' => "Elke week"
, 'Tous_les_mois' => "Elke maand, "
, 'Tous_les_ans' => "Elk jaar"
, 'version_html' => '<strong>HTML versie</strong>'
, 'version_texte' => '<strong>Tekstversie</strong>'
, 'erreur_import' => 'Het document voor de import vertoont een fout op lijn '
, 'envoi_manuel' => "Envoi manuel"
, 'format_date' => 'Y/m/d'
, 'importer' => 'een lijst van geabonneerden importeren'
, 'importer_fichier' => 'een document importeren'
, 'importer_fichier_txt' => '<p><strong>Uw lijst van geabonneerden moet een eenvoudig document (tekst) zijn dat slechts één emailadres per lijn bevat</strong></p>'
, 'importer_preciser' => '<p>De lijsten en het formaat dat overeenkomt met de import van geaboneerden, verduidelijken</p>'
, 'prochain_envoi_prevu' => 'Volgende verzending voorzien' // + gerer
, 'option_import_' => "Option d'importation "
, 'forcer_abos_' => "Forcer les abonnements (si l&#39;adresse mail existe dans la base, forcer l&#39;abonnement
	pour la s&#233;lection, pour cet abonn&#233;)."
, 'erreur_import_base' => "Erreur importation. Data incorrect ou erreur base SQL."
, 'erreur_n_fois' => "(erreur rencontree @n@ fois)"
, 'Liste_de_destination_s' => "Liste de destination : @s@"
, 'Listes_de_destination_s' => "Listes de destination : @s@"
, 'pas_dimport' => "Pas d&#39;import. Soit le fichier est vide, soit toutes les adresses sont d&#233;j&#224; abonn&#233;es."

// exec/spiplistes_liste_edit.php
, 'texte_dinsctription_' => "Inschrijvingstekst: "
, 'Creer_une_liste_' => "Cr&eacute;er une liste "
, 'en_debut_de_semaine' => "en d&eacute;but de semaine"
, 'en_debut_de_mois' => "en d&eacute;but de mois"
, 'envoi_non_programme' => "Envoi non programm&eacute;"
, 'edition_dune_liste' => "Edition d'une liste"
, 'texte_contenu_pied' => '<br />(Message ajout&eacute; en bas de chaque email au moment de l\'envoi)<br />'
, 'texte_pied' => '<p><strong>Tekst van pagina-einde</strong>'
, 'modifier_liste' => 'Deze lijst wijzigen '
, 'txt_abonnement' => '(Geef de tekst aan voor het abonnement voor deze lijst, op de publieke site tonen of de lijst actief is)'

// exec/spiplistes_liste_gerer.php
, 'forcer_les_abonnement_liste' => "Forcer les abonnements pour cette liste"
, 'periodicite_tous_les_n_s' => "P&eacute;riodicit&eacute; : tous les @n@ @s@"
, 'liste_sans_titre' => 'Liste sans titre'
, 'statut_interne' => "Priv&eacute;"
, 'statut_publique' => "Publiek"
, 'adresse' => "Indiquez ici l&#39;adresse &agrave; utiliser pour les r&eacute;ponses de mails 
	(&agrave; d&eacute;faut, l&#39;adresse du webmestre sera utilis&eacute;e comme adresse de r&eacute;ponse) :"
, 'Ce_courrier_ne_sera_envoye_qu_une_fois' => "Ce courrier ne sera envoy&eacute; qu'une fois."
, 'adresse_de_reponse' => "Adresse de r&eacute;ponse"
, 'adresse_mail_retour' => 'Adresse email du gestionnaire de la liste (reply-to)'
, 'Attention_action_retire_invites' => "Attention: cette action retire les invit&eacute;s de la liste des abonn&eacute;s."
, 'A_partir_de' => "&Agrave; partir de"
, 'Apercu_plein_ecran' => "Aper&ccedil;u plein &eacute;cran dans une nouvelle fen&ecirc;tre"
, 'Attention_suppression_liste' => "Attention ! Vous demandez la suppression d'une liste de diffusion. 
	Les abonn&eacute;s seront retir&eacute;s de cette liste de diffusion automatiquement. "
, 'Abonner_tous_les_invites_public' => "Abonner tous les membres invit&eacute;s &agrave; cette liste publique."
, 'Abonner_tous_les_inscrits_prives' => "Abonner tous les membres &agrave; cette liste priv&eacute;e, sauf les visiteurs."
, 'boite_confirmez_envoi_liste' => "Vous avez demand&eacute; l'envoi imm&eacute;diat de cette liste 
	de diffusion.<br />
	Svp, veuillez confirmer votre demande."
, 'cette_liste_est_' => "Deze lijst is: @s@"
, 'Confirmer_la_suppression_de_la_liste' => "Confirmer la suppression de la liste "
, 'Confirmez_requete' => "Veuillez confirmer la requ&ecirc;te."
, 'date_expedition_' => "Date d'exp&eacute;dition "
, 'Dernier_envoi_le_' => "Dernier envoi le :"
, 'forcer_abonnement_desc' => "Vous pouvez forcer ici les abonnements &agrave; cette liste, soit pour tous 
   les membres inscrits (visiteurs, auteurs et administrateurs), soit pour tous 
   les visiteurs."
, 'forcer_abonnement_aide' => "<strong>Attention</strong>: un membre abonn&eacute; ne re&ccedil;oit pas forc&eacute;ment 
   le courrier de cette liste. Il faut attendre qu'il confirme lui-m&ecirc;me 
   le format de r&eacute;ception : html ou texte seul.<br />
	Vous pouvez forcer le format par abonn&eacute; <a href='@lien_retour@'>sur la page du suivi des abonnements</a>"
, 'forcer_abonnements_nouveaux' => "En s&eacute;lectionnant l'option <strong>Forcer les abonnements au format...</strong>, 
	vous confirmez le format de r&eacute;ception des nouveaux abonn&eacute;s.
	Les anciens abonn&eacute;s conservent leur pr&eacute;f&eacute;rence de r&eacute;ception."
, 'Forcer_desabonner_tous_les_inscrits' => "D&eacute;sabonner tous les membres inscrits pour cette liste."
, 'gestion_dune_liste' => "Gestion d'une liste"
, 'message_sujet' => 'Onderwerp '
, 'mods_cette_liste' => "Les mod&eacute;rateurs de cette liste"
, 'nbre_abonnes' => "aantal geabonneerden: "
, 'nbre_mods' => "Aantal moderatoren: "
, 'patron_manquant_message' => "Vous devez appliquer un grand patron avant de param&eacute;trer l'envoi de cette 
	liste."
, 'liste_sans_patron' => "Liste sans patron." // courriers_listes
, 'Patron_grand_' => "Grand patron "
, 'sommaire_date_debut' => "A partir de la date définie ci-dessus"
, 'abos_cette_liste' => "Les abonn&eacute;s &agrave; cette liste"
, 'confirme_envoi' => 'Veuillez confirmer l\'envoi'
, 'env_esquel' => 'Geprogrameerd versturen van het patroon'
, 'env_maint' => 'Nu versturen'
, 'date_act' => 'Bijgewerkte gegevens'
, 'forcer_les_abonnements_au_format_' => "Forcer les abonnements au format : "
, 'pas_denvoi_auto_programme' => "Er werd geen automitische verzending voor deze lijst geprogrammeerd."
, 'Pas_de_periodicite' => "Pas de p&eacute;riodicit&eacute;."
, 'prog_env' => 'Een automatische verzending programmeren'
, 'prog_env_non' => 'Geen verzendingen programmeren'
, 'conseil_regenerer_pied' => "<br />Ce patron est issu d'une ancienne version de SPIP-Listes.<br />
	Conseil: s&eacute;lectionnez &agrave; nouveau le patron de pied pour prendre en compte le multilinguisme 
	et/ou la version &#39;texte seul&#39; du patron."
, 'boite_alerte_manque_vrais_abos' => "Il n&#39;y a pas d&#39;abonn&eacute;s pour cette liste de diffusion,
	ou les abonn&eacute;s n'ont pas de format de r&eacute;ception.
	<br />
	Corrigez le format de r&eacute;ception pour au moins un abonn&eacute; avant de valider l'envoi."	

// exec/spiplistes_listes_toutes.php
// exec/spiplistes_maintenance.php
, 'abonnes' => 'geabonneerden'
, '1_abonne' => '1 geabonneerd'
, 'annulation_chrono_' => "Annulation du chrono pour "
, 'conseil_sauvegarder_avant' => "<strong>Conseil</strong>: faire une sauvegarde de la base avant de confirmer la suppression 
   @objet@. L'annulation est impossible ici."
, 'des_formats' => "des formats"
, 'des_listes' => "des listes"
, 'des_abonnements' => "des abonnements"
, 'confirmer_supprimer_formats' => "Supprimer les formats de r&eacute;ception des abonn&eacute;s."
, 'maintenance_objet' => "Maintenance @objet@"
, 'nb_abos' => "qt."
, 'pas_de_liste' => "Aucune liste de type &laquo;envoi non programm&eacute;&raquo;."
, 'pas_de_format' => "Aucun format de r&eacute;ception d&eacute;fini pour les abonn&eacute;s."
, 'pas_de_liste_en_auto' => "Aucune liste de type &laquo;envoi programm&eacute;&raquo; (chrono)."
, 'forcer_formats_' => "Forcer le format de r&#233;ception "
, 'forcer_formats_desc' => "Forcer le format de r&#233;ception pour tous les abonn&#233;s..."
, 'modification_objet' => "Modification @objet@"
, 'Suppression_de__s' => "Suppression de : @s@"
, 'suppression_' => "Suppression @objet@"
, 'suppression_chronos_' => "Supprimer les envois programm&eacute;s (chrono) "
, 'suppression_chronos_desc' => "Si vous supprimez son chrono, la liste n'est pas supprim&eacute;e. Sa p&eacute;riodicit&eacute; 
	est conserv&eacute;e mais l'envoi est suspendu. Pour r&eacute;activer le chrono, il faut lui red&eacute;finir une date de premier 
	envoi. "
, 'Supprimer_les_listes' => "Supprimer les listes"
, 'Supprimer_la_liste' => "Supprimer la liste..."
, 'Suspendre_abonnements' => "Suspendre les abonnements pour ce compte"
, 'separateur_de_champ_' => "S&eacute;parateur de champ "
, 'separateur_tabulation' => "tabulation (<code>\\t</code>)"
, 'separateur_semicolon' => "point-virgule (<code>;</code>)"
, 'nettoyage_' => "Nettoyage "
, 'confirmer_nettoyer_abos' => "Confirmer le nettoyage de  la table des abonn&#233;s."
, 'pas_de_pb_abonnements' => "Pas d&#39;erreur rencontr&#233;e sur la table des abonnements."
, '_n_abos_' => " @n@ abonnements "
, '_1_abo_' => " 1 abonnement "
, '_n_auteurs_' => " @n@ auteurs "
, '_1_auteur_' => " 1 auteur "

// exec/spiplistes_menu_navigation.php
// exec/spiplistes_voir_journal.php
// genie/spiplistes_cron.php
// inc/spiplistes_agenda.php
, 'boite_agenda_titre_' => "Planning des diffusions "
, 'boite_agenda_legende' => "Sur @nb_jours@ jours"
, 'boite_agenda_voir_jours' => "Voir sur les @nb_jours@ jours coulants"

// inc/spiplistes_api.php
// inc/spiplistes_api_abstract_sql.php
// inc/spiplistes_api_courrier.php
// inc/spiplistes_api_globales.php
// inc/spiplistes_api_journal.php
, 'titre_page_voir_journal' => "Journal de SPIP-Listes"
, 'mode_debug_actif' => "Mode debug actif"

// inc/spiplistes_api_presentation.php
, '_aide' => '<p>SPIP-Listes permet d\'envoyer un courrier ou des courriers automatiques &agrave; des abonn&eacute;s.</p>
	<p>Vous pouvez &eacute;crire un texte simple, composer votre courrier en HTML ou appliquer un "patron" &agrave; 
	votre courrier</p>
	<p>Via un formulaire d\'inscription public, les abonn&eacute;s d&eacute;finissent eux-m&ecirc;mes leur statut d\'abonnement, 
	les listes auxquelles ils s\'abonnent et le format
	dans lequel ils souhaitent recevoir les courriers (HTML/texte). </p>
	<p>Tout courrier sera traduit automatiquement en format texte pour les abonn&eacute;s qui en ont fait la demande.</p>
	<p><strong>Note :</strong><br />L\'envoi des mails peut prendre quelques minutes : les lots partent peu &agrave; 
	peu quand les utilisateurs parcourent le site public. Vous pouvez aussi provoquer manuellement l\'envoi des lots 
	en cliquant sur le lien "suivi des envois" pendant un envoi.</p>'
, 'envoi_en_cours' => 'Verzending bezig'
, 'nb_destinataire_sing' => " bestemmeling"
, 'nb_destinataire_plur' => " Bestemmelingen"
, 'aucun_destinataire' => "geen bestemmeling"
, '1_liste' => '@n@ liste'
, 'n_listes' => '@n@ listes'
, 'utilisez_formulaire_ci_contre' => "Utilisez le formulaire ci-contre pour activer/d&eacute;activer cette option."
, 'texte_boite_en_cours' => 'SPIP-Listes envoie un courrier.<p>Cette boite disparaitra une fois l\'envoi achev&eacute;.</p>'
, 'meleuse_suspendue_info' => "L'envoi des courriers en attente d'exp&eacute;dition est suspendu."
, 'casier_a_courriers' => "Casier &agrave; courriers" // + courriers_casier
, 'Pas_de_donnees' => "D&eacute;sol&eacute;, mais l'enregistrement demand&eacute; n'existe pas dans la base de donn&eacute;es."
, '_dont_n_sans_format_reception' => ", dont @n@ sans format de r&eacute;ception"
, 'mode_simulation' => "Mode simulation"
, 'mode_simulation_info' => "Le mode simulation est activ&eacute;. La m&eacute;leuse fait semblant d'envoyer le courrier. 
	En r&eacute;alit&eacute;, aucun courrier n'est exp&eacute;di&eacute;."
, 'meleuse_suspendue' => "Meleuse suspendue"
, 'Meleuse_reactivee' => "M&egrave;leuse r&eacute;activ&eacute;e"
, 'nb_abonnes_sing' => " abonn&eacute;"
, 'nb_abonnes_plur' => " abonn&eacute;s"
, 'nb_moderateur_sing' => " mod&eacute;rateur"
, 'nb_moderateur_plur' => " mod&eacute;rateurs"
, 'aide_en_ligne' => "Aide en ligne"

// inc/spiplistes_dater_envoi.php
, 'attente_validation' => "attente validation"
, 'courrier_en_cours_' => "Courrier en traitement "
, 'date_non_precisee' => "Date non pr&eacute;cis&eacute;e"

// inc/spiplistes_destiner_envoi.php
, 'email_tester' => 'Testen per email'
, 'Choix_non_defini' => 'Pas de choix d&eacute;fini.'
, 'Destination' => "Destination"
, 'aucune_liste_dispo' => "Aucune liste disponible."

// inc/spiplistes_import.php
// inc/spiplistes_lister_courriers_listes.php
, 'Prochain_envoi_' => "Prochain envoi "

// inc/spiplistes_listes_forcer_abonnement.php
// inc/spiplistes_listes_selectionner_auteur.php
, 'lien_trier_nombre' => "Trier par nombre d&#039;abonnements"
, 'Abonner_format_html' => "Abonner au format HTML"
, 'Abonner_format_texte' => "Abonner au format texte"
, 'ajouter_un_moderateur' => "Ajouter un mod&eacute;rateur "
, 'Desabonner' => "D&eacute;sabonner"
, 'Pas_adresse_email' => "Pas d&#039;adresse email"
, 'sup_mod' => "Supprimer ce mod&eacute;rateur"
, 'supprimer_un_abo' => "Supprimer un abonné de cette liste"
, 'supprimer_cet_abo' => "Supprimer cet abonné de cette liste" // + pipeline
, 'abon_ajouter' => "Ajouter un abonn&eacute; "

// inc/spiplistes_mail.inc.php
// inc/spiplistes_meleuse.php
, 'erreur_sans_destinataire' => 'Error: er kan geen bestemmeling gevonden worden voor dit bericht'
, 'envoi_annule' => 'Verzending abroken'
, 'sans_adresse' => ' De mail werd niet verzonden -> Gelieve een antwoordadres op te geven'
, 'erreur_mail' => 'Error: het bericht kan  niet verstuurd worden (nagaan of de php mail () beschikbaar is)'
, 'modif_abonnement_text' => 'Om uw abonnement te wijzigen, gelieve de volgende link aan te klikken: '
, 'msg_abonne_sans_format' => "format de reception manquant"
, 'modif_abonnement_html' => "<br />Cliquez ici pour modifier votre abonnement"

// inc/spiplistes_naviguer_paniers.php
// inc/spiplistes_pipeline_I2_cfg_form.php
// inc/spiplistes_pipeline_affiche_milieu.php
, 'Adresse_email_obligatoire' => "Une adresse email est obligatoire pour pouvoir vous abonner aux listes de diffusion. 
	Si vous d&eacute;sirez profiter de ce service, merci de modifier votre fiche en compl&eacute;tant ce champ. "
, 'Alert_abonnement_sans_format' => "Votre abonnement est suspendu. Vous ne recevrez pas les courriers des listes de 
	diffusion list&eacute;es ci-dessous. Pour recevoir &agrave; nouveau le courrier 
	de vos listes pr&eacute;f&eacute;r&eacute;es, choisissez un format de r&eacute;ception 
	et validez ce formulaire. "
, 'abonnements_aux_courriers' => "Abonnements aux courriers"
, 'Forcer_abonnement_erreur' => "Erreur technique signal&eacute;e lors de la modification d'une liste abonn&eacute;e. 
	V&eacute;rifiez cette liste avant de poursuivre votre op&eacute;ration."
, 'Format_obligatoire_pour_diffusion' => "Pour confirmer l'abonnement de ce compte, vous devez s&eacute;lectionner un format 
	de r&eacute;ception."
, 'Valider_abonnement' => "Valider cet abonnement"
, 'vous_etes_abonne_aux_listes_selectionnees_' => "Vous &ecirc;tes abonn&eacute; aux listes s&eacute;lectionn&eacute;es "

// inc/spiplistes_pipeline_ajouter_boutons.php
// inc/spiplistes_pipeline_ajouter_onglets.php
// inc/spiplistes_pipeline_header_prive.php
// inc/spiplistes_pipeline_insert_head.php

// formulaires, patrons, etc.
, 'abo_1_lettre' => 'Nieuwsbrief'
, 'abonnement_seule_liste_dispo' => "Abonnement &agrave; la seule liste disponible "
, 'abo_listes' => 'Abonnement'
, 'abonnement_0' => 'Abonnement'
, 'abonnement_titre_mail'=>'Modifier votre abonnement'
, 'abonnement_titre_mail'=>'Uw abonnement wijzigen'
, 'lire' => 'Lezen'
, 'listes_de_diffusion_' => "Verzendingslijst "
, 'jour' => 'dag'
, 'jours' => 'dagen'
, 'abonnement_bouton'=>'Uw abonnement aanpassen'
, 'abonnement_cdt' => "<a href='http://bloog.net/?page=spip-listes'>SPIP-Listes</a>"
, 'abonnement_change_format' => "U kan de vorm waarin u de nieuwsbrief ontvangt, wijzigen of u uitschrijven: "
, 'abonnement_texte_mail' => 'Geef hieronder het emailadres aan waarmee u geaboneerd was op de nieuwsbrief
	U zal een email ontvangen die u toegang geeft tot de pagina waarop u uw abonnement kan wijzigen.'
, 'article_entier' => 'Het volledige artikel lezen'
, 'form_forum_identifiants' => 'Bevestiging'
, 'form_forum_identifiant_confirm'=>'Uw abonnement werd opgeslagen, u zal een bevestigingsmail ontvangen.'
, 'demande_enregistree_retour_mail' => "
	Votre demande est enregistr&#233;e. Vous allez recevoir un mail de confirmation.
	"
, 'effectuez_modif_validez' => "
	<span>Bonjour @s@,</span>
	<br />
	Effectuez les modifications souhait&#233;es pour votre abonnement, puis validez.
	"
, 'vous_etes_desabonne' => "
	Vous &#234;tes maintenant d&#233;sabonn&#233; aux listes de diffusion,
	mais votre inscription sur ce site est toujours valide. Pour revenir &#224; ce formulaire de modification
	d&#39;abonnement, utilisez le lien qui vous a &#233;t&#233; envoy&#233; ou entrez &#224; nouveau votre
	adresse email dans le formulaire d&#39;inscription.
	"
, 'inscription_mail_forum' => 'Ziehier de inlognaam waarmee u zich kan inloggen in de site @nom_site_spip@ (@adresse_site@)'
, 'inscription_mail_redac' => 'Ziehier de inlognaam waarmee u zich kan inloggen in de site @nom_site_spip@ (@adresse_site@) 
	en in de interface voor de redactie (@adresse_site@/ecrire)'
, 'inscription_visiteurs' => 'L&acute;abonnement vous permet 
	d&acute;intervenir sur les forums r&eacute;serv&eacute;s aux visiteurs enregistr&eacute;s et de recevoir 
	les lettres d&acute;informations.'
, 'inscription_redacteurs' => "L&#39;espace de r&#233;daction de ce site est ouvert aux visiteurs apr&#232;s inscription.
	Une fois enregistr&#233;, vous pourrez consulter les articles en cours de r&#233;daction, proposer des articles
	et participer &#224; tous les forums.  L&#39;inscription permet &#233;galement d&#39;acc&#233;der aux parties du 
	site en acc&#232;s restreint et de recevoir les lettres d&#39;informations."
, 'mail_non' => 'U bent niet geabonneerd op de nieuwsbrief van de site @naam_site_spip@'
, 'messages_auto' => 'Automatische berichten'
, 'nouveaute_intro' => 'Goeiedag, <br />Dit zijn de nieuwigheden  van de site'
, 'nom' => 'naam van de gebruiker'
, 'texte_lettre_information' => 'Ziehier de nieuwsbrief van '
, 'vous_pouvez_egalement' => 'U kan ook'
, 'vous_inscrire_auteur' => 'U inschrijven als auteur'
, 'voir_discussion' => 'De discussie bekijken'
, 'inconnu' => 'niet langer geabonneerd op de lijst'
, 'infos_liste' => 'Over deze lijst'
, 'editeur' => 'Editor: '
, 'html_description' => " Texte enrichi (caract&egrave;res en gras ou en italique, parfois accompagn&eacute; d&#39;images)"
, 'texte_brut' => "Bruto tekst"
, 'vous_etes_abonne_aux_listes_' => "Vous &ecirc;tes abonn&eacute; aux listes de diffusion :"
, 'vous_etes_abonne_a_la_liste_' => "Vous &ecirc;tes abonn&eacute; &agrave; la liste de diffusion :"

// tableau items *_options
, 'Liste_de_destination' => "Liste de destination"
, 'Listes_1_du_mois' => "Publiques, 1<sup><small>er</small></sup> du mois."
, 'Liste_diffusee_le_premier_de_chaque_mois' => "Liste diffus&eacute;e le premier de chaque mois. "
, 'Listes_autre' => "Autre pr&eacute;riodicit&eacute;"
, 'Listes_autre_periode' => "Listes publiques autre pr&eacute;riodicit&eacute;"
, 'Listes_diffusion_prive' => "Listes priv&eacute;es"
, 'Liste_hebdo' => "Liste hebdomadaire"
, 'Publiques_hebdos' => "Publiques, hebdomadaires"
, 'Listes_diffusion_hebdo' => "Listes publiques hebdomadaires"
, 'Liste_mensuelle' => "Liste mensuelle"
, 'Publiques_mensuelles' => "Publiques, mensuelles"
, 'Listes_diffusion_mensuelle' => "Listes publiques mensuelles"
, 'Listes_diffusion_publiques_desc' => "L'abonnement &agrave; ces listes est propos&eacute; sur le site public."
, 'Liste_annuelle' => "Liste annuelle"
, 'Publiques_annuelles' => "Publiques, annuelles"
, 'Listes_diffusion_annuelle' => "Listes publiques annuelles"
, 'Listes_diffusion_publique' => 'Listes de diffusion publiques'
, 'Listes_diffusion_privees' => 'Listes de diffusion priv&eacute;es'
, 'Listes_diffusion_privees_desc' => "L'abonnement &agrave; ces listes est réserv&eacute;e aux administrateurs et auteurs du site."
, 'Listes_diffusion_suspendue' => 'Listes de diffusion suspendues'
, 'Listes_diffusion_suspendue_desc' => " "
, 'Courriers_en_cours_de_redaction' => 'Courriers en cours de r&eacute;daction'
, 'Courriers_en_cours_denvoi' => 'Courriers en cours d\'envoi'
, 'Courriers_prets_a_etre_envoye' => "Courriers pr&ecirc;ts &agrave; &ecirc;tre envoy&eacute;s"
, 'Courriers_publies' => "Courriers publi&eacute;s"
, 'Courriers_auto_publies' => "Courriers automatiques publi&eacute;s"
, 'Courriers_stope' => "Courriers stopp&eacute;s en cours d'envoi"
, 'Courriers_vides' => "Courriers annul&eacute;s (vides)"
, 'Courriers_sans_destinataire' => "Courriers sans destinataire (liste vide)"
, 'Courriers_sans_liste' => "Courriers sans abonn&eacute;s (liste manquante)"
, 'devenir_redac'=>'redacteur worden van deze site'
, 'devenir_membre'=>'Word lid van deze site'
, 'devenir_abonne' => "U inschrijven op deze site"
, 'desabonnement_valid'=>'Het volgende adres is niet langer ingeschreven op de nieuwsbrief' 
, 'pass_recevoir_mail'=>'U zal een email ontvangen die u zal uitleggen hoe u uw abonnement kan wijzigen. '
, 'discussion_intro' => 'Goeiedag, <br />ziehier de discussies die gehouden werden op deze site'
, 'En_redaction' => "En r&eacute;daction"
, 'En_cours' => "En cours"
, 'editeur_nom' => "Nom de l'&eacute;diteur "
, 'editeur_adresse' => "Adresse "
, 'editeur_rcs' => "N&deg; RCS "
, 'editeur_siret' => "N&deg; SIRET "
, 'editeur_url' => "URL du site de l'&eacute;diteur "
, 'editeur_logo' => 'URL (ou DATA URL sheme) du logotype de l&#8217;&#233;diteur '
, 'Envoi_abandonne' => "Envoi abandonn&eacute;"
, 'Liste_prive' => "Liste priv&eacute;e"
, 'Liste_publique' => "Liste publique"
, 'message_redac' => 'In staat van redactie en klaar voor verzending'
, 'Prets_a_envoi' => "Pr&ecirc;ts &agrave; l'envoi"
, 'Publies' => "Publi&eacute;s"
, 'publies_auto' => "Publi&eacute;s (auto)"
, 'Stoppes' => "Stopp&eacute;s"
, 'Sans_destinataire' => "Sans destinataire"
, 'Sans_abonnement' => "Sans abonnement"
, 'sans_abonne' => "sans abonn&eacute;"
, 'sans_moderateur' => "sans mod&eacute;rateur"

// raccourcis des paniers
, 'aller_au_panier_' => "Aller au panier "
, 'aller_aux_listes_' => "Aller aux listes "
, 'Nouveau_courrier' => 'Cr&eacute;er un nouveau courrier'
, 'Nouvelle_liste_de_diffusion' => 'Nieuwe verzendingslijst opstellen'
, 'trieuse_suspendue' => "Trieuse suspendue"
, 'trieuse_suspendue_info' => "Le traitement des listes de diffusion programm&eacute;es est suspendu."
, 'Trieuse_reactivee' => "Trieuse r&eacute;activ&eacute;e"

// mots
, 'ajout' => "Ajout"
, 'aucun' => "aucun"
, 'Configuration' => 'Configuratie'
, 'courriers' => 'Courriers'
, 'creation' => "Cr&#233;ation"
, '_de_' => " de "
, 'email' => 'E-mail'
, 'format' => 'Format'
, 'modifier' => 'Wijzigen'
, 'max_' => "Max "
, 'Patrons' => 'Patronen'
, 'patron_' => "Patroon: "
, 'spiplistes' => "SPIP-Listes"
, 'recherche' => 'Zoeken'
, 'retablir' => "R&eacute;tablir"
, 'site' => 'Website'
, 'sujets' => 'Onderwerpen'
, 'sup_' => "Sup."
, 'total' => "Total "
, 'voir' => 'bekijken'
, 'Vides' => "Leeg"
, 'choisir' => 'kiezen'
, 'desabo' => 'd&eacute;sabo'
, 'desabonnement' => 'Uitschrijving'
, 'desabonnes' => 'Uitgeschrevenen'
, 'destinataire' => 'bestemmeling'
, 'destinataires' => 'Bestemmelingen'
, 'erreur' => 'Error'
, 'html' => 'HTML'
, 'retour_link' => 'Terugkeer'
, 'texte' => 'Tekst'
, 'version' => 'versie'
, 'fichier_' => "Fichier "

, 'jquery_inactif' => "jQuery non d&#233;tect&#233;. Merci de l&#39;activer."

///////
// a priori, pas|plus utilise'
, 'supprime_contact_base' => 'Definitief van de database verwijderen'
, 'forcer_lot' => 'De volgende lijst versturen'
, 'erreur_destinataire' => 'Verzendingsprobleem: de mail werd niet verstuurd'
, 'contacts_lot' => 'Contacten van deze lijst'
, 'envoi_fini' => 'Verzending volbracht'
, 'non_courrier' => 'Geen berichten meer te versturen'
, 'non_html' => 'Uw software is blijkbaar niet in staat de grafische versie (HTML) van deze email correct weer te geven'
, 'envoi_erreur' => 'Error: SPIP-lijsten vinden geen bestemmelingen voor dit bericht'
, 'email_reponse' => 'antwoord email: '
, 'envoi_listes' => 'Verzending naar de geabonneerden van de lijst: '
, 'confirmer' => 'bevestigen'
, 'listes_emails' => 'Nieuwsbrieven'
, 'info_liste_1' => 'liste'
, 'bonjour' => 'Goeiedag,' // deja dans SPIP
, 'envoi_tous' => 'Verzending naar alle geabonneerden'
, 'patron_detecte' => '<p><strong>Patron d&eacute;tect&eacute; pour la version texte</strong><p>'
, 'val_texte' => 'Texte'
, 'membres_sans_messages_connecte' => 'U heeft geen nieuwe berichten'
, 'messages_derniers' => 'Laatste berichten'
, 'pas_abonne_en_ce_moment' => "n'est pas abonn&eacute;"
, 'reinitialiser' => 'Opfrissen'
, 'mail_a_envoyer' => 'Aantal verzonden berichten: '
, 'lettre_d_information' => 'Nieuwsbrief'
, 'desole' => 'sorry'
, 'Historique_des_envois' => 'Historiek van de verzendigen'
, 'patron_disponibles' => 'beschikbare patronen'
, 'liste_diff_publiques' => 'Publieke verzendingslijsten<br /><i>De pagina van de publieke site stelt de inschrijving voor de volgende lijsten voor</i>'
, 'messages_non_lus_grand' => 'Geen nieuwe berichten'
, 'messages_repondre' => 'Nieuw antwoord'
, 'Liste_abandonnee' => "Liste abandonn&eacute;e"
, 'par_date' => 'Volgens datum van inschrijving'
, 'info_auto' => 'SPIP-lijsten voor spip kunnen aan de geabonneerden regelmatig een bericht sturen met daarin de nieuwigheden van de site (recent gepubliceerde artikels en korte beschrijvingen).'
, 'format2' => 'Format :'
, 'liste_des_abonnes' => "Liste des abonn&eacute;s"
, 'lieu' => 'Localisation'
, 'efface_base' => 'werd verwijderd van de lijst en de database'
, 'lot_suivant' => 'De verzending dwingen'
, 'listes_internes' => 'Interne verzendingslijst <br /><i>Op het moment van de verzending, worden de volgende lijsten voorgesteld onder de bestemmelingen.</i>'
, 'adresses_importees' => "Geïmporteerde adressen"
, 'aff_envoye' => 'Verzonden emails'
, 'abonner' => 'abonneren'
, 'abonnes_liste_int' => 'Geabonneerden van de interne lijst: '
, 'abonnes_liste_pub' => 'Geabonneerden van de publieke lijst: '
, 'actualiser' => 'Vernieuwen'
, 'a_destination_de_' => 'ten aanzien van '
, 'aff_lettre_auto' => 'Verstuurde nieuwsbrieven'
, 'alerte_edit' => 'Het formulier hieronder geeft u de mogelijkheid om de tekst van een email te wijzigen. 
	U kan kiezen te beginnen door een patroon te importeren om zo de inhoud van uw bericht te beheren.'
, 'alerte_modif' => '<strong>Na het zichtbaar maken van uw email kan u er de inhoud nog van veranderen.</strong>'
, 'lock' => 'Actief Lock: '
, 'Apercu' => "Aper&ccedil;u"
, 'bouton_listes' => 'Nieuwsbrief'
, 'bouton_modifier' => 'Dit bericht wijzigen'
, 'dans_jours' => 'in'
, 'charger_le_patron' => 'De berichten beheren'
, 'choix_defini' => 'Geen specifieke keuze.\n'
, 'definir_squel_choix' => 'Tijdens het redigeren van een nieuw bericht laten SPIP lijsten u toe een patroon te laden. 
	Door op een knop te drukken, laadt u in het tekstbericht de inhoud van een van de skeletten uit het 
	repertorium<strong>/patrons</strong>(gesitueerd in de wortel van uw Spip site). 
	<p><strong>U kan deze skeletten aanpassen aan uw persoonlijke voorkeur</strong></p> 
	<ul><li>De skeletten kunnen HTML codes bevatten.</li>
	<li>Dit skelet kan een Spip-krul bevatten.</li>
	<li>Na het laden van een patroon kan u de inhoud van het bericht voor het versturen, wijzigen</li>
	</ul><p>De functie “een patroon laden” laat u toe om gepersonaliseerde HTML templates te gebruiken voor uw berichten of om thematische nieuwsbrieven te maken waarvan de inhoud dankzij de Spip-krullen gedefiniëerd is.</p>
	<p>Opgelet: dit skelet mag geen kader body, head of html bevatten, maar enkel een HTML code of Spip krullen.</p>'
, 'definir_squel' => 'Kies het berichtmodel dat u wil bekijken.'
, 'courrier_realise_avec_spiplistes' => "Courrier r&eacute;alis&eacute; avec SPIP-Listes"
, 'definir_squel_texte' => 'Indien u over toegangscodes voor FTP beschikt, kan u SPIP skeletten toevoegen aan het repertorium of de patronen (in de wortel van de Spip site).'
, 'dernier_envoi'=>'Laatste berichten dateren van'
, 'desabonnement_confirm'=>'U staat op het punt om uw abonnement op de nieuwsbrief op te geven'
, 'date_depuis'=>'vanaf @delai@'
, 'envoi_charset' => 'Charset van het versturen'
, 'envoi_nouv' => 'Versturen van nieuwigheden'
, 'envoi_program' => 'Geprogrameerd versturen'
, 'envoi_smtp' => 'Tijdens het versturen via de SMTP methode definieert dit veld het adres van de bestemmeling.'
, 'envoi_texte' => 'Als het bericht u bevalt, kan u het versturen'
, 'email_envoi' => 'Verzending van emails'
, 'envoi' => 'Verzending:'
, 'erreur_install' => '<h3>foutmelding: spip-lijst is verkeerd geïnstalleerd!</h3>'
, 'erreur_install2' => '<p>Ga de verschillende stappen van de installatie na, vooral of u <i>mijn_opties.txt</i> correct herbenoemde in <i>mijn_opties.php</i>.</p>'
, 'exporter' => 'De lijst van geabonneerden exporteren'
, 'Erreur_appel_courrier' => "Erreur lors de l'appel du courrier"
, 'faq' => 'FAQ'
, 'forum' => 'Forum'
, 'ferme' => 'Deze discussie is afgesloten'
, 'gestion_du_courrier' => "Gestion du courrier"
, 'info_heberg' => 'Sommige hosts desactiveren het automatisch versturen van emails vanaf hun server. In dat geval zullen de volgende functies van de SPIP-lijsten voor SPIP niet functioneren.'
, 'info_nouv' => 'U heeft het versturen van nieuwigheden geactiveerd'
, 'info_nouv_texte' => 'Volgende verzending van nieuwigheden binnen @proch@ dagen'
, 'log' => 'Logs'
, 'login' => 'Verbinding'
, 'logout' => 'Loskoppeling'
, 'mail_format' => 'U bent geabonneerd op de nieuwsbrief van de site @naam_site_spip@ in vorm'
, 'messages_auto_texte' => '<p>Normaal gezien, laat het skelet van de nieuwigheden toe om de lijst automatisch 
	te verzenden met de laatst verstuurde artikels en samenvattingen die geplaatst werden sinds de laatste automatische verzending </p>
	<p> U kan het bericht personaliseren door het adres een logo te geven en een beeld voor de tussentitels tijdens het editen van het betreffende bestan.  
	<strong>"nieuwigheden.html"</strong> (in het repertorium /dist).</p>'
, 'membres_groupes' => 'Gebruikersgroepen'
, 'membres_profil' => 'Profiel'
, 'membres_messages_deconnecte' => 'Zich inloggen om privé berichten te controleren'
, 'membres_avec_messages_connecte' => 'U heeft @nombres@ nieuw(e) bericht(en)'
, 'message' => 'Bericht: '
, 'message_date' => 'Verstuurd op '
, 'messages' => 'berichten'
, 'messages_forum_clos' => 'Forum gedesactiveerd'
, 'messages_nouveaux' => 'Nieuwe berichten'
, 'messages_pas_nouveaux' => 'Geen nieuwe berichten'
, 'messages_voir_dernier' => 'Het laatste bericht zien'
, 'moderateurs' => "Moderator(en)"
, 'mis_a_jour' => 'Updaten'
, 'nouveaux_messages' => 'Nieuwe berichten'
, 'numero' => 'N&nbsp;'
, 'photos' => 'Foto\'s'
, 'poster' => 'Een bericht verzenden'
, 'publie' => 'Gepubliceerd op'
, 'aucune_liste_publique' => "Aucune liste de diffusion publique disponible."
, 'revenir_haut' => 'Terugkeren naar het begin van de pagina'
, 'reponse' => 'Als antwoord op het bericht'
, 'reponse_plur' => 'antwoorden'
, 'reponse_sing' => 'antwoord'
, 'retour' => 'Email adres van de beheerder van de lijst (reply-to)'
, 'Suivi_des_abonnements' => 'Opvolging van de abonnementen'
, 'sujet_nouveau' => 'Nieuw onderwerp'
, 'sujet_auteur' => 'Auteur'
, 'sujet_visites' => 'Bezoeken'
, 'sujet_courrier_auto' => 'Onderwerp van automatische verzending: '
, 'sujets_aucun' => 'Momenteel geen onderwerp in dit forum'
, 'sujet_clos_titre' => 'Onderwerp Gesloten'
, 'sujet_clos_texte' => 'Dit onderwerp is gesloten, u kan er niet op verzenden.'
, 'masquer_le_journal_SPIPLISTES' => "Masquer le journal de SPIP-Listes"
, 'abon' => 'GEABONNEERDEN'
, 'abonees' => 'alle geabonneerden'
, 'abonnement_newsletter' => '<strong>Abonnement op de nieuwsbrief</strong>'
, 'acces_a_la_page' => 'Vous n\'U heeft geen toegang tot deze pagina.'
, 'adresse_deja_inclus' => 'Adres bestaat reeds'
, 'Choisir_cette_liste' => 'Deze lijst kiezen'
, 'Charger_un_patron' => "Charger un patron"
, 'date_ref' => 'Referentiedag'
, 'efface' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base'
, 'email_collec' => 'Een bericht editen'
, 'email_test_liste' => 'Versturen naar een verzendingslijst'
, 'envoyer' => 'de email verzendenl'
, 'envoyer_a' => 'Verzending naar '
, 'listes_poubelle' => 'Uw verzendingslijst in de prullenbak'
, 'Liste_numero_:' => 'Liste num&eacute;ro :'
, 'mail_tache_courante' => 'Berichten verzonden voor de lopende taak: '
, 'messages_auto_envoye' => 'Automatisch verzonden berichten'
, 'nb_abonnes' => 'In de lijsten: '
, 'nb_inscrits' => 'In de site:  '
, 'nb_listes' => 'Inschrijving in alle lijsten: '
, 'nouvelle_abonne' => 'De volgende geabonneerde heeft zich aan de lijst toegevoegd'
, 'pas_acces' => 'U heeft geen toegang tot deze pagina.'
, 'plus_abonne' => ' is niet langer op de lijst geabonneerd '
, 'prochain_envoi_aujd' => 'Volgende verzending voorzien voor vandaag'
, 'prochain_envoi_prevu_dans' => 'Volgende verzending voorzien binnen '
, 'program' => 'Programmatie van automatische berichten'
, 'plein_ecran' => "(Volledig scherm)"
, 'remplir_tout' => 'Alle velden dienen ingevuld te worden'
, 'repartition' => 'Verdeling'
, 'squel' => 'Patron : &nbsp;'
, 'suivi_envois' => 'Verloop van de verzendingen'
, 'supprime_contact' => 'Dit contact van de lijst verwijderen'
, 'tableau_bord' => 'Randbord'
, 'toutes' => 'Alle geabonneerden'
, 'acces_refuse' => 'U heeft niet langer toegang meer tot de site'
, 'confirmation_format' => ' in vorm '
, 'confirmation_liste_unique_1' => 'U bent geabonneerd op de nieuwslijsten van de site'
, 'confirmation_liste_unique_2' =>'U heeft er voor gekozen om de berichten te ontvangen voor de volgende lijst:'
, 'confirmation_listes_multiples_1' => 'U bent geabonneerd op de nieuwslijst van de site '
, 'confirmation_listes_multiples_2' => 'U heeft er voor gekozen om de berichten te ontvangen voor de volgende lijsten:'
, 'contacts' => 'Aantal contacten'
, 'patron_erreur' => 'Het betreffende patroon geeft niet de resultaten met de gekozen parameters'
, 'abonees_titre' => 'Abonn&eacute;s'
, 'options' => 'radio|brut|Format :|Html,Texte,D&eacute;sabonnement|html,texte,non'

);

?>