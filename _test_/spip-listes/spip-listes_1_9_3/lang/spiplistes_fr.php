<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_fr'] = array(


//_ 
'_aide' => '<p>SPIP-Listes permet d\'envoyer un courrier ou des courriers automatiques &agrave; des abonn&eacute;s.</p> <p>Vous pouvez &eacute;crire un texte simple, composer votre courrier en HTML ou appliquer un "patron" &agrave; votre courrier</p>
<p>Via un formulaire d\'inscription public, les abonn&eacute;s d&eacute;finissent eux-m&ecirc;mes leur statut d\'abonnement, les listes auxquelles ils s\'abonnent et le format
dans lequel ils souhaitent recevoir les courriers (HTML/texte). </p><p>Tout courrier sera traduit automatiquement en format texte pour les abonn&eacute;s qui en ont fait la demande.</p><p><strong>Note :</strong><br />L\'envoi des mails peut prendre quelques minutes : les lots partent peu &agrave; peu quand les utilisateurs parcourent le site public. Vous pouvez aussi provoquer manuellement l\'envoi des lots en cliquant sur le lien "suivi des envois" pendant un envoi.</p>',
'_aide_import' => "Vous pouvez importer ici une liste d'abonn&eacute;s &agrave; partir de votre 
   ordinateur.<br />
Cette liste d'abonn&eacute;s doit &ecirc;tre au format texte seul, une ligne 
   par abonn&eacute;. Chaque ligne doit &ecirc;tre compos&eacute;e ainsi :<br />
<tt style='display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;'>adresse@mail<span style='color:#f66'>[separateur]</span>login<span style='color:#f66'>[separateur]</span>nom</tt>
<tt style='color:#f66'>[separateur]</tt> est un caract&egrave;re de tabulation ou un point-virgule.<br /><br />
L'adresse email doit &ecirc;tre unique, ainsi que le login. Si cette adresse 
   email ou se login existe dans la base du site, la ligne sera rejet&eacute;e.<br />
Le premier champ adresse@mail est obligatoire. Les deux autres champs peuvent 
   &ecirc;tre ignor&eacute;s (vous pouvez importer des listes issues des anciennes versions de SPIP-Listes)."
, 'aide_en_ligne' => "Aide en ligne"

//A
, 'abo_1_lettre' => 'Lettre d\'information',
'abonne_une_seule_liste_publique' => "Abonnement &agrave; la seule liste disponible ",
'abonnement_0' => 'Abonnement',
'abonnement'=>'Vous souhaitez modifier votre abonnement &agrave; la lettre d\'information',
'abonnement_bouton'=>'Modifier votre abonnement',
'abonnement_cdt' => '<a href=\'http://bloog.net/spip-listes/\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'Vous pouvez changer de format de r&eacute;ception ou vous d&eacute;sabonner : '
, 'abonnement_mail_text' => 'Pour modifier votre abonnement, veuillez vous rendre &agrave; l\'adresse suivante : '
, 'abonnement_mail_passcookie' => "(ceci est un message automatique)
	Pour modifier votre abonnement &agrave; la lettre d\'information de ce site :
	@nom_site_spip@ (@adresse_site@)
	
	Veuillez vous rendre &agrave; l\'adresse suivante :
	
	@adresse_site@/spip.php?page=abonnement&d=@cookie@
	
	Vous pourrez alors confirmer la modification de votre abonnement.",
'abonnement_modifie'=>'Vos modifications sont prises en compte',
'abonnement_nouveau_format'=>'Votre format de r&eacute;ception est d&eacute;sormais : ',
'abonnement_titre_mail'=>'Modifier votre abonnement',
'abonnement_texte_mail'=>'Indiquez ci-dessous l\'adresse email sous laquelle vous vous &ecirc;tes pr&eacute;c&eacute;demment enregistr&eacute;. 
	Vous recevrez un email permettant d\'acc&eacute;der &agrave; la page de modification de votre abonnement.',
