<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_fr'] = array(


//_ 
'_aide' => '<p>SPIP-Listes permet d\'envoyer un courrier ou des courriers automatiques &agrave; des abonn&eacute;s.</p> <p>Vous pouvez &eacute;crire un texte simple, composer votre courrier en HTML ou appliquer un "patron" &agrave; votre courrier</p>
<p>Via un formulaire d\'inscription public, les abonn&eacute;s d&eacute;finissent eux-m&ecirc;mes leur statut d\'abonnement, les listes auxquelles ils s\'abonnent et le format
dans lequel ils souhaitent recevoir les courriers (HTML/texte). </p><p>Tout courrier sera traduit automatiquement en format texte pour les abonn&eacute;s qui en ont fait la demande.</p><p><b>Note :</b><br />L\'envoi des mails peut prendre quelques minutes : les lots partent peu &agrave; peu quand les utilisateurs parcourent le site public. Vous pouvez aussi provoquer manuellement l\'envoi des lots en cliquant sur le lien "suivi des envois" pendant un envoi.</p>',
'_aide_import' => "Vous pouvez importer ici une liste d'abonn&eacute;s &agrave; partir de votre 
   ordinateur.<br />
Cette liste d'abonn&eacute;s doit &ecirc;tre au format texte seul, une ligne 
   par abonn&eacute;. Chaque ligne doit &ecirc;tre compos&eacute;e ainsi :<br />
<tt style='display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;'>adresse@mail<span style='color:#f66'>[tabulation]</span>login<span style='color:#f66'>[tabulation]</span>nom</tt>
<tt style='color:#f66'>[tabulation]</tt> est un caract&egrave;re de tabulation.<br /><br />
L'adresse email doit &ecirc;tre unique, ainsi que le login. Si cette adresse 
   email ou se login existe dans la base du site, la ligne sera rejet&eacute;e.<br />
Le premier champ adresse@mail est obligatoire. Les deux autres champs peuvent 
   &ecirc;tre ignor&eacute;s (vous pouvez importer des listes issues des anciennces versions de SPIP-Listes).",

// A
'abo_1_lettre' => 'Lettre d\'information',
'abonnement_0' => 'Abonnement',
'abonnement'=>'Vous souhaitez modifier votre abonnement &agrave; la lettre d\'information',
'abonnement_bouton'=>'Modifier votre abonnement',
'abonnement_cdt' => '<a href=\'http://bloog.net/spip-listes/\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'Vous pouvez changer de format de r&eacute;ception ou vous d&eacute;sabonner : ',
'abonnement_mail' => 'Pour modifier votre abonnement, veuillez vous rendre &agrave; l\'adresse suivante',
'abonnement_mail_passcookie' => '(ceci est un message automatique)
Pour modifier votre abonnement &agrave; la lettre d\'information de ce site :
@nom_site_spip@ (@adresse_site@)

Veuillez vous rendre &agrave; l\'adresse suivante :

@adresse_site@/spip.php?page=abonnement&d=@cookie@

Vous pourrez alors confirmer la modification de votre abonnement.',
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
'adresse' => 'Indiquez ici l\'adresse &agrave; utiliser pour les retours de mails (&agrave; d&eacute;faut, l\'adresse du webmestre sera utilis&eacute;e comme adresse de retour) :',
'adresse_envoi' => 'Adresse d\'envoi par d&eacute;faut',
'adresses_importees' => 'Adresses import&eacute;es',
'adresse_smtp' => 'adresse email du <i>sender</i> SMTP',
'aff_redac' => 'Courriers en cours de r&eacute;daction',
'aff_encours' => 'Courriers en cours d\'envoi',
'aff_envoye' => 'Courriers envoy&eacute;s',
'aff_lettre_auto' => 'Lettres des nouveaut&eacute;s envoy&eacute;es',
'alerte_edit' => 'Le formulaire ci-dessous permet de modifier le texte d\'un courrier. Vous pouvez choisir de commencer par importer un patron pour g&eacute;n&eacute;rer le contenu de votre message.',
'alerte_modif' => '<b>Apr&egrave;s l\'affichage de votre courrier, vous pourrez en modifier le contenu</b>',
'annuler_envoi' => 'Annuler l\'envoi',
'article_entier' => 'Lire l\'article entier',
'abonnes_titre' => 'Abonn&eacute;s',
'adresse_de_reponse' => "Adresse de r&eacute;ponse",
'adresse_mail_retour' => 'Adresse email du gestionnaire de la liste (reply-to)',
'Attention_action_retire_invites' => "Attention: cette action retire les invit&eacute;s de la liste des abonn&eacute;s.",
'A_partir_de' => "&Agrave; partir de",
'aucun' => "aucun",

