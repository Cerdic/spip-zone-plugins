<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_activer' => 'Activer', # NEW
	'bouton_annuler' => 'Annuler', # NEW
	'bouton_desactiver' => 'Désactiver', # NEW
	'bouton_envoyer' => 'Envoyer', # NEW
	'bouton_fermer' => 'Fermer', # NEW
	'bouton_reessayer' => 'Recommencer', # NEW
	'bouton_reset' => 'Réinitialiser', # NEW
	'bouton_send_by_mail' => 'Send by email', # NEW
	'bouton_send_by_mail_ttl' => 'Send this page by email', # NEW

	// C
	'cfg_legend_balise' => 'Concernant la balise "#TIPAFRIEND"', # NEW
	'cfg_legend_patron' => 'Concernant les patrons de mail', # NEW
	'cfg_legend_squelette' => 'Concernant le formulaire d\'envoi', # NEW
	'cfg_texte_descr' => 'Le plugin ajoute à SPIP un module permettant d\'envoyer une page (<i>son contenu, son adresse ainsi qu\'un message</i>) à un ou plusieurs destinataires par e-mail.', # NEW
	'cfg_titre_descr' => 'Configuration du plugin <i>Tip A Friend</i>', # NEW
	'cfgform_comment_close_button' => 'active par défaut, cette option vous permet de choisir de montrer ou non le bouton \'Fermer\' en bas de la fenêtre ; <strong>cette option est automatiquement désactivée si les en-têtes sont eux-mêmes désactivés ci-dessus</strong>.', # NEW
	'cfgform_comment_contenu' => 'sélectionnez ici le type de contenu de l\'objet SPIP (<i>article, brève, auteur ...</i>) qui sera inclus dans le mail transmis.', # NEW
	'cfgform_comment_header' => 'cette option vous permet de choisir si les informations de la balise &lt;head&gt; de la page doivent être présentes ou non (<i>il peut être utile de les désactiver si vous utilisez une fenêtre javascript type \'thickbox\', ou au contraire de forcer leur affichage dans le même contexte avec un contenu en frame</i>).', # NEW
	'cfgform_comment_javascript' => 'vous pouvez désactiver la fonction d\'ouverture de la popup (<i>dans le cas de l\'utilisation de fenêtres javascript type \'thickbox\' ou \'fancybox\' par exemple</i>).', # NEW
	'cfgform_comment_options' => 'vous devez indiquer des attributs complets, par exemple : "class=\'thickbox\'", ils seront automatiquement ajoutés au lien inclus dans vos squelettes ; <b>utilisez seulement des guillemets simples</b>.', # NEW
	'cfgform_comment_options_url' => 'vous pouvez ici indiquer une liste d\'arguments, par exemple : "arg=valeur&arg2=nouvelle_valeur", ils seront automatiquement ajoutés à l\'URL générée par la balise.', # NEW
	'cfgform_comment_patron' => 'patron par défaut du mail dans sa version classique (<i>texte brut</i>).', # NEW
	'cfgform_comment_patron_html' => 'si vous utilisez cette option, le mail envoyé comportera tout de même le premier squelette en version texte brut ; laissez le champ vide pour annuler cette option.', # NEW
	'cfgform_comment_reset' => 'vous pouvez ici définir l\'action du bouton "Annuler" du formulaire (<i>redéfinir cette action peut vous permettre de fermer la thickbox plutôt que la fenêtre par exemple</i>).', # NEW
	'cfgform_comment_squelette' => 'si vous avez créé un squelette personnel pour la boîte de dialogue du plugin (<i>sur le modèle du fichier "tip_a_friend.html"</i>) indiquez-le ici ; votre squelette devra obligatoirement inclure le formulaire "<b>tipafriend_form</b>".', # NEW
	'cfgform_comment_taf_css' => 'le plugin définit des styles CSS sur le modèle des styles de la distribution de SPIP ; ces styles sont inclus au formulaire par défaut mais vous pouvez ici choisir de ne pas les inclure.', # NEW
	'cfgform_info_balise' => 'La balise renvoie le lien ouvrant la page du formulaire d\'envoi. Vous pouvez changer l\'image affichée en éditant directement le squelette "<strong>modeles/tipafriend.html</strong>" du plugin.', # NEW
	'cfgform_info_patron_html' => '<strong>Si le plugin <a href="http://contrib.spip.net/?article3371"><strong>Facteur</strong></a> est installé et actif sur votre site</strong>, il est possible de construire une version HTML du mail envoyé.', # NEW
	'cfgform_info_patrons' => 'Vos patrons personnels sont à placer dans le sous-répertoire "<strong>patrons/</strong>" de votre répertoire de squelettes.', # NEW
	'cfgform_info_squelettes' => 'Vos squelettes personnels sont à placer directement dans votre répertoire de squelettes.', # NEW
	'cfgform_option_contenu_introduction' => 'Le titre et l\'introduction', # NEW
	'cfgform_option_contenu_rien' => 'Rien', # NEW
	'cfgform_option_contenu_tout' => 'Tout l\'objet', # NEW
	'cfgform_titre_close_button' => 'Inclure le bouton \'Fermer\'', # NEW
	'cfgform_titre_contenu' => 'Contenu des objets SPIP inclus au mail', # NEW
	'cfgform_titre_header' => 'Inclure les en-têtes HTML', # NEW
	'cfgform_titre_javascript' => 'Fonction javascript standard (ouverture d\'une popup)', # NEW
	'cfgform_titre_options' => 'Attribut(s) ajouté(s) au lien créé par la balise', # NEW
	'cfgform_titre_options_url' => 'Argument(s) ajouté(s) à l\'URL du lien créé par la balise', # NEW
	'cfgform_titre_patron' => 'Patron du mail envoyé', # NEW
	'cfgform_titre_patron_html' => 'Patron du mail au format HTML', # NEW
	'cfgform_titre_reset' => 'Action du bouton d\'annulation', # NEW
	'cfgform_titre_squelette' => 'Squelette utilisé pour le formulaire tipafriend', # NEW
	'cfgform_titre_taf_css' => 'Inclure les définitions CSS par défaut', # NEW

	// D
	'doc_chapo' => 'Le plugin "Tip A Friend" propose un formulaire complet pour envoyer une page d\'un site SPIP ({n\'importe laquelle}) à une liste d\'adresses e-mail.', # NEW
	'doc_en_ligne' => 'Documentation du plugin sur Spip-Contrib', # NEW
	'doc_titre_court' => 'Documentation TipAFriend', # NEW
	'doc_titre_page' => 'Documentation du plugin "Tip A Friend"', # NEW
	'docskel_sep' => '----', # NEW
	'documentation' => '
