<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_fr'] = array(


//_ 
'_aide' => '<p>SPIP-Listes permet d\'envoyer un courrier ou des messages automatiques &agrave; des abonn&eacute;s.</p> <p>Vous pouvez &eacute;crire un texte simple, composer votre courrier en HTML ou appliquer un "patron" &agrave; votre message</p>
<p>Via un formulaire d\'inscription public, les abonn&eacute;s d&eacute;finissent eux-m&ecirc;mes leur statut d\'abonnement, les listes auxquelles ils s\'abonnent et le format
dans lequel ils souhaitent recevoir les courriers (HTML/texte). </p><p>Tout message sera traduit automatiquement en format texte pour les abonn&eacute;s qui en ont fait la demande.</p><p><b>Note :</b><br />L\'envoi des mails peut prendre quelques minutes : les lots partent peu &agrave; peu quand les utilisateurs parcourent le site public. Vous pouvez aussi provoquer manuellement l\'envoi des lots en cliquant sur le lien "suivi des envois" pendant un envoi.</p>',

// A
'abo_1_lettre' => 'Lettre d\'information',
'abonnement' => 'Abonnement',
'abonnement'=>'Vous souhaitez modifier votre abonnement &agrave; la lettre d\'information',
'abonnement_bouton'=>'Modifier votre abonnement',
'abonnement_cdt' => '<a href=\'http://bloog.net\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'Vous pouvez changer de format de r&eacute;ception ou vous d&eacute;sabonner : ',
'abonnement_mail' => 'Pour modifier votre abonnement, veuillez vous rendre &agrave; l\'adresse suivante',
'abonnement_mail_passcookie' => '(ceci est un message automatique)
Pour modifier votre abonnement &agrave la lettre d\'information de ce site :
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
'actualiser' => 'Actualiser',
'adresse' => 'Indiquez ici l\'adresse &agrave; utiliser pour les retours de mails (&agrave; d&eacute;faut, l\'adresse du webmestre sera utilis&eacute;e comme adresse de retour) :',
'adresses_importees' => 'Adresses import&eacute;es',
'aff_redac' => 'Courriers en cours de r&eacute;daction',
'aff_encours' => 'Courriers en cours d\'envoi',
'aff_envoye' => 'Courriers envoy&eacute;s',
'aff_lettre_auto' => 'Lettres des nouveaut&eacute;s envoy&eacute;es',
'aff_envoye' => 'Courriers envoy&eacute;s',
'alerte_edit' => 'Attention : ce message peut &ecirc;tre modifi&eacute; par tous les administrateurs du site et est re&ccedil;u par tous les abonn&eacute;s. N\'utilisez la lettre d\'information que pour exposer par mail des &eacute;v&eacute;nements importants de la vie du site.',
'alerte_modif' => '<b>Apr&egrave;s l\'affichage de votre message, vous pourrez en modifier le contenu</b>',
'annuler_envoi' => 'Annuler l\'envoi',

//B

//C
'Cette_liste_est' => 'Cette liste est',
'charger_patron' => 'Choisir un patron pour le courrier',
'charger_le_patron' => 'G&eacute;n&eacute;rer le message',
'Configuration' => 'Configuration',
'courriers' => 'Courriers',

//D
'definir_squel' => 'Choisir le mod&egrave;le de message &agrave; pr&eacute;visualiser',
'definir_squel_choix' => 'A la r&eacute;daction d\'un nouveau courrier, SPIP-Listes vous permet de charger un patron. En appuyant sur un bouton, vous chargez dans le corps du message le contenu d\'un des squelettes du repertoire <b>/patrons</b> (situ&eacute; &agrave; la racine de votre site Spip). <p><b>Vous pouvez &eacute;diter et modifier ces squelettes selon vos go&ucirc;ts.</b></p> <ul><li>Ces squelettes peuvent contenir du code HTML classique</li>
<li>Ce squelette peut contenir des boucles Spip</li>
<li>Apr&egrave;s le chargement du patron, vous pourrez re-&eacute;diter le message avant envoi (pour ajouter du texte)</li>
</ul><p>La fonction "charger un patron" permet donc d\'utiliser des gabarits HTML personnalis&eacute;s pour vos courriers ou de cr&eacute;er des lettres d\'information th&eacute;matiques dont le contenu est d&eacute;fini gr&acirc;ce aux boucles Spip.</p><p>Attention : ce squelette ne doit pas contenir de balises body, head ou html mais juste du code HTML ou des boucles Spip.</p>',
'definir_squel_texte' => 'Si vous disposez des codes d\'acc&egrave;s au FTP, vous pouvez ajouter des squelettes SPIP dans le r&eacute;pertoire /patrons (&agrave; la racine de votre site Spip).',
'devenir_redac'=>'devenir r&eacute;dacteur pour ce site',
'devenir_abonne'=>'Vous inscrire sur ce site',
'desabonnement_valid'=>'L\'adresse suivante n\'est plus abonn&eacute;e &agrave; la lettre d\'information' ,
'pass_recevoir_mail'=>'Vous allez recevoir un email vous indiquant comment modifier votre abonnement. ',
'desabonnement_confirm'=>'Vous &ecirc;tes sur le point de r&eacute;silier votre abonnement &agrave; la lettre d\'information',
'date_depuis'=>'depuis @delai@', 


