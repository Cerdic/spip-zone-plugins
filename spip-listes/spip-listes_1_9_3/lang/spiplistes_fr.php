<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

$GLOBALS['i18n_spiplistes_fr'] = array(

// CP-20081126: classement par scripts
// action/spiplistes_agenda.php
// action/spiplistes_changer_statut_abonne.php
// action/spiplistes_envoi_lot.php
// action/spiplistes_journal.php
// action/spiplistes_lire_console.php
// action/spiplistes_liste_des_abonnes.php
// action/spiplistes_listes_abonner_auteur.php
// action/spiplistes_moderateurs_gerer.php
'voir_historique' => 'Voir l\'historique des envois'
, 'pas_de_liste_prog' => "Aucune liste programm&eacute;e."

// action/spiplistes_supprimer_abonne.php
// balise/formulaire_abonnement.php
, 'inscription_liste_f' => 'Vous avez choisi de recevoir les courriers adress&eacute;s &#224; la liste suivante en format @f@ : '
, 'inscription_listes_f' => 'Vous avez choisi de recevoir les courriers adress&eacute;s aux listes suivantes en format @f@ : '
, 'inscription_reponse_s' => "Vous &#234;tes abonn&#233; &#224; la liste d'information du site @s@"
, 'inscription_reponses_s' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site @s@'
, 'vous_abonne_aucune_liste' => "Vous n'&ecirc;tes pas abonn&eacute; &#224; une liste de diffusion"
, 'liste_dispo_site_' => "Liste de diffusion disponible sur ce site : "
, 'listes_dispos_site_' => "Listes de diffusion disponibles sur ce site : "
, 'desole_pas_de_liste' => "D&#233;sol&#233;, il n&#39;y a pas de liste de diffusion disponible pour le moment."
, 'pour_vous_abonner' => "Pour vous abonner aux listes de diffusion"
// obsolete
, 'abonnement_mail_passcookie' => "
	<br />
	Pour modifier votre abonnement aux lettres d'information du site <strong>@nom_site_spip@</strong> (@adresse_site@), 	
	veuillez vous rendre &#224; l'adresse suivante :<br /><br />
	<a href='@adresse_site@/spip.php?page=abonnement&d=@cookie@'>@adresse_site@/spip.php?page=abonnement&d=@cookie@</a><br /><br />
	Vous pourrez alors confirmer la modification de votre abonnement.
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
, 'confirmation_inscription' => "Confirmation de votre inscription"
, 'souhait_modifier_abo'=>'Vous souhaitez modifier votre abonnement &#224; la lettre d\'information'
, 'suspendre_abonnement_' => "Suspendre mon abonnement "
, 'vous_etes_redact' => "Vous &#234;tes inscrit en tant que r&#233;dacteur."
, 'vous_etes_membre' => "Vous &#234;tes membre abonn&#233; aux listes de diffusion de ce site.
	Il est parfois n&#233;cessaire de s&#39;authentifier pour avoir acc&#232;s &#224; ces listes."

// balise/formulaire_modif_abonnement.php
, 'abonnement_modifie' => 'Vos modifications sont prises en compte'
, 'abonnement_nouveau_format' => 'Votre format de r&eacute;ception est d&eacute;sormais : '

// base/spiplistes_init.php
, 'autorisation_inscription' => 'SPIP-listes vient d\'activer l\'autorisation de s\'inscrire aux visiteurs du site'

// base/spiplistes_tables.php
// base/spiplistes_upgrade.php
// docs/spiplistes_aide_fr.html
// exec/spiplistes_abonne_edit.php
, 'adresse_mail_obligatoire' => "Adresse mail manquante. Abonnement impossible."
, 'abonne_sans_format' => "Ce compte est actuellement d&eacute;sabonn&eacute;. Aucun format de courrier n'est 
	d&eacute;fini. Il ne peut pas recevoir de courrier. D&eacute;finissez un format 
	de r&eacute;ception pour ce compte afin de valider son abonnement."
, 'Desabonner_temporaire' => "D&eacute;sabonner temporairement ce compte."
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
, 'mettre_a_jour' => '<h3>SPIP-listes va mettre &#224; jour</h3>'
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
, 'abonne_aucune_liste' => 'Abonn&eacute;s &#224; aucune liste'
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
, 'requiert_identification' => "Requiert une identification"
, 'adresse_smtp' => "Adresse email du <em>sender</em> SMTP"
, '_aide_install' => "<p>Bienvenue dans le monde de SPIP-Listes.</p>
	<p class='verdana2'>Par d&eacute;faut, &#224; l'installation, SPIP-Listes est en mode <em>simulation 
	d'envoi</em> afin de vous permettre de d&eacute;couvrir les fonctionnalit&eacute;s 
	et d'effectuer vos premiers tests.</p>
	<p class='verdana2'>Pour valider les diff&eacute;rentes options de SPIP-Listes, rendez-vous <a href='@url_config@'>sur 
	la page de configuration</a>.</p>"
, 'adresse_envoi_defaut' => "Adresse d&#39;envoi par d&eacute;faut"
, 'adresse_on_error_defaut' => "Adresse de retour par d&eacute;faut pour les erreurs"
, 'pas_sur' => '<p>Si vous n\'&ecirc;tes pas s&ucirc;r, choisissez la fonction mail de PHP.</p>'
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
, 'Patron_du_tampon' => "Patron du tampon "
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
	Si vous faites une mise &#224; jour de votre courrier, pensez &#224; vider cette boîte avant de g&eacute;n&eacute;rer le contenu."
, 'charger_patron' => 'Choisir un patron pour le courrier'
, 'Courrier_numero_' => "Courrier num&eacute;ro :" // + _gerer
, 'Creer_un_courrier_' => "Cr&eacute;er un courrier :"
, 'choisir_un_patron_' => "Choisir un patron "
, 'Courrier_edit_desc' => 'Vous pouvez choisir de g&eacute;n&eacute;rer automatiquement le contenu du courrier
	ou r&eacute;diger simplement votre courrier dans la bo&icirc;te <strong>texte du courrier</strong>.'
, 'Contenu_a_partir_de_date_' => "Contenu &#224; partir de cette date "
, 'Cliquez_Generer_desc' => "Cliquez sur <strong>@titre_bouton@</strong> pour injecter le r&eacute;sultat 
	dans la bo&icirc;te @titre_champ_texte@."
, 'Lister_articles_de_rubrique' => "Et lister les articles de la rubrique "
, 'Lister_articles_mot_cle' => "Et lister les articles du mot-cl&eacute; "
, 'edition_du_courrier' => "Edition du courrier" // + gerer
, 'generer_un_sommaire' => "G&eacute;n&eacute;rer un sommaire"
, 'generer_patron_' => "G&eacute;n&eacute;rer le patron "
, 'generer_patron_avant' => "avant le sommaire"
, 'generer_patron_apres' => "apr&egrave;s le sommaire."
, 'introduction_du_courrier_' => "Introduction &#224; votre courrier, avant le contenu issu du site "
, 'Modifier_un_courrier__' => "Modifier un courrier :"
, 'Modifier_ce_courrier' => "Modifier ce courrier"
, 'sujet_courrier' => '<strong>Sujet du courrier</strong> [obligatoire]'
, 'texte_courrier' => '<strong>Texte du courrier</strong> (HTML autoris&eacute;)'
, 'avec_patron_pied__' => "Avec le patron de pied : "

// exec/spiplistes_courrier_gerer.php
, 'Erreur_Adresse_email_invalide' => 'Erreur: l\'adresse email que vous avez fournie n\'est pas valide'
, 'langue_' => '<strong>Langue :</strong>&nbsp;'
, 'calcul_patron' => 'Calcul avec le patron version texte'
, 'calcul_html' => 'Calcul depuis la version HTML du message'
, 'dupliquer_ce_courrier' => "Dupliquer ce courrier"
, 'destinataire_sans_format_alert' => "Destinataire sans format de r&eacute;ception.
	Appliquez un format de r&eacute;ception (texte ou html) pour ce compte ou s&eacute;lectionnez un autre destinataire."
, 'envoi_date' => 'Date de l\'envoi : '
, 'envoi_debut' => 'Debut de l\'envoi : '
, 'envoi_fin' => 'Fin de l\'envoi : '
, 'erreur_envoi' => 'Nombre d\'envois en erreur : '
, 'Erreur_liste_vide' => "Erreur: cette liste n'a pas d'abonn&eacute;s."
, 'Erreur_courrier_introuvable' => "Erreur: ce courrier n'existe pas." // + previsu
, 'Envoyer_ce_courrier' => "Envoyer ce courrier"
, 'format_html__n' => "Format html : @n@"
, 'format_texte__n' => "Format texte : @n@"
, 'message_arch' => 'Courrier archiv&eacute;'
, 'message_en_cours' => 'Courrier en cours d\'envoi'
, 'message_type' => 'Courrier &eacute;lectronique'
, 'sur_liste' => 'Sur la liste' // + casier
, 'Supprimer_ce_courrier' => "Supprimer ce courrier"
, 'email_adresse' => 'Adresse email de test' // + liste
, 'email_test' => 'Envoyer un email de test'
, 'Erreur_courrier_titre_vide' => "Erreur: votre courrier n'a pas de titre."
, 'message_en_cours' => 'Ce courrier est en cours de r&eacute;daction'
, 'modif_envoi' => 'Vous pouvez le modifier ou demander son envoi'
, 'message_presque_envoye' =>'Ce courrier est sur le point d\'&ecirc;tre envoy&eacute;'
, 'Erreur_Adresse_email_inconnue' => 'Attention, l\'adresse email de test que vous avez fournie ne correspond &#224; 
	aucun abonn&eacute;, <br />l\'envoi ne peut se faire, veuillez reprendre la proc&eacute;dure<br /><br />'

// exec/spiplistes_courrier_previsu.php
, 'lettre_info' => 'La lettre d\'information du site'

// exec/spiplistes_courriers_casier.php
// exec/spiplistes_import_export.php
, 'Exporter_une_liste_d_abonnes' => "Exporter une liste d'abonn&eacute;s"
, 'Exporter_une_liste_de_non_abonnes' => "Exporter une liste de non abonn&eacute;s"
, '_aide_import' => "Vous pouvez importer ici une liste d'abonn&eacute;s &#224; partir de votre 
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
, 'Tous_les_s' => "Tous les @s@"
, 'Toutes_les_semaines' => "Toutes les semaines"
, 'Tous_les_mois' => "Tous les mois, "
, 'Tous_les_ans' => "Tous les ans"
, 'version_html' => '<strong>Version HTML</strong>'
, 'version_texte' => '<strong>Version texte</strong>'
, 'erreur_import' => 'Le fichier d\'import pr&eacute;sente une erreur &#224; la ligne '
, 'envoi_manuel' => "Envoi manuel"
, 'format_date' => 'Y/m/d'
, 'importer' => 'Importer une liste d\'abonn&eacute;s'
, 'importer_fichier' => 'Importer un fichier'
, 'importer_fichier_txt' => '<p><strong>Votre liste d\'abonn&eacute;s doit &ecirc;tre un fichier simple (texte) 
	qui ne comporte qu\'une adresse e-mail par ligne</strong></p>'
, 'importer_preciser' => '<p>Pr&eacute;cisez les listes et le format correspondant &#224; votre import d\'abonn&eacute;s</p>'
, 'prochain_envoi_prevu' => 'Prochain envoi pr&eacute;vu' // + gerer
, 'option_import_' => "Option d'importation "
, 'forcer_abos_' => "Forcer les abonnements (si l&#39;adresse mail existe dans la base, forcer l&#39;abonnement
	pour la s&#233;lection, pour cet abonn&#233;)."
, 'erreur_import_base' => "Erreur importation. Data incorrect ou erreur base SQL."
, 'erreur_n_fois' => "(erreur rencontree @n@ fois)"
, 'Liste_de_destination_s' => "Liste de destination : @s@"
, 'Listes_de_destination_s' => "Listes de destination : @s@"
, 'pas_dimport' => "Pas d&#39;import. Soit le fichier est vide, soit toutes les adresses sont d&#233;j&#224; abonn&#233;es."

// exec/spiplistes_liste_edit.php
, 'texte_dinsctription_' => "Texte d'inscription : "
, 'Creer_une_liste_' => "Cr&eacute;er une liste "
, 'en_debut_de_semaine' => "en d&eacute;but de semaine"
, 'en_debut_de_mois' => "en d&eacute;but de mois"
, 'envoi_non_programme' => "Envoi non programm&eacute;"
, 'edition_dune_liste' => "Edition d'une liste"
, 'texte_contenu_pied' => '<br />(Message ajout&eacute; en bas de chaque email au moment de l\'envoi)<br />'
, 'texte_pied' => '<p><strong>Texte du pied de page</strong>'
, 'modifier_liste' => 'Modifier cette liste '
, 'txt_abonnement' => '(Indiquez ici le texte pour l\'abonnement &#224; cette liste, affich&eacute; 
	sur le site public si la liste est active)'