'abonner' => 's\'abonner',
'abonnes_liste_int' => 'Abonn&eacute;s aux listes internes : ',
'abonnes_liste_pub' => 'Abonn&eacute;s aux listes publiques : ',
'actualiser' => 'Actualiser',
'a_destination' => '&agrave; destination de ',
'adresse' => 'Indiquez ici l\'adresse &agrave; utiliser pour les r&eacute;ponses de mails 
	(&agrave; d&eacute;faut, l\'adresse du webmestre sera utilis&eacute;e comme adresse de r&eacute;ponse) :',
'adresse_envoi' => 'Adresse d\'envoi par d&eacute;faut',
'adresses_importees' => 'Adresses import&eacute;es',
'adresse_smtp' => 'adresse email du <i>sender</i> SMTP',
'aff_redac' => 'Courriers en cours de r&eacute;daction',
'aff_encours' => 'Courriers en cours d\'envoi',
'aff_envoye' => 'Courriers envoy&eacute;s',
'aff_lettre_auto' => 'Lettres des nouveaut&eacute;s envoy&eacute;es',
'alerte_edit' => 'Le formulaire ci-dessous permet de modifier le texte d\'un courrier. Vous pouvez choisir de commencer par importer un patron pour g&eacute;n&eacute;rer le contenu de votre message.',
'alerte_modif' => '<strong>Apr&egrave;s l\'affichage de votre courrier, vous pourrez en modifier le contenu</strong>',
'annuler_envoi' => 'Annuler l\'envoi',
'article_entier' => 'Lire l\'article entier',
'abonnes_titre' => 'Abonn&eacute;s',
'adresse_de_reponse' => "Adresse de r&eacute;ponse",
'adresse_mail_retour' => 'Adresse email du gestionnaire de la liste (reply-to)',
'Attention_action_retire_invites' => "Attention: cette action retire les invit&eacute;s de la liste des abonn&eacute;s.",
'A_partir_de' => "&Agrave; partir de",
'aucun' => "aucun",
'Apercu_plein_ecran' => "Aper&ccedil;u plein &eacute;cran dans une nouvelle fen&ecirc;tre",
'Apercu' => "Aper&ccedil;u",
'generer_Apercu' => "G&eacute;n&eacute;rer et Aper&ccedil;u",
'aller_au_panier_' => "Aller au panier ",
'aller_aux_listes_' => "Aller aux listes "
, 'annulation_chrono_' => "Annulation du chrono pour "
, 'Abonner_format_html' => "Abonner au format HTML",
'Abonner_format_texte' => "Abonner au format texte",
'Attention_suppression_liste' => "Attention ! Vous demandez la suppression d'une liste de diffusion. Les abonn&eacute;s 
seront retir&eacute;s de cette liste de diffusion automatiquement. ",
'Abonner_tous_les_invites_public' => "Abonner tous les membres invit&eacute;s &agrave; cette liste publique.",
'Abonner_tous_les_inscrits_prives' => "Abonner tous les membres &agrave; cette liste priv&eacute;e, sauf les invit&eacute;s.",
'Adresse_email_obligatoire' => "Une adresse email est obligatoire pour pouvoir vous abonner aux listes de diffusion. 
	Si vous d&eacute;sirez profiter de ce service, merci de modifier votre fiche en compl&eacute;tant ce champ. ",
'Alert_abonnement_sans_format' => "Votre abonnement est suspendu. Vous ne recevrez pas les courriers des listes de 
	diffusion list&eacute;es ci-dessous. Pour recevoir &agrave; nouveau le courrier 
	de vos listes pr&eacute;f&eacute;r&eacute;es, choisissez un format de r&eacute;ception 
	et validez ce formulaire. "
, 'adresse_mail_obligatoire' => "Adresse mail manquante. Abonnement impossible."
, 'abonne_sans_format' => "Ce compte est actuellement d&eacute;sabonn&eacute;. Aucun format de courrier n'est 
	d&eacute;fini. Il ne peut pas recevoir de courrier. D&eacute;finissez un format 
	de r&eacute;ception pour ce compte afin de valider son abonnement."
, 'msg_abonne_sans_format' => "format de reception manquant"

//B

, 'bouton_listes' => 'Lettres d\'information'
, 'bouton_modifier' => 'Modifier ce courrier'
, 'abonnements_aux_courriers' => "Abonnements aux courriers",

//C
'calcul_patron' => 'Calcul avec le patron version texte',
'calcul_html' => 'Calcul depuis la version HTML du message',
'Cette_liste_est' => 'Cette liste est',
'charger_patron' => 'Choisir un patron pour le courrier',
'charger_le_patron' => 'G&eacute;n&eacute;rer le courrier',
'choix_defini' => 'Pas de choix d&eacute;finis.\n'
, 'Configuration' => 'Configuration'
, 'Cliquez_ici_pour_modifier_votre_abonnement' => "Cliquez ici pour modifier votre abonnement"
, 'courriers' => 'Courriers',
'chercher_un_auteur' => "Chercher un auteur",
'Courrier_numero_:' => "Courrier num&eacute;ro :",
'Creer_un_courrier_:' => "Cr&eacute;er un courrier :",
'Creer_une_liste_' => "Cr&eacute;er une liste ",
'Choix_non_defini' => 'Pas de choix d&eacute;fini.',
'Complement_des_courriers' => "Compl&eacute;ment des courriers",
'Complement_lien_en_tete' => "Lien sur le courrier", 
'Complement_ajouter_lien_en_tete' => "Ajouter un lien en en-t&ecirc;te du courrier", 
'Complement_lien_en_tete_desc' => "Cette option vous permet de rajouter en t&ecirc;te du courrier HTML envoy&eacute; le lien 
   du courrier original pr&eacute;sent sur votre site.",
'Complement_tampon_editeur' => "Ajouter le tampon Editeur",
'Complement_tampon_editeur_desc' => "Cette option vous permet de rajouter le tampon de l'&eacute;diteur en fin de courrier. ",
'Complement_tampon_editeur_label' => "Ajouter le tampon Editeur en fin de courrier"
, 'Casier_a_courriers' => "Casier &agrave; courriers"
, 'Courriers_en_cours_de_redaction' => 'Courriers en cours de r&eacute;daction',
'Courriers_prets_a_etre_envoye' => "Courriers pr&ecirc;ts &agrave; &ecirc;tre envoy&eacute;s"
, 'Courriers_publies' => "Courriers publi&eacute;s"
, 'Courriers_auto_publies' => "Courriers automatiques publi&eacute;s",
'Courriers_stope' => "Courriers stopp&eacute;s en cours d'envoi",
'Courriers_vides' => "Courriers annul&eacute;s (vides)",
'Courriers_sans_destinataire' => "Courriers sans destinataire (liste vide)",
'Courriers_sans_liste' => "Courriers sans abonn&eacute;s (liste manquante)",
'Ce_courrier_ne_sera_envoye_qu_une_fois' => "Ce courrier ne sera envoy&eacute; qu'une fois.",
'conseil_sauvegarder_avant' => "<strong>Conseil</strong>: faire une sauvegarde de la base avant de confirmer la suppression 
   @objet@. L'annulation est impossible ici.",
'Choisir_un_patron' => "Choisir un patron ",
'Courrier_edit_desc' => 'Vous pouvez choisir de g&eacute;n&eacute;rer automatiquement le contenu du courrier
	ou r&eacute;diger simplement votre courrier dans la bo&icirc;te <strong>texte du courrier</strong>.',
'Contenu_a_partir_de_date_' => "Contenu &agrave; partir de cette date ",
'Cliquez_Generer_desc' => "Cliquez sur <strong>@titre_bouton@</strong> pour injecter le r&eacute;sultat 
	dans la bo&icirc;te @titre_champ_texte@.",
'courrier_realise_avec_spiplistes' => "Courrier r�alis� avec SPIP-Listes",
'Confirmer_la_suppression_de_la_liste' => "Confirmer la suppression de la liste ",
'Confirmez_requete' => "Veuillez confirmer la requ&ecirc;te.",
'Confirmez_envoi_liste' => "Vous avez demand&eacute; l'envoi imm&eacute;diat de cette liste 
	de diffusion.<br />
	Svp, veuillez confirmer votre demande."
, 'confirmer_supprimer_formats' => "Supprimer les formats de r&eacute;ception des abonn&eacute;s."


//D
, 'des_formats' => "des formats"
, 'des_listes' => "des listes"
, '_de_' => " de "
, 'dans_jours' => 'dans'
, '_dont_' => " dont "
, 'definir_squel' => 'Choisir le mod&egrave;le de courrier &agrave; pr&eacute;visualiser',
'definir_squel_choix' => 'A la r&eacute;daction d\'un nouveau courrier, SPIP-Listes vous permet de charger un patron. En appuyant sur un bouton, vous chargez dans le corps du courrier le contenu d\'un des squelettes du repertoire <strong>/patrons</strong> (situ&eacute; &agrave; la racine de votre site Spip). <p><strong>Vous pouvez &eacute;diter et modifier ces squelettes selon vos go&ucirc;ts.</strong></p> <ul><li>Ces squelettes peuvent contenir du code HTML classique</li>
	<li>Ce squelette peut contenir des boucles Spip</li>
	<li>Apr&egrave;s le chargement du patron, vous pourrez re-&eacute;diter le courrier avant envoi (pour ajouter du texte)</li>
	</ul><p>La fonction "charger un patron" permet donc d\'utiliser des gabarits HTML personnalis&eacute;s pour vos courriers ou de cr&eacute;er des lettres d\'information th&eacute;matiques dont le contenu est d&eacute;fini gr&acirc;ce aux boucles Spip.</p><p>Attention : ce squelette ne doit pas contenir de balises body, head ou html mais juste du code HTML ou des boucles Spip.</p>',
'definir_squel_texte' => 'Si vous disposez des codes d\'acc&egrave;s au FTP, vous pouvez ajouter des squelettes SPIP dans le r&eacute;pertoire /patrons (&agrave; la racine de votre site Spip).',
'dernier_envoi'=>'Dernier envoi il y a',
'devenir_redac'=>'devenir r&eacute;dacteur pour ce site',
'devenir_abonne'=>'Vous inscrire sur ce site',
'desabonnement_valid'=>'L\'adresse suivante n\'est plus abonn&eacute;e &agrave; la lettre d\'information' ,
'pass_recevoir_mail'=>'Vous allez recevoir un email vous indiquant comment modifier votre abonnement. ',
'desabonnement_confirm'=>'Vous &ecirc;tes sur le point de r&eacute;silier votre abonnement &agrave; la lettre d\'information',
'date_depuis'=>'depuis @delai@', 
'discussion_intro' => 'Bonjour, <br />Voici les discussions d&eacute;marr&eacute;es sur le site',
'Destination' => "Destination",
'Date_non_precisee' => "Date non pr&eacute;cis&eacute;e",
'Dernier_envoi_le_:' => "Dernier envoi le :",
'Pas_de_donnees' => "D&eacute;sol&eacute;, mais l'enregistrement demand&eacute; n'existe pas dans la base de donn&eacute;es.",
'Desabonner_temporaire' => "D&eacute;sabonner temporairement ce compte.",
'Desabonner_definitif' => "D&eacute;sabonner ce compte de toutes les listes de diffusion.",
'Desabonner' => "D&eacute;sabonner"
, 'dupliquer_ce_courrier' => "Dupliquer ce courrier"
, 'desabonne_sing' => ' d&eacute;sabonn&eacute;'
, 'desabonnes_plur' => ' d&eacute;sabonn&eacute;s'

//E
, 'email' => 'E-mail',
'email_envoi' => 'Envoi des emails',
'envoi' => 'Envoi :',
'envoi_charset' => 'Charset de l\'envoi',
'envoi_date' => 'Date de l\'envoi : ',
'envoi_debut' => 'Debut de l\'envoi : ',
'envoi_fin' => 'Fin de l\'envoi : ',
'envoi_nouv' => 'Envoi des nouveaut&eacute;s',
'envoi_patron' => 'Envoi du patron',
'envoi_program' => 'Envoi programm&eacute;',
'envoi_smtp' => 'Lors d\'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l\'adresse de l\'envoyeur.',
'envoi_texte' => 'Si ce courrier vous convient, vous pouvez l\'envoyer',
'erreur_envoi' => 'Nombre d\'envois en erreur : ',
'erreur_install' => '<h3>erreur: spip-listes est mal install&eacute;!</h3>',
'erreur_install2' => '<p>V&eacute;rifier les &eacute;tapes d\'installation, notamment si vous avez bien renomm&eacute;<i>mes_options.txt</i> en <i>mes_options.php</i>.</p>',
'exporter' => 'Exporter la liste d\'abonn&eacute;s',
'Erreur_liste_vide' => "Erreur: cette liste n'a pas d'abonn&eacute;s.",
'Erreur_courrier_introuvable' => "Erreur: ce courrier n'existe pas.",
'Envoi_des_courriers' => "Envoi des courriers",
'Envoyer_ce_courrier' => "Envoyer ce courrier",
'Exporter_une_liste_d_abonnes' => "Exporter une liste d'abonn&eacute;s",
'Exporter_une_liste_de_non_abonnes' => "Exporter une liste de non abonn&eacute;s",
'En_redaction' => "En r&eacute;daction",
'En_cours' => "En cours",
'Envoi_abandonne' => "Envoi abandonn&eacute;",
'Erreur_appel_courrier' => "Erreur lors de l'appel du courrier",
'Lister_articles_de_rubrique' => "Et lister les articles de la rubrique ",
'Lister_articles_mot_cle' => "Et lister les articles du mot-cl&eacute; ",
'editeur_nom' => "Nom de l'&eacute;diteur ",
'editeur_adresse' => "Adresse ",
'editeur_rcs' => "N&deg; RCS ",
'editeur_siret' => "N&deg; SIRET ",
'editeur_url' => "URL du site de l'&eacute;diteur ",
'editeur_logo' => "URL du logotype de l'&eacute;diteur "
, 'en_debut_de_semaine' => "en d&eacute;but de semaine"
, 'en_debut_de_mois' => "en d&eacute;but de mois"
, 'envoi_non_programme' => "Envoi non programm&eacute;"
, 'editer_fiche_abonne' => "Editer la fiche de l'abonn&eacute;"
,

//F
'faq' => 'FAQ',
'forum' => 'Forum',
'ferme' => 'Cette discussion est cl&ocirc;tur&eacute;e',
'form_forum_identifiants' => 'Confirmation',
'form_forum_identifiant_confirm'=>'Votre abonnement est enregistr&eacute;, vous allez recevoir un mail de confirmation.',
'format' => 'Format',
'format2' => 'Format :',
'format_html' => 'Format html : ',
'format_texte' => 'Format texte : ',
'format_de_reception' => "Format de r&eacute;ception",
'format_de_reception_desc' => "Vous pouvez choisir un format global de r&eacute;ception des courriers pour 
   cet abonn&eacute;.<br /><br />
   Vous pouvez &eacute;galement d&eacute;sabonner temporairement ce contact. 
   Il reste inscrit dans les listes en tant que destinataire, mais les courriers 
   ne lui seront pas envoy&eacute;s tant que vous ne lui aurez pas d&eacute;fini un format de r&eacute;ception de courriers.",
'Forcer_les_abonnement_liste' => "Forcer les abonnement pour cette liste",
'Forcer_abonnement_desc' => "Vous pouvez forcer ici les abonnements &agrave; cette liste, soit pour tous 
   les membres inscrits (visiteurs, auteurs et administrateurs), soit pour tous 
   les visiteurs.",
'Forcer_abonnement_aide' => "<strong>Attention</strong>: un membre abonn&eacute; ne re&ccedil;oit pas forc&eacute;ment 
   le courrier de cette liste. Il faut attendre qu'il confirme lui-m&ecirc;me 
   le format de r&eacute;ception : html ou texte seul.<br />
	Vous pouvez forcer le format par abonn&eacute; <a href='@lien_retour@'>sur la page du suivi des abonnements</a>",
'Forcer_desabonner_tous_les_inscrits' => "D&eacute;sabonner tous les membres inscrits pour cette liste.",
'Forcer_abonnement_erreur' => "Erreur technique signal&eacute;e lors de la modification d'une liste abonn&eacute;e. 
	V&eacute;rifiez cette liste avant de poursuivre votre op&eacute;ration.",
'Format_obligatoire_pour_diffusion' => "Pour confirmer l'abonnement de ce compte, vous devez s&eacute;lectionner un format 
	de r&eacute;ception."
, 'format_reception' => "Format de r&eacute;ception :"

//G
, 'Generer_le_contenu' => "G&eacute;n&eacute;rer le contenu",

//H
'Historique_des_envois' => 'Historique des envois',

//I
'info_auto' => 'SPIP-Listes pour spip peut envoyer r&eacute;guli&egrave;rement aux inscrits, l\'annonce des derni&egrave;res nouveaut&eacute;s du site (articles et br&egrave;ves r&eacute;cemment publi&eacute;s).',
'info_heberg' => 'Certains h&eacute;bergeurs d&eacute;sactivent l\'envoi automatique de mails depuis leurs serveurs. Dans ce cas, les fonctionnalit&eacute;s suivantes de SPIP-Listes pour SPIP ne fonctionneront pas',
'info_nouv' => 'Vous avez activ&eacute; l\'envoi des nouveaut&eacute;s',
'info_nouv_texte' => 'Prochain envoi des nouveaut&eacute;s dans @proch@ jours',
'inscription_mail_forum' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@) et &agrave; l\'interface de r&eacute;daction (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'L&acute;abonnement vous permet d&acute;acc&eacute;der aux parties du site en acc&egrave;s restreint,
	d&acute;intervenir sur les forums r&eacute;serv&eacute;s aux visiteurs enregistr&eacute;s et de recevoir les lettres d&acute;informations.'