//B

'bouton_listes' => 'Lettres d\'information',
'bouton_modifier' => 'Modifier ce courrier',

//C
'calcul_patron' => 'Calcul avec le patron version texte',
'calcul_html' => 'Calcul depuis la version HTML du message',
'Cette_liste_est' => 'Cette liste est',
'charger_patron' => 'Choisir un patron pour le courrier',
'charger_le_patron' => 'G&eacute;n&eacute;rer le courrier',
'choix_defini' => 'Pas de choix d&eacute;finis.\n',
'Configuration' => 'Configuration',
'courriers' => 'Courriers',
'chercher_un_auteur' => "Chercher un auteur",
'Courrier_numero_:' => "Courrier num&eacute;ro :",
'Creer_un_courrier_:' => "Cr&eacute;er un courrier :",
'Choix_non_defini' => 'Pas de choix d&eacute;fini.',
'Complement_des_courriers' => "Compl&eacute;ment des courriers",
'Complement_lien_en_tete' => "Lien sur le courrier", 
'Complement_ajouter_lien_en_tete' => "Ajouter un lien en en-t&ecirc;te du courrier", 
'Complement_lien_en_tete_desc' => "Cette option vous permet de rajouter en t&ecirc;te du courrier HTML envoy&eacute; le lien 
   du courrier original pr&eacute;sent sur votre site.",
'Complement_lien_de_tete' => "<div style='font-size=75%;padding:0 1ex;background-color:#ffc;text-align:center;'>
	<div>Ce courrier est au format HTML.</div>
	<div>Si vous rencontrez 
   des difficult&eacute;s &agrave; le lire ici, <a href='@liencourrier@'>vous pouvez 
   consulter l'original sur le site @nomsite@</a>.</div>
	</div>",
'Console' => "Console",
'Console_syslog_desc' => "Vous &ecirc;tes sur un r&eacute;seau local (@IP_LAN@). Si besoin, vous pouvez activer la console sur syslog au lieu des journaux SPIP.",
'Console_syslog_texte' => "Activer les journaux syst&egrave;mes (syslog)",
'Casier_a_courriers' => "Casier &agrave; courriers",
'Courriers_en_cours_de_redaction' => 'Courriers en cours de r&eacute;daction',
'Courriers_prets_a_etre_envoye' => "Courriers pr&ecirc;ts &agrave; &ecirc;tre envoy&eacute;",
'Courriers_publies' => "Courriers publi&eacute;s",
'Courriers_auto_publies' => "Courriers automatiques publi&eacute;s",
'Courriers_stope' => "Courriers stopp&eacute;s en cours d'envoi",
'Courriers_vides' => "Courriers annul&eacute;s (vides)",
'Courriers_sans_destinataire' => "Courriers sans destinataire (liste vide)",
'Courriers_sans_liste' => "Courriers sans abonn&eacute;s (liste manquante)",
'Ce_courrier_ne_sera_envoye_qu_une_fois' => "Ce courrier ne sera envoy&eacute; qu'une fois.",