// exec/spiplistes_liste_gerer.php
, 'forcer_les_abonnement_liste' => "Forcer les abonnements pour cette liste"
, 'periodicite_tous_les_n_s' => "P&eacute;riodicit&eacute; : tous les @n@ @s@"
, 'liste_sans_titre' => 'Liste sans titre'
, 'statut_interne' => "Priv&eacute;"
, 'statut_publique' => "Publique"
, 'adresse' => "Indiquez ici l&#39;adresse &#224; utiliser pour les r&eacute;ponses de mails 
	(&#224; d&eacute;faut, l&#39;adresse du webmestre sera utilis&eacute;e comme adresse de r&eacute;ponse) :"
, 'Ce_courrier_ne_sera_envoye_qu_une_fois' => "Ce courrier ne sera envoy&eacute; qu'une fois."
, 'adresse_de_reponse' => "Adresse de r&eacute;ponse"
, 'adresse_mail_retour' => 'Adresse email du gestionnaire de la liste (reply-to)'
, 'Attention_action_retire_invites' => "Attention: cette action retire les invit&eacute;s de la liste des abonn&eacute;s."
, 'A_partir_de' => "&#224; partir de"
, 'Apercu_plein_ecran' => "Aper&ccedil;u plein &eacute;cran dans une nouvelle fen&ecirc;tre"
, 'Attention_suppression_liste' => "Attention ! Vous demandez la suppression d'une liste de diffusion. 
	Les abonn&eacute;s seront retir&eacute;s de cette liste de diffusion automatiquement. "
, 'Abonner_tous_les_invites_public' => "Abonner tous les membres invit&eacute;s &#224; cette liste publique."
, 'Abonner_tous_les_inscrits_prives' => "Abonner tous les membres &#224; cette liste priv&eacute;e, sauf les visiteurs."
, 'boite_confirmez_envoi_liste' => "Vous avez demand&eacute; l'envoi imm&eacute;diat de cette liste 
	de diffusion.<br />
	Svp, veuillez confirmer votre demande."
, 'cette_liste_est_' => "Cette liste est : @s@"
, 'Confirmer_la_suppression_de_la_liste' => "Confirmer la suppression de la liste "
, 'Confirmez_requete' => "Veuillez confirmer la requ&ecirc;te."
, 'date_expedition_' => "Date d'exp&eacute;dition "
, 'Dernier_envoi_le_' => "Dernier envoi le :"
, 'forcer_abonnement_desc' => "Vous pouvez forcer ici les abonnements &#224; cette liste, soit pour tous 
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
, 'message_sujet' => 'Sujet '
, 'mods_cette_liste' => "Les mod&eacute;rateurs de cette liste"
, 'nbre_abonnes' => "Nombre d'abonn&eacute;s : "
, 'nbre_mods' => "Nombre de mod&eacute;rateurs : "
, 'patron_manquant_message' => "Vous devez appliquer un grand patron avant de param&eacute;trer l'envoi de cette 
	liste."
, 'liste_sans_patron' => "Liste sans patron." // courriers_listes
, 'Patron_grand_' => "Grand patron "
, 'sommaire_date_debut' => "A partir de la date définie ci-dessus"
, 'abos_cette_liste' => "Les abonn&eacute;s &#224; cette liste"
, 'confirme_envoi' => 'Veuillez confirmer l\'envoi'
, 'env_esquel' => 'Envoi programm&eacute; du patron'
, 'env_maint' => 'Envoyer maintenant'
, 'date_act' => 'Donn&eacute;es actualis&eacute;es'
, 'forcer_les_abonnements_au_format_' => "Forcer les abonnements au format : "
, 'pas_denvoi_auto_programme' => "Il n'y a pas d&#39;envoi automatique planifi&eacute; pour cette liste de diffusion."
, 'Pas_de_periodicite' => "Pas de p&eacute;riodicit&eacute;."
, 'prog_env' => 'Programmer un envoi automatique'
, 'prog_env_non' => 'Ne pas programmer d\'envoi'
, 'conseil_regenerer_pied' => "<br />Ce patron est issu d'une ancienne version de SPIP-Listes.<br />
	Conseil: s&eacute;lectionnez &#224; nouveau le patron de pied pour prendre en compte le multilinguisme 
	et/ou la version &#39;texte seul&#39; du patron."
, 'boite_alerte_manque_vrais_abos' => "Il n&#39;y a pas d&#39;abonn&eacute;s pour cette liste de diffusion,
	ou les abonn&eacute;s n'ont pas de format de r&eacute;ception.
	<br />
	Corrigez le format de r&eacute;ception pour au moins un abonn&eacute; avant de valider l'envoi."	

// exec/spiplistes_listes_toutes.php
// exec/spiplistes_maintenance.php
, 'abonnes' => 'abonn&eacute;s'
, '1_abonne' => '1 abonn&eacute;'
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
, '_aide' => '<p>SPIP-Listes permet d\'envoyer un courrier ou des courriers automatiques &#224; des abonn&eacute;s.</p>
	<p>Vous pouvez &eacute;crire un texte simple, composer votre courrier en HTML ou appliquer un "patron" &#224; 
	votre courrier</p>
	<p>Via un formulaire d\'inscription public, les abonn&eacute;s d&eacute;finissent eux-m&ecirc;mes leur statut d\'abonnement, 
	les listes auxquelles ils s\'abonnent et le format
	dans lequel ils souhaitent recevoir les courriers (HTML/texte). </p>
	<p>Tout courrier sera traduit automatiquement en format texte pour les abonn&eacute;s qui en ont fait la demande.</p>
	<p><strong>Note :</strong><br />L\'envoi des mails peut prendre quelques minutes : les lots partent peu &#224; 
	peu quand les utilisateurs parcourent le site public. Vous pouvez aussi provoquer manuellement l\'envoi des lots 
	en cliquant sur le lien "suivi des envois" pendant un envoi.</p>'
, 'envoi_en_cours' => 'Envoi en cours'
, 'nb_destinataire_sing' => " destinataire"
, 'nb_destinataire_plur' => " destinataires"
, 'aucun_destinataire' => "aucun destinataire"
, '1_liste' => '@n@ liste'
, 'n_listes' => '@n@ listes'
, 'utilisez_formulaire_ci_contre' => "Utilisez le formulaire ci-contre pour activer/d&eacute;activer cette option."
, 'texte_boite_en_cours' => 'SPIP-Listes envoie un courrier.<p>Cette boite disparaitra une fois l\'envoi achev&eacute;.</p>'
, 'meleuse_suspendue_info' => "L'envoi des courriers en attente d'exp&eacute;dition est suspendu."
, 'casier_a_courriers' => "Casier &#224; courriers" // + courriers_casier
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
, 'email_tester' => 'Tester par email'
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
, 'erreur_sans_destinataire' => 'Erreur : aucun destinataire ne peut &ecirc;tre trouv&eacute; pour ce courrier'
, 'envoi_annule' => 'Envoi annul&eacute;'
, 'sans_adresse' => ' Mail non envoy&eacute; -> Veuillez d&eacute;finir une adresse de r&eacute;ponse'
, 'erreur_mail' => 'Erreur : envoi du mail impossible (v&eacute;rifier si mail() de php est disponible)'
, 'abonnement_mail_text' => 'Pour modifier votre abonnement, veuillez vous rendre &#224; l\'adresse suivante : '
, 'msg_abonne_sans_format' => "format de reception manquant"
, 'Cliquez_ici_pour_modifier_votre_abonnement' => "<br />Cliquez ici pour modifier votre abonnement"

// inc/spiplistes_naviguer_paniers.php
// inc/spiplistes_pipeline_I2_cfg_form.php
// inc/spiplistes_pipeline_affiche_milieu.php
, 'Adresse_email_obligatoire' => "Une adresse email est obligatoire pour pouvoir vous abonner aux listes de diffusion. 
	Si vous d&eacute;sirez profiter de ce service, merci de modifier votre fiche en compl&eacute;tant ce champ. "
, 'Alert_abonnement_sans_format' => "Votre abonnement est suspendu. Vous ne recevrez pas les courriers des listes de 
	diffusion list&eacute;es ci-dessous. Pour recevoir &#224; nouveau le courrier 
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
, 'abo_1_lettre' => 'Liste de diffusion '
, 'abonnement_seule_liste_dispo' => "Abonnement &#224; la seule liste disponible "
, 'abo_listes' => 'Abonnement'
, 'abonnement_0' => 'Abonnement'
, 'abonnement_titre_mail'=>'Modifier votre abonnement'
, 'votre_abo_listes' => "Votre abonnement aux listes de diffusion"
, 'lire' => 'Lire'
, 'listes_de_diffusion_' => "Listes de diffusion "
, 'jour' => 'jour'
, 'jours' => 'jours'
, 'abonnement_bouton'=>'Modifier votre abonnement'
, 'abonnement_cdt' => "<a href='http://bloog.net/?page=spip-listes'>SPIP-Listes</a>"
, 'abonnement_change_format' => "Vous pouvez changer de format de r&eacute;ception ou vous d&eacute;sabonner : "
, 'abonnement_texte_mail' => 'Indiquez ci-dessous l\'adresse email sous laquelle vous vous &ecirc;tes 
	pr&eacute;c&eacute;demment enregistr&eacute;. 
	Vous recevrez un email permettant d\'acc&eacute;der &#224; la page de modification de votre abonnement.'
, 'article_entier' => 'Lire l\'article entier'
, 'form_forum_identifiants' => 'Confirmation'
, 'form_forum_identifiant_confirm'=>'Votre inscription est enregistr&#233;e. Vous allez recevoir un mail de confirmation.'
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
, 'inscription_mail_forum' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@)'
, 'inscription_mail_redac' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@) 
	et &#224; l\'interface de r&eacute;daction (@adresse_site@/ecrire)'