//E
'email' => 'E-mail',
'envoi' => 'Envoi :',
'envoi_nouv' => 'Envoi des nouveaut&eacute;s',
'envoi_program' => 'Envoi programm&eacute;',
'envoi_texte' => 'Si ce message vous convient, vous pouvez l\'envoyer',
'exporter' => 'Exporter la liste d\'abonn&eacute;s',

//F
'faq' => 'FAQ',
'forum' => 'Forum',
'ferme' => 'Cette discussion est cl&ocirc;tur&eacute;e',
'form_forum_identifiants' => 'Confirmation',
'form_forum_identifiant_confirm'=>'Votre abonnement est enregistr&eacute;, vous allez recevoir un mail de confirmation.',
'format' => 'Format',

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
'jours' => 'jours',

//L
'Listes_de_diffusion' => 'Listes de diffusion',
'login' => 'Connexion',
'logout' => 'D&eacute;connexion',
'lot_suivant' => 'Provoquer l\'envoi du lot suivant',
'lieu' => 'Localisation',
'lettre_d_information' => 'Lettre d\'information',

//M
'mail_format' => 'Vous &ecirc;tes abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@ en format',
'mail_non' => 'Vous n\'&ecirc;tes pas abonn&eacute; &agrave; la lettre d\'information du site @nom_site_spip@',
'message_arch' => 'Message archiv&eacute;',
'messages_auto' => 'Messages automatiques',
'messages_auto_texte' => '<p>Par d&eacute;faut, le squelette des nouveaut&eacute;s permet d\'envoyer automatiquement la liste des articles et br&egrave;ves publi&eacute;s sur le site depuis le dernier envoi automatique. </p><p>vous pouvez personnaliser le message en d&eacute;finissant l\'adresse d\'un logo et d\'une image de fond pour les titres de parties en &eacute;ditant le fichier nomm&eacute; <b>"nouveautes.html"</b> (situ&eacute; &agrave; la racine de votre site Spip).</p>',
'message_redac' => 'En cours de r&eacute;daction et pr&ecirc;t &agrave; l\'envoi',
'message_en_cours' => 'Message en cours d\'envoi',
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
'messages' => 'Messages',
'Messages_automatiques' => 'Messages automatiques programm&eacute;s',
'messages_derniers' => 'Derniers Messages',
'messages_forum_clos' => 'Forum d&eacute;sactiv&eacute;',
'messages_nouveaux' => 'Nouveaux messages',
'messages_pas_nouveaux' => 'Pas de nouveaux messages',
'messages_non_lus_grand' => 'Pas de nouveaux messages',
'messages_repondre' => 'Nouvelle R&eacute;ponse',
'messages_voir_dernier' => 'Voir le dernier message',
'moderateurs' => 'Mod&eacute;rateur(s)',
'modifier' => 'Modifier',

//n
'nom' => 'Nom d\'utilisateur',
'Nouveau_courrier' => 'Nouveau courrier',
'nouveaux_messages' => 'Nouveaux messages',
'Nouvelle_liste_de_diffusion' => 'Nouvelle liste de diffusion',

//P
'par_date' => 'Por fecha de inscripci&oacute;n',
'patron_disponibles' => 'Patrons disponibles',
'Patrons' => 'Patrons',
'poster' => 'Poster un Message',

//R
'recherche' => 'Rechercher',
'revenir_haut' => 'Revenir en haut de la page',
'reponse' => 'En r&eacute;ponse au message',
'retour' => 'Adresse email du gestionnaire de la liste (reply-to)',