Cette page vous permet de tester l\'utilisation du plugin en fonction de votre site, de votre configuration et de vos personnalisations. Les différents liens proposés ajoutent un objet SPIP ou incluent un modèle dans le corps de la page. Vous pouvez modifier ces inclusions en éditant le paramètre correspondant de l\'URL courante.

{{{La balise TIPAFRIEND}}}

{{Utilisation}}

Le plugin propose une balise qui construit un lien ouvrant la page d\'envoi du mail d\'information en fonction de l\'objet SPIP courant. Cette balise accepte un unique argument, optionnel, permettant de définir :
-* soit {{le squelette utilisé pour générer ce lien}}, il faut alors indiquer le nom du squelette en question ({sans l\'extension ".html"}) ; le squelette doit être présent dans votre répertoire de modèles ;
-* soit {{le type de lien présenté}} ; si vous indiquez l\'argument "{{mini}}", la balise renverra uniquement l\'image du lien, sans le texte "Envoyer cette page ...".

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

Les liens ci-dessous ajoutent un objet SPIP à la page courante, laissant apparaître le rendu de la balise TIPAFRIEND.
- [Ajouter l\'article 1->@url_article@] <small>(id_article=...)</small>
- [Ajouter la brève 2->@url_breve@] <small>(id_breve=...)</small>
- [Recalculer la page->@url_recalcul@]
- [Retour à la page vierge->@url_vierge@]

Pour modifier l\'argument de la balise dans cette page de tests, ajoutez l\'argument "{{arg=...}}" à l\'URL courante ({par exemple pour utiliser l\'argument "mini", cliquez dans la barre d\'adresse de votre navigateur et ajoutez à la fin de l\'adresse courante "&arg=mini"}).

{{{Les modèles}}}

Les liens ci-dessous vous permettent de tester les modèles utilisés en page web ({avec des valeurs fictives}) ou de les inclure à la page courante.
- [Inclure le modèle \'tipafriend_mail_default.html\'->@url_model@] <small>(model=...)</small>
- [Voir le modèle brut avec des données fictives->@url_model_brut@]
- [Voir le modèle HTML avec des données fictives->@url_model_html@] <small>(nécessite le plugin {{[Facteur->http://contrib.spip.net/?article3371]}})</small>

{{{Paramètres de CFG pour TIPAFRIEND}}}

Si le plugin {{[CFG : moteur de configuration->http://contrib.spip.net/?rubrique575]}} est actif sur votre site, le lien ci-dessous vous présente les valeurs de configuration enregistrées pour le plugin "Tip A Friend".

@cfg_param@', # NEW

	// E
	'error_dest' => 'Vous n\'avez indiqué aucun destinataire', # NEW
	'error_exp' => 'Vous n\'avez pas indiqué votre adresse mail', # NEW
	'error_exp_nom' => 'Vous devez indiquer votre nom', # NEW
	'error_not_mail' => 'Il semble que l\'adresse saisie ne soit pas un e-mail', # NEW
	'error_one_is_not_mail' => 'Il semble qu\'une des adresses saisies au moins ne soit pas un e-mail', # NEW

	// F
	'form_dest_label' => 'Receivers e-mail addresses', # NEW
	'form_exp_label' => 'Votre adresse e-mail', # NEW
	'form_exp_nom_label' => 'Votre nom', # NEW
	'form_exp_send_label' => '<em>Vous joindre en copie du mail (champ "Cc")</em>', # NEW
	'form_intro' => 'Pour transmettre l\'adresse de cette page, indiquez les adresses e-mail de vos contacts, votre propre adresse e-mail ainsi que vote nom. Vous pouvez également si vous le souhaitez ajouter un commentaire qui sera inclus dans le corps du message.<br /><small>{{*}} {Aucune de ces informations ne sera conservée.}</small>', # NEW
	'form_message_label' => 'Vous pouvez ajouter un texte', # NEW
	'form_separe_virgule' => '<em>Vous pouvez indiquer plusieurs adresses, en les séparant par un point-virgule.</em>', # NEW
	'form_title' => 'Envoyer une page par e-mail', # NEW

	// I
	'info_doc' => 'Si vous rencontrez des problèmes pour afficher cette page, [cliquez-ici->@link@].', # NEW
	'info_doc_titre' => 'Note concernant l\'affichage de cette page', # NEW
	'info_skel_doc' => 'Cette page de documentation est conçue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du répertoire "squelettes-dist/"}). Si vous ne parvenez pas à visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de gérer son affichage :

-* [Mode "texte simple"->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode "squelette Zpip"->@mode_zpip@] ({squelette Z compatible})
-* [Mode "squelette SPIP"->@mode_spip@] ({compatible distribution})', # NEW

	// L
	'licence' => 'Copyright © 2009 [Piero Wbmstr->http://contrib.spip.net/PieroWbmstr] distribué sous licence [GNU GPL v3->http://www.opensource.org/licenses/gpl-3.0.html].', # NEW

	// M
	'mail_body_01' => '@nom_exped@ (contact : @mail_exped@) vous invite à consulter le document ci-dessous, tiré du site @nom_site@, susceptible de vous intéresser.', # NEW
	'mail_body_01_html' => '<strong>@nom_exped@</strong> (contact : <a href="mailto:@mail_exped@">@mail_exped@</a>) vous invite à consulter le document ci-dessous, tiré du site <strong>@nom_site@</strong>, susceptible de vous intéresser.', # NEW
	'mail_body_02' => '@nom_exped@ vous joint ce message :', # NEW
	'mail_body_02_html' => '@nom_exped@ vous joint ce message :', # NEW
	'mail_body_03' => 'Titre du document : \'@titre_document@\'', # NEW
	'mail_body_03_html' => 'Titre du document : \'@titre_document@\'', # NEW
	'mail_body_04' => 'Adresse de cette page sur l\'Internet : @url_document@', # NEW
	'mail_body_04_html' => 'Adresse de cette page sur l\'Internet : <a href="@url_document@">@url_document@</a>', # NEW
	'mail_body_05' => 'Contenu de la page concernée (en version texte brut) : ', # NEW
	'mail_body_05_html' => 'Contenu de la page concernée : ', # NEW
	'mail_body_extrait' => '( extrait ) ', # NEW
	'mail_titre_default' => 'Informations du site @nom_site@', # NEW
	'message_envoye' => 'OK - Votre message a bien été envoyé.', # NEW
	'message_pas_envoye' => '!! - Votre message n\'a pas pu être envoyé pour une raison inconnue ... Veuillez nous en excuser et <a href="@self@" title="Recharger la page">réessayer</a>.', # NEW

	// N
	'new_window' => 'Nouvelle fenêtre', # NEW

	// P
	'page_test' => 'Page de test (locale)', # NEW
	'page_test_balise' => 'Rendu de la balise TIPAFRIEND', # NEW
	'page_test_cfg_pas_installe' => 'Le Plugin [CFG->http://contrib.spip.net/?rubrique575] ne semble pas installé ...', # NEW
	'page_test_fin_simulation' => '-- Fin de l\'inclusion pour simulation', # NEW
	'page_test_in_new_window' => 'Page de test en nouvelle fenêtre', # NEW
	'page_test_menu_inclure' => 'Inclure le modèle \'tipafriend_mail_default.html\'', # NEW
	'page_test_models_comment' => 'Les liens ci-dessous vous permettent de tester les modèles utilisés en page web (<i>avec des valeurs fictives</i>).', # NEW
	'page_test_test_model_brut' => 'Voir le modèle brut avec des données fictives', # NEW
	'page_test_test_model_html' => 'Voir le modèle HTML avec des données fictives', # NEW
	'page_test_title' => 'Test du plugin "Tip A Friend"', # NEW
	'page_test_titre_inclusion_model' => '-- Inclusion du modèle \'@model@\' (<i>valeurs fictives</i>)', # NEW
	'page_test_titre_inclusion_objet' => '-- Simulation de page de @objet@ n° @id_objet@ (<i>titre + introduction</i>)', # NEW
	'popup_name' => 'Envoyer une information par e-mail', # NEW

	// T
	'taftest_arguments_balise_dyn' => 'Arguments reçus dans balise dynamique', # NEW
	'taftest_arguments_balise_stat' => 'Arguments reçus dans balise statique', # NEW
	'taftest_chargement_patron' => 'chargement du patron \'@patron@\'', # NEW
	'taftest_content' => '<b><u>Détails du mail envoyé</u></b>', # NEW
	'taftest_contexte_modele' => 'Contexte envoyé au modèle', # NEW
	'taftest_creation_objet_champs' => 'Création d\'un objet \'Champs\' pour l\'ID objet', # NEW
	'taftest_creation_objet_texte' => 'Création d\'un objet \'Texte\' pour le nom d\'objet', # NEW
	'taftest_from' => '<b><i>Expéditeur</i></b>', # NEW
	'taftest_mail_content' => '<b><i>Corps du mail</i></b>', # NEW
	'taftest_mail_content_html' => '<b><i>Corps du mail version HTML</i></b>', # NEW
	'taftest_mail_headers' => '<b><i>Headers</i></b>', # NEW
	'taftest_mail_retour' => '<b><i>Retour de la fonction mail()</i></b>', # NEW
	'taftest_mail_title' => '<b><i>Titre du mail</i></b>', # NEW
	'taftest_modele_demande' => 'Modèle demandé par l\'utilisateur', # NEW
	'taftest_param_form' => 'Paramètres transmis au formulaire', # NEW
	'taftest_patron_pas_trouve' => 'Le patron \'@patron@\' n\'a pas été trouvé !<br />Chargement du patron par défaut.', # NEW
	'taftest_skel_pas_trouve' => 'Le squelette \'@skel@\' n\'a pas été trouvé !<br />Chargement du squelette par défaut.', # NEW
	'taftest_title' => 'TipAFriend DEBUG', # NEW
	'taftest_to' => '<b><i>Destinataires</i></b>', # NEW
	'tipafriend' => 'Tip A Friend'
);

?>