, 'inscription_redacteurs' =>'L\'espace de r&eacute;daction de ce site est ouvert aux visiteurs apr&egrave;s inscription.
	Une fois enregistr&eacute;, vous pourrez consulter les articles en cours de r&eacute;daction, proposer des articles
	et participer &agrave; tous les forums.  L\'inscription permet &eacute;galement d\'acc&eacute;der aux parties du site en acc&egrave;s restreint
	et de recevoir les lettres d\'informations.'
, 'import_export' => 'Import / Export'
, 'introduction_du_courrier_' => "Introduction &agrave; votre courrier, avant le contenu issu du site ",

//J
'jour' => 'jour',
'jours' => 'jours',

//L
'langue' => '<strong>Langue :</strong>&nbsp;',
'Langue_du_courrier_' => "Langue du courrier ",
'lire' => 'Lire',
'listes_de_diffusion_' => "Listes de diffusion ",
'log' => 'Logs',
'login' => 'Connexion',
'logout' => 'D&eacute;connexion',
'lot_suivant' => 'Provoquer l\'envoi du lot suivant',
'lieu' => 'Localisation',
'lettre_d_information' => 'Lettre d\'information',
'liste_des_abonnes' => "Liste des abonn&eacute;s",
'Liste_de_destination' => "Liste de destination",
'lien_trier_nombre' => "Trier par nombre d&acute;abonnements",
'Liste_prive' => "Liste priv&eacute;e",
'Liste_publique' => "Liste publique",
'Liste_abandonnee' => "Liste abandonn&eacute;e",
'Liste_diffusee_le_premier_de_chaque_mois' => "Liste diffus&eacute;e le premier de chaque mois. "
, 'log_console' => "Console"
, 'log_details_console' => "D&eacute;tails de la console"
, 'log_voir_destinataire' => "Lister les adresses email des destinataires dans la console lors de l'envoi."
, 'log_console_syslog_desc' => "Vous &ecirc;tes sur un r&eacute;seau local (@IP_LAN@). Si besoin, vous pouvez activer la console sur syslog au lieu des journaux SPIP (conseill&eacute; sous unix)."
, 'log_console_syslog_texte' => "Activer les journaux syst&egrave;mes (renvoi sur syslog)"
, 'log_console_syslog' => "Console syslog"
, 'log_voir_les_journaux' => "Voir les journaux SPIPLISTES"