//D
'_de_' => " de ",
'dans_jours' => 'dans',
'definir_squel' => 'Choisir le mod&egrave;le de courrier &agrave; pr&eacute;visualiser',
'definir_squel_choix' => 'A la r&eacute;daction d\'un nouveau courrier, SPIP-Listes vous permet de charger un patron. En appuyant sur un bouton, vous chargez dans le corps du courrier le contenu d\'un des squelettes du repertoire <b>/patrons</b> (situ&eacute; &agrave; la racine de votre site Spip). <p><b>Vous pouvez &eacute;diter et modifier ces squelettes selon vos go&ucirc;ts.</b></p> <ul><li>Ces squelettes peuvent contenir du code HTML classique</li>
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

//E
'email' => 'E-mail',
'email_envoi' => 'Envoi des emails',
'envoi' => 'Envoi :',
'envoi_charset' => 'Charset de l\'envoi',
'envoi_date' => 'Date de l\'envoi : ',
'envoi_debut' => 'Debut de l\'envoi : ',
'envoi_fin' => 'Fin de l\'envoi : ',
'envoi_nouv' => 'Envoi des nouveaut&eacute;s',
'envoi_patron' => 'envoi du patron',
'envoi_program' => 'Envoi programm&eacute;',
'envoi_smtp' => 'Lors d\'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l\'adresse de l\'envoyeur.',
'envoi_texte' => 'Si ce courrier vous convient, vous pouvez l\'envoyer',
'erreur_envoi' => 'Nombre d\'envois en erreur : ',
'erreur_install' => '<h3>erreur: spip-listes est mal install&eacute;!</h3>',
'erreur_install2' => '<p>V&eacute;rifier les &eacute;tapes d\'installation, notamment si vous avez bien renomm&eacute;<i>mes_options.txt</i> en <i>mes_options.php</i>.</p>',
'exporter' => 'Exporter la liste d\'abonn&eacute;s',
'Erreur_liste_vide' => "Erreur: cette liste n'a pas d'abonn&eacute;s.",
'Envoi_des_courriers' => "Envoi des courriers",
'Envoyer_ce_courrier' => "Envoyer ce courrier",
'Exporter_une_liste_d_abonnes' => "Exporter une liste d'abonn&eacute;s",
'Exporter_une_liste_de_non_abonnes' => "Exporter une liste de non abonn&eacute;s",
'En_redaction' => "En r&eacute;daction",
'En_cours' => "En cours",
'Envoi_abandonne' => "Envoi abandonn&eacute;",

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
'Format_de_reception_:' => "Format de r&eacute;ception :",
'Format_de_reception_desc' => "Vous pouvez choisir un format global de r&eacute;ception des courriers pour 
   cet abonn&eacute;.<br /><br />
   Vous pouvez &eacute;galement d&eacute;sabonner temporairement ce contact. 
   Il reste inscrit dans les listes en tant que destinataire, mais les courriers 
   ne lui seront pas envoy&eacute;s tant que vous ne lui aurez pas d&eacute;fini un format de r&eacute;ception de courriers.",

//H
'Historique_des_envois' => 'Historique des envois',

//I
'info_auto' => 'SPIP-Listes pour spip peut envoyer r&eacute;guli&egrave;rement aux inscrits, l\'annonce des derni&egrave;res nouveaut&eacute;s du site (articles et br&egrave;ves r&eacute;cemment publi&eacute;s).',
'info_heberg' => 'Certains h&eacute;bergeurs d&eacute;sactivent l\'envoi automatique de mails depuis leurs serveurs. Dans ce cas, les fonctionnalit&eacute;s suivantes de SPIP-Listes pour SPIP ne fonctionneront pas',
'info_nouv' => 'Vous avez activ&eacute; l\'envoi des nouveaut&eacute;s',
'info_nouv_texte' => 'Prochain envoi des nouveaut&eacute;s dans @proch@ jours',
'inscription_mail_forum' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@) et &agrave; l\'interface de r&eacute;daction (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'L\'abonnement vous permet d\'acc&eacute;der aux parties du site en acc&egrave;s restreint,
d\'intervenir sur les forums r&eacute;serv&eacute;s aux visiteurs enregistr&eacute;s et de recevoir les lettres d\'informations.' ,

