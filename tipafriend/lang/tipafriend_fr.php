<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/tipafriend/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_activer' => 'Activer',
	'bouton_annuler' => 'Annuler',
	'bouton_desactiver' => 'D&eacute;sactiver',
	'bouton_envoyer' => 'Envoyer',
	'bouton_fermer' => 'Fermer',
	'bouton_reessayer' => 'Recommencer',
	'bouton_reset' => 'R&eacute;initialiser',
	'bouton_send_by_mail' => 'Envoyer par email',
	'bouton_send_by_mail_ttl' => 'Envoyer cette page par email',

	// C
	'cfg_legend_balise' => 'Concernant la balise "&#035;TIPAFRIEND"',
	'cfg_legend_patron' => 'Concernant les patrons de mail',
	'cfg_legend_squelette' => 'Concernant le formulaire d\'envoi',
	'cfg_texte_descr' => 'Le plugin ajoute &agrave; SPIP un module permettant d\'envoyer une page (<i>son contenu, son adresse ainsi qu\'un message</i>) &agrave; un ou plusieurs destinataires par e-mail.',
	'cfg_titre_descr' => 'Configuration du plugin <i>Tip A Friend</i>',
	'cfgform_comment_close_button' => 'active par d&eacute;faut, cette option vous permet de choisir de montrer ou non le bouton \'Fermer\' en bas de la fen&ecirc;tre ; <strong>cette option est automatiquement d&eacute;sactiv&eacute;e si les en-t&ecirc;tes sont eux-m&ecirc;mes d&eacute;sactiv&eacute;s ci-dessus</strong>.',
	'cfgform_comment_contenu' => 's&eacute;lectionnez ici le type de contenu de l\'objet SPIP (<i>article, br&egrave;ve, auteur ...</i>) qui sera inclus dans le mail transmis.',
	'cfgform_comment_header' => 'cette option vous permet de choisir si les informations de la balise &lt;head&gt; de la page doivent &ecirc;tre pr&eacute;sentes ou non (<i>il peut &ecirc;tre utile de les d&eacute;sactiver si vous utilisez une fen&ecirc;tre javascript type \'thickbox\', ou au contraire de forcer leur affichage dans le m&ecirc;me contexte avec un contenu en frame</i>).',
	'cfgform_comment_javascript' => 'vous pouvez d&eacute;sactiver la fonction d\'ouverture de la popup (<i>dans le cas de l\'utilisation de fen&ecirc;tres javascript type \'thickbox\' ou \'fancybox\' par exemple</i>).',
	'cfgform_comment_options' => 'vous devez indiquer des attributs complets, par exemple : "class=\'thickbox\'", ils seront automatiquement ajout&eacute;s au lien inclus dans vos squelettes ; <b>utilisez seulement des guillemets simples</b>.',
	'cfgform_comment_patron' => 'patron par d&eacute;faut du mail dans sa version classique (<i>texte brut</i>).',
	'cfgform_comment_patron_html' => 'si vous utilisez cette option, le mail envoy&eacute; comportera tout de m&ecirc;me le premier squelette en version texte brut ; laissez le champ vide pour annuler cette option.',
	'cfgform_comment_reset' => 'vous pouvez ici d&eacute;finir l\'action du bouton "Annuler" du formulaire (<i>red&eacute;finir cette action peut vous permettre de fermer la thickbox plut&ocirc;t que la fen&ecirc;tre par exemple</i>).',
	'cfgform_comment_squelette' => 'si vous avez cr&eacute;&eacute; un squelette personnel pour la bo&icirc;te de dialogue du plugin (<i>sur le mod&egrave;le du fichier "tip_a_friend.html"</i>) indiquez-le ici ; votre squelette devra obligatoirement inclure le formulaire "<b>tipafriend_form</b>".',
	'cfgform_comment_taf_css' => 'le plugin d&eacute;finit des styles CSS sur le mod&egrave;le des styles de la distribution de SPIP ; ces styles sont inclus au formulaire par d&eacute;faut mais vous pouvez ici choisir de ne pas les inclure.',
	'cfgform_info_balise' => 'La balise renvoie le lien ouvrant la page du formulaire d\'envoi. Vous pouvez changer l\'image affich&eacute;e en &eacute;ditant directement le squelette "<strong>modeles/tipafriend.html</strong>" du plugin.',
	'cfgform_info_patron_html' => '<strong>Si le plugin <a href="http://www.spip-contrib.net/?article3371"><strong>Facteur</strong></a> est install&eacute; et actif sur votre site</strong>, il est possible de construire une version HTML du mail envoy&eacute;.',
	'cfgform_info_patrons' => 'Vos patrons personnels sont &agrave; placer dans le sous-r&eacute;pertoire "<strong>patrons/</strong>" de votre r&eacute;pertoire de squelettes.',
	'cfgform_info_squelettes' => 'Vos squelettes personnels sont &agrave; placer directement dans votre r&eacute;pertoire de squelettes.',
	'cfgform_option_contenu_introduction' => 'Le titre et l\'introduction',
	'cfgform_option_contenu_rien' => 'Rien',
	'cfgform_option_contenu_tout' => 'Tout l\'objet',
	'cfgform_titre_close_button' => 'Inclure le bouton \'Fermer\'',
	'cfgform_titre_contenu' => 'Contenu des objets SPIP inclus au mail',
	'cfgform_titre_header' => 'Inclure les en-t&ecirc;tes HTML',
	'cfgform_titre_javascript' => 'Fonction javascript standard (ouverture d\'une popup)',
	'cfgform_titre_options' => 'Attribut(s) ajout&eacute;(s) au lien cr&eacute;&eacute; par la balise',
	'cfgform_titre_patron' => 'Patron du mail envoy&eacute;',
	'cfgform_titre_patron_html' => 'Patron du mail au format HTML',
	'cfgform_titre_reset' => 'Action du bouton d\'annulation',
	'cfgform_titre_squelette' => 'Squelette utilis&eacute; pour le formulaire tipafriend',
	'cfgform_titre_taf_css' => 'Inclure les d&eacute;finitions CSS par d&eacute;faut',

	// D
	'doc_chapo' => 'Le plugin "Tip A Friend" propose un formulaire complet pour envoyer une page d\'un site SPIP ({n\'importe laquelle}) &#224; une liste d\'adresses e-mail.',
	'doc_en_ligne' => 'Documentation du plugin sur Spip-Contrib',
	'doc_titre_court' => 'Documentation TipAFriend',
	'doc_titre_page' => 'Documentation du plugin "Tip A Friend"',
	'docskel_sep' => '----',
	'documentation' => '