, 'inscription_visiteurs' => 'L&acute;abonnement vous permet 
	d&acute;intervenir sur les forums r&eacute;serv&eacute;s aux visiteurs enregistr&eacute;s et de recevoir 
	les lettres d&acute;informations.'
, 'inscription_redacteurs' => "L&#39;espace de r&#233;daction de ce site est ouvert aux visiteurs apr&#232;s inscription.
	Une fois enregistr&#233;, vous pourrez consulter les articles en cours de r&#233;daction, proposer des articles
	et participer &#224; tous les forums.  L&#39;inscription permet &#233;galement d&#39;acc&#233;der aux parties du 
	site en acc&#232;s restreint et de recevoir les lettres d&#39;informations."
, 'mail_non' => 'Vous n\'&ecirc;tes pas abonn&eacute; &#224; la lettre d\'information du site @nom_site_spip@'
, 'messages_auto' => 'Courriers automatiques'
, 'nouveaute_intro' => 'Bonjour, <br />Voici les nouveaut&eacute;s publi&eacute;es sur le site'
, 'nom' => 'Nom d\'utilisateur'
, 'texte_lettre_information' => 'Voici la lettre d\'information de '
, 'vous_pouvez_egalement' => 'Vous pouvez &eacute;galement'
, 'vous_inscrire_auteur' => 'vous inscrire en tant qu\'auteur'
, 'voir_discussion' => 'Voir la discussion'
, 'inconnu' => 'n\'est plus abonn&eacute; &#224; la liste'
, 'infos_liste' => 'Informations sur cette liste'
, 'editeur' => 'Editeur : '
, 'html_description' => " Texte enrichi (caract&egrave;res en gras ou en italique, parfois accompagn&eacute; d&#39;images)"
, 'texte_brut' => "Texte brut"
, 'vous_etes_abonne_aux_listes_' => "Vous &ecirc;tes abonn&eacute; aux listes de diffusion :"
, 'vous_etes_abonne_a_la_liste_' => "Vous &ecirc;tes abonn&eacute; &#224; la liste de diffusion :"

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
, 'Listes_diffusion_publiques_desc' => "L'abonnement &#224; ces listes est propos&eacute; sur le site public."
, 'Liste_annuelle' => "Liste annuelle"
, 'Publiques_annuelles' => "Publiques, annuelles"
, 'Listes_diffusion_annuelle' => "Listes publiques annuelles"
, 'Listes_diffusion_publique' => 'Listes de diffusion publiques'
, 'Listes_diffusion_privees' => 'Listes de diffusion priv&eacute;es'
, 'Listes_diffusion_privees_desc' => "L'abonnement &#224; ces listes est réserv&eacute;e aux administrateurs et auteurs du site."
, 'Listes_diffusion_suspendue' => 'Listes de diffusion suspendues'
, 'Listes_diffusion_suspendue_desc' => " "
, 'Courriers_en_cours_de_redaction' => 'Courriers en cours de r&eacute;daction'
, 'Courriers_en_cours_denvoi' => 'Courriers en cours d\'envoi'
, 'Courriers_prets_a_etre_envoye' => "Courriers pr&ecirc;ts &#224; &ecirc;tre envoy&eacute;s"
, 'Courriers_publies' => "Courriers publi&eacute;s"
, 'Courriers_auto_publies' => "Courriers automatiques publi&eacute;s"
, 'Courriers_stope' => "Courriers stopp&eacute;s en cours d'envoi"
, 'Courriers_vides' => "Courriers annul&eacute;s (vides)"
, 'Courriers_sans_destinataire' => "Courriers sans destinataire (liste vide)"
, 'Courriers_sans_liste' => "Courriers sans abonn&eacute;s (liste manquante)"
, 'devenir_redac'=>'Devenir r&eacute;dacteur pour ce site'
, 'devenir_membre'=>'Devenir membre du site'
, 'devenir_abonne' => "Vous inscrire sur ce site"
, 'desabonnement_valid'=>'L\'adresse suivante n\'est plus abonn&eacute;e &#224; la lettre d\'information' 
, 'pass_recevoir_mail'=>'Vous allez recevoir un email vous indiquant comment modifier votre abonnement. '
, 'discussion_intro' => 'Bonjour, <br />Voici les discussions d&eacute;marr&eacute;es sur le site'
, 'En_redaction' => "En r&eacute;daction"
, 'En_cours' => "En cours"
, 'editeur_nom' => "Nom de l'&eacute;diteur "
, 'editeur_adresse' => "Adresse "
, 'editeur_rcs' => "N&deg; RCS "
, 'editeur_siret' => "N&deg; SIRET "
, 'editeur_url' => "URL du site de l'&eacute;diteur "
, 'editeur_logo' => "URL du logotype de l'&eacute;diteur "
, 'Envoi_abandonne' => "Envoi abandonn&eacute;"
, 'Liste_prive' => "Liste priv&eacute;e"
, 'Liste_publique' => "Liste publique"
, 'message_redac' => 'En cours de r&eacute;daction et pr&ecirc;t &#224; l\'envoi'
, 'Prets_a_envoi' => "Pr&ecirc;ts &#224; l'envoi"
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
, 'Nouvelle_liste_de_diffusion' => 'Cr&eacute;er une nouvelle liste de diffusion'
, 'trieuse_suspendue' => "Trieuse suspendue"
, 'trieuse_suspendue_info' => "Le traitement des listes de diffusion programm&eacute;es est suspendu."
, 'Trieuse_reactivee' => "Trieuse r&eacute;activ&eacute;e"