//M
, 'mail_format' => 'Vous &ecirc;tes abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@ en format',
'mail_non' => 'Vous n\'&ecirc;tes pas abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@',
'maintenance_objet' => "Maintenance @objet@",
'message_arch' => 'Courrier archiv&eacute;',
'messages_auto' => 'Courriers automatiques',
'messages_auto_texte' => '<p>Par d&eacute;faut, le squelette des nouveaut&eacute;s permet d\'envoyer automatiquement la liste des articles et br&egrave;ves publi&eacute;s sur le site depuis le dernier envoi automatique. </p><p>vous pouvez personnaliser le message en d&eacute;finissant l\'adresse d\'un logo et d\'une image de fond pour les titres de parties en &eacute;ditant le fichier nomm&eacute; <strong>"nouveautes.html"</strong> (situ&eacute; &agrave; dans le rep&eacute;rtoire /dist).</p>',
'message_redac' => 'En cours de r&eacute;daction et pr&ecirc;t &agrave; l\'envoi',
'message_en_cours' => 'Courrier en cours d\'envoi',
'message_type' => 'Courrier &eacute;lectronique',
'membres_liste' => 'Liste des Membres',
'membres_groupes' => 'Groupes d\'utilisateurs',
'membres_profil' => 'Profil',
'membres_messages_deconnecte' => 'Se connecter pour v&eacute;rifier ses messages priv&eacute;s',
'membres_sans_messages_connecte' => 'Vous n\'avez pas de nouveaux messages',
'membres_avec_messages_connecte' => 'Vous avez @nombres@ nouveau(x) message(s)',
'message' => 'Message : ',
'message_date' => 'Post&eacute; le ',
'message_sujet' => 'Sujet ',
'messages' => 'Courriers',
'Messages_automatiques' => 'Courriers automatiques programm&eacute;s',
'messages_derniers' => 'Derniers Messages',
'messages_forum_clos' => 'Forum d&eacute;sactiv&eacute;',
'messages_nouveaux' => 'Nouveaux messages',
'messages_pas_nouveaux' => 'Pas de nouveaux messages',
'messages_non_lus_grand' => 'Pas de nouveaux messages',
'messages_repondre' => 'Nouvelle R&eacute;ponse',
'messages_voir_dernier' => 'Voir le dernier message',
'methode_envoi' => 'M&eacute;thode d\'envoi'
, 'mettre_a_jour' => '<h3>SPIP-listes va mettre &agrave; jour</h3>'
, 'moderateurs' => "Mod&eacute;rateur(s)"
, 'mods_cette_liste' => "Les mod&eacute;rateurs de cette liste"
, 'modifier' => 'Modifier',
'mis_a_jour' => 'Mis &agrave; jour',
'Modifier_un_courrier_:' => "Modifier un courrier :",
'Modifier_ce_courrier' => "Modifier ce courrier",
'mode_simulation' => "Mode simulation",
'mode_simulation_info' => "Le mode simulation est activ&eacute;. La m&eacute;leuse fait semblant d'envoyer le courrier. 
	En r&eacute;alit&eacute;, aucun courrier n'est exp&eacute;di&eacute;."