Cette page vous permet de tester l\'utilisation du plugin en fonction de votre site, de votre configuration et de vos personnalisations. Les diff&eacute;rents liens propos&eacute;s ajoutent un objet SPIP ou incluent un mod&egrave;le dans le corps de la page. Vous pouvez modifier ces inclusions en &eacute;ditant le param&egrave;tre correspondant de l\'URL courante.

{{{La balise TIPAFRIEND}}}

{{Utilisation}}

Le plugin propose une balise qui construit un lien ouvrant la page d\'envoi du mail d\'information en fonction de l\'objet SPIP courant. Cette balise accepte un unique argument, optionnel, permettant de d&eacute;finir :
-* soit {{le squelette utilis&eacute; pour g&eacute;n&eacute;rer ce lien}}, il faut alors indiquer le nom du squelette en question ({sans l\'extension ".html"}) ; le squelette doit &ecirc;tre pr&eacute;sent dans votre r&eacute;pertoire de mod&egrave;les ;
-* soit {{le type de lien pr&eacute;sent&eacute;}} ; si vous indiquez l\'argument "{{mini}}", la balise renverra uniquement l\'image du lien, sans le texte "Envoyer cette page ...".

{{Exemple}}

<cadre class="spip">
// balise seule
#TIPAFRIEND
// pour ne voir que l\'image
#TIPAFRIEND{mini}
// ou avec un modele personnel
#TIPAFRIEND{mon_modele}
</cadre>

{{Tests}}

Les liens ci-dessous ajoutent un objet SPIP &agrave; la page courante, laissant appara&icirc;tre le rendu de la balise TIPAFRIEND.
- [Ajouter l\'article 1->@url_article@] <small>(id_article=...)</small>
- [Ajouter la br&egrave;ve 2->@url_breve@] <small>(id_breve=...)</small>
- [Recalculer la page->@url_recalcul@]
- [Retour &agrave; la page vierge->@url_vierge@]

Pour modifier l\'argument de la balise dans cette page de tests, ajoutez l\'argument "{{arg=...}}" &agrave; l\'URL courante ({par exemple pour utiliser l\'argument "mini", cliquez dans la barre d\'adresse de votre navigateur et ajoutez &agrave; la fin de l\'adresse courante "&arg=mini"}).

{{{Les mod&egrave;les}}}

Les liens ci-dessous vous permettent de tester les mod&egrave;les utilis&eacute;s en page web ({avec des valeurs fictives}) ou de les inclure &agrave; la page courante.
- [Inclure le mod&egrave;le \'tipafriend_mail_default.html\'->@url_model@] <small>(model=...)</small>
- [Voir le mod&egrave;le brut avec des donn&eacute;es fictives->@url_model_brut@]
- [Voir le mod&egrave;le HTML avec des donn&eacute;es fictives->@url_model_html@] <small>(n&eacute;cessite le plugin {{[Facteur->http://www.spip-contrib.net/?article3371]}})</small>

{{{Param&egrave;tres de CFG pour TIPAFRIEND}}}

Si le plugin {{[CFG : moteur de configuration->http://www.spip-contrib.net/?rubrique575]}} est actif sur votre site, le lien ci-dessous vous pr&eacute;sente les valeurs de configuration enregistr&eacute;es pour le plugin "Tip A Friend".

@cfg_param@',

	// E
	'error_dest' => 'Vous n\'avez indiqu&eacute; aucun destinataire',
	'error_exp' => 'Vous n\'avez pas indiqu&eacute; votre adresse mail',
	'error_exp_nom' => 'Vous devez indiquer votre nom',
	'error_not_mail' => 'Il semble que l\'adresse saisie ne soit pas un e-mail',
	'error_one_is_not_mail' => 'Il semble qu\'une des adresses saisies au moins ne soit pas un e-mail',

	// F
	'form_dest_label' => 'Adresses e-mail des destinataires',
	'form_exp_label' => 'Votre adresse e-mail',
	'form_exp_nom_label' => 'Votre nom',
	'form_exp_send_label' => '<em>Vous joindre en copie du mail (champ "Cc")</em>',
	'form_intro' => 'Pour transmettre l\'adresse de cette page, indiquez les adresses e-mail de vos contacts, votre propre adresse e-mail ainsi que vote nom. Vous pouvez &eacute;galement si vous le souhaitez ajouter un commentaire qui sera inclus dans le corps du message.<br /><small>{{*}} {Aucune de ces informations ne sera conserv&eacute;e.}</small>',
	'form_message_label' => 'Vous pouvez ajouter un texte',
	'form_separe_virgule' => '<em>Vous pouvez indiquer plusieurs adresses, en les s&eacute;parant par un point-virgule.</em>',
	'form_title' => 'Envoyer une page par e-mail',

	// I
	'info_doc' => 'Si vous rencontrez des probl&#232;mes pour afficher cette page, [cliquez-ici->@link@].',
	'info_doc_titre' => 'Note concernant l&#039;affichage de cette page',
	'info_skel_doc' => 'Cette page de documentation est con&#231;ue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du r&#233;pertoire &#034;squelettes-dist/&#034;}). Si vous ne parvenez pas &#224; visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de g&#233;rer son affichage :

-* [Mode &#034;texte simple&#034;->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode &#034;squelette Zpip&#034;->@mode_zpip@] ({squelette Z compatible})
-* [Mode &#034;squelette SPIP&#034;->@mode_spip@] ({compatible distribution})',

	// L
	'licence' => 'Copyright &#169; 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] distribu&eacute; sous licence [GNU GPL v3->http://www.opensource.org/licenses/gpl-3.0.html].',

	// M
	'mail_body_01' => '@nom_exped@ (contact : @mail_exped@) vous invite &agrave; consulter le document ci-dessous, tir&eacute; du site @nom_site@, susceptible de vous int&eacute;resser.',
	'mail_body_01_html' => '<strong>@nom_exped@</strong> (contact : <a href="mailto:@mail_exped@">@mail_exped@</a>) vous invite &agrave; consulter le document ci-dessous, tir&eacute; du site <strong>@nom_site@</strong>, susceptible de vous int&eacute;resser.',
	'mail_body_02' => '@nom_exped@ vous joint ce message :',
	'mail_body_02_html' => '@nom_exped@ vous joint ce message :',
	'mail_body_03' => 'Titre du document : \'@titre_document@\'',
	'mail_body_03_html' => 'Titre du document : \'@titre_document@\'',
	'mail_body_04' => 'Adresse de cette page sur l\'Internet : @url_document@',
	'mail_body_04_html' => 'Adresse de cette page sur l\'Internet : <a href="@url_document@">@url_document@</a>',
	'mail_body_05' => 'Contenu de la page concern&eacute;e (en version texte brut) : ',
	'mail_body_05_html' => 'Contenu de la page concern&eacute;e : ',
	'mail_body_extrait' => '( extrait ) ',
	'mail_titre_default' => 'Informations du site @nom_site@',
	'message_envoye' => 'OK - Votre message a bien &eacute;t&eacute; envoy&eacute;.',
	'message_pas_envoye' => '!! - Votre message n\'a pas pu &ecirc;tre envoy&eacute; pour une raison inconnue ... Veuillez nous en excuser et <a href="@self@" title="Recharger la page">r&eacute;essayer</a>.',

	// N
	'new_window' => 'Nouvelle fen&#234;tre',

	// P
	'page_test' => 'Page de test (locale)',
	'page_test_balise' => 'Rendu de la balise TIPAFRIEND',
	'page_test_cfg_pas_installe' => 'Le Plugin [CFG->http://www.spip-contrib.net/?rubrique575] ne semble pas install&eacute; ...',
	'page_test_fin_simulation' => '-- Fin de l\'inclusion pour simulation',
	'page_test_in_new_window' => 'Page de test en nouvelle fen&#234;tre',
	'page_test_menu_inclure' => 'Inclure le mod&egrave;le \'tipafriend_mail_default.html\'',
	'page_test_models_comment' => 'Les liens ci-dessous vous permettent de tester les mod&egrave;les utilis&eacute;s en page web (<i>avec des valeurs fictives</i>).',
	'page_test_test_model_brut' => 'Voir le mod&egrave;le brut avec des donn&eacute;es fictives',
	'page_test_test_model_html' => 'Voir le mod&egrave;le HTML avec des donn&eacute;es fictives',
	'page_test_title' => 'Test du plugin "Tip A Friend"',
	'page_test_titre_inclusion_model' => '-- Inclusion du mod&egrave;le \'@model@\' (<i>valeurs fictives</i>)',
	'page_test_titre_inclusion_objet' => '-- Simulation de page de @objet@ n&deg; @id_objet@ (<i>titre + introduction</i>)',
	'popup_name' => 'Envoyer une information par e-mail',

	// T
	'taftest_arguments_balise_dyn' => 'Arguments re&ccedil;us dans balise dynamique',
	'taftest_arguments_balise_stat' => 'Arguments re&ccedil;us dans balise statique',
	'taftest_chargement_patron' => 'chargement du patron \'@patron@\'',
	'taftest_content' => '<b><u>D&eacute;tails du mail envoy&eacute;</u></b>',
	'taftest_contexte_modele' => 'Contexte envoy&eacute; au mod&egrave;le',
	'taftest_creation_objet_champs' => 'Cr&eacute;ation d\'un objet \'Champs\' pour l\'ID objet',
	'taftest_creation_objet_texte' => 'Cr&eacute;ation d\'un objet \'Texte\' pour le nom d\'objet',
	'taftest_from' => '<b><i>Exp&eacute;diteur</i></b>',
	'taftest_mail_content' => '<b><i>Corps du mail</i></b>',
	'taftest_mail_content_html' => '<b><i>Corps du mail version HTML</i></b>',
	'taftest_mail_headers' => '<b><i>Headers</i></b>',
	'taftest_mail_retour' => '<b><i>Retour de la fonction mail()</i></b>',
	'taftest_mail_title' => '<b><i>Titre du mail</i></b>',
	'taftest_modele_demande' => 'Mod&egrave;le demand&eacute; par l\'utilisateur',
	'taftest_param_form' => 'Param&egrave;tres transmis au formulaire',
	'taftest_patron_pas_trouve' => 'Le patron \'@patron@\' n\'a pas &eacute;t&eacute; trouv&eacute; !<br />Chargement du patron par d&eacute;faut.',
	'taftest_skel_pas_trouve' => 'Le squelette \'@skel@\' n\'a pas &eacute;t&eacute; trouv&eacute; !<br />Chargement du squelette par d&eacute;faut.',
	'taftest_title' => 'TipAFriend DEBUG',
	'taftest_to' => '<b><i>Destinataires</i></b>',
	'tipafriend' => 'Tip A Friend'
);

?>