// mots
, 'ajout' => "Ajout"
, 'aucun' => "aucun"
, 'Configuration' => 'Configuration'
, 'courriers' => 'Courriers'
, 'creation' => "Cr&#233;ation"
, '_de_' => " de "
, 'email' => 'E-mail'
, 'format' => 'Format'
, 'modifier' => 'Modifier'
, 'max_' => "Max "
, 'Patrons' => 'Patrons'
, 'patron_' => "Patron : "
, 'spiplistes' => "SPIP-Listes"
, 'recherche' => 'Rechercher'
, 'retablir' => "R&eacute;tablir"
, 'site' => 'Site web'
, 'sujets' => 'Sujets'
, 'sup_' => "Sup."
, 'total' => "Total "
, 'voir' => 'voir'
, 'Vides' => "Vides"
, 'choisir' => 'Choisir'
, 'desabo' => 'd&eacute;sabo'
, 'desabonnement' => 'D&eacute;sabonnement&nbsp;'
, 'desabonnes' => 'D&eacute;sabonn&eacute;s'
, 'destinataire' => 'destinataire'
, 'destinataires' => 'Destinataires'
, 'erreur' => 'Erreur'
, 'html' => 'HTML'
, 'retour_link' => 'Retour'
, 'texte' => 'Texte'
, 'version' => 'version'
, 'fichier_' => "Fichier "

