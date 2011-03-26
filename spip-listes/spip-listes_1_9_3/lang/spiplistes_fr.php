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
'voir_historique' => 'Voir l&#8217;historique des envois'
, 'pas_de_liste_prog' => "Aucune liste programm&#233;e."

// action/spiplistes_supprimer_abonne.php
// balise/formulaire_abonnement.php
// formulaires/spip_listes_inscriptions.php
, 'inscription_liste_f' => 'Vous avez choisi de recevoir les courriers adress&#233;s &#224; la liste suivante en format @f@ : '
, 'inscription_listes_f' => 'Vous avez choisi de recevoir les courriers adress&#233;s aux listes suivantes en format @f@ : '
, 'inscription_reponse_s' => 'Vous &#234;tes abonn&#233; &#224; la liste d&#8217;information du site @s@'
, 'inscription_reponses_s' => 'Vous &ecirc;tes abonn&#233; aux listes d&#8217;informations du site @s@'
, 'vous_abonne_aucune_liste' => 'Vous n&#8217;&ecirc;tes pas abonn&#233; &#224; une liste de diffusion'
, 'liste_dispo_site_' => 'Liste de diffusion disponible sur ce site : '
, 'listes_dispos_site_' => 'Listes de diffusion disponibles sur ce site : '
, 'desole_pas_de_liste' => 'D&#233;sol&#233;, il n&#8217;y a pas de liste de diffusion disponible pour le moment.'
, 'pour_vous_abonner' => 'Pour vous abonner aux listes de diffusion'
// obsolete
, 'abonnement_mail_passcookie' => '
	<br />
	Pour modifier votre abonnement aux lettres d&#8217;information du site <strong>@nom_site_spip@</strong> (@adresse_site@), 	
	veuillez vous rendre &#224; l&#8217;adresse suivante :<br /><br />
	<a href="@adresse_site@/spip.php?page=abonnement&d=@cookie@">@adresse_site@/spip.php?page=abonnement&d=@cookie@</a><br /><br />
	Vous pourrez alors confirmer la modification de votre abonnement.
	<br/>'
, 'bienvenue_sur_la_liste_' => 'Bienvenue sur les listes de diffusion du site '
, 'vos_abos_sur_le_site_' => 'Vos abonnements sur le site '
, 'votre_format_de_reception_' => 'Votre format de r&#233;ception '
, '_cliquez_lien_formulaire' => 'cliquez sur ce lien pour acc&#233;der au formulaire pr&#233;sent sur le site'
, 'pour_modifier_votre_abo_' => 'Pour modifier votre abonnement '
, 'abonnement_explication' => 'Entrez votre adresse email dans le champ ci-dessous et s&eacute;lectionnez la ou les listes auxquelles vous souhaitez vous abonner.'
, 'abonnement_presentation' => '
	Entrez votre adresse email dans le champ ci-dessous.
	Vous recevrez &#224; cette adresse un courrier de confirmation d&#8217;inscription et un lien.
	Ce lien vous permettra de s&#233;lectionner les listes de diffusion publi&#233;es ici.
	'
, 'confirmation_inscription' => 'Confirmation de votre inscription'
, 'suspendre_abonnement_' => 'Suspendre mon abonnement '
, 'vous_etes_redact' => 'Vous &#234;tes inscrit en tant que r&#233;dacteur.'
, 'vous_etes_membre' => 'Vous &#234;tes membre abonn&#233; aux listes de diffusion de ce site.
	Il est parfois n&#233;cessaire de s&#8217;authentifier pour avoir acc&#232;s &#224; ces listes.'
, 'saisie_erreurs' => 'Votre saisie contient des erreurs !'
, 'demande_ok' => 'Votre demande a bien &#233;t&#233; prise en compte. Vous recevrez prochainement une confirmation.'
, 'demande_ko' => 'D&#233;sol&#233;, mais une erreur a &#233;t&#233; rencontr&#233;e lors de l\'envoi de la confirmation d\'abonnement.
		SVP, essayez de vous inscrire plus tard.'
, 'champ_obligatoire' => 'Ce champ est obligatoire'

// balise/formulaire_modif_abonnement.php
, 'abonnement_modifie' => 'Vos modifications sont prises en compte'
, 'abonnement_nouveau_format' => 'Votre format de r&#233;ception est d&#233;sormais : '

// base/spiplistes_init.php
, 'autorisation_inscription' => 'SPIP-listes vient d&#8217;activer l&#8217;autorisation de s&#8217;inscrire aux visiteurs du site'

// base/spiplistes_tables.php
// base/spiplistes_upgrade.php
// docs/spiplistes_aide_fr.html
// exec/spiplistes_abonne_edit.php
, 'adresse_mail_obligatoire' => 'Adresse mail manquante. Abonnement impossible.'
, 'abonne_sans_format' => 'Ce compte est actuellement d&#233;sabonn&#233;. Aucun format de courrier n&#8217;est 
	d&#233;fini. Il ne peut pas recevoir de courrier. D&#233;finissez un format 
	de r&#233;ception pour ce compte afin de valider son abonnement.'
, 'Desabonner_temporaire' => 'D&#233;sabonner temporairement ce compte.'
, 'Desabonner_definitif' => 'D&#233;sabonner ce compte de toutes les listes de diffusion.'
, 'export_etendu_' => 'Export &#233;tendu '
, 'exporter_statut' => 'Exporter le statut (invit&#233;, r&#233;dacteur, etc.)'
, 'editer_fiche_abonne' => 'Editer la fiche de l&#8217;abonn&#233;'
, 'edition_dun_abonne' => 'Edition d&#8217;un abonn&#233;'
, 'format_de_reception' => 'Format de r&#233;ception' // + formulaire
, 'format_reception' => 'Format de r&#233;ception :'
, 'format_de_reception_desc' => 'Vous pouvez choisir un format global de r&#233;ception des courriers pour  
   cet abonn&#233;.<br /><br />
   Vous pouvez &#233;galement d&#233;sabonner temporairement ce contact. 
   Il reste inscrit dans les listes en tant que destinataire, mais les courriers 
   ne lui seront pas envoy&#233;s tant que vous ne lui aurez pas d&#233;fini un format de r&#233;ception de courriers.'
, 'mettre_a_jour' => '<h3>SPIP-listes va mettre &#224; jour</h3>'
, 'regulariser' => 'regulariser les desabonnes avec listes...<br />'
, 'Supprimer_ce_contact' => 'Supprimer ce contact'
, 'abonne_listes' => 'Ce contact est abonn&#233; aux listes suivantes'
, 'n_duplicata_mail' => '@n@ duplicata(s)'
, 'n_incorrect_mail' => '@n@ incorrect(s)'

// exec/spiplistes_abonnes_tous.php
, 'repartition_abonnes' => 'R&#233;partition des abonnements'
, 'abonnes_titre' => 'Abonn&#233;s'
, 'chercher_un_auteur' => 'Chercher un auteur'
, 'une_inscription' => 'Un abonn&#233; trouv&#233;'
, 'suivi' => 'Suivi des abonnements' // + presentation
, 'abonne_aucune_liste' => 'Abonn&#233;s &#224; aucune liste'
, 'format_aucun' => 'Aucun'
, 'repartition_formats' => 'R&#233;partition des formats'