, 'mode_suspendre_trieuse' => "Suspendre le traitement des envois des listes de diffusion",
'Suspendre_le_tri_des_listes' => "Cette option vous permet - en cas d'engorgement - de suspendre le traitement des 
	listes de diffusion programm&eacute;es et de red&eacute;finir les param&egrave;tres 
	d'envoi. D&eacute;sactivez ensuite cette option pour reprendre le traitement des 
	listes de diffusion programm&eacute;es.",
'mode_suspendre_meleuse' => "Suspendre l'envoi des courriers",
'suspendre_lenvoi_des_courriers' => "Cette option vous permet - en cas d'engorgement 
	- d'annuler l'envoi des courriers. D&eacute;sactivez ensuite cette option pour 
	reprendre les exp&eacute;ditions en cours. ",
'meleuse_suspendue' => "Meleuse suspendue",
'meleuse_suspendue_info' => "L'envoi des courriers en attente d'exp&eacute;dition est suspendu.",
'Meleuse_reactivee' => "M&egrave;leuse r&eacute;activ&eacute;e"

//n
, 'nb_abonnes_sing' => " abonn&eacute;"
, 'nb_abonnes_plur' => " abonn&eacute;s"
, 'nb_moderateur_sing' => " mod&eacute;rateur"
, 'nb_moderateur_plur' => " mod&eacute;rateurs"
, 'nb_destinataire_sing' => " destinataire"
, 'nb_destinataire_plur' => " destinataires"
, 'nbre_abonnes' => "Nombre d\'abonn&eacute;s : "
, 'nbre_mods' => "Nombre de mod&eacute;rateurs : "
, 'nom' => 'Nom d\'utilisateur',
'nombre_lot' => 'Nombre d\'envois par lot',
'Nouveau_courrier' => 'Cr&eacute;er un nouveau courrier',
'nouveaute_intro' => 'Bonjour, <br />Voici les nouveaut&eacute;s publi&eacute;es sur le site',
'nouveaux_messages' => 'Nouveaux messages',
'Nouvelle_liste_de_diffusion' => 'Cr&eacute;er une nouvelle liste de diffusion',
'numero' => 'N&nbsp;',
'nb_abos' => "qt.",

//P
'pas_abonne_en_ce_moment' => "n'est pas abonn�",
'par_date' => 'Par date d\'inscription',
'patron_disponibles' => 'Patrons disponibles',
'Patrons' => 'Patrons',
'pas_sur' => '<p>Si vous n\'&ecirc;tes pas s&ucirc;r, choisissez la fonction mail de PHP.</p>',
'photos' => 'Photos',
'php_mail' => 'Utiliser la fonction mail() de PHP',
'poster' => 'Poster un Message',
'publie' => 'Publi&eacute; le',
'Pas_de_liste_pour_import' => "Vous devez cr&eacute;er au moins une liste de destination afin de pouvoir importer 
	vos abonn&eacute;s.",
'Periodicite_:' => "P&eacute;riodicit&eacute; : ",
'Prets_a_envoi' => "Pr&ecirc;ts &agrave; l'envoi"
, 'Publies' => "Publi&eacute;s"
, 'publies_auto' => "Publi&eacute;s (auto)"
, 'pas_de_liste' => "Aucune liste de type envoi non programm&eacute;."
, 'pas_de_format' => "Aucun format de r&eacute;ception d&eacute;fini pour les abonn&eacute;s."
, 'aucune_liste_dispo' => "Aucune liste disponible."
, 'aucune_liste_publique' => "Aucune liste de diffusion publique disponible."
, 'pas_de_liste_en_auto' => "Aucune liste de type envoi programm&eacute; (chrono)."
, 'Patron_du_tampon' => "Patron du tampon ",
'Patron_de_pied_' => "Patron de pied ",
'Patron_grand_' => "Grand patron ",
'Pas_adresse_email' => "Pas d&acute;adresse email",
'Patron_manquant' => "Vous devez appliquer un grand patron avant de param&eacute;trer l'envoi de cette 
liste."
, 'personnaliser_le_courrier' => "Personnaliser le courrier"
, 'personnaliser_le_courrier_desc' => 
	"Vous pouvez personnaliser le courrier pour chaque abonn&eacute; en ins&eacute;rant 
   dans votre patron les tags n&eacute;cessaires. Par exemple, pour ins&eacute;rer 
   le nom de votre abonn&eacute; dans son courrier lors de l'envoi, placez dans 
   votre patron _AUTEUR_NOM_ (notez le tiret bas en d&eacute;but et fin de tag)."