'inscription_redacteurs' =>'L\'espace de r&eacute;daction de ce site est ouvert aux visiteurs apr&egrave;s inscription.
Une fois enregistr&eacute;, vous pourrez consulter les articles en cours de r&eacute;daction, proposer des articles
et participer &agrave; tous les forums.  L\'inscription permet &eacute;galement d\'acc&eacute;der aux parties du site en acc&egrave;s restreint
et de recevoir les lettres d\'informations.',
'import_export' => 'Import / Export',

//J
'jour' => 'jour',
'jours' => 'jours',

//L
'langue' => '<strong>Langue :</strong>&nbsp;',
'Langue_du_courrier_:' => "Langue du courrier :",
'lire' => 'Lire',
'Listes_de_diffusion' => 'Listes de diffusion',
'log' => 'Logs',
'login' => 'Connexion',
'logout' => 'D&eacute;connexion',
'lot_suivant' => 'Provoquer l\'envoi du lot suivant',
'lieu' => 'Localisation',
'lettre_d_information' => 'Lettre d\'information',
'liste_des_abonnes' => "Liste des abonn&eacute;s",
'Listes_de_diffusion' => 'Listes de diffusion',
'Liste_de_destination' => "Liste de destination",

//M
'mail_format' => 'Vous &ecirc;tes abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@ en format',
'mail_non' => 'Vous n\'&ecirc;tes pas abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@',
'message_arch' => 'Courrier archiv&eacute;',
'messages_auto' => 'Courriers automatiques',
'messages_auto_texte' => '<p>Par d&eacute;faut, le squelette des nouveaut&eacute;s permet d\'envoyer automatiquement la liste des articles et br&egrave;ves publi&eacute;s sur le site depuis le dernier envoi automatique. </p><p>vous pouvez personnaliser le message en d&eacute;finissant l\'adresse d\'un logo et d\'une image de fond pour les titres de parties en &eacute;ditant le fichier nomm&eacute; <b>"nouveautes.html"</b> (situ&eacute; &agrave; dans le rep&eacute;rtoire /dist).</p>',
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
'methode_envoi' => 'M&eacute;thode d\'envoi',
'mettre_a_jour' => '<h3>SPIP-listes va mettre a jour</h3>',
'moderateurs' => 'Mod&eacute;rateur(s)',
'modifier' => 'Modifier',
'mis_a_jour' => 'Mis &agrave; jour',
'Modifier_un_courrier_:' => "Modifier un courrier :",
'Modifier_ce_courrier' => "Modifier ce courrier",
'Mode_simulation' => "Mode simulation",
'Simuler_les_envois' => "Simuler les envois de courriers",

//n
'nb_abonnes_plur' => ' abonn&eacute;s',
'nb_abonnes_sing' => ' abonn&eacute;',
'nbre_abonnes' => 'Nombre d\'abonn&eacute;s : ',
'nom' => 'Nom d\'utilisateur',
'nombre_lot' => 'Nombre d\'envois par lot',
'Nouveau_courrier' => 'Nouveau courrier',
'nouveaute_intro' => 'Bonjour, <br />Voici les nouveaut&eacute;s publi&eacute;es sur le site',
'nouveaux_messages' => 'Nouveaux messages',
'Nouvelle_liste_de_diffusion' => 'Nouvelle liste de diffusion',
'numero' => 'N&nbsp;',

//P
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
'Prets_a_envoi' => "Pr&ecirc;ts &agrave; l'envoi",
'Publies' => "Publi&eacute;s",

//R
'recherche' => 'Rechercher',
'regulariser' => 'regulariser les desabonnes avec listes...<br />',
'revenir_haut' => 'Revenir en haut de la page',
'reponse' => 'En r&eacute;ponse au message',
'reponse_plur' => 'r&eacute;ponses',
'reponse_sing' => 'r&eacute;ponse',
'retour' => 'Adresse email du gestionnaire de la liste (reply-to)',
'Resultat_import' => "R&eacute;sultat import",