, 'jquery_inactif' => "jQuery non d&#233;tect&#233;. Merci de l&#39;activer."

///////
// a priori, pas|plus utilise'
, 'supprime_contact_base' => 'Supprimer d&eacute;finitivement de la base'
, 'forcer_lot' => 'Provoquer l\'envoi du lot suivant'
, 'erreur_destinataire' => 'Erreur destinataire : pas d\'envoi'
, 'contacts_lot' => 'Contacts de ce lot'
, 'envoi_fini' => 'Envois termin&eacute;s'
, 'non_courrier' => 'Pas / plus de courrier &#224; envoyer'
, 'non_html' => 'Votre logiciel de messagerie ne peut apparemment pas afficher correctement la version graphique (HTML) de cet e-mail'
, 'envoi_erreur' => 'Erreur : SPIP-Listes ne trouve pas de destinataire pour ce courrier'
, 'email_reponse' => 'Email de r&eacute;ponse : '
, 'envoi_listes' => 'Envoi &#224; destination des abonn&eacute;s &#224; la liste : '
, 'confirmer' => 'Confirmer'
, 'listes_emails' => 'Lettres d\'information'
, 'info_liste_1' => 'liste'
, 'bonjour' => 'Bonjour,' // deja dans SPIP
, 'envoi_tous' => 'Envoi &#224; destination de tous les inscrits'
, 'patron_detecte' => '<p><strong>Patron d&eacute;tect&eacute; pour la version texte</strong><p>'
, 'val_texte' => 'Texte'
, 'membres_sans_messages_connecte' => 'Vous n\'avez pas de nouveaux messages'
, 'messages_derniers' => 'Derniers Messages'
, 'pas_abonne_en_ce_moment' => "n'est pas abonn&eacute;"
, 'reinitialiser' => 'reinitialiser'
, 'mail_a_envoyer' => 'Nombre de mails &#224; envoyer : '
, 'lettre_d_information' => 'Lettre d\'information'
, 'desole' => 'D&eacute;sol&eacute;'
, 'Historique_des_envois' => 'Historique des envois'
, 'patron_disponibles' => 'Patrons disponibles'
, 'liste_diff_publiques' => 'Listes de diffusion publiques<br /><i>La page du site public propose l\'inscription &#224; ces listes.</i>'
, 'messages_non_lus_grand' => 'Pas de nouveaux messages'
, 'messages_repondre' => 'Nouvelle R&eacute;ponse'
, 'Liste_abandonnee' => "Liste abandonn&eacute;e"
, 'par_date' => 'Par date d\'inscription'
, 'info_auto' => 'SPIP-Listes pour spip peut envoyer r&eacute;guli&egrave;rement aux inscrits, l\'annonce des derni&egrave;res nouveaut&eacute;s du site (articles et br&egrave;ves r&eacute;cemment publi&eacute;s).'
, 'format2' => 'Format :'
, 'liste_des_abonnes' => "Liste des abonn&eacute;s"
, 'lieu' => 'Localisation'
, 'efface_base' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base'
, 'lot_suivant' => 'Provoquer l\'envoi du lot suivant'
, 'listes_internes' => 'Listes de diffusion internes<br /><i>Au moment de l\'envoi d\'un courrier, ces listes sont propos&eacute;es parmi les destinataires</i>'
, 'adresses_importees' => "Adresses import&eacute;es"
, 'aff_envoye' => 'Courriers envoy&eacute;s'
, 'abonner' => 's\'abonner'
, 'abonnes_liste_int' => 'Abonn&eacute;s aux listes internes : '
, 'abonnes_liste_pub' => 'Abonn&eacute;s aux listes publiques : '
, 'actualiser' => 'Actualiser'
, 'a_destination_de_' => '&#224; destination de '
, 'aff_lettre_auto' => 'Lettres des nouveaut&eacute;s envoy&eacute;es'
, 'alerte_edit' => 'Le formulaire ci-dessous permet de modifier le texte d\'un courrier. 
	Vous pouvez choisir de commencer par importer un patron pour g&eacute;n&eacute;rer le contenu de votre message.'