, 'personnaliser_le_courrier_label' => "Activer la personnalisation du courrier"

//R
, 'recherche' => 'Rechercher',
'regulariser' => 'regulariser les desabonnes avec listes...<br />',
'revenir_haut' => 'Revenir en haut de la page',
'reponse' => 'En r&eacute;ponse au message',
'reponse_plur' => 'r&eacute;ponses',
'reponse_sing' => 'r&eacute;ponse',
'retour' => 'Adresse email du gestionnaire de la liste (reply-to)',
'Resultat_import' => "R&eacute;sultat import"
, 'retablir' => "R&eacute;tablir"

//S
, 'smtp' => 'Utiliser SMTP',
'spip_ident' => 'Requiert une identification',
'smtp_hote' => 'H&ocirc;te',
'smtp_port' => 'Port',
'spip_listes' => 'Spip listes',
'suivi' => 'Suivi des abonnements',
'Suivi_des_abonnements' => 'Suivi des abonnements',
'sujet_nouveau' => 'Nouveau sujet',
'sujet_auteur' => 'Auteur',
'sujet_courrier' => '<strong>Sujet du courrier</strong> [obligatoire]',
'sujet_courrier_auto' => 'Sujet du courrier automatique : ',
'sujet_visites' => 'Visites',
'sujets' => 'Sujets',
'sujets_aucun' => 'Pas de sujet dans ce forum pour l\'instant',
'site' => 'Site web',
'sujet_clos_titre' => 'Sujet Clos',
'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.',
'sur_liste' => 'Sur la liste',
'Supprimer_ce_courrier' => "Supprimer ce courrier",
'Selectionnez_une_liste_pour_import' => "Vous devez s&eacute;lectionner au moins une liste de diffusion pour pouvoir importer 
	les abonn&eacute;s.",
'Selectionnez_une_liste_de_destination' => "S&eacute;lectionnez une ou plusieurs listes de destination pour vos abonn&eacute;s.",
'Stoppes' => "Stopp&eacute;s",
'Sans_destinataire' => "Sans destinataire",
'Sans_abonnement' => "Sans abonnement"
, 'sans_abonne' => "sans abonn&eacute;"
, 'sans_moderateur' => "sans mod&eacute;rateur"
, 'aucun_destinataire' => "aucun destinataire"
, 'Supprimer_ce_contact' => "Supprimer ce contact",
'Suppression_de' => "Suppression de"
, 'suppression_' => "Suppression @objet@"
, 'suppression_chronos_' => "Supprimer les envois programm&eacute;s (chrono) "
, 'suppression_chronos_desc' => "Si vous supprimez son chrono, la liste n'est pas supprim&eacute;e. Sa p&eacute;riodicit&eacute; 
	est conserv&eacute;e mais l'envoi est suspendu. Pour r&eacute;activer le chrono, il faut lui red&eacute;finir une date de premier 
	envoi. "
, 'Supprimer_les_listes' => "Supprimer les listes"
, 'Supprimer_la_liste' => "Supprimer la liste...",
'Suspendre_abonnements' => "Suspendre les abonnements pour ce compte",
'separateur_de_champ_' => "S&eacute;parateur de champ ",
'separateur_tabulation' => "tabulation (<code>\\t</code>)",
'separateur_semicolon' => "point-virgule (<code>;</code>)",
'simulation_desactive' => "Mode simulation d&eacute;sactiv&eacute;."
, 'simuler_les_envois' => "Simuler les envois de courriers"
, 'sup_mod' => "Supprimer ce mod&eacute;rateur"

 //T
, 'texte_boite_en_cours' => 'SPIP-Listes envoie un courrier.<p>Cette boite disparaitra une fois l\'envoi achev&eacute;.</p>',
'texte_courrier' => '<strong>Texte du courrier</strong> (HTML autoris&eacute;)',
'texte_contenu_pied' => '<br />(Message ajout&eacute; en bas de chaque email au moment de l\'envoi)<br />',
'texte_lettre_information' => 'Voici la lettre d\'information de ',
'texte_pied' => '<p><strong>Texte du pied de page</strong>',
'Tous_les' => 'Tous les'
, 'Toutes_les_semaines' => "Toutes les semaines"
, 'Tous_les_mois' => "Tous les mois, "
, 'Tous_les_ans' => "Tous les ans"
, 'total' => "Total "
, 'Trieuse_reactivee' => "Trieuse r&eacute;activ&eacute;e"
, 'trieuse_suspendue' => "Trieuse suspendue"
, 'trieuse_suspendue_info' => "Le traitement des listes de diffusion programm&eacute;es est suspendu."

//U
, 'Utilisez_formulaire' => "Utilisez le formulaire ci-contre pour activer/d&eacute;activer cette option.",

//V
'version_html' => '<strong>Version HTML</strong>',
'version_texte' => '<strong>Version texte</strong>',
'voir' => 'voir',
'vous_pouvez_egalement' => 'Vous pouvez &eacute;galement',
'vous_inscrire_auteur' => 'vous inscrire en tant qu\'auteur',
'voir_discussion' => 'Voir la discussion',
'masquer_les_journaux_SPIPLISTES' => "Masquer les journaux SPIPLISTES"
, 'Vides' => "Vides",
'Valider_abonnement' => "Valider cet abonnement"
, 'vous_etes_abonne_aux_listes_selectionnees_' => "Vous &ecirc;tes abonn&eacute; aux listes s&eacute;lectionn&eacute;es "