// exec/spiplistes_aide.php
// exec/spiplistes_autocron.php
// exec/spiplistes_config.php
, 'personnaliser_le_courrier' => 'Personnaliser le courrier'
, 'personnaliser_le_courrier_desc' => 
	'Vous pouvez personnaliser le courrier pour chaque abonn&#233; en ins&#233;rant 
   dans votre patron les tags n&#233;cessaires. Par exemple, pour ins&#233;rer 
   le nom de votre abonn&#233; dans son courrier lors de l&#8217;envoi, placez dans 
   votre patron _AUTEUR_NOM_ (notez le tiret bas en d&#233;but et fin de tag).'
, 'formulaire_abonnement' => 'Formulaire <em>Abonnement</em>'
, 'formulaire_abonnement_effet' => 'Appliquer l&#8217;effet plier/d&#233;plier sur les textes descriptifs des listes de diffusion.'
, 'utiliser_smtp' => 'Utiliser SMTP'
, 'requiert_identification' => 'Requiert une identification'
, 'adresse_smtp' => 'Adresse email du <em>sender</em> SMTP'
, '_aide_install' => '<p>Bienvenue dans le monde de SPIP-Listes.</p>
	<p class="verdana2">Par d&#233;faut, &#224; l&#8217;installation, SPIP-Listes est en mode <em>simulation 
	d&#8217;envoi</em> afin de vous permettre de d&#233;couvrir les fonctionnalit&#233;s 
	et d&#8217;effectuer vos premiers tests.</p>
	<p class="verdana2">Pour valider les diff&#233;rentes options de SPIP-Listes, rendez-vous <a href="@url_config@">sur 
	la page de configuration</a>.</p>'
, 'adresse_envoi_defaut' => 'Adresse d&#8217;envoi par d&#233;faut'
, 'adresse_email_reply_to' => 'Adresse de retour (reply-to)'
, 'adresse_on_error_defaut' => 'Adresse de retour par d&#233;faut pour les erreurs (return-path)'
, 'pas_sur' => '<p>Si vous n&#8217;&ecirc;tes pas s&ucirc;r, choisissez la fonction mail de PHP.</p>'
, 'Complement_des_courriers' => 'Compl&#233;ment des courriers'
, 'Complement_lien_en_tete' => 'Lien sur le courrier'
, 'Complement_ajouter_lien_en_tete' => 'Ajouter un lien en en-t&ecirc;te du courrier'
, 'Complement_lien_en_tete_desc' => 'Cette option vous permet de rajouter en t&ecirc;te du courrier HTML envoy&#233; le lien 
   du courrier original pr&#233;sent sur votre site.'
, 'completer_titre_courrier_nom_site' => 'Compl&#233;ter le titre du courrier'
, 'completer_titre_courrier_nom_site_desc' => 'Le titre des listes est
	automatiquement compl&#233;t&#233; par le nom du site.'
, 'Complement_tampon_editeur' => 'Tampon Editeur'
, 'Complement_tampon_editeur_desc' => '
	Le tampon &#233;diteur est un petit bloc texte
	ajout&#233; automatiquement en fin de courrier au moment de l&#8217;envoi.<br />
	Ce bloc est un petit patron modifiable, configurable, compos&#233;
	du nom de l&#39;&#233;diteur, de ses coordonn&#233;es, voire de son logotype.
	'
, 'Complement_tampon_editeur_label' => 'Ajouter le tampon Editeur en fin de courrier'
, 'Envoi_des_courriers' => 'Envoi des courriers'
, 'log_console' => 'Console'
, 'log_console_debug' => 'Mode debug'
, 'log_console_debug_activer' => 'Activer le mode debug (verbeux. A d&#233;sactiver si inutile)'
, 'log_details_console' => 'D&#233;tails de la console'
, 'log_voir_destinataire' => 'Lister les adresses email des destinataires dans la console lors de l&#8217;envoi.'
, 'log_console_syslog_desc' => 'Vous &ecirc;tes sur un r&#233;seau local (@IP_LAN@). Si besoin, vous pouvez activer la console sur syslog au lieu des journaux SPIP (conseill&#233; sous unix).'
, 'log_console_syslog_texte' => 'Activer les journaux syst&egrave;mes (renvoi sur syslog)'
, 'log_console_syslog' => 'Console syslog'
, 'log_voir_le_journal' => 'Voir le journal de SPIP-Listes'
, 'log_configurer' => 'Configurer la console'
, 'recharger_journal' => 'Recharger le journal'
, 'fermer_journal' => 'Fermer le journal'
, 'methode_envoi' => 'M&#233;thode d&#8217;envoi'
, 'mode_suspendre_trieuse' => 'Suspendre le traitement des envois des listes de diffusion'
, 'Suspendre_le_tri_des_listes' => 'Cette option vous permet - en cas d&#8217;engorgement - de suspendre le traitement des 
	listes de diffusion programm&#233;es et de red&#233;finir les param&egrave;tres 
	d&#8217;envoi. D&#233;sactivez ensuite cette option pour reprendre le traitement des 
	listes de diffusion programm&#233;es.'
, 'mode_suspendre_meleuse' => 'Suspendre l&#8217;envoi des courriers'
, 'suspendre_lenvoi_des_courriers' => 'Cette option vous permet - en cas d&#8217;engorgement 
	- d&#8217;annuler l&#8217;envoi des courriers. D&#233;sactivez ensuite cette option pour 
	reprendre les exp&#233;ditions en cours. '
, 'nombre_lot' => 'Nombre d&#8217;envois par lot'
, 'php_mail' => 'Utiliser la fonction mail() de PHP'
, 'patron_du_tampon_' => 'Patron du tampon : '
, 'Patron_de_pied_' => 'Patron de pied '
, 'personnaliser_le_courrier_label' => 'Activer la personnalisation du courrier'
, 'parametrer_la_meleuse' => 'Param&#233;trer la meleuse'
, 'smtp_hote' => 'H&ocirc;te'
, 'smtp_port' => 'Port'
, 'simulation_desactive' => 'Mode simulation d&#233;sactiv&#233;.'
, 'simuler_les_envois' => 'Simuler les envois de courriers'
, 'abonnement_simple' => '<strong>Abonnement simple : </strong><br /><em>Les abonn&#233;s re&ccedil;oivent un message 
	de confirmation apr&egrave;s leur abonnement</em>'
, 'abonnement_code_acces' => '<strong>Abonnement avec codes d&#8217;acc&egrave;s : </strong><br /><i>Les abonn&#233;s 
	re&ccedil;oivent en plus un login et un mot de passe qui leur permettront de s&#8217;identifier sur le site. </i>'
, 'mode_inscription' => 'Param&#233;trer le mode d&#8217;inscription des visiteurs'

// exec/spiplistes_courrier_edit.php
, 'Generer_le_contenu' => 'G&#233;n&#233;rer le contenu'
, 'Langue_du_courrier_' => 'Langue du courrier :'
, 'generer_Apercu' => 'G&#233;n&#233;rer et Aper&ccedil;u'
, 'a_partir_de_patron' => 'A partir d&#8217;un patron'
, 'avec_introduction' => 'Avec texte d&#8217;introduction'
, 'calcul_patron_attention' => 'Certains patrons ins&egrave;rent dans leur r&#233;sultat le texte ci-dessous (Texte du courrier). 
	Si vous faites une mise &#224; jour de votre courrier, pensez &#224; vider cette boîte avant de g&#233;n&#233;rer le contenu.'
, 'charger_patron' => 'Choisir un patron pour le courrier'
, 'Courrier_numero_' => 'Courrier num&#233;ro :' // + _gerer
, 'Creer_un_courrier_' => 'Cr&#233;er un courrier :'
, 'choisir_un_patron_' => 'Choisir un patron '
, 'Courrier_edit_desc' => 'Vous pouvez choisir de g&#233;n&#233;rer automatiquement le contenu du courrier
	ou r&#233;diger simplement votre courrier dans la bo&icirc;te <strong>texte du courrier</strong>.'
, 'Contenu_a_partir_de_date_' => 'Contenu &#224; partir de cette date '
, 'Cliquez_Generer_desc' => 'Cliquez sur <strong>@titre_bouton@</strong> pour injecter le r&#233;sultat 
	dans la bo&icirc;te @titre_champ_texte@.'
, 'Lister_articles_de_rubrique' => 'Et lister les articles de la rubrique '
, 'Lister_articles_mot_cle' => 'Et lister les articles du mot-cl&#233; '
, 'edition_du_courrier' => 'Edition du courrier' // + gerer
, 'generer_un_sommaire' => 'G&#233;n&#233;rer un sommaire'
, 'generer_patron_' => 'G&#233;n&#233;rer le patron '
, 'generer_patron_avant' => 'avant le sommaire'
, 'generer_patron_apres' => 'apr&egrave;s le sommaire.'
, 'introduction_du_courrier_' => 'Introduction &#224; votre courrier, avant le contenu issu du site '
, 'Modifier_un_courrier__' => 'Modifier un courrier :'
, 'Modifier_ce_courrier' => 'Modifier ce courrier'
, 'sujet_courrier' => '<strong>Sujet du courrier</strong> [obligatoire]'
, 'texte_courrier' => '<strong>Texte du courrier</strong> (HTML autoris&#233;)'
, 'avec_patron_pied__' => 'Avec le patron de pied : '

// exec/spiplistes_courrier_gerer.php
, 'Erreur_Adresse_email_invalide' => 'Erreur: l&#8217;adresse email que vous avez fournie n&#8217;est pas valide'
, 'langue_' => '<strong>Langue :</strong>&nbsp;'
, 'calcul_patron' => 'Calcul avec le patron version texte'
, 'calcul_html' => 'Calcul depuis la version HTML du message'
, 'dupliquer_ce_courrier' => 'Dupliquer ce courrier'
, 'destinataire_sans_format_alert' => 'Destinataire sans format de r&#233;ception.
	Appliquez un format de r&#233;ception (texte ou html) pour ce compte ou s&#233;lectionnez un autre destinataire.'
, 'envoi_date' => 'Date de l&#8217;envoi : '
, 'envoi_debut' => 'Debut de l&#8217;envoi : '
, 'envoi_fin' => 'Fin de l&#8217;envoi : '
, 'erreur_envoi' => 'Nombre d&#8217;envois en erreur : '
, 'Erreur_liste_vide' => 'Erreur: cette liste n&#8217;a pas d&#8217;abonn&#233;s.'
, 'Erreur_courrier_introuvable' => 'Erreur: ce courrier n&#8217;existe pas.' // + previsu
, 'Envoyer_ce_courrier' => 'Envoyer ce courrier'
, 'format_html__n' => 'Format html : @n@'
, 'format_texte__n' => 'Format texte : @n@'
, 'message_arch' => 'Courrier archiv&#233;'
, 'message_en_cours' => 'Courrier en cours d&#8217;envoi'
, 'message_type' => 'Courrier &#233;lectronique'
, 'sur_liste' => 'Sur la liste' // + casier
, 'Supprimer_ce_courrier' => 'Supprimer ce courrier'
, 'email_adresse' => 'Adresse email de test' // + liste
, 'email_test' => 'Envoyer un email de test'
, 'Erreur_courrier_titre_vide' => 'Erreur: votre courrier n&#8217;a pas de titre.'
, 'message_en_cours' => 'Ce courrier est en cours de r&#233;daction'
, 'modif_envoi' => 'Vous pouvez le modifier ou demander son envoi'
, 'message_presque_envoye' =>'Ce courrier est sur le point d&#8217;&ecirc;tre envoy&#233;'
, 'Erreur_Adresse_email_inconnue' => 'Attention, l&#8217;adresse email de test que vous avez fournie ne correspond &#224; 
	aucun abonn&#233;, <br />l&#8217;envoi ne peut se faire, veuillez reprendre la proc&#233;dure<br /><br />'

// exec/spiplistes_courrier_previsu.php
, 'lettre_info' => 'La lettre d&#8217;information du site'

// exec/spiplistes_courriers_casier.php
// exec/spiplistes_import_export.php
, 'Exporter_une_liste_d_abonnes' => 'Exporter une liste d&#8217;abonn&#233;s'
, 'Exporter_une_liste_de_non_abonnes' => 'Exporter une liste de non abonn&#233;s'
, '_aide_import' => 'Vous pouvez importer ici une liste d&#8217;abonn&#233;s &#224; partir de votre 
   ordinateur.<br />
	Cette liste d&#8217;abonn&#233;s doit &ecirc;tre au format texte seul, une ligne 
   par abonn&#233;. Chaque ligne doit &ecirc;tre compos&#233;e ainsi :<br />
	<tt style="display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;">adresse@mail<span style="color:#f66">[separateur]</span>login<span style="color:#f66">[separateur]</span>nom</tt>
	<tt style="color:#f66">[separateur]</tt> est un caract&egrave;re de tabulation ou un point-virgule.<br /><br />
	L&#8217;adresse email doit &ecirc;tre unique, ainsi que le login. Si cette adresse 
   email ou ce login existent dans la base du site, la ligne sera rejet&#233;e.<br />
	Le premier champ adresse@mail est obligatoire. Les deux autres champs peuvent 
   &ecirc;tre ignor&#233;s (vous pouvez importer des listes issues des anciennes versions de SPIP-Listes).'
, 'annuler_envoi' => 'Annuler l&#8217;envoi' // + _gerer
, 'envoi_patron' => 'Envoi du patron'
, 'import_export' => 'Import / Export'
, 'incorrect_ou_dupli' => ' (incorrect ou dupli)'
, 'membres_liste' => 'Liste des Membres'
, 'Messages_automatiques' => 'Courriers automatiques programm&#233;s'
, 'Pas_de_liste_pour_import' => 'Vous devez cr&#233;er au moins une liste de destination afin de pouvoir importer 
	vos abonn&#233;s.'
, 'Resultat_import' => 'R&#233;sultat import'
, 'Selectionnez_une_liste_pour_import' => 'Vous devez s&#233;lectionner au moins une liste de diffusion pour pouvoir importer 
	les abonn&#233;s.'
, 'Selectionnez_une_liste_de_destination' => 'S&#233;lectionnez une ou plusieurs listes de destination pour vos abonn&#233;s.'
, 'Tous_les_s' => 'Tous les @s@'
, 'Toutes_les_semaines' => 'Toutes les semaines'
, 'Tous_les_mois' => 'Tous les mois, '
, 'Tous_les_ans' => 'Tous les ans'
, 'version_html' => '<strong>Version HTML</strong>'
, 'version_texte' => '<strong>Version texte</strong>'
, 'erreur_import' => 'Le fichier d&#8217;import pr&#233;sente une erreur &#224; la ligne '
, 'envoi_manuel' => 'Envoi manuel'
, 'format_date' => 'Y/m/d'
, 'importer' => 'Importer une liste d&#8217;abonn&#233;s'
, 'importer_fichier' => 'Importer un fichier'
, 'importer_fichier_txt' => '<p><strong>Votre liste d&#8217;abonn&#233;s doit &ecirc;tre un fichier simple (texte) 
	qui ne comporte qu&#8217;une adresse e-mail par ligne</strong></p>'
, 'importer_preciser' => '<p>Pr&#233;cisez les listes et le format correspondant &#224; votre import d&#8217;abonn&#233;s</p>'
, 'prochain_envoi_prevu' => 'Prochain envoi pr&#233;vu' // + gerer
, 'option_import_' => 'Option d&#8217;importation '
, 'forcer_abos_' => 'Forcer les abonnements (si l&#8217;adresse mail existe dans la base, forcer l&#8217;abonnement
	pour la s&#233;lection, pour cet abonn&#233;).'
, 'erreur_import_base' => 'Erreur importation. Data incorrect ou erreur base SQL.'
, 'erreur_n_fois' => '(erreur rencontree @n@ fois)'
, 'Liste_de_destination_s' => 'Liste de destination : @s@'
, 'Listes_de_destination_s' => 'Listes de destination : @s@'
, 'pas_dimport' => 'Pas d&#8217;import. Soit le fichier est vide, soit toutes les adresses sont d&#233;j&#224; abonn&#233;es.'
, 'nb_comptes_importees_en_ms_dont_' => '@nb@ fiches import&#233;es en @ms@ ms. dont : '
, 'nb_fiches_crees' => '@nb@ comptes cr&#233;&#233;s'
, 'nb_comptes_modifies' => '@nb@ comptes modifi&#233;s'
, 'nb_comptes_ignores' => '@nb@ comptes ignor&#233;s (d&#233;j&#224; dans la base)'
, 'format_de_reception_' => 'Format de r&#233;ception : '

// exec/spiplistes_liste_edit.php
, 'texte_dinsctription_' => 'Texte d&#8217;inscription : '
, 'Creer_une_liste_' => 'Cr&#233;er une liste '
, 'en_debut_de_semaine' => 'en d&#233;but de semaine'
, 'en_debut_de_mois' => 'en d&#233;but de mois'
, 'envoi_non_programme' => 'Envoi non programm&#233;'
, 'edition_dune_liste' => 'Edition d&#8217;une liste'
, 'texte_contenu_pied' => '<br />(Message ajout&#233; en bas de chaque email au moment de l&#8217;envoi)<br />'
, 'texte_pied' => '<p><strong>Texte du pied de page</strong>'
, 'modifier_liste' => 'Modifier cette liste '
, 'txt_abonnement' => 'Indiquez ici le texte pour l&#8217;abonnement &#224; cette liste, affich&#233; 
	sur le site public si la liste est active.'

// exec/spiplistes_liste_gerer.php
, 'forcer_les_abonnement_liste' => 'Forcer les abonnements pour cette liste'
, 'periodicite_tous_les_n_s' => 'P&#233;riodicit&#233; : tous les @n@ @s@'
, 'liste_sans_titre' => 'Liste sans titre'
, 'statut_interne' => 'Priv&#233;'
, 'statut_publique' => 'Publique'
, 'adresse' => 'Indiquez ici l&#8217;adresse &#224; utiliser pour les r&#233;ponses de mails 
	(&#224; d&#233;faut, l&#8217;adresse du webmestre sera utilis&#233;e comme adresse de r&#233;ponse).
	<br /><br />
	L&#8217;adresse peut &#234;tre de la forme :
	<ul style="margin-top:0">
	<li>webmaster@example.org</li>
	<li>Nom &#60;webmaster@example.org&#62;</li>
	</ul>'
, 'Ce_courrier_ne_sera_envoye_qu_une_fois' => 'Ce courrier ne sera envoy&#233; qu&#8217;une fois.'
, 'adresse_de_reponse' => 'Adresse de r&#233;ponse'
, 'adresse_mail_retour' => 'Adresse email du gestionnaire de la liste (reply-to)'
, 'Attention_action_retire_invites' => 'Attention: cette action retire les invit&#233;s de la liste des abonn&#233;s.'
, 'A_partir_de' => '&#224; partir de'
, 'Apercu_plein_ecran' => 'Aper&ccedil;u plein &#233;cran dans une nouvelle fen&ecirc;tre'
, 'Attention_suppression_liste' => 'Attention ! Vous demandez la suppression d&#8217;une liste de diffusion. 
	Les abonn&#233;s seront retir&#233;s de cette liste de diffusion automatiquement. '
, 'Abonner_tous_les_invites_public' => 'Abonner tous les membres invit&#233;s &#224; cette liste publique.'
, 'Abonner_tous_les_inscrits_prives' => 'Abonner tous les membres &#224; cette liste priv&#233;e, sauf les visiteurs.'
, 'boite_confirmez_envoi_liste' => 'Vous avez demand&#233; l&#8217;envoi imm&#233;diat de cette liste 
	de diffusion.<br />
	Svp, veuillez confirmer votre demande.'
, 'cette_liste_est_' => 'Cette liste est : @s@'
, 'Confirmer_la_suppression_de_la_liste' => 'Confirmer la suppression de la liste '
, 'Confirmez_requete' => 'Veuillez confirmer la requ&ecirc;te.'
, 'date_expedition_' => 'Date d&#8217;exp&#233;dition '
, 'Dernier_envoi_le_' => 'Dernier envoi le :'
, 'forcer_abonnement_desc' => 'Vous pouvez forcer ici les abonnements &#224; cette liste, soit pour tous 
   les membres inscrits (visiteurs, auteurs et administrateurs), soit pour tous 
   les visiteurs.'
, 'forcer_abonnement_aide' => '<strong>Attention</strong>: un membre abonn&#233; ne re&ccedil;oit pas forc&#233;ment 
   le courrier de cette liste. Il faut attendre qu&#8217;il confirme lui-m&ecirc;me 
   le format de r&#233;ception : html ou texte seul.<br />
	Vous pouvez forcer le format par abonn&#233; <a href="@lien_retour@">sur la page du suivi des abonnements</a>'
, 'forcer_abonnements_nouveaux' => 'En s&#233;lectionnant l&#8217;option <strong>Forcer les abonnements au format...</strong>, 
	vous confirmez le format de r&#233;ception des nouveaux abonn&#233;s.
	Les anciens abonn&#233;s conservent leur pr&#233;f&#233;rence de r&#233;ception.'
, 'Forcer_desabonner_tous_les_inscrits' => 'D&#233;sabonner tous les membres inscrits pour cette liste.'
, 'gestion_dune_liste' => 'Gestion d&#8217;une liste'
, 'message_sujet' => 'Sujet '
, 'mods_cette_liste' => 'Les mod&#233;rateurs de cette liste'
, 'nbre_abonnes' => 'Nombre d&#8217;abonn&#233;s : '
, 'nbre_mods' => 'Nombre de mod&#233;rateurs : '
, 'patron_manquant_message' => 'Vous devez appliquer un grand patron avant de param&#233;trer l&#8217;envoi de cette 
	liste.'
, 'liste_sans_patron' => 'Liste sans patron.' // courriers_listes
, 'Patron_grand_' => 'Grand patron '
, 'sommaire_date_debut' => 'A partir de la date d&#233;finie ci-dessus'
, 'abos_cette_liste' => 'Les abonn&#233;s &#224; cette liste'
, 'confirme_envoi' => 'Veuillez confirmer l&#8217;envoi'
, 'env_esquel' => 'Envoi programm&#233; du patron'
, 'env_maint' => 'Envoyer maintenant'
, 'date_act' => 'Donn&#233;es actualis&#233;es'
, 'forcer_les_abonnements_au_format_' => 'Forcer les abonnements au format : '
, 'pas_denvoi_auto_programme' => 'Il n&#8217;y a pas d&#8217;envoi automatique planifi&#233; pour cette liste de diffusion.'
, 'Pas_de_periodicite' => 'Pas de p&#233;riodicit&#233;.'
, 'prog_env' => 'Programmer un envoi automatique'
, 'prog_env_non' => 'Ne pas programmer d&#8217;envoi'
, 'conseil_regenerer_pied' => '<br />Ce patron est issu d&#8217;une ancienne version de SPIP-Listes.<br />
	Conseil: s&#233;lectionnez &#224; nouveau le patron de pied pour prendre en compte le multilinguisme 
	et/ou la version &#8217;texte seul&#8217; du patron.'
, 'boite_alerte_manque_vrais_abos' => 'Il n&#8217;y a pas d&#8217;abonn&#233;s pour cette liste de diffusion,
	ou les abonn&#233;s n&#8217;ont pas de format de r&#233;ception.
	<br />
	Corrigez le format de r&#233;ception pour au moins un abonn&#233; avant de valider l&#8217;envoi.'	

// exec/spiplistes_listes_toutes.php
// exec/spiplistes_maintenance.php
, 'annulation_chrono_' => 'Annulation du chrono pour '
, 'conseil_sauvegarder_avant' => '<strong>Conseil</strong>: faire une sauvegarde de la base avant de confirmer la suppression 
   @objet@. L&#8217;annulation est impossible ici.'
, 'des_formats' => 'des formats'
, 'des_listes' => 'des listes'
, 'des_abonnements' => 'des abonnements'
, 'confirmer_supprimer_formats' => 'Supprimer les formats de r&#233;ception des abonn&#233;s.'
, 'maintenance_objet' => 'Maintenance @objet@'
, 'nb_abos' => 'qt.'
, 'pas_de_liste' => 'Aucune liste de type &laquo;envoi non programm&#233;&raquo;.'
, 'pas_de_format' => 'Aucun format de r&#233;ception d&#233;fini pour les abonn&#233;s.'
, 'pas_de_liste_en_auto' => 'Aucune liste de type &laquo;envoi programm&#233;&raquo; (chrono).'
, 'forcer_formats_' => 'Forcer le format de r&#233;ception '
, 'forcer_formats_desc' => 'Forcer le format de r&#233;ception pour tous les abonn&#233;s...'
, 'modification_objet' => 'Modification @objet@'
, 'Suppression_de__s' => 'Suppression de : @s@'
, 'suppression_' => 'Suppression @objet@'
, 'suppression_chronos_' => 'Supprimer les envois programm&#233;s (chrono) '
, 'suppression_chronos_desc' => 'Si vous supprimez son chrono, la liste n&#8217;est pas supprim&#233;e. Sa p&#233;riodicit&#233; 
	est conserv&#233;e mais l&#8217;envoi est suspendu. Pour r&#233;activer le chrono, il faut lui red&#233;finir une date de premier 
	envoi. '
, 'Supprimer_les_listes' => 'Supprimer les listes'
, 'Supprimer_la_liste' => 'Supprimer la liste...'
, 'Suspendre_abonnements' => 'Suspendre les abonnements pour ce compte'
, 'separateur_de_champ_' => 'S&#233;parateur de champ '
, 'separateur_tabulation' => 'tabulation (<code>\\t</code>)'
, 'separateur_semicolon' => 'point-virgule (<code>;</code>)'
, 'nettoyage_' => 'Nettoyage '
, 'confirmer_nettoyer_abos' => 'Confirmer le nettoyage de  la table des abonn&#233;s.'
, 'pas_de_pb_abonnements' => 'Pas d&#8217;erreur rencontr&#233;e sur la table des abonnements.'
, '_n_abos_' => ' @n@ abonnements '
, '_1_abo_' => ' 1 abonnement '
, 'aucun_abonmt' => 'aucun abonnement.'
, '_n_auteurs_' => ' @n@ auteurs '
, '_1_auteur_' => ' 1 auteur '
, 'abonnes' => 'abonn&#233;s'
, '_n_abonnes_' => ' @n@ abonn&#233;s '
, '1_abonne' => '1 abonn&#233;'
, 'aucun_abo' => 'aucun abonn&#233;.'


// exec/spiplistes_menu_navigation.php
// exec/spiplistes_voir_journal.php
// genie/spiplistes_cron.php
// inc/spiplistes_agenda.php
, 'boite_agenda_titre_' => 'Planning des diffusions '
, 'boite_agenda_legende' => 'Sur @nb_jours@ jours'
, 'boite_agenda_voir_jours' => 'Voir sur les @nb_jours@ jours coulants'

// inc/spiplistes_api.php
// inc/spiplistes_api_abstract_sql.php
// inc/spiplistes_api_courrier.php
// inc/spiplistes_api_globales.php
// inc/spiplistes_api_journal.php
, 'titre_page_voir_journal' => 'Journal de SPIP-Listes'
, 'mode_debug_actif' => 'Mode debug actif'

// inc/spiplistes_api_presentation.php
, '_aide' => '<p>SPIP-Listes permet d&#8217;envoyer un courrier ou des courriers automatiques &#224; des abonn&#233;s.</p>
	<p>Vous pouvez &#233;crire un texte simple, composer votre courrier en HTML ou appliquer un "patron" &#224; 
	votre courrier</p>
	<p>Via un formulaire d&#8217;inscription public, les abonn&#233;s d&#233;finissent eux-m&ecirc;mes leur statut d&#8217;abonnement, 
	les listes auxquelles ils s&#8217;abonnent et le format
	dans lequel ils souhaitent recevoir les courriers (HTML/texte). </p>
	<p>Tout courrier sera traduit automatiquement en format texte pour les abonn&#233;s qui en ont fait la demande.</p>
	<p><strong>Note :</strong><br />L&#8217;envoi des mails peut prendre quelques minutes : les lots partent peu &#224; 
	peu quand les utilisateurs parcourent le site public. Vous pouvez aussi provoquer manuellement l&#8217;envoi des lots 
	en cliquant sur le lien "suivi des envois" pendant un envoi.</p>'
, 'envoi_en_cours' => 'Envoi en cours'
, 'nb_destinataire_sing' => ' destinataire'
, 'nb_destinataire_plur' => ' destinataires'
, 'aucun_destinataire' => 'aucun destinataire'
, '1_liste' => '@n@ liste'
, 'n_listes' => '@n@ listes'
, 'utilisez_formulaire_ci_contre' => 'Utilisez le formulaire ci-contre pour activer/d&#233;activer cette option.'
, 'texte_boite_en_cours' => 'SPIP-Listes envoie un courrier.<p>Cette boite disparaitra une fois l&#8217;envoi achev&#233;.</p>'
, 'meleuse_suspendue_info' => 'L&#8217;envoi des courriers en attente d&#8217;exp&#233;dition est suspendu.'
, 'casier_a_courriers' => 'Casier &#224; courriers' // + courriers_casier
, 'Pas_de_donnees' => 'D&#233;sol&#233;, mais l&#8217;enregistrement demand&#233; n&#8217;existe pas dans la base de donn&#233;es.'
, '_dont_n_sans_format_reception' => ', dont @n@ sans format de r&#233;ception'
, 'mode_simulation' => 'Mode simulation'
, 'mode_simulation_info' => 'Le mode simulation est activ&#233;. La m&#233;leuse fait semblant d&#8217;envoyer le courrier. 
	En r&#233;alit&#233;, aucun courrier n&#8217;est exp&#233;di&#233;.'
, 'meleuse_suspendue' => 'Meleuse suspendue'
, 'Meleuse_reactivee' => 'M&egrave;leuse r&#233;activ&#233;e'
, 'nb_abonnes_sing' => ' abonn&#233;'
, 'nb_abonnes_plur' => ' abonn&#233;s'
, 'nb_moderateur_sing' => ' mod&#233;rateur'
, 'nb_moderateur_plur' => ' mod&#233;rateurs'
, 'aide_en_ligne' => 'Aide en ligne'

// inc/spiplistes_dater_envoi.php
, 'attente_validation' => 'attente validation'
, 'courrier_en_cours_' => 'Courrier en traitement '
, 'date_non_precisee' => 'Date non pr&#233;cis&#233;e'

// inc/spiplistes_destiner_envoi.php
, 'email_tester' => 'Tester par email'
, 'Choix_non_defini' => 'Pas de choix d&#233;fini.'
, 'Destination' => 'Destination'
, 'aucune_liste_dispo' => 'Aucune liste disponible.'

// inc/spiplistes_import.php
// inc/spiplistes_lister_courriers_listes.php
, 'Prochain_envoi_' => 'Prochain envoi '

// inc/spiplistes_listes_forcer_abonnement.php
// inc/spiplistes_listes_selectionner_auteur.php
, 'lien_trier_nombre' => 'Trier par nombre d&#8217;abonnements'
, 'Abonner_format_html' => 'Abonner au format HTML'
, 'Abonner_format_texte' => 'Abonner au format texte'
, 'ajouter_un_moderateur' => 'Ajouter un mod&#233;rateur '
, 'Desabonner' => 'D&#233;sabonner'
, 'Pas_adresse_email' => 'Pas d&#8217;adresse email'
, 'sup_mod' => 'Supprimer ce mod&#233;rateur'
, 'supprimer_un_abo' => 'Supprimer un abonn&#233; de cette liste'
, 'supprimer_cet_abo' => 'Supprimer cet abonn&#233; de cette liste' // + pipeline
, 'abon_ajouter' => 'Ajouter un abonn&#233; '
, '_au_format_s' => ' au format @s@'

// inc/spiplistes_mail.inc.php
// inc/spiplistes_meleuse.php
, 'erreur_sans_destinataire' => 'Erreur : aucun destinataire ne peut &ecirc;tre trouv&#233; pour ce courrier'
, 'envoi_annule' => 'Envoi annul&#233;'
, 'sans_adresse' => ' Mail non envoy&#233; -> Veuillez d&#233;finir une adresse de r&#233;ponse'
, 'erreur_mail' => 'Erreur : envoi du mail impossible (v&#233;rifier si mail() de php est disponible)'
, 'msg_abonne_sans_format' => 'format de reception manquant'
, 'modif_abonnement_html' => '<br />Cliquez ici pour modifier votre abonnement'
, 'modif_abonnement_text' => 'Pour modifier votre abonnement, veuillez vous rendre &#224; l&#8217;adresse suivante : '
, 'stop_abonnement_html' => 'Pour vous d&#233;sabonner, cliquez ici.'
, 'stop_abonnement_text' => 'Pour vous désabonner, cliquez sur ce lien: '
, 'erreur_queue_supprimer_courrier' => '@s@ premiere etiquette en erreur. id_courier = 0. Supprimer cette etiquette manuellement de la table spip_auteurs_courriers !'

// inc/spiplistes_naviguer_paniers.php
// inc/spiplistes_pipeline_I2_cfg_form.php
// inc/spiplistes_pipeline_affiche_milieu.php
, 'Adresse_email_obligatoire' => 'Une adresse email est obligatoire pour pouvoir vous abonner aux listes de diffusion. 
	Si vous d&#233;sirez profiter de ce service, merci de modifier votre fiche en compl&#233;tant ce champ. '
, 'Alert_abonnement_sans_format' => 'Votre abonnement est suspendu. Vous ne recevrez pas les courriers des listes de 
	diffusion list&#233;es ci-dessous. Pour recevoir &#224; nouveau le courrier 
	de vos listes pr&#233;f&#233;r&#233;es, choisissez un format de r&#233;ception 
	et validez ce formulaire. '
, 'abonnements_aux_courriers' => 'Abonnements aux courriers'
, 'Forcer_abonnement_erreur' => 'Erreur technique signal&#233;e lors de la modification d&#8217;une liste abonn&#233;e. 
	V&#233;rifiez cette liste avant de poursuivre votre op&#233;ration.'
, 'Format_obligatoire_pour_diffusion' => 'Pour confirmer l&#8217;abonnement de ce compte, vous devez s&#233;lectionner un format 
	de r&#233;ception.'
, 'Valider_abonnement' => 'Valider cet abonnement'
, 'vous_etes_abonne_aux_listes_selectionnees_' => 'Vous &ecirc;tes abonn&#233; aux listes s&#233;lectionn&#233;es '

// inc/spiplistes_pipeline_ajouter_boutons.php
// inc/spiplistes_pipeline_ajouter_onglets.php
// inc/spiplistes_pipeline_header_prive.php
// inc/spiplistes_pipeline_insert_head.php

// formulaires, patrons, etc.
, 'abo_1_lettre' => 'Liste de diffusion '
, 'abonnement_seule_liste_dispo' => 'Abonnement &#224; la seule liste disponible '
, 'abo_listes' => 'Abonnement'
, 'abonnement_0' => 'Abonnement'
, 'abonnement_titre_mail' => 'Modifier votre abonnement'
, 'votre_abo_listes' => 'Votre abonnement aux listes de diffusion'
, 'lire' => 'Lire'
, 'listes_de_diffusion_' => 'Listes de diffusion '
, 'jour' => 'jour'
, 'jours' => 'jours'
, 'abonnement_bouton' => 'Modifier votre abonnement'
, 'abonnement_cdt' => '<a href="http://bloog.net/?page=spip-listes">SPIP-Listes</a>'
, 'abonnement_change_format' => 'Vous pouvez changer de format de r&#233;ception ou vous d&#233;sabonner : '
, 'abonnement_texte_mail' => 'Indiquez ci-dessous l&#8217;adresse email sous laquelle vous vous &ecirc;tes 
	pr&#233;c&#233;demment enregistr&#233;. 
	Vous recevrez un email permettant d&#8217;acc&#233;der &#224; la page de modification de votre abonnement.'
, 'article_entier' => 'Lire l&#8217;article entier'
, 'form_forum_identifiants' => 'Confirmation'
, 'form_forum_identifiant_confirm' => 'Votre inscription est enregistr&#233;e. Vous allez recevoir un mail de confirmation.'
, 'demande_enregistree_retour_mail' => '
	Votre demande est enregistr&#233;e. Vous allez recevoir un mail de confirmation.
	'
, 'effectuez_modif_validez' => 'Effectuez les modifications souhait&#233;es pour votre abonnement, puis validez.'
, 'vous_etes_desabonne' => '
	Vous &#234;tes maintenant d&#233;sabonn&#233; aux listes de diffusion,
	mais votre inscription sur ce site est toujours valide. Pour revenir &#224; ce formulaire de modification
	d&#8217;abonnement, utilisez le lien qui vous a &#233;t&#233; envoy&#233; ou entrez &#224; nouveau votre
	adresse email dans le formulaire d&#8217;inscription.
	'
, 'inscription_mail_forum' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@)'
, 'inscription_mail_redac' => 'Voici vos identifiants pour vous connecter au site @nom_site_spip@ (@adresse_site@) 
	et &#224; l&#8217;interface de r&#233;daction (@adresse_site@/ecrire)'
, 'inscription_visiteurs' => 'L&acute;abonnement vous permet 
	d&acute;intervenir sur les forums r&#233;serv&#233;s aux visiteurs enregistr&#233;s et de recevoir 
	les lettres d&acute;informations.'
, 'inscription_redacteurs' => 'L&#8217;espace de r&#233;daction de ce site est ouvert aux visiteurs apr&#232;s inscription.
	Une fois enregistr&#233;, vous pourrez consulter les articles en cours de r&#233;daction, proposer des articles
	et participer &#224; tous les forums.  L&#8217;inscription permet &#233;galement d&#8217;acc&#233;der aux parties du 
	site en acc&#232;s restreint et de recevoir les lettres d&#8217;informations.'
, 'mail_non' => 'Vous n&#8217;&ecirc;tes pas abonn&#233; &#224; la lettre d&#8217;information du site @nom_site_spip@'
, 'messages_auto' => 'Courriers automatiques'
, 'nouveaute_intro' => 'Bonjour, <br />Voici les nouveaut&#233;s publi&#233;es sur le site'
, 'nom' => 'Nom d&#8217;utilisateur'
, 'texte_lettre_information' => 'Voici la lettre d&#8217;information de '
, 'vous_pouvez_egalement' => 'Vous pouvez &#233;galement'
, 'vous_inscrire_auteur' => 'vous inscrire en tant qu&#8217;auteur'
, 'voir_discussion' => 'Voir la discussion'
, 'inconnu' => 'n&#8217;est plus abonn&#233; &#224; la liste'
, 'infos_liste' => 'Informations sur cette liste'
, 'editeur' => 'Editeur : '
, 'html_description' => ' Texte enrichi (caract&egrave;res en gras ou en italique) parfois accompagn&#233; d&#8217;images'
, 'texte_brut' => 'Texte brut'
, 'vous_etes_abonne_aux_listes_' => 'Vous &ecirc;tes abonn&#233; aux listes de diffusion :'
, 'vous_etes_abonne_a_la_liste_' => 'Vous &ecirc;tes abonn&#233; &#224; la liste de diffusion :'
, 'votre_email' => 'Votre email'
, 'votre_email_' => 'Votre adresse email : '
, 'liste_inconnue' => 'liste inconnue'
, 'cet_email_pas_valide' => 'Cet email n&#8217;est pas valide'
, 'cet_email_deja_enregistre' => 'Cet email est d&#233;ja enregistr&#233;.'

// tableau items *_options
, 'Liste_de_destination' => 'Liste de destination'
, 'Listes_1_du_mois' => 'Publiques, 1<sup><small>er</small></sup> du mois.'
, 'Liste_diffusee_le_premier_de_chaque_mois' => 'Liste diffus&#233;e le premier de chaque mois. '
, 'Listes_autre' => 'Autre p&#233;riodicit&#233;'
, 'Listes_autre_periode' => 'Listes publiques autre pr&#233;riodicit&#233;'
, 'listes_privees_autre_periode' => 'Listes priv&#233;es autre pr&#233;riodicit&#233;'
, 'Listes_diffusion_prive' => 'Listes priv&#233;es'
, 'Liste_hebdo' => 'Liste hebdomadaire'
, 'Publiques_hebdos' => 'Publiques, hebdomadaires'
, 'Listes_diffusion_hebdo' => 'Listes publiques hebdomadaires'
, 'listes_privees_hebdo' => 'Listes priv&#233;es hebdomadaires'
, 'Liste_mensuelle' => 'Liste mensuelle'
, 'Publiques_mensuelles' => 'Publiques, mensuelles'
, 'Listes_diffusion_mensuelle' => 'Listes publiques mensuelles'
, 'listes_privees_mensuelle' => 'Listes priv&#233;es mensuelles'
, 'Listes_diffusion_publiques_desc' => 'L&#8217;abonnement &#224; ces listes est propos&#233; sur le site public.'
, 'Liste_annuelle' => 'Liste annuelle'
, 'Publiques_annuelles' => 'Publiques, annuelles'
, 'privees_annuelles' => 'Priv&#233;es, annuelles'
, 'Listes_diffusion_annuelle' => 'Listes publiques annuelles'
, 'listes_privees_annuelle' => 'Listes priv&#233;es annuelles'
, 'Listes_diffusion_publique' => 'Listes de diffusion publiques'
, 'Listes_diffusion_privees' => 'Listes de diffusion priv&#233;es'
, 'Listes_diffusion_privees_desc' => 'L&#8217;abonnement &#224; ces listes est r&#233;serv&#233;e aux administrateurs et auteurs du site.'
, 'Listes_diffusion_suspendue' => 'Listes de diffusion suspendues'
, 'Listes_diffusion_suspendue_desc' => ' '
, 'Courriers_en_cours_de_redaction' => 'Courriers en cours de r&#233;daction'
, 'Courriers_en_cours_denvoi' => 'Courriers en cours d&#8217;envoi'
, 'Courriers_prets_a_etre_envoye' => 'Courriers pr&ecirc;ts &#224; &ecirc;tre envoy&#233;s'
, 'Courriers_publies' => 'Courriers publi&#233;s'
, 'Courriers_auto_publies' => 'Courriers automatiques publi&#233;s'
, 'Courriers_stope' => 'Courriers stopp&#233;s en cours d&#8217;envoi'
, 'Courriers_vides' => 'Courriers annul&#233;s (vides)'
, 'Courriers_sans_destinataire' => 'Courriers sans destinataire (liste vide)'
, 'Courriers_sans_liste' => 'Courriers sans abonn&#233;s (liste manquante)'
, 'devenir_redac' => 'Devenir r&#233;dacteur pour ce site'
, 'devenir_membre' => 'Devenir membre du site'
, 'devenir_abonne' => 'Vous inscrire sur ce site'
, 'desabonnement_valid' => 'L&#8217;adresse suivante n&#8217;est plus abonn&#233;e &#224; la lettre d&#8217;information' 
, 'pass_recevoir_mail' => 'Vous allez recevoir un email vous indiquant comment modifier votre abonnement. '
, 'discussion_intro' => 'Bonjour, <br />Voici les discussions d&#233;marr&#233;es sur le site'
, 'En_redaction' => 'En r&#233;daction'
, 'En_cours' => 'En cours'
, 'editeur_nom' => 'Nom de l&#8217;&#233;diteur '
, 'editeur_adresse' => 'Adresse '
, 'editeur_rcs' => 'N&deg; RCS '
, 'editeur_siret' => 'N&deg; SIRET '
, 'editeur_url' => 'URL du site de l&#8217;&#233;diteur '
, 'editeur_logo' => 'URL (ou DATA URL sheme) du logotype de l&#8217;&#233;diteur '
, 'Envoi_abandonne' => 'Envoi abandonn&#233;'
, 'Liste_prive' => 'Liste priv&#233;e'
, 'Liste_publique' => 'Liste publique'
, 'message_redac' => 'En cours de r&#233;daction et pr&ecirc;t &#224; l&#8217;envoi'
, 'Prets_a_envoi' => 'Pr&ecirc;ts &#224; l&#8217;envoi'
, 'Publies' => 'Publi&#233;s'
, 'publies_auto' => 'Publi&#233;s (auto)'
, 'Stoppes' => 'Stopp&#233;s'
, 'Sans_destinataire' => 'Sans destinataire'
, 'Sans_abonnement' => 'Sans abonnement'
, 'sans_abonne' => 'sans abonn&#233;'
, 'sans_moderateur' => 'sans mod&#233;rateur'

// raccourcis des paniers
, 'aller_au_panier_' => 'Aller au panier '
, 'aller_aux_listes_' => 'Aller aux listes '
, 'Nouveau_courrier' => 'Cr&#233;er un nouveau courrier'
, 'Nouvelle_liste_de_diffusion' => 'Cr&#233;er une nouvelle liste de diffusion'
, 'trieuse_suspendue' => 'Trieuse suspendue'
, 'trieuse_suspendue_info' => 'Le traitement des listes de diffusion programm&#233;es est suspendu.'
, 'Trieuse_reactivee' => 'Trieuse r&#233;activ&#233;e'

// mots
, 'ajout' => 'Ajout'
, 'aucun' => 'aucun'
, 'Configuration' => 'Configuration'
, 'courriers' => 'Courriers'
, 'creation' => 'Cr&#233;ation'
, '_de_' => ' de '
, '_dont_' => ' dont '
, '_avec_' => ' avec '
, 'email' => 'E-mail'
, 'format' => 'Format'
, 'modifier' => 'Modifier'
, 'max_' => 'Max '
, 'Patrons' => 'Patrons'
, 'patron_' => 'Patron : '
, 'spiplistes' => 'SPIP-Listes'
, 'recherche' => 'Rechercher'
, 'retablir' => 'R&#233;tablir'
, 'site' => 'Site web'
, 'sujets' => 'Sujets'
, 'sup_' => 'Sup.'
, 'total' => 'Total '
, 'voir' => 'voir'
, 'Vides' => 'Vides'
, 'choisir' => 'Choisir'
, 'desabo' => 'd&#233;sabo'
, 'desabonnement' => 'D&#233;sabonnement&nbsp;'
, 'desabonnes' => 'D&#233;sabonn&#233;s'
, 'destinataire' => 'destinataire'
, 'destinataires' => 'Destinataires'
, 'erreur' => 'Erreur'
, 'html' => 'HTML'
, 'retour_link' => 'Retour'
, 'texte' => 'Texte'
, 'version' => 'version'
, 'fichier_' => 'Fichier '

, 'jquery_inactif' => 'jQuery non d&#233;tect&#233;. Merci de l&#8217;activer.'
, 'javascript_inactif' => 'Javascript inactif. Cette page ne peut pas fonctionner
	sans Javascript. Merci de l&#8217;activer.'


// description de la page abonnement
, 'description_page_abonnement' => 'Page sur laquelle les abonn&eacute;s sont dirig&eacute;s pour s\'inscrire ou modifier leur abonnement.'
, 'nom_page_abonnement' => 'Page d\'abonnement aux listes'

// noisettes
, 'noisette_formulaire_spip_listes_inscription' => 'Formulaire d\'inscription aux listes de diffusion'
, 'noisette_formulaire_gestion_abonnement' => 'Formulaire de gestion de l\'abonnement aux listes de diffusion'
, 'modifier_abonnement' => 'Modifier votre abonnement aux listes de diffusions'
, 'noisette_formulaire_inscription_gestion_abonnement' => 'Formulaire d\'inscription et gestion de l\'abonnement aux listes de diffusion'
, 'description_noisette_formulaire_inscription_gestion_abonnement' => 'Affiche le formulaire de gestion de l\'abonnement si un cookie est pr&eacute;sent ou si un auteur est identifi&eacute;, sinon affiche le formulaire d\'inscription.'
, 'lettres_information' => 'Lettres d\'information'



///////
// a priori, pas|plus utilise'
, 'supprime_contact_base' => 'Supprimer d&#233;finitivement de la base'
, 'forcer_lot' => 'Provoquer l&#8217;envoi du lot suivant'
, 'erreur_destinataire' => 'Erreur destinataire : pas d&#8217;envoi'
, 'contacts_lot' => 'Contacts de ce lot'
, 'envoi_fini' => 'Envois termin&#233;s'
, 'non_courrier' => 'Pas / plus de courrier &#224; envoyer'
, 'non_html' => 'Votre logiciel de messagerie ne peut apparemment pas afficher correctement la version graphique (HTML) de cet e-mail'
, 'envoi_erreur' => 'Erreur : SPIP-Listes ne trouve pas de destinataire pour ce courrier'
, 'email_reponse' => 'Email de r&#233;ponse : '
, 'envoi_listes' => 'Envoi &#224; destination des abonn&#233;s &#224; la liste : '
, 'confirmer' => 'Confirmer'
, 'listes_emails' => 'Lettres d&#8217;information'
, 'info_liste_1' => 'liste'
, 'bonjour' => 'Bonjour,' // deja dans SPIP
, 'envoi_tous' => 'Envoi &#224; destination de tous les inscrits'
, 'patron_detecte' => '<p><strong>Patron d&#233;tect&#233; pour la version texte</strong><p>'
, 'val_texte' => 'Texte'
, 'membres_sans_messages_connecte' => 'Vous n&#8217;avez pas de nouveaux messages'
, 'messages_derniers' => 'Derniers Messages'
, 'pas_abonne_en_ce_moment' => 'n&#8217;est pas abonn&#233;'
, 'reinitialiser' => 'reinitialiser'
, 'mail_a_envoyer' => 'Nombre de mails &#224; envoyer : '
, 'lettre_d_information' => 'Lettre d&#8217;information'
, 'desole' => 'D&#233;sol&#233;'
, 'Historique_des_envois' => 'Historique des envois'
, 'patron_disponibles' => 'Patrons disponibles'
, 'liste_diff_publiques' => 'Listes de diffusion publiques<br /><i>La page du site public propose l&#8217;inscription &#224; ces listes.</i>'
, 'messages_non_lus_grand' => 'Pas de nouveaux messages'
, 'messages_repondre' => 'Nouvelle R&#233;ponse'
, 'Liste_abandonnee' => 'Liste abandonn&#233;e'
, 'par_date' => 'Par date d&#8217;inscription'
, 'info_auto' => 'SPIP-Listes pour spip peut envoyer r&#233;guli&egrave;rement aux inscrits, l&#8217;annonce des derni&egrave;res nouveaut&#233;s du site (articles et br&egrave;ves r&#233;cemment publi&#233;s).'
, 'format2' => 'Format :'
, 'liste_des_abonnes' => 'Liste des abonn&#233;s'
, 'lieu' => 'Localisation'
, 'efface_base' => 'a &#233;t&#233; effac&#233; des listes et de la base'
, 'lot_suivant' => 'Provoquer l&#8217;envoi du lot suivant'
, 'listes_internes' => 'Listes de diffusion internes<br /><i>Au moment de l&#8217;envoi d&#8217;un courrier, ces listes sont propos&#233;es parmi les destinataires</i>'
, 'adresses_importees' => 'Adresses import&#233;es'
, 'aff_envoye' => 'Courriers envoy&#233;s'
, 'abonner' => 's&#8217;abonner'
, 'abonnes_liste_int' => 'Abonn&#233;s aux listes internes : '
, 'abonnes_liste_pub' => 'Abonn&#233;s aux listes publiques : '
, 'actualiser' => 'Actualiser'
, 'a_destination_de_' => '&#224; destination de '
, 'aff_lettre_auto' => 'Lettres des nouveaut&#233;s envoy&#233;es'
, 'alerte_edit' => 'Le formulaire ci-dessous permet de modifier le texte d&#8217;un courrier. 
	Vous pouvez choisir de commencer par importer un patron pour g&#233;n&#233;rer le contenu de votre message.'
, 'alerte_modif' => '<strong>Apr&egrave;s l&#8217;affichage de votre courrier, vous pourrez en modifier le contenu</strong>'
, 'lock' => 'Lock actif : '
, 'Apercu' => 'Aper&ccedil;u'
, 'bouton_listes' => 'Lettres d&#8217;information'
, 'bouton_modifier' => 'Modifier ce courrier'
, 'dans_jours' => 'dans'
, 'charger_le_patron' => 'G&#233;n&#233;rer le courrier'
, 'choix_defini' => 'Pas de choix d&#233;fini.\n'
, 'definir_squel_choix' => 'A la r&#233;daction d&#8217;un nouveau courrier, SPIP-Listes vous permet de charger un patron. 
	En appuyant sur un bouton, vous chargez dans le corps du courrier le contenu d&#8217;un des squelettes du 
	repertoire <strong>/patrons</strong> (situ&#233; &#224; la racine de votre site Spip). 
	<p><strong>Vous pouvez &#233;diter et modifier ces squelettes selon vos go&ucirc;ts.</strong></p> 
	<ul><li>Ces squelettes peuvent contenir du code HTML classique</li>
	<li>Ce squelette peut contenir des boucles Spip</li>
	<li>Apr&egrave;s le chargement du patron, vous pourrez re-&#233;diter le courrier avant envoi (pour ajouter du texte)</li>
	</ul><p>La fonction "charger un patron" permet donc d&#8217;utiliser des gabarits HTML personnalis&#233;s pour vos courriers 
	ou de cr&#233;er des lettres d&#8217;information th&#233;matiques dont le contenu est d&#233;fini gr&acirc;ce aux boucles Spip.</p>
	<p>Attention : ce squelette ne doit pas contenir de balises body, head ou html mais juste du code HTML ou des boucles Spip.</p>'
, 'definir_squel' => 'Choisir le mod&egrave;le de courrier &#224; pr&#233;visualiser'
, 'courrier_realise_avec_spiplistes' => 'Courrier r&#233;alis&#233; avec SPIP-Listes'
, 'definir_squel_texte' => 'Si vous disposez des codes d&#8217;acc&egrave;s au FTP, vous pouvez ajouter des squelettes SPIP dans le r&#233;pertoire /patrons (&#224; la racine de votre site Spip).'
, 'dernier_envoi' => 'Dernier envoi il y a'
, 'desabonnement_confirm' => 'Vous &ecirc;tes sur le point de r&#233;silier votre abonnement &#224; la lettre d&#8217;information'
, 'date_depuis' => 'depuis @delai@'
, 'envoi_charset' => 'Charset de l&#8217;envoi'
, 'envoi_nouv' => 'Envoi des nouveaut&#233;s'
, 'envoi_program' => 'Envoi programm&#233;'
, 'envoi_smtp' => 'Lors d&#8217;un envoi via la m&#233;thode SMTP ce champ d&#233;finit l&#8217;adresse de l&#8217;envoyeur.'
, 'envoi_texte' => 'Si ce courrier vous convient, vous pouvez l&#8217;envoyer'
, 'email_envoi' => 'Envoi des emails'
, 'envoi' => 'Envoi :'
, 'erreur_install' => '<h3>erreur: spip-listes est mal install&#233;!</h3>'
, 'erreur_install2' => '<p>V&#233;rifier les &#233;tapes d&#8217;installation, notamment si vous avez bien renomm&#233;<i>mes_options.txt</i> en <i>mes_options.php</i>.</p>'
, 'exporter' => 'Exporter la liste d&#8217;abonn&#233;s'
, 'Erreur_appel_courrier' => 'Erreur lors de l&#8217;appel du courrier'
, 'faq' => 'FAQ'
, 'forum' => 'Forum'
, 'ferme' => 'Cette discussion est cl&ocirc;tur&#233;e'
, 'gestion_du_courrier' => 'Gestion du courrier'
, 'info_heberg' => 'Certains h&#233;bergeurs d&#233;sactivent l&#8217;envoi automatique de mails depuis leurs serveurs. 
	Dans ce cas, les fonctionnalit&#233;s suivantes de SPIP-Listes pour SPIP ne fonctionneront pas'
, 'info_nouv' => 'Vous avez activ&#233; l&#8217;envoi des nouveaut&#233;s'
, 'info_nouv_texte' => 'Prochain envoi des nouveaut&#233;s dans @proch@ jours'
, 'log' => 'Logs'
, 'login' => 'Connexion'
, 'logout' => 'D&#233;connexion'
, 'mail_format' => 'Vous &ecirc;tes abonn&#233; &#224; la lettre d&#8217;information du site @nom_site_spip@ en format'
, 'messages_auto_texte' => '<p>Par d&#233;faut, le squelette des nouveaut&#233;s permet d&#8217;envoyer automatiquement 
	la liste des articles et br&egrave;ves publi&#233;s sur le site depuis le dernier envoi automatique. </p>
	<p>vous pouvez personnaliser le message en d&#233;finissant l&#8217;adresse d&#8217;un logo et d&#8217;une image de fond 
	pour les titres de parties en &#233;ditant le fichier nomm&#233; <strong>"nouveautes.html"</strong> 
	(situ&#233; &#224; dans le rep&#233;rtoire /dist).</p>'
, 'membres_groupes' => 'Groupes d&#8217;utilisateurs'
, 'membres_profil' => 'Profil'
, 'membres_messages_deconnecte' => 'Se connecter pour v&#233;rifier ses messages priv&#233;s'
, 'membres_avec_messages_connecte' => 'Vous avez @nombres@ nouveau(x) message(s)'
, 'message' => 'Message : '
, 'message_date' => 'Post&#233; le '
, 'messages' => 'Courriers'
, 'messages_forum_clos' => 'Forum d&#233;sactiv&#233;'
, 'messages_nouveaux' => 'Nouveaux messages'
, 'messages_pas_nouveaux' => 'Pas de nouveaux messages'
, 'messages_voir_dernier' => 'Voir le dernier message'
, 'moderateurs' => 'Mod&#233;rateur(s)'
, 'mis_a_jour' => 'Mis &#224; jour'
, 'nouveaux_messages' => 'Nouveaux messages'
, 'numero' => 'N&nbsp;'
, 'photos' => 'Photos'
, 'poster' => 'Poster un Message'
, 'publie' => 'Publi&#233; le'
, 'aucune_liste_publique' => 'Aucune liste de diffusion publique disponible.'
, 'revenir_haut' => 'Revenir en haut de la page'
, 'reponse' => 'En r&#233;ponse au message'
, 'reponse_plur' => 'r&#233;ponses'
, 'reponse_sing' => 'r&#233;ponse'
, 'retour' => 'Adresse email du gestionnaire de la liste (reply-to)'
, 'Suivi_des_abonnements' => 'Suivi des abonnements'
, 'sujet_nouveau' => 'Nouveau sujet'
, 'sujet_auteur' => 'Auteur'
, 'sujet_visites' => 'Visites'
, 'sujet_courrier_auto' => 'Sujet du courrier automatique : '
, 'sujets_aucun' => 'Pas de sujet dans ce forum pour l&#8217;instant'
, 'sujet_clos_titre' => 'Sujet Clos'
, 'sujet_clos_texte' => 'Ce sujet est clos, vous ne pouvez pas y poster.'
, 'masquer_le_journal_SPIPLISTES' => 'Masquer le journal de SPIP-Listes'
, 'abon' => 'LES ABONNES'
, 'abonees' => 'tous les abonn&#233;s'
, 'abonnement_newsletter' => '<strong>Abonnement &#224; la lettre d&#8217;information</strong>'
, 'acces_a_la_page' => 'Vous n&#8217;avez pas acc&egrave;s &#224; cette page.'
, 'adresse_deja_inclus' => 'Adresse d&#233;j&#224; connue'
, 'Choisir_cette_liste' => 'Choisir cette liste'
, 'Charger_un_patron' => 'Charger un patron'
, 'date_ref' => 'Date de r&#233;f&#233;rence'
, 'efface' => 'a &#233;t&#233; effac&#233; des listes et de la base'
, 'email_collec' => 'R&#233;diger un courrier'
, 'email_test_liste' => 'Envoyer vers une liste de diffusion'
, 'envoyer' => 'envoyer le mail'
, 'envoyer_a' => 'Envoi vers '
, 'listes_poubelle' => 'Vos listes de diffusion &#224; la poubelle'
, 'Liste_numero_:' => 'Liste num&#233;ro :'
, 'mail_tache_courante' => 'Mails envoy&#233;s pour la t&acirc;che courante : '
, 'messages_auto_envoye' => 'Courriers automatiques envoy&#233;s'
, 'nb_abonnes' => 'Dans les listes : '
, 'nb_inscrits' => 'Dans le site :  '
, 'nb_listes' => 'Incriptions dans toutes les listes : '
, 'nouvelle_abonne' => 'L&#8217;abonn&#233; suivant a &#233;t&#233; ajout&#233; la liste'
, 'pas_acces' => 'Vous n&#8217;avez pas acc&egrave;s &#224; cette page.'
, 'plus_abonne' => ' n&#8217;est plus abonn&#233; &#224; la liste '
, 'prochain_envoi_aujd' => 'Prochain envoi pr&#233;vu aujourd&#8217;hui'
, 'prochain_envoi_prevu_dans' => 'Prochain envoi pr&#233;vu dans '
, 'program' => 'Programmation des courriers automatiques'
, 'plein_ecran' => '(Plein &#233;cran)'
, 'remplir_tout' => 'Tous les champs doivent &ecirc;tre remplis'
, 'repartition' => 'R&#233;partition'
, 'squel' => 'Patron : &nbsp;'
, 'suivi_envois' => 'Suivi des envois'
, 'supprime_contact' => 'Supprimer ce contact d&#233;finitivement'
, 'tableau_bord' => 'Tableau de bord'
, 'toutes' => 'Tous les inscrits'
, 'acces_refuse' => 'Vous n&#8217;avez plus acc&egrave;s &#224; ce site'
, 'confirmation_format' => ' en format '
, 'confirmation_liste_unique_1' => 'Vous &ecirc;tes abonn&#233; &agrave la liste d&#8217;information du site'
, 'confirmation_liste_unique_2' =>'Vous avez choisi de recevoir les courriers adress&#233;s &agrave la liste suivante :'
, 'confirmation_listes_multiples_1' => 'Vous &ecirc;tes abonn&#233; aux listes d&#8217;informations du site '
, 'confirmation_listes_multiples_2' => 'Vous avez choisi de recevoir les courriers adress&#233;s aux listes suivantes :'
, 'contacts' => 'Nombre de contacts'
, 'patron_erreur' => 'Le patron sp&#233;cifi&#233; ne donne pas de r&#233;sulat avec les param&egrave;tres choisis'
, 'abonees_titre' => 'Abonn&#233;s'
, 'options' => 'radio|brut|Format :|Html,Texte,D&#233;sabonnement|html,texte,non'
, 'souhait_modifier_abo' => 'Vous souhaitez modifier votre abonnement.'

);