, 'alerte_modif' => '<strong>Apr&egrave;s l\'affichage de votre courrier, vous pourrez en modifier le contenu</strong>'
, 'lock' => 'Lock actif : '
, 'Apercu' => "Aper&ccedil;u"
, 'bouton_listes' => 'Lettres d\'information'
, 'bouton_modifier' => 'Modifier ce courrier'
, 'dans_jours' => 'dans'
, 'charger_le_patron' => 'G&eacute;n&eacute;rer le courrier'
, 'choix_defini' => 'Pas de choix d&eacute;fini.\n'
, 'definir_squel_choix' => 'A la r&eacute;daction d\'un nouveau courrier, SPIP-Listes vous permet de charger un patron. 
	En appuyant sur un bouton, vous chargez dans le corps du courrier le contenu d\'un des squelettes du 
	repertoire <strong>/patrons</strong> (situ&eacute; &#224; la racine de votre site Spip). 
	<p><strong>Vous pouvez &eacute;diter et modifier ces squelettes selon vos go&ucirc;ts.</strong></p> 
	<ul><li>Ces squelettes peuvent contenir du code HTML classique</li>
	<li>Ce squelette peut contenir des boucles Spip</li>
	<li>Apr&egrave;s le chargement du patron, vous pourrez re-&eacute;diter le courrier avant envoi (pour ajouter du texte)</li>
	</ul><p>La fonction "charger un patron" permet donc d\'utiliser des gabarits HTML personnalis&eacute;s pour vos courriers 
	ou de cr&eacute;er des lettres d\'information th&eacute;matiques dont le contenu est d&eacute;fini gr&acirc;ce aux boucles Spip.</p>
	<p>Attention : ce squelette ne doit pas contenir de balises body, head ou html mais juste du code HTML ou des boucles Spip.</p>'
, 'definir_squel' => 'Choisir le mod&egrave;le de courrier &#224; pr&eacute;visualiser'
, 'courrier_realise_avec_spiplistes' => "Courrier r&eacute;alis&eacute; avec SPIP-Listes"
, 'definir_squel_texte' => 'Si vous disposez des codes d\'acc&egrave;s au FTP, vous pouvez ajouter des squelettes SPIP dans le r&eacute;pertoire /patrons (&#224; la racine de votre site Spip).'
, 'dernier_envoi'=>'Dernier envoi il y a'
, 'desabonnement_confirm'=>'Vous &ecirc;tes sur le point de r&eacute;silier votre abonnement &#224; la lettre d\'information'
, 'date_depuis'=>'depuis @delai@'
, 'envoi_charset' => 'Charset de l\'envoi'
, 'envoi_nouv' => 'Envoi des nouveaut&eacute;s'
, 'envoi_program' => 'Envoi programm&eacute;'
, 'envoi_smtp' => 'Lors d\'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l\'adresse de l\'envoyeur.'
, 'envoi_texte' => 'Si ce courrier vous convient, vous pouvez l\'envoyer'
, 'email_envoi' => 'Envoi des emails'
, 'envoi' => 'Envoi :'
, 'erreur_install' => '<h3>erreur: spip-listes est mal install&eacute;!</h3>'
, 'erreur_install2' => '<p>V&eacute;rifier les &eacute;tapes d\'installation, notamment si vous avez bien renomm&eacute;<i>mes_options.txt</i> en <i>mes_options.php</i>.</p>'
, 'exporter' => 'Exporter la liste d\'abonn&eacute;s'
, 'Erreur_appel_courrier' => "Erreur lors de l'appel du courrier"
, 'faq' => 'FAQ'
, 'forum' => 'Forum'
, 'ferme' => 'Cette discussion est cl&ocirc;tur&eacute;e'
, 'gestion_du_courrier' => "Gestion du courrier"
, 'info_heberg' => 'Certains h&eacute;bergeurs d&eacute;sactivent l\'envoi automatique de mails depuis leurs serveurs. 
	Dans ce cas, les fonctionnalit&eacute;s suivantes de SPIP-Listes pour SPIP ne fonctionneront pas'
, 'info_nouv' => 'Vous avez activ&eacute; l\'envoi des nouveaut&eacute;s'
, 'info_nouv_texte' => 'Prochain envoi des nouveaut&eacute;s dans @proch@ jours'
, 'log' => 'Logs'
, 'login' => 'Connexion'
, 'logout' => 'D&eacute;connexion'
, 'mail_format' => 'Vous &ecirc;tes abonn&eacute; &#224; la lettre d\'information du site @nom_site_spip@ en format'
, 'messages_auto_texte' => '<p>Par d&eacute;faut, le squelette des nouveaut&eacute;s permet d\'envoyer automatiquement 
	la liste des articles et br&egrave;ves publi&eacute;s sur le site depuis le dernier envoi automatique. </p>
	<p>vous pouvez personnaliser le message en d&eacute;finissant l\'adresse d\'un logo et d\'une image de fond 
	pour les titres de parties en &eacute;ditant le fichier nomm&eacute; <strong>"nouveautes.html"</strong> 
	(situ&eacute; &#224; dans le rep&eacute;rtoire /dist).</p>'
, 'membres_groupes' => 'Groupes d\'utilisateurs'
, 'membres_profil' => 'Profil'
, 'membres_messages_deconnecte' => 'Se connecter pour v&eacute;rifier ses messages priv&eacute;s'
, 'membres_avec_messages_connecte' => 'Vous avez @nombres@ nouveau(x) message(s)'
, 'message' => 'Message : '
, 'message_date' => 'Post&eacute; le '
, 'messages' => 'Courriers'
, 'messages_forum_clos' => 'Forum d&eacute;sactiv&eacute;'
, 'messages_nouveaux' => 'Nouveaux messages'
, 'messages_pas_nouveaux' => 'Pas de nouveaux messages'
, 'messages_voir_dernier' => 'Voir le dernier message'
, 'moderateurs' => "Mod&eacute;rateur(s)"
, 'mis_a_jour' => 'Mis &#224; jour'
, 'nouveaux_messages' => 'Nouveaux messages'
, 'numero' => 'N&nbsp;'
, 'photos' => 'Photos'
, 'poster' => 'Poster un Message'
, 'publie' => 'Publi&eacute; le'
, 'aucune_liste_publique' => "Aucune liste de diffusion publique disponible."
, 'revenir_haut' => 'Revenir en haut de la page'
, 'reponse' => 'En r&eacute;ponse au message'
, 'reponse_plur' => 'r&eacute;ponses'
, 'reponse_sing' => 'r&eacute;ponse'
, 'retour' => 'Adresse email du gestionnaire de la liste (reply-to)'
, 'Suivi_des_abonnements' => 'Suivi des abonnements'
, 'sujet_nouveau' => 'Nouveau sujet'
, 'sujet_auteur' => 'Auteur'
, 'sujet_visites' => 'Visites'
, 'sujet_courrier_auto' => 'Sujet du courrier automatique : '
, 'sujets_aucun' => 'Pas de sujet dans ce forum pour l\'instant'
, 'sujet_clos_titre' => 'Sujet Clos'
, 'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.'
, 'masquer_le_journal_SPIPLISTES' => "Masquer le journal de SPIP-Listes"
, 'abon' => 'LES ABONNES'
, 'abonees' => 'tous les abonn&eacute;s'
, 'abonnement_newsletter' => '<strong>Abonnement &#224; la lettre d\'information</strong>'
, 'acces_a_la_page' => 'Vous n\'avez pas acc&egrave;s &#224; cette page.'
, 'adresse_deja_inclus' => 'Adresse d&eacute;j&#224; connue'
, 'Choisir_cette_liste' => 'Choisir cette liste'
, 'Charger_un_patron' => "Charger un patron"
, 'date_ref' => 'Date de r&eacute;f&eacute;rence'
, 'efface' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base'
, 'email_collec' => 'R&eacute;diger un courrier'
, 'email_test_liste' => 'Envoyer vers une liste de diffusion'
, 'envoyer' => 'envoyer le mail'
, 'envoyer_a' => 'Envoi vers '
, 'listes_poubelle' => 'Vos listes de diffusion &#224; la poubelle'
, 'Liste_numero_:' => 'Liste num&eacute;ro :'
, 'mail_tache_courante' => 'Mails envoy&eacute;s pour la t&acirc;che courante : '
, 'messages_auto_envoye' => 'Courriers automatiques envoy&eacute;s'
, 'nb_abonnes' => 'Dans les listes : '
, 'nb_inscrits' => 'Dans le site :  '
, 'nb_listes' => 'Incriptions dans toutes les listes : '
, 'nouvelle_abonne' => 'L\'abonn&eacute; suivant a &eacute;t&eacute; ajout&eacute; la liste'
, 'pas_acces' => 'Vous n\'avez pas acc&egrave;s &#224; cette page.'
, 'plus_abonne' => ' n\'est plus abonn&eacute; &#224; la liste '
, 'prochain_envoi_aujd' => 'Prochain envoi pr&eacute;vu aujourd\'hui'
, 'prochain_envoi_prevu_dans' => 'Prochain envoi pr&eacute;vu dans '
, 'program' => 'Programmation des courriers automatiques'
, 'plein_ecran' => "(Plein &eacute;cran)"
, 'remplir_tout' => 'Tous les champs doivent &ecirc;tre remplis'
, 'repartition' => 'R&eacute;partition'
, 'squel' => 'Patron : &nbsp;'
, 'suivi_envois' => 'Suivi des envois'
, 'supprime_contact' => 'Supprimer ce contact d&eacute;finitivement'
, 'tableau_bord' => 'Tableau de bord'
, 'toutes' => 'Tous les inscrits'
, 'acces_refuse' => 'Vous n\'avez plus acc&egrave;s &#224; ce site'
, 'confirmation_format' => ' en format '
, 'confirmation_liste_unique_1' => 'Vous &ecirc;tes abonn&eacute; &agrave la liste d\'information du site'
, 'confirmation_liste_unique_2' =>'Vous avez choisi de recevoir les courriers adress&eacute;s &agrave la liste suivante :'
, 'confirmation_listes_multiples_1' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site '
, 'confirmation_listes_multiples_2' => 'Vous avez choisi de recevoir les courriers adress&eacute;s aux listes suivantes :'
, 'contacts' => 'Nombre de contacts'
, 'patron_erreur' => 'Le patron sp&eacute;cifi&eacute; ne donne pas de r&eacute;sulat avec les param&egrave;tres choisis'
, 'abonees_titre' => 'Abonn&eacute;s'
, 'options' => 'radio|brut|Format :|Html,Texte,D&eacute;sabonnement|html,texte,non'

);

?>