, 'abon' => 'LES ABONNES'
, 'abos_cette_liste' => "Les abonn&eacute;s &agrave; cette liste"
, 'abon_ajouter' => 'AJOUTER UN ABONNE &nbsp; ',
'abonees' => 'tous les abonn&eacute;s',
'abonne_listes' => 'Ce contact est abonn&eacute; aux listes suivantes',
'abonne_aucune_liste' => 'Abonn&eacute;s &agrave; aucune liste',
'abonnement_simple' => '<strong>Abonnement simple : </strong><br /><i>Les abonn&eacute;s re&ccedil;oivent un message de confirmation apr&egrave;s leur abonnement</i>',
'abonnement_code_acces' => '<strong>Abonnement avec codes d\'acc&egrave;s : </strong><br /><i>Les abonn&eacute;s re&ccedil;oivent en plus un login et un mot de passe qui leur permettront de s\'identifier sur le site. </i>',
'abonnement_newsletter' => '<strong>Abonnement &agrave; la lettre d\'information</strong>',
'acces_a_la_page' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.',
'adresse_deja_inclus' => 'Adresse d&eacute;j&agrave; connue',
'autorisation_inscription' => 'SPIP-listes vient d\'activer l\'autorisation de s\'inscrire aux visiteurs du site'

, 'choisir' => 'Choisir'
, 'Choisir_cette_liste' => 'Choisir cette liste',
'confirme_envoi' => 'Veuillez confirmer l\'envoi',
'Charger_un_patron' => "Charger un patron",

'date_act' => 'Donn&eacute;es actualis&eacute;es',
'date_ref' => 'Date de r&eacute;f&eacute;rence',
'desabo' => 'd&eacute;sabo',
'desabonnement' => 'D&eacute;sabonnement&nbsp;',
'desabonnes' => 'D&eacute;sabonn&eacute;s',
'desole' => 'D&eacute;sol&eacute;',
'destinataire' => 'destinataire',
'destinataires' => 'Destinataires',

'efface' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base',
'efface_base' => 'a &eacute;t&eacute; effac&eacute; des listes et de la base',
'email_adresse' => 'Adresse email de test',
'email_collec' => 'R&eacute;diger un courrier',
'email_test' => 'Envoyer un email de test',
'email_test_liste' => 'Envoyer vers une liste de diffusion',
'email_tester' => 'Tester par email',
'env_esquel' => 'Envoi programm&eacute; du patron',
'env_maint' => 'Envoyer maintenant',
'envoyer' => 'envoyer le mail',
'envoyer_a' => 'Envoi vers ',
'erreur' => 'Erreur',
'erreur_import' => 'Le fichier d\'import pr&eacute;sente une erreur &agrave; la ligne ',
'Erreur_courrier_titre_vide' => "Erreur: votre courrier n'a pas de titre."
, 'envoi_manuel' => "Envoi manuel"

, 'format_date' => 'Y/m/d',
'format_aucun' => "Aucun",

'html' => 'HTML',

'importer' => 'Importer une liste d\'abonn&eacute;s',
'importer_fichier' => 'Importer un fichier',
'importer_fichier_txt' => '<p><strong>Votre liste d\'abonn&eacute;s doit &ecirc;tre un fichier simple (texte) qui ne comporte qu\'une adresse e-mail par ligne</strong></p>',
'importer_preciser' => '<p>Pr&eacute;cisez les listes et le format correspondant &agrave; votre import d\'abonn&eacute;s</p>',
'inconnu' => 'n\'est plus abonn&eacute; &agrave; la liste',

'liste_diff_publiques' => 'Listes de diffusion publiques<br /><i>La page du site public propose l\'inscription &agrave; ces listes.</i>',
'liste_sans_titre' => 'Liste sans titre',
'listes_internes' => 'Listes de diffusion internes<br /><i>Au moment de l\'envoi d\'un courrier, ces listes sont propos&eacute;es parmi les destinataires</i>',
'listes_poubelle' => 'Vos listes de diffusion &agrave; la poubelle',
'lock' => 'Lock actif : ',
'Liste_numero_:' => 'Liste num&eacute;ro :'
, 'Listes_autre' => "Autre pr&eacute;riodicit&eacute;"
, 'Listes_autre_periode' => "Listes publiques autre pr&eacute;riodicit&eacute;"
, 'Listes_diffusion_prive' => "Listes priv&eacute;es"
, 'Liste_hebdo' => "Liste hebdomadaire"
, 'Listes_diffusion_hebdo' => "Listes publiques hebdomadaires"
, 'Listes_diffusion_hebdo_desc' => "L'abonnement &agrave; ces listes &agrave; p&eacute;riodicit&eacute; hebdomadaire 
	est propos&eacute; sur le site public."
, 'Liste_mensuelle' => "Liste mensuelle"
, 'Listes_diffusion_mensuelle' => "Listes publiques mensuelles"
, 'Listes_diffusion_mensuelle_desc' => "L'abonnement &agrave; ces listes &agrave; p&eacute;riodicit&eacute; mensuelle 
	est propos&eacute; sur le site public."
, 'Liste_annuelle' => "Liste annuelle"
, 'Listes_diffusion_annuelle' => "Listes publiques annuelles"
, 'Listes_diffusion_annuelle_desc' => "L'abonnement &agrave; ces listes &agrave; p&eacute;riodicit&eacute; annuelle 
	est propos&eacute; sur le site public."
, 'Listes_diffusion_publique' => 'Listes de diffusion publiques',
'Listes_diffusion_publique_desc' => 'La page du site public propose l\'inscription &agrave; ces listes.',
'Listes_diffusion_interne' => 'Listes de diffusion internes',
'Listes_diffusion_interne_desc' => 'Au moment de l\'envoi d\'un courrier, ces listes sont propos&eacute;es parmi les destinataires.',
'Listes_diffusion_suspendue' => 'Listes de diffusion suspendues',
'Listes_diffusion_suspendue_desc' => ' ',

'mail_a_envoyer' => 'Nombre de mails &agrave; envoyer : ',
'mail_tache_courante' => 'Mails envoy&eacute;s pour la t&acirc;che courante : ',
'messages_auto_envoye' => 'Courriers automatiques envoy&eacute;s',
'message_en_cours' => 'Ce courrier est en cours de r&eacute;daction',
'message_presque_envoye' =>'Ce courrier est sur le point d\'&ecirc;tre envoy&eacute;',
'mode_inscription' => 'Param&eacute;trer le mode d\'inscription des visiteurs',
'modif_envoi' => 'Vous pouvez le modifier ou demander son envoi',
'modifier_liste' => 'Modifier cette liste ',