//S
'smtp' => 'Utiliser SMTP',
'spip_ident' => 'Requiert une identification',
'smtp_hote' => 'H&ocirc;te',
'smtp_port' => 'Port',
'spip_listes' => 'Spip listes',
'suivi' => 'Suivi des abonnements',
'Suivi_des_abonnements' => 'Suivi des abonnements',
'sujet_nouveau' => 'Nouveau sujet',
'sujet_auteur' => 'Auteur',
'sujet_courrier' => '<b>Sujet du courrier</b> (obligatoire)<br />',
'sujet_courrier_auto' => 'Sujet du courrier automatique : ',
'sujet_visites' => 'Visites',
'sujets' => 'Sujets',
'sujets_aucun' => 'Pas de sujet dans ce forum pour l\'instant',
'site' => 'Site web',
'sujet_clos_titre' => 'Sujet Clos',
'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.',
'sur_liste' => 'sur la liste',
'Supprimer_ce_courrier' => "Supprimer ce courrier",
'Selectionnez_une_liste_pour_import' => "Vous devez s&eacute;lectionner au moins une liste de diffusion pour pouvoir importer 
	les abonn&eacute;s.",
'Selectionnez_une_liste_de_destination' => "S&eacute;lectionnez une ou plusieurs listes de destination pour vos abonn&eacute;s.",
'Stoppes' => "Stopp&eacute;s",
'Sans_destinataire' => "Sans destinataire",

 //T
'texte_boite_en_cours' => 'SPIP-Listes envoie un courrier.<p>Cette boite disparaitra une fois l\'envoi achev&eacute;.</p>',
'texte_courrier' => '<b>Texte du courrier</b> (HTML autoris&eacute;)',
'texte_contenu_pied' => '<br />(Message ajout&eacute; en bas de chaque email au moment de l\'envoi)<br />',
'texte_lettre_information' => 'Voici la lettre d\'information de ',
'texte_pied' => '<p><b>Texte du pied de page</b>',
'Tous_les' => 'Tous les',

//V
'version_html' => '<b>Version HTML</b>',
'version_texte' => '<b>Version texte</b>',
'voir' => 'voir',
'vous_pouvez_egalement' => 'Vous pouvez &eacute;galement',
'vous_inscrire_auteur' => 'vous inscrire en tant qu\'auteur',
'voir_discussion' => 'Voir la discussion',
'Voir_les_journaux_SPIPLISTES' => "Voir les journaux SPIPLISTES",
'Vides' => "Vides",

// ====================== spip_listes.php3 ======================
'abon' => 'ABONNES',
'abon_ajouter' => 'AJOUTER UN ABONNE &nbsp; ',
'abonees' => 'tous les abonn&eacute;s',
'abonne_listes' => 'Ce contact est abonn&eacute; aux listes suivantes',
'abonne_aucune_liste' => 'Abonn&eacute;s &agrave; aucune liste',
'abonnement_simple' => '<b>Abonnement simple : </b><br /><i>Les abonn&eacute;s re&ccedil;oivent un message de confirmation apr&egrave;s leur abonnement</i>',
'abonnement_code_acces' => '<b>Abonnement avec codes d\'acc&egrave;s : </b><br /><i>Les abonn&eacute;s re&ccedil;oivent en plus un login et un mot de passe qui leur permettront de s\'identifier sur le site. </i>',
'abonnement_newsletter' => '<b>Abonnement &agrave; la lettre d\'information</b>',
'acces_a_la_page' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.',
'adresse_deja_inclus' => 'Adresse d&eacute;j&agrave; connue',
'autorisation_inscription' => 'SPIP-listes vient d\'activer l\'autorisation de s\'inscrire aux visiteurs du site',

'choisir' => 'Choisir',
'Choisir_cette_liste' => 'Choisir cette liste',
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
'Erreur_courrier_titre_vide' => "Erreur: votre courrier n'a pas de titre.",

'format_date' => 'Y/m/d',
'format_aucun' => "Aucun",

'html' => 'HTML',