//S
'suivi' => 'Suivi des abonnements',
'Suivi_des_abonnements' => 'Suivi des abonnements',
'sujet_nouveau' => 'Nouveau sujet',
'sujet_auteur' => 'Auteur',
'sujet_visites' => 'Visites',
'sujets' => 'Sujets',
'sujets_aucun' => 'Pas de sujet dans ce forum pour l\'instant',
'site' => 'Site web',
'sujet_clos_titre' => 'Sujet Clos',
'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.',
 
 //T
'texte_boite_en_cours' => 'SPIP-Listes envoie un message automatique en ce moment. <p> Vous pouvez provoquer l\'envoi acc&eacute;l&eacute;r&eacute; des lots gr&acirc;ce au lien ci-dessous.</p> <p>Cette boite disparaitra une fois l\'envoi achev&eacute;.</p>',
'texte_lettre_information' => 'Voici la lettre d\'information de ',
'Tous_les' => 'Tous les',

//V
'voir' => 'voir',

// ====================== spip_listes.php3 ======================
'abon' => 'ABONNES',
'abon_ajouter' => 'AJOUTER UN ABONNE &nbsp; ',
'abonees' => 'Tous les abonn&eacute;s',
'abonne_listes' => 'Ce contact est abonn&eacute; aux listes suivantes',
'abonnement_simple' => '<b>Abonnement simple : </b><br /><i>Les abonn&eacute;s re&ccedil;oivent un message de confirmation apr&egrave;s leur abonnement</i>',
'abonnement_code_acces' => '<b>Abonnement avec codes d\'acc&egrave;s : </b><br /><i>Les abonn&eacute;s re&ccedil;oivent en plus un login et un mot de passe qui leur permettront de s\'identifier sur le site. </i>',
'abonnement_newsletter' => '<b>Abonnement &agrave; la lettre d\'information</b>',
'acces_a_la_page' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.',
'adresse_deja_inclus' => 'L\'adresse est d&eacute;j&agrave; connue',
'autorisation_inscription' => 'SPIP-listes vient d\'activer l\'autorisation de s\'inscrire aux visiteurs du site',

'choisir' => 'Choisir',
'choisir_cette' => 'Choisir cette liste',
'confirme_envoi' => 'Veuillez confirmer l\'envoi',

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
'email_collec' => 'E-mail collectif',
'email_test' => 'Envoyer un email de test',
'email_test_liste' => 'Envoyer vers une liste de diffusion',
'email_tester' => 'Tester par email',
'env_esquel' => 'Envoi programm&eacute; du patron',
'env_maint' => 'Envoyer maintenant',
'envoyer' => 'envoyer le mail',
'envoyer_a' => 'Envoi vers',
'erreur' => 'Erreur',
'erreur_import' => 'Le fichier d\'import pr&eacute;sente une erreur &agrave; la ligne ',

'format_date' => 'Y/m/d',

'html' => 'HTML',

'importer' => 'Importer une liste d\'abonn&eacute;s',
'importer_fichier' => 'Importer un fichier',
'importer_fichier_txt' => '<p><b>Votre liste d\'abonn&eacute;s doit &ecirc;tre un fichier simple (texte) qui ne comporte qu\'une adresse e-mail par ligne</b></p>',
'importer_preciser' => '<p>Pr&eacute;cisez les listes et le format correspondant &agrave; votre import d\'abonn&eacute;s</p>',
'inconnu' => 'n\'est plus abonn&eacute; &agrave; la liste',

'liste_diff_publiques' => 'Listes de diffusion publiques<br /><i>La page du site public propose l\'inscription &agrave; ces listes.</i>',
'liste_sans_titre' => 'Liste sans titre',
'listes_internes' => 'Listes de diffusion internes<br /><i>Au moment de l\'envoi d\'un message, ces listes sont propos&eacute;es parmi les destinataires</i>',
'listes_poubelle' => 'Vos listes de diffusion &agrave; la poubelle',
'lock' => 'Lock actif : ',

'mail_a_envoyer' => 'Nombre de mails &agrave; envoyer : ',
'mail_tache_courante' => 'Mails envoy&eacute;s pour la t&acirc;che courante : ',
'messages_auto_envoye' => 'Messages automatiques envoy&eacute;s',
'message_en_cours' => 'Un message est en cours d\'envoi',
'message_presque_envoye' =>'Ce message est sur le point d\'&ecirc;tre envoy&eacute;.',
'mode_inscription' => 'Param&eacute;trer le mode d\'inscription des visiteurs',
'modif_envoi' => 'Vous pouvez le modifier ou demander son envoi.',
'modifier_liste' => 'Modifier cette liste :',

'nb_abonnes' => 'Dans les listes : ',
'nb_inscrits' => 'Dans le site :  ',
'nb_listes' => 'Incriptions dans toutes les listes : ',
'non_program' => 'Il n\'y a pas de message automatique programm&eacute; pour cette liste.',
'nouvelle_abonne' => 'L\'abonn&eacute; suivant a &eacute;t&eacute; ajout&eacute; la liste',

'pas_acces' => 'Vous n\'avez pas acc&egrave;s &agrave; cette page.',
'plus_abonne' => ' n\'est plus abonn&eacute; &agrave; la liste ',
'prochain_envoi_aujd' => 'Prochain envoi pr&eacute;vu aujourd\'hui',
'prochain_envoi_prevu' => 'Prochain envoi pr&eacute;vu',
'prochain_envoi_prevu_dans' => 'Prochain envoi pr&eacute;vu dans',
'prog_env' => 'Programmer un envoi automatique',
'prog_env_non' => 'Ne pas programmer d\'envoi',
'program' => 'Programmation des messages automatiques',

'remplir_tout' => 'Tous les champs doivent &ecirc;tre remplis',
'repartition' => 'R&eacute;partition',
'retour_link' => 'Retour',

'sans_envoi' => 'Attention, l\'adresse email de test que vous avez fournie ne correspond &agrave; aucun abonn&eacute;, <br />l\'envoi ne peut se faire, veuillez reprendre la proc&eacute;dure<br /><br />',
'squel' => 'Patron : &nbsp;',
'statut_interne' => 'Interne',
'statut_publique' => 'Publique',
'suivi_envois' => 'Suivi des envois',
'supprime_contact' => 'Supprimer ce contact d&eacute;finitivement',
'supprime_contact_base' => 'Supprimer d&eacute;finitivement de la base',

'tableau_bord' => 'Tableau de bord',
'texte' => 'texte',
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
'confirmation_liste_unique_2' =>'Vous avez choisi de recevoir les messages adress&eacute;s &agrave la liste suivante :',
'confirmation_listes_multiples_1' => 'Vous &ecirc;tes abonn&eacute; aux listes d\'informations du site ',
'confirmation_listes_multiples_2' => 'Vous avez choisi de recevoir les messages adress&eacute;s aux listes suivantes :',

'erreur_adresse' => 'Erreur, l\'adresse email que vous avez fournie n\'est pas valide',

'infos_liste' => 'Informations sur cette liste',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'Nombre de contacts',
'contacts_lot' => 'Contacts de ce lot',
'editeur' => 'Editeur : ',
'envoi_en_cours' => 'Envoi en cours',
'envoi_tous' => 'Envoi &agrave; destination de tous les inscrits',
'envoi_listes' => 'Envoi &agrave; destination des abonn&eacute;s &agrave; la liste : ',
'envoi_erreur' => 'Erreur : SPIP-listes ne trouve pas de destinataire pour ce message',
'email_reponse' => 'Email de r&eacute;ponse : ',
'envoi_annule' => 'Envoi annul&eacute;',
'envoi_fini' => 'Envois termin&eacute;s',
'erreur_destinataire' => 'Erreur destinataire : pas d\'envoi',
'erreur_sans_destinataire' => 'Erreur : aucun destinataire ne peut &ecirc;tre trouv&eacute; pour ce message',
'erreur_mail' => 'Erreur : envoi du mail impossible (v�rifier si mail() de php est disponible)',

'forcer_lot' => 'Provoquer l\'envoi du lot suivant',

'non_courrier' => 'Pas / plus de courrier &agrave; envoyer',
'non_html' => 'Votre logiciel de messagerie ne peut apparemment pas afficher correctement la version graphique (HTML) de cet e-mail',
'sans_adresse' => 'Mail non envoy&eacute; -> Veuillez d&eacute;finir une adresse de r&eacute;ponse',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Confirmer',

'lettre_info' => 'La lettre d\'information du site',

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
'inscription_liste' => 'Vous avez choisi de recevoir les messages adress&eacute;s &agrave; la liste suivante : ',
'inscription_listes' => 'Vous avez choisi de recevoir les messages adress&eacute;s aux listes suivantes : ',
'inscription_format' => ' en format ',

);

?>