'nb_abonnes' => 'Dans les listes : ',
'nb_inscrits' => 'Dans le site :  ',
'nb_listes' => 'Incriptions dans toutes les listes : '
, 'Pas_de_courrier_auto_programme' => "Il n'y a pas de courrier automatique planifi&eacute; pour cette liste."
, 'Pas_de_periodicite' => "Pas de p&eacute;riodicit&eacute;."
, 'nouvelle_abonne' => 'L\'abonn&eacute; suivant a &eacute;t&eacute; ajout&eacute; la liste',

'pas_acces' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.',
'plus_abonne' => ' n\'est plus abonn&eacute; &agrave; la liste ',
'prochain_envoi_aujd' => 'Prochain envoi pr&eacute;vu aujourd\'hui',
'prochain_envoi_prevu' => 'Prochain envoi pr&eacute;vu',
'prochain_envoi_prevu_dans' => 'Prochain envoi pr&eacute;vu dans ',
'prog_env' => 'Programmer un envoi automatique',
'prog_env_non' => 'Ne pas programmer d\'envoi',
'program' => 'Programmation des courriers automatiques',
'plein_ecran' => "(Plein &eacute;cran)",
'Prochain_envoi_' => "Prochain envoi ",

'reinitialiser' => 'reinitialiser',
'remplir_tout' => 'Tous les champs doivent &ecirc;tre remplis',
'repartition' => 'R&eacute;partition',
'retour_link' => 'Retour',
'repartition_abonnes' => "R&eacute;partition des abonn&eacute;s",
'repartition_formats' => "R&eacute;partition des formats",

'Erreur_Adresse_email_inconnue' => 'Attention, l\'adresse email de test que vous avez fournie ne correspond &agrave; aucun abonn&eacute;, <br />l\'envoi ne peut se faire, veuillez reprendre la proc&eacute;dure<br /><br />',
'squel' => 'Patron : &nbsp;',
'statut_interne' => 'Interne',
'statut_publique' => 'Publique',
'suivi_envois' => 'Suivi des envois',
'supprime_contact' => 'Supprimer ce contact d&eacute;finitivement',
'supprime_contact_base' => 'Supprimer d&eacute;finitivement de la base',

'tableau_bord' => 'Tableau de bord',
'texte' => 'Texte',
'toutes' => 'Tous les inscrits',
'txt_abonnement' => '(Indiquez ici le texte pour l\'abonnement &agrave; cette liste, affich&eacute; sur le site public si la liste est active)',
'txt_inscription' => 'Texte d\'inscription : ',

'une_inscription' => 'Un abonn&eacute; trouv&eacute;',

'val_texte' => 'Texte',
'version' => 'version',
'voir_historique' => 'Voir l\'historique des envois',


// ====================== inscription-listes.php3 / abonnement.php3 ======================

'abo_listes' => 'Abonnement',
'acces_refuse' => 'Vous n\'avez plus acc&egrave;s &agrave; ce site',

'confirmation_format' => ' en format ',
'confirmation_liste_unique_1' => 'Vous &ecirc;tes abonn&eacute; &agrave la liste d\'information du site',
'confirmation_liste_unique_2' =>'Vous avez choisi de recevoir les courriers adress&eacute;s &agrave la liste suivante :',
'confirmation_listes_multiples_1' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site ',
'confirmation_listes_multiples_2' => 'Vous avez choisi de recevoir les courriers adress&eacute;s aux listes suivantes :',

'Erreur_Adresse_email_invalide' => 'Erreur: l\'adresse email que vous avez fournie n\'est pas valide',

'infos_liste' => 'Informations sur cette liste',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'Nombre de contacts',
'contacts_lot' => 'Contacts de ce lot',
'editeur' => 'Editeur : ',
'envoi_en_cours' => 'Envoi en cours',
'envoi_tous' => 'Envoi &agrave; destination de tous les inscrits',
'envoi_listes' => 'Envoi &agrave; destination des abonn&eacute;s &agrave; la liste : ',
'envoi_erreur' => 'Erreur : SPIP-listes ne trouve pas de destinataire pour ce courrier',
'email_reponse' => 'Email de r&eacute;ponse : ',
'envoi_annule' => 'Envoi annul&eacute;',
'envoi_fini' => 'Envois termin&eacute;s',
'erreur_destinataire' => 'Erreur destinataire : pas d\'envoi',
'erreur_sans_destinataire' => 'Erreur : aucun destinataire ne peut &ecirc;tre trouv&eacute; pour ce courrier',
'erreur_mail' => 'Erreur : envoi du mail impossible (v&eacute;rifier si mail() de php est disponible)',

'forcer_lot' => 'Provoquer l\'envoi du lot suivant',

'non_courrier' => 'Pas / plus de courrier &agrave; envoyer',
'non_html' => 'Votre logiciel de messagerie ne peut apparemment pas afficher correctement la version graphique (HTML) de cet e-mail',
'sans_adresse' => 'Mail non envoy&eacute; -> Veuillez d&eacute;finir une adresse de r&eacute;ponse'
, 'confirmer' => 'Confirmer'
, 'lettre_info' => 'La lettre d\'information du site'
, 'patron_detecte' => '<p><strong>Patron d&eacute;tect&eacute; pour la version texte</strong><p>'
, 'patron_erreur' => 'Le patron sp&eacute;cifi&eacute; ne donne pas de r&eacute;sulat avec les param&egrave;tres choisis'
, 'abonees_titre' => 'Abonn&eacute;s'
, 'listes_emails' => 'Lettres d\'information'
, 'options' => 'radio|brut|Format :|Html,Texte,D&eacute;sabonnement|html,texte,non'
, 'bonjour' => 'Bonjour,'
, 'inscription_response' => 'Vous &ecirc;tes abonn&eacute; &agrave; la liste d\'information du site '
, 'inscription_responses' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site '
, 'inscription_liste' => 'Vous avez choisi de recevoir les courriers adress&eacute;s &agrave; la liste suivante : '
, 'inscription_listes' => 'Vous avez choisi de recevoir les courriers adress&eacute;s aux listes suivantes : '
, 'inscription_format' => ' en format '
, 'info_1_liste' => '1 liste'
, 'info_liste_1' => 'liste'
, 'info_liste_2' => 'listes'
, 'info_1_abonne' => '1 abonn&eacute;'
, 'info_abonnes' => 'abonn&eacute;s'

);

?>