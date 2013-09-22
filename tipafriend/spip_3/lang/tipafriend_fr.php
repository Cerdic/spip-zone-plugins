<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/tipafriend/spip_3/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_activer' => 'Activer',
	'bouton_annuler' => 'Annuler',
	'bouton_desactiver' => 'Désactiver',
	'bouton_envoyer' => 'Envoyer',
	'bouton_fermer' => 'Fermer',
	'bouton_reessayer' => 'Recommencer',
	'bouton_reset' => 'Réinitialiser',
	'bouton_send_by_mail' => 'Envoyer par email',
	'bouton_send_by_mail_ttl' => 'Envoyer cette page par email',

	// C
	'cfg_legend_balise' => 'Concernant la balise "#TIPAFRIEND"',
	'cfg_legend_patron' => 'Concernant les patrons de mail',
	'cfg_legend_squelette' => 'Concernant le formulaire d’envoi',
	'cfg_texte_descr' => 'Le plugin ajoute à SPIP un module permettant d’envoyer une page (<i>son contenu, son adresse ainsi qu’un message</i>) à un ou plusieurs destinataires par e-mail.',
	'cfg_titre_descr' => 'Configuration du plugin <i>Tip A Friend</i>',
	'cfgform_comment_close_button' => 'Active par défaut, cette option vous permet de choisir de montrer ou non le bouton ’Fermer’ en bas de la fenêtre ; <strong>cette option est automatiquement désactivée si les en-têtes sont eux-mêmes désactivés ci-dessus</strong>.',
	'cfgform_comment_contenu' => 'Sélectionnez ici le type de contenu de l’objet SPIP (<i>article, brève, auteur ...</i>) qui sera inclus dans le mail transmis.',
	'cfgform_comment_header' => 'Cette option vous permet de choisir si les informations de la balise &lt;head&gt; de la page doivent être présentes ou non (<i>il peut être utile de les désactiver si vous utilisez une fenêtre javascript type ’thickbox’, ou au contraire de forcer leur affichage dans le même contexte avec un contenu en frame</i>).',
	'cfgform_comment_javascript' => 'Vous pouvez désactiver la fonction d’ouverture de la popup (<i>dans le cas de l’utilisation de fenêtres javascript type ’thickbox’ ou ’fancybox’ par exemple</i>).',
	'cfgform_comment_options' => 'Vous devez indiquer des attributs complets, par exemple : "class=’thickbox’", ils seront automatiquement ajoutés au lien inclus dans vos squelettes ; <b>utilisez seulement des guillemets simples</b>.',
	'cfgform_comment_options_url' => 'Vous pouvez ici indiquer une liste d’arguments, par exemple : "arg=valeur&arg2=nouvelle_valeur", ils seront automatiquement ajoutés à l’URL générée par la balise.',
	'cfgform_comment_patron' => 'Patron par défaut du mail dans sa version classique (<i>texte brut</i>).',
	'cfgform_comment_patron_html' => 'Si vous utilisez cette option, le mail envoyé comportera tout de même le premier squelette en version texte brut ; laissez le champ vide pour annuler cette option.',
	'cfgform_comment_reset' => 'Vous pouvez ici définir l’action du bouton "Annuler" du formulaire (<i>redéfinir cette action peut vous permettre de fermer la thickbox plutôt que la fenêtre par exemple</i>).',
	'cfgform_comment_squelette' => 'Si vous avez créé un squelette personnel pour la boîte de dialogue du plugin (<i>sur le modèle du fichier "tip_a_friend.html"</i>) indiquez-le ici ; votre squelette devra obligatoirement inclure le formulaire "<b>tipafriend_form</b>".',
	'cfgform_comment_taf_css' => 'Le plugin définit des styles CSS sur le modèle des styles de la distribution de SPIP ; ces styles sont inclus au formulaire par défaut mais vous pouvez ici choisir de ne pas les inclure.',
	'cfgform_info_balise' => 'La balise renvoie le lien ouvrant la page du formulaire d’envoi. Vous pouvez changer l’image affichée en modifiant une copie du squelette "<strong>modeles/tipafriend.html</strong>" du plugin.',
	'cfgform_info_patron_html' => '<strong>Si le plugin <a href="http://www.spip-contrib.net/?article3371"><strong>Facteur</strong></a> est installé et actif sur votre site</strong>, il est possible de construire une version HTML du mail envoyé.',
	'cfgform_info_patrons' => 'Vos patrons personnels sont à placer dans le sous-répertoire "<strong>patrons/</strong>" de votre répertoire de squelettes.',
	'cfgform_info_squelettes' => 'Vos squelettes personnels sont à placer directement dans votre répertoire de squelettes.',
	'cfgform_option_contenu_introduction' => 'Le titre et l’introduction',
	'cfgform_option_contenu_rien' => 'Rien',
	'cfgform_option_contenu_tout' => 'Tout l’objet',
	'cfgform_titre_close_button' => 'Inclure le bouton ’Fermer’',
	'cfgform_titre_contenu' => 'Contenu des objets SPIP inclus au mail',
	'cfgform_titre_header' => 'Inclure les en-têtes HTML',
	'cfgform_titre_javascript' => 'Fonction javascript standard (ouverture d’une popup)',
	'cfgform_titre_options' => 'Attribut(s) ajouté(s) au lien créé par la balise',
	'cfgform_titre_options_url' => 'Argument(s) ajouté(s) à l’URL du lien créé par la balise',
	'cfgform_titre_patron' => 'Patron du mail envoyé',
	'cfgform_titre_patron_html' => 'Patron du mail au format HTML',
	'cfgform_titre_reset' => 'Action du bouton d’annulation',
	'cfgform_titre_squelette' => 'Squelette utilisé pour le formulaire tipafriend',
	'cfgform_titre_taf_css' => 'Inclure les définitions CSS par défaut',

	// D
	'doc_chapo' => 'Le plugin "Tip A Friend" propose un formulaire complet pour envoyer une page d’un site SPIP ({n’importe laquelle}) à une liste d’adresses e-mail.',
	'doc_en_ligne' => 'Documentation',
	'doc_titre_court' => 'Documentation TipAFriend',
	'doc_titre_page' => 'Documentation du plugin "Tip A Friend"',
	'docskel_sep' => '----',
	'documentation' => '
Cette page vous permet de tester l’utilisation du plugin en fonction de votre site, de votre configuration et de vos personnalisations. Les différents liens proposés ajoutent un objet SPIP ou incluent un modèle dans le corps de la page. Vous pouvez modifier ces inclusions en éditant le paramètre correspondant de l’URL courante.

{{{La balise TIPAFRIEND}}}

{{Utilisation}}

Le plugin propose une balise qui construit un lien ouvrant la page d’envoi du mail d’information en fonction de l’objet SPIP courant. Cette balise accepte un unique argument, optionnel, permettant de définir :
-* soit {{le squelette utilisé pour générer ce lien}}, il faut alors indiquer le nom du squelette en question ({sans l’extension ".html"}) ; le squelette doit être présent dans votre répertoire de modèles ;
-* soit {{le type de lien présenté}} ; si vous indiquez l’argument "{{mini}}", la balise renverra uniquement l’image du lien, sans le texte "Envoyer cette page ...".

{{Exemple}}

<cadre class="spip">
// balise seule
#TIPAFRIEND
// pour ne voir que l’image
#TIPAFRIEND{mini}
// ou avec un modele personnel
#TIPAFRIEND{mon_modele}
</cadre>

{{Tests}}

Les liens ci-dessous ajoutent un objet SPIP à la page courante, laissant apparaître le rendu de la balise TIPAFRIEND.
- [Ajouter l’article 1->@url_article@] <small>(id_article=...)</small>
- [Ajouter la brève 2->@url_breve@] <small>(id_breve=...)</small>
- [Recalculer la page->@url_recalcul@]
- [Retour à la page vierge->@url_vierge@]

Pour modifier l’argument de la balise dans cette page de tests, ajoutez l’argument "{{arg=...}}" à l’URL courante ({par exemple pour utiliser l’argument "mini", cliquez dans la barre d’adresse de votre navigateur et ajoutez à la fin de l’adresse courante "&arg=mini"}).

{{{Les modèles}}}

Les liens ci-dessous vous permettent de tester les modèles utilisés en page web ({avec des valeurs fictives}) ou de les inclure à la page courante.
- [Inclure le modèle ’tipafriend_mail_default.html’->@url_model@] <small>(model=...)</small>
- [Voir le modèle brut avec des données fictives->@url_model_brut@]
- [Voir le modèle HTML avec des données fictives->@url_model_html@] <small>(nécessite le plugin {{[Facteur->http://www.spip-contrib.net/?article3371]}})</small>

{{{Paramètres de configuration}}}

Le bloc ci-dessous vous présente les valeurs de configuration enregistrées pour le plugin "Tip A Friend".

@cfg_param@',

	// E
	'error_dest' => 'Vous n’avez indiqué aucun destinataire',
	'error_exp' => 'Vous n’avez pas indiqué votre adresse mail',
	'error_exp_nom' => 'Vous devez indiquer votre nom',
	'error_not_mail' => 'Il semble que l’adresse saisie ne soit pas un e-mail',
	'error_one_is_not_mail' => 'Il semble qu’une des adresses saisies au moins ne soit pas un e-mail',

	// F
	'form_dest_label' => 'Adresses e-mail des destinataires',
	'form_exp_label' => 'Votre adresse e-mail',
	'form_exp_nom_label' => 'Votre nom',
	'form_exp_send_label' => '<em>Vous joindre en copie du mail (champ "Cc")</em>',
	'form_intro' => 'Pour transmettre l’adresse de cette page, indiquez les adresses e-mail de vos contacts, votre propre adresse e-mail ainsi que vote nom. Vous pouvez également si vous le souhaitez ajouter un commentaire qui sera inclus dans le corps du message.<br /><small>{{*}} {Aucune de ces informations ne sera conservée.}</small>',
	'form_message_label' => 'Vous pouvez ajouter un texte',
	'form_separe_virgule' => '<em>Vous pouvez indiquer plusieurs adresses, en les séparant par un point-virgule.</em>',
	'form_title' => 'Envoyer une page par e-mail',

	// I
	'info_doc' => 'Si vous rencontrez des problèmes pour afficher cette page, [cliquez-ici->@link@].',
	'info_doc_titre' => 'Note concernant l’affichage de cette page',
	'info_skel_doc' => 'Cette page de documentation est conçue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du répertoire "squelettes-dist/"}). Si vous ne parvenez pas à visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de gérer son affichage :

-* [Mode "texte simple"->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode "squelette Zpip"->@mode_zpip@] ({squelette Z compatible})
-* [Mode "squelette SPIP"->@mode_spip@] ({compatible distribution})',

	// L
	'licence' => 'Copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] distribué sous licence [GNU GPL v3->http://www.opensource.org/licenses/gpl-3.0.html].',

	// M
	'mail_body_01' => '@nom_exped@ (contact : @mail_exped@) vous invite à consulter le document ci-dessous, tiré du site @nom_site@, susceptible de vous intéresser.',
	'mail_body_01_html' => '<strong>@nom_exped@</strong> (contact : <a href="mailto:@mail_exped@">@mail_exped@</a>) vous invite à consulter le document ci-dessous, tiré du site <strong>@nom_site@</strong>, susceptible de vous intéresser.',
	'mail_body_02' => '@nom_exped@ vous joint ce message :',
	'mail_body_02_html' => '@nom_exped@ vous joint ce message :',
	'mail_body_03' => 'Titre du document : ’@titre_document@’',
	'mail_body_03_html' => 'Titre du document : ’@titre_document@’',
	'mail_body_04' => 'Adresse de cette page sur l’Internet : @url_document@',
	'mail_body_04_html' => 'Adresse de cette page sur l’Internet : <a href="@url_document@">@url_document@</a>',
	'mail_body_05' => 'Contenu de la page concernée (en version texte brut) : ',
	'mail_body_05_html' => 'Contenu de la page concernée : ',
	'mail_body_extrait' => '( extrait ) ',
	'mail_titre_default' => 'Informations du site @nom_site@',
	'message_envoye' => 'OK - Votre message a bien été envoyé.',
	'message_pas_envoye' => ' !! - Votre message n’a pas pu être envoyé pour une raison inconnue ... Veuillez nous en excuser et <a href="@self@" title="Recharger la page">réessayer</a>.',

	// N
	'new_window' => 'Nouvelle fenêtre',

	// P
	'page_test' => 'Page de test (locale)',
	'page_test_balise' => 'Rendu de la balise TIPAFRIEND',
	'page_test_cfg_pas_installe' => 'Le Plugin [CFG->http://www.spip-contrib.net/?rubrique575] ne semble pas installé ...',
	'page_test_fin_simulation' => '— Fin de l’inclusion pour simulation',
	'page_test_in_new_window' => 'Page de test en nouvelle fenêtre',
	'page_test_menu_inclure' => 'Inclure le modèle ’tipafriend_mail_default.html’',
	'page_test_models_comment' => 'Les liens ci-dessous vous permettent de tester les modèles utilisés en page web (<i>avec des valeurs fictives</i>).',
	'page_test_test_model_brut' => 'Voir le modèle brut avec des données fictives',
	'page_test_test_model_html' => 'Voir le modèle HTML avec des données fictives',
	'page_test_title' => 'Test du plugin "Tip A Friend"',
	'page_test_titre_inclusion_model' => '— Inclusion du modèle ’@model@’ (<i>valeurs fictives</i>)',
	'page_test_titre_inclusion_objet' => '— Simulation de page de @objet@ n° @id_objet@ (<i>titre + introduction</i>)',
	'popup_name' => 'Envoyer une information par e-mail',

	// T
	'taftest_arguments_balise_dyn' => 'Arguments reçus dans balise dynamique',
	'taftest_arguments_balise_stat' => 'Arguments reçus dans balise statique',
	'taftest_chargement_patron' => 'chargement du patron ’@patron@’',
	'taftest_content' => '<b><u>Détails du mail envoyé</u></b>',
	'taftest_contexte_modele' => 'Contexte envoyé au modèle',
	'taftest_creation_objet_champs' => 'Création d’un objet ’Champs’ pour l’ID objet',
	'taftest_creation_objet_texte' => 'Création d’un objet ’Texte’ pour le nom d’objet',
	'taftest_from' => '<b><i>Expéditeur</i></b>',
	'taftest_mail_content' => '<b><i>Corps du mail</i></b>',
	'taftest_mail_content_html' => '<b><i>Corps du mail version HTML</i></b>',
	'taftest_mail_headers' => '<b><i>Headers</i></b>',
	'taftest_mail_retour' => '<b><i>Retour de la fonction mail()</i></b>',
	'taftest_mail_title' => '<b><i>Titre du mail</i></b>',
	'taftest_modele_demande' => 'Modèle demandé par l’utilisateur',
	'taftest_param_form' => 'Paramètres transmis au formulaire',
	'taftest_patron_pas_trouve' => 'Le patron ’@patron@’ n’a pas été trouvé !<br />Chargement du patron par défaut.',
	'taftest_skel_pas_trouve' => 'Le squelette ’@skel@’ n’a pas été trouvé !<br />Chargement du squelette par défaut.',
	'taftest_title' => 'TipAFriend DEBUG',
	'taftest_to' => '<b><i>Destinataires</i></b>',
	'tipafriend' => 'Tip A Friend'
);

?>