'importer' => 'Importer une liste d\'abonn&eacute;s',
'importer_fichier' => 'Importer un fichier',
'importer_fichier_txt' => '<p><b>Votre liste d\'abonn&eacute;s doit &ecirc;tre un fichier simple (texte) qui ne comporte qu\'une adresse e-mail par ligne</b></p>',
'importer_preciser' => '<p>Pr&eacute;cisez les listes et le format correspondant &agrave; votre import d\'abonn&eacute;s</p>',
'inconnu' => 'n\'est plus abonn&eacute; &agrave; la liste',

'liste_diff_publiques' => 'Listes de diffusion publiques<br /><i>La page du site public propose l\'inscription &agrave; ces listes.</i>',
'liste_sans_titre' => 'Liste sans titre',
'listes_internes' => 'Listes de diffusion internes<br /><i>Au moment de l\'envoi d\'un courrier, ces listes sont propos&eacute;es parmi les destinataires</i>',
'listes_poubelle' => 'Vos listes de diffusion &agrave; la poubelle',
'lock' => 'Lock actif : ',
'Liste_numero_:' => 'Liste num&eacute;ro :',
'Listes_diffusion_mensuelle' => "Listes publiques mensuelles",
'Listes_diffusion_mensuelle_desc' => "L'abonnement &agrave; ces listes &agrave; p&eacute;riodicit&eacute; mensuelle 
	est propos&eacute; sur le site public.",
'Listes_diffusion_publique' => 'Listes de diffusion publiques',
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
'modifier_liste' => 'Modifier cette liste :',

'nb_abonnes' => 'Dans les listes : ',
'nb_inscrits' => 'Dans le site :  ',
'nb_listes' => 'Incriptions dans toutes les listes : ',
'Pas_de_courrier_auto_programme' => 'Il n\'y a pas de courrier automatique programm&eacute; pour cette liste.',
'nouvelle_abonne' => 'L\'abonn&eacute; suivant a &eacute;t&eacute; ajout&eacute; la liste',

'pas_acces' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.',
'plus_abonne' => ' n\'est plus abonn&eacute; &agrave; la liste ',
'prochain_envoi_aujd' => 'Prochain envoi pr&eacute;vu aujourd\'hui',
'prochain_envoi_prevu' => 'Prochain envoi pr&eacute;vu',
'prochain_envoi_prevu_dans' => 'Prochain envoi pr&eacute;vu dans ',
'prog_env' => 'Programmer un envoi automatique',
'prog_env_non' => 'Ne pas programmer d\'envoi',
'program' => 'Programmation des courriers automatiques',
'plein_ecran' => '(Plein &eacute;cran)',

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
'sans_adresse' => 'Mail non envoy&eacute; -> Veuillez d&eacute;finir une adresse de r&eacute;ponse',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Confirmer',

'lettre_info' => 'La lettre d\'information du site',

'patron_detecte' => '<p><strong>Patron d&eacute;tect&eacute; pour la version texte</strong><p>',

'patron_erreur' => 'Le patron sp&eacute;cifi&eacute; ne donne pas de r&eacute;sulat avec les param&egrave;tres choisis',



// ====================== listes.html ======================

'abonees_titre' => 'Abonn&eacute;s',


// ====================== inc-presentation.php3 ======================

'listes_emails' => 'Lettres d\'information',


// ====================== mes-options.php3 ======================


'options' => 'radio|brut|Format :|Html,Texte,D&eacute;sabonnement|html,texte,non',

// ====================== mes-options.php3 ======================

'bonjour' => 'Bonjour,',

'inscription_response' => 'Vous &ecirc;tes abonn&eacute; &agrave; la liste d\'information du site ',
'inscription_responses' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site ',
'inscription_liste' => 'Vous avez choisi de recevoir les courriers adress&eacute;s &agrave; la liste suivante : ',
'inscription_listes' => 'Vous avez choisi de recevoir les courriers adress&eacute;s aux listes suivantes : ',
'inscription_format' => ' en format ',

'info_1_liste' => '1 liste',
'info_liste_2' => 'listes'

);

?>
