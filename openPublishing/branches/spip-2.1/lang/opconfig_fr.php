<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

// v 0.5

// A
'attention_minimum' => 'Attention : vous devez au minimum avoir coch&eacute; une rubrique.',

// C

'creer_rubrique' => 'Cr&eacute;er une nouvelle rubrique.',
'creer_auteur' => 'Cr&eacute;er un nouvel auteur.',
'creer_groupe' => 'Cr&eacute;er un nouveau groupe.',
'composition_article' => 'Composition d\'un article',
'choix_agenda' => 'Choix de la rubrique Agenda',

// D
'description_plugin' => 'Le plugin <b>Publication Ouverte</b> permet la publication d\'articles par les visiteurs de votre site depuis l\'espace publique, sans qu\'il soit n&eacute;cessaire de s\'identifier. Les options ci-dessous permettent d\'adapter le fonctionnement du plugin &agrave; votre site.',

// E

'erreur_auteur_anonyme' => 'Aucun auteur anonyme n\'est d&eacute;fini. Vous devez obligatoirement choisir un auteur lors de la configuration du plugin "Publication Ouverte".',
'erreur_rubrique' => 'Aucune rubrique n\'est d&eacute;finie. Vous devez obligatoirement choisir une rubrique lors de la configuration du plugin "Publication Ouverte".',
'erreur_configuration' => 'Attention, la configuration du plugin "Publication Ouverte" comporte des erreurs :',
'erreur_configuration_page' => 'Page de configuration : <a href="ecrire/?exec=cfg&cfg=op">Configurer le plugin Publication Ouverte</a>',
'erreur_groupe' => 'Aucun groupe de mot-cl&eacute; n\'est d&eacute;fini. Si vous avez permettez aux redacteurs de choisir des mots-cl&eacute;s, vous devez obligatoirement choisir des groupes de mots-cl&eacute;s lors de la configuration du plugin "Publication Ouverte". ',

'expliq_agenda' => 'Si vous cochez l\'une des options "gestion de Agenda du plugin Publication Ouverte", le plugin "Publication Ouverte" proposera aux r&eacute;dacteurs de publier leurs articles sous forme de "date" dans l\'agenda.<br />Il s\'agit en r&eacute;alit&eacute; soit de publier le contenu de l\'article sous forme de br&egrave;ve dans une rubrique sp&eacute;cifique, soit de publier  l\'article dans une rubrique sp&eacute;cifique sans transformation en br&egrave;ve.<br /> La premi&egrave;re m&eacute;thode correspond au m&eacute;canisme des version pr&eacute;c&eacute;dente du plugin, la seconde permet de transf&eacute;rer rapidement un &eacute;venement de l\'agenda vers une autre rubrique.',
'expliq_auteur_anonyme' =>'L\'auteur "anonyme" est l\'auteur au nom du quel les r&eacute;dacteurs publieront leurs articles. Cr&eacute;ez cet auteur dans votre liste d\'auteur (remplissez uniquement le champ "Signature" et laissez son statut en "r&eacute;dacteur"), puis s&eacute;lectionnez le ci-dessous.',
'expliq_auteur_squelette' => 'Afin de r&eacute;cup&eacute;rer les donn&eacute;es d\'identification du r&eacute;dacteur, vous pouvez utiliser dans une boucle ARTICLES la balise EXTRA avec le filtre OP_pseudo ou OP_mail.',
'expliq_groupes_absent' => 'Attention, aucun groupe de mot-cl&eacute; n\'est d&eacute;fini. Veuillez ajouter des groupes de mots-cl&eacute;s (activez les mot-cl&eacute;s puis cr&eacute;ez les groupes necessaires. N\'oubliez pas de cr&eacute;er des mots-cl&eacute;s dans ces nouveaux groupes.).',
'expliq_groupes' => 'Si vous avez activ&eacute; l\'option "permettre aux r&eacute;dacteurs de choisir leurs mots-cl&eacute;s", vous pouvez alors choisir les diff&eacute;rents groupes de mot-cl&eacute;s qui leur serons propos&eacute;s. Cochez ou d&eacute;cochez les groupes de mots-cl&eacute;s i-dessous.',
'expliq_rubrique_absent' => 'Attention, aucune rubrique n\'est d&eacute;finie. Veuillez ajouter des rubriques (Menu "Edition" de votre interface priv&eacute;e).',
'expliq_rubrique_op' => 'Lorsqu\'un r&eacute;dacteur utilise le formulaire de publication ouverte, Il pourra choisir de ranger son article parmi les rubriques que vous lui proposez. Pour choisir ces rubriques, cochez les dans la liste ci-dessus.',
'expliq_rubrique_squelette' => 'Dans votre squelette, pour lister toutes vos rubriques "Publication ouverte", vous pouvez utiliser le crit&egrave;re {openPublishing} dans une boucle RUBRIQUES',
'expliq_options' => 'Ces options permettre d\'&eacute;tendre ou de restreindre les fonctionnalit&eacute;s du plugin "Publication Ouverte". Les options coch&eacute;es seront disponibles dans l\'espace de r&eacute;daction',
'expliq_composition_article' => 'Un article peut &ecirc;tre compos&eacute; de plusieurs champs qui ne sont pas forc&eacute;ment utilis&eacute; par tous les sites. Vous pouvez donc activer ou pas les champs ci-dessous. L\'espace de r&eacute;daction ne proposera aux r&eacute;dacteurs que les champs coch&eacute;s (ainsi que, \'&eacute;videmment, le titre et le texte de l\'article).',
'expliq_statut' => 'Une fois que le r&eacute;dacteur a valid&eacute; son article, celui-ci appara&icirc;tra dans l\'espace priv&eacute;e de votre site avec le statut suivant :',
'expliq_posttraitement' => 'Il s\'agit ici d\'effectuer des traitements de protection sur l\'article. Ces options s\'appliqueront apr&egrave;s la validation de l\'article par le r&eacute;dacteur.',
'expliq_renvois' => 'Lorsqu\'un r&eacute;dacteur valide un article ou abandonne, le formulaire affiche un message et redirige le r&eacute;dacteur au bout de quelques secondes vers une autre page du site. Les options ci-dessous permettent de configurer cela. Attention, il faut indiquer des urls de type : &laquo; /spip.php?page=ma_page &raquo;, le plugin compl&eacute;tera automatiquement l\'url.',

'explication_chapo' => 'Le chapeau est totalement optionnel. Si vous n’en avez pas besoin, laissez-le vide, la pr&eacute;sentation du site s’adaptera automatiquement &agrave; sa pr&eacute;sence ou absence.',
'explication_ps' => 'Le post-scriptum est totalement optionnel. Si vous n’en avez pas besoin, laissez-le vide, la pr&eacute;sentation du site s’adaptera automatiquement &agrave; sa pr&eacute;sence ou absence.',
'explication_surtitre' => 'Le surtitre est totalement optionnel. Si vous n’en avez pas besoin, laissez-le vide, la pr&eacute;sentation du site s’adaptera automatiquement &agrave; sa pr&eacute;sence ou absence.',
'explication_soustitre' => 'Le soustitre est totalement optionnel. Si vous n’en avez pas besoin, laissez-le vide, la pr&eacute;sentation du site s’adaptera automatiquement &agrave; sa pr&eacute;sence ou absence.',
'explication_descriptif' => 'Le descriptif est totalement optionnel. Si vous n’en avez pas besoin, laissez-le vide, la pr&eacute;sentation du site s’adaptera automatiquement &agrave; sa pr&eacute;sence ou absence.',

// G
'gestion_rubrique' => 'Gestion des rubriques',
'gestion_auteur' => 'l\'auteur "anonyme"',
'gestion_agenda' => 'Gestion de l\'agenda',
'gestion_renvois' => 'Gestion des renvois',

// I
'info_motclefs' => 'Mots-cl&eacute;s : ',
'info_rubrique' => 'Rubrique : ',

// O
'options_dispos' => 'Les options disponibles',
'option_motclef' => 'Permettre aux r&eacute;dacteurs de choisir les mots clefs',
'option_document' => 'Permettre aux r&eacute;dacteurs d\'inclure des documents dans l\'article',
'option_logo' => '... de type "logo"',
'option_auteur_spip' => 'Permettre aux r&eacute;dacteurs enregistr&eacute;s en tant que "auteur Spip" de publier les articles en leur nom',
'option_doc_distant' => 'Permettre aux r&eacute;dacteurs d\'inclure des documents distants',
'option_authentification' => 'Permettre aux r&eacute;dacteurs de s\'identifier (mail et pseudo).',
'option_docimage' => '... de type "image"',
'option_docdocument' => '... de type "document"',
'option_docauto' => 'imposer un type de document automatiquement (image, document ou logo).',

// P
'publie_motscles' => 'Choisir des mots-cl&eacute;s',
'publie_motscles_explique' => 'Les mots-cl&eacute;s sont utiles pour r&eacute;f&eacute;rencer votre article sur ce site et
pour le retrouver lors d\'une recherche par th&eacute;matique.',
'publie_rubrique' => 'Choisissez votre rubrique',
'publie_texte' => 'Texte',
'publie_titre' => 'Titre',
'publie_surtitre' => 'Sur-titre :',
'publie_soustitre' => 'Sous-titre :',
'publie_chapo' => 'Chapeau :',
'publie_descriptif' => 'Descriptif rapide :',
'publie_ps' => 'Post-scriptum :',
'post_traitement' => 'Post-traitement',
'post_notification' => 'Activer la notification mail lors de la publication des articles (vous devez avoir activ&eacute; "Suivi de l\'activit&eacute; &eacute;ditoriale" dans votre configuration SPIP)',
'post_majuscule' => 'Autoriser les majuscules dans les titres',
'post_antispam' => 'Protection anti-spam sur les adresses mails',
'post_taille_min' => 'Taille minimal du titre :',
'post_pipeline' => 'Activer l\'interactivit&eacute; avec les autres plugins (cf. documentation sur spip-contrib)',
'post_char' => 'caract&egrave;res',

// R
'revenir_haut' => 'Revenir en haut.',
'renvois_url_validation' => 'Url de retour en cas de validation',
'renvois_url_abandon' => 'Url de retour en cas d\'abandon',
'renvois_texte_abandon' => 'Texte en cas d\'abandon',
'renvois_texte_validation' => 'Texte en cas de validation',
'renvois_temps' => 'Temps d\'attente avant le renvoi (exprim&eacute; en secondes)',

// S
'statut_article' => 'Statut des articles',
'statut_en_redaction' => 'En R&eacute;daction',
'statut_proposer' => 'Proposer &agrave; la validation',
'statut_valide' => 'Valid&eacute;',

// T
'titre_rubrique_op' => 'Gestion des rubriques "Publication Ouverte"',
'titre_auteur_anonyme' => 'L\'auteur "anonyme"',
'titre_composition_article' => 'Composition d\'un article',
'titre_options' => 'Options disponibles aux r&eacute;dacteurs',
'titre_groupes' => 'Choix des groupes de mot-cl&eacute;s a utiliser',
'titre_posttraitement' => 'Post-traitement des articles',
'titre_statut_op' => 'Statut des articles apr&egrave;s la r&eacute;daction',
'titre_renvois' => 'Gestion des renvois',
'titre_agenda_op' => 'Gestion de l\'agenda par le plugin "Publication Ouverte"',

// U
'utiliser_surtitre' => 'Utiliser le champ Sur-titre',
'utiliser_soustitre' => 'Utiliser le champ Sous-titre',
'utiliser_chapo' => 'Utiliser le champ Chapeau',
'utiliser_descriptif' => 'Utiliser le champ Descriptif',
'utiliser_ps' => 'Utiliser le champ Post-Scriptum',
'utiliser_agenda_breve' => 'utiliser la gestion Agenda "Publication Ouverte" (&eacute;v&eacute;nements sous forme de br&egrave;ves).',
'utiliser_agenda_article' => 'utiliser la gestion Agenda "Publication Ouverte" (&eacute;v&eacute;nements sous forme d\'articles).',


// v 0.4

// A
'aller_config' => 'Vous pouvez en toute s&eacute;curit&eacute; aller modifier les options de configuration du plugin "Publication Ouverte"',

'avant_toute_chose' => 'Avant toute chose, munissez vous du num&eacute;ro id du dernier auteur cr&eacute;er AVANT l\'installation du plugin openPublishing 0.3',

'attention_temps' => 'Attention, cette op&eacute;ration peut prendre un certain temps (en fonction du nombres d\'articles publi&eacute; en openPublishing dans votre base).',


'auteur' => 'auteur',

// B
'bonne_nouvelle' => 'Bonne nouvelle, le plugin "Publication Ouverte" n\'utilise plus de tables "perso" dans la base de donn&eacute;e de spip.',
'base_attention' => 'Attention, ces actions vont agir sur la base de donn&eacute;e de spip. Soyez absolument certain de ce que vous faites avant de le faire !',


// C
'cas_neuve' => 'Cas d\'une installation "neuve"',
'cas_mise_a_jour' => 'Cas d\'une mise &agrave; jour du plugin',
'correction_num_id' => 'Correction des num&eacute;ros d\'identifications des auteurs',


'configuration' => 'Configuration Publication Ouverte',
'caracteres' => ' caract&eacute;res',
'choix_auteur_spip' => 'Publier de mani&egrave;re anonyme.',

// D

'documents_lies' => 'Les documents attach&eacute;s &agrave; votre article',

'document_inclusion' => 'Document (le fichier apparaitra sous forme de vignette cliquable)',
'document_explique' => 'Cette option permet de charger des documents provenant de votre ordinateur et de les lier &agrave; votre article. Si vous choississez l\'option "image", vous pourrez alors inclure votre image directement dans le texte de votre article. Si vous choisissez l\'option "document", vous pourrez alors inclure votre document sous forme de vignette cliquable directement dans le texte de votre article.',
'document_distant' => 'OU incluez un document distant',
'expliq_document_distant' => 'Un document distant est un document pr&eacute;sent sur un autre serveur, qui sera li&eacute; &agrave; votre article, mais sans &ecirc;tre dupliqu&eacute;',
'description_document' => 'D&eacute;crivez votre document',
'descriptif_doc' => 'Descriptif',

// E
'expliq_sup_table' => 'La version 0.4 du plugin "Publication Ouverte" n\'utilise plus les tables spip_op_config et spip_op_rubriques. Si votre ancienne version du plugin &eacute;tait la 0.3 ou la 0.2.2, il vous faut supprimer totalement ces tables et refaire la configuration de votre plugin :',
'expliq_transfert_auteurs' => 'La version 0.4 du plugin "Publication Ouverte" n\'utilise plus la table spip_op_auteurs pour stocker les donn&eacute;es d\'identifications des r&eacute;dacteurs, mais utilise les champs "extras" de la table spip_articles. L\'option suivante va vous permettre de mettre &agrave; jour tous les articles ayant eu un r&eacute;dacteur s\'&eacute;tant identifi&eacute; via ce syst&egrave;me. Cela vous permettra de conserver les donn&eacute;es identifications de vos r&eacute;dacteurs et donc de supprimer la table spip_op_auteurs devenue obsol&eacute;te',
'expliq_num_id' => 'La version 0.3 du plugin openPublishing utilisait par d&eacute;faut l\'auteur "anonymous" portant le num&eacute;ro d\'identification 999. Cela &eacute;tait une tr&agrave;s mauvaise id&eacute;e, car pour tout auteur cr&eacute;&eacute; par la suite, l\'identification automatique d&eacute;marait &agrave; partir du num&eacute;ro 1000 ce qui pouvais causer de multiples bugs dans l\'utilisation de spip. L\'outil ci-dessous va vous permettre, si vous &ecirc;tes dans ce cas, de supprimer l\'auteur portant l\'identification 999 (le fameux auteur anonymous) et de re-donner aux auteurs portant l\'identification 1000 ou plus leur v&eacute;ritable num&eacute;ro id.',









'exemple_trois_auteurs' => 'Exemple avec trois auteurs :',
'expliq_balise' => 'le code donn&eacute; en-dessous de votre image est a placer dans le texte de votre article si vous souhaitez inclure l\'image ou la vignette du document dans votre texte (right, center, left renseigne l\'alignement que prendra votre image  ou vignette).',
'erreur_die' => 'Veuillez effectuer la configuration du plugin openPublishing.',
'erreur_protection' => 'tentative de modification d\'un article d&eacute;j&agrave; publi&eacute;',
'erreur_min_len' => 'Attention, votre titre doit contenir au minimum ',
'erreur_upload' => 'erreur d\'upload, le fichier temporaire est introuvable, il ce peut que vous tentiez d\'uploader un fichier trop volumineux. La taille maximale autoris&eacute;e est de 5 Mo',
'erreur_extension' => 'erreur d\'upload. L\'extention de votre fichier n\'est pas autoris&eacute;e ...',
'expliq_spip_auteur' => 'Vous &ecirc;tes actuellement logg&eacute;. Si vous voulez publier cet article de mani&egrave;re anonyme, cochez la case ci-dessous.',




// L
'logo_article' => 'Le logo de votre article',
'logo_inclusion' =>'Logo (l\'image deviendra le logo de votre article)',
'logo_existe_deja' =>'Le logo existe d&eacute;j&agrave; !',

// O


'option_tagmachine' => 'Permettre aux r&eacute;dacteurs d\'utiliser le plugin tag-machine',

'op_base_titre' => 'openPublishing : base de donn&eacute;e',

// P
'premiere_fois' => 'C\'est la premi&egrave;re fois que vous installez le plugin "Publication Ouverte" (anciennement openPublishing), et vous l\'utilisez pour la premi&egrave;re fois.',






// R
'rien_a_faire' => 'Vous n\'avez rien &agrave; faire (r&eacute;jouissez vous !), ne touchez surtout pas aux options ci-dessous, celles ci concernent les personnes ayant install&eacute;es une version du plugin openPublishing, et mettant &agrave; jour leur plugin.',

'retour' => 'Retour',

// S
'structure' => 'La structure du plugin a &eacute;volu&eacute; depuis la premi&egrave;re version &agrave; la version pr&eacute;sente, notamment au niveau de l\'organisation de la base de donn&eacute;e. C\'est pourquoi les outils ci-dessous vont vous aider &agrave; mettre &agrave; jour votre base de donn&eacute;e sans risquer de perdre vos donn&eacute;es. Suivez correctement l\'ordre des actions propos&eacute;es.',
'sup_auteurs' => 'Suppression de la table spip_op_auteurs',
'sup_auteur_anonymous' => 'Supprimez l\'auteur portant le num&eacute;ro id 999',
'sup_table' => 'Supprimer les tables obsol&eacute;tes (op_config et op_rubriques)',
'sup_logo' => 'Supprimer le logo',

// T
'telecharge_install' => 'Vous venez de t&eacute;l&eacute;charger la version 0.4 du plugin "Publication Ouverte". Vous avez supprim&eacute; votre ancienne version openPublishing dans votre r&eacute;pertoire "plugins/" et vous l\'avez remplac&eacute; par celle-ci.',







'titre_doc' => 'Titre',

'transfert_auteurs' => 'Mettre &agrave; jours les champs extra de la table spip_articles.',
'transfert_auteurs_suite' => '(&agrave; partir des informations de la table spip_op_auteurs).',
'telecharger_document' => 'T&eacute;l&eacute;chargez votre document',
'transfert_auteurs_ok' => 'Si l\'op&eacute;ration c\'est bien d&eacute;roul&eacute;e, vous pouvez maintenant supprimer la table obsol&eacute;te.',
'type_document' => 'Le type de votre document',

// U

'utiliser_agenda' => 'utiliser la gestion Agenda du plugin "Publication Ouverte"',

'utiliser_article_agenda' => 'utiliser des articles post-dat&eacute;',




// V 0.2.2 / 0.3


// A
'ajouter_agenda' => 'Ajouter dans l\'agenda',
'abandonner' => 'Abandonner',
'anti_spam' => 'Activer l\'anti-spam ?',
'antispam_oui' => 'Les @ des adresses mails du texte seront transform&eacute;s',
'antispam_non' => 'Les @ des adresses mails ne seront pas transform&eacute;s',
'agenda_explique' => 'L\'orsque l\'utilisateur coche la case "Agenda", son article est publi&eacute; sous forme de br&egrave;ve dans la rubrique indiqu&eacute;e ci-dessus',
'agenda_explique2' => 'Si vous publiez votre article en tant que br&egrave;ve dans l\'agenda, celui n\'apparaitra pas dans la rubrique selectionn&eacute;e mais dans la rubrique "Agenda" du site.',
'agenda_rubrique' =>  'rubrique de l\'agenda : ',
'agenda_active' => 'activer l\'agenda ?&nbsp;',
'agenda_oui' => 'L\'agenda sera activ&eacute;',
'agenda_non' => 'L\'agenda ne sera pas activ&eacute;',
'ajout_correct' => ' a &eacute;t&eacute; correctement ajout&eacute;e.',
'ajout_incorrect' => ' n\'a pas &eacute;t&eacute; ajout&eacute;e : erreur inconue.',
'aide_formulaire' => 'Aide : Cliquez sur les fl&egrave;ches pour "d&eacute;rouler" le menu.',
'aide_inclusion' => 'Pour inclure directement une image ou un document dans votre article, recopiez dans votre texte le code figurant sous la vignette. Vous pouvez aussi ne rien faire, dans ce cas, vos documents apparaitrons dans une liste sous votre article.',

// C

'configurer_op' => 'Configurer openPublishing',
'champ_surtitre' => 'Le champ sur-titre',
'champ_surtitre_oui' => 'sur-titre disponible',
'champ_surtitre_non' => 'sur-titre non disponible',
'champ_soustitre' => 'Le champ sous-titre',
'champ_soustitre_oui' => 'sous-titre disponible',
'champ_soustitre_non' => 'sous-titre non disponible',
'champ_chapo' => 'Le champ chapeau',
'champ_chapo_oui' => 'chapeau disponible',
'champ_chapo_non' => 'chapeau non disponible',
'champ_descriptif' => 'Le champ descriptif rapide',
'champ_descriptif_oui' => 'descriptif rapide disponible',
'champ_descriptif_non' => 'descriptif rapide non disponible',
'champ_ps' => 'Le champ post-scriptum',
'champ_ps_oui' => 'post-scriptum disponible',
'champ_ps_non' => 'post-scriptum non disponible',

// D
'document_joint' => 'Joindre un document ou une image',
'deja_base' => ' est d&eacute;j&agrave; dans la base.',
'deinstaller' => 'D&eacute;sinstaller l\'openPublishing',

// E
'explication_agenda' => 'Pour ajouter votre article dans l\'agenda (dans le cas d\'un &eacute;v&eacute;nement), cochez la case ci-dessous et choisissez la date et l\'heure correspondant &agrave; l\'&eacute;v&eacute;nement. Votre article sera publi&eacute; dans l\'agenda (et non dans une des rubriques "contributions").',
'explication_rubrique' => 'La rubrique "locale" concerne uniquement les articles traitant de Lille et de ses environs (nord-pas de calais), La rubrique "non locale" concerne les autres parties du monde, et la rubrique "analyse" concerne les articles sans attaches g&eacute;ographiques particuli&egrave;res',
'explication_titre' => 'Evitez les majuscules, choississez un titre court et repr&eacute;sentatif du contenu de l\'article.',


'explication_article' => 'A FAIRE : placer ici des conseil de r&eacute;daction d\'article ...',
'extensions_acceptees' => 'Les extensions de fichier accept&eacute;es sont :',
'erreur_anonymous' => 'erreur, pas d\'auteur anonymous dans la base, publication impossible',
'erreur_insertion' => 'erreur lors de l\'insertion de votre article dans la base de donn&eacute;e, veuillez contactez les responsables du site',


//G
'gestion_agenda' => 'Gestion de l\'agenda',
'gestion_autre' => 'Autres &eacute;lements de configuration',
'gestion_champ' => 'Champs disponibles lors de l\'&eacute;dition d\'un article',

// I
'identification' => 'Identification',
'identif_explique' => 'Vous pouvez vous identifier, mais cela n\'est pas obligatoire. Ces options ajoutent simplement votre nom ou pseudo et les autres informations tels que le mail, le num&eacute;ro de t&eacute;l&eacute;phone, etc ..., &agrave; la suite de votre article.',
'image_inclusion' => 'Image (pour inclusion directe dans l\'article)',
'installer' => 'Installer les tables openPublishing',
'info_version' => 'Version install&eacute;e : ',
'info_auteur' => 'Id anonymous : ',
'info_agenda' => 'Gestion agenda : ',
'info_document' => 'Gestion documents : ',
'info_statut' => 'Statut des articles : ',
'info_motclefstag' => 'Gestion des mots-cl&eacute;s',
'info_tagmachine' => 'Plugin Tag Machine : ',

'info_minlen' => 'Longueur minimal du titre : ',
'info_traitement' => 'Post-traitement',
'info_titre' => 'Titre en minuscule : ',
'info_antispam' => 'Anti-spam : ',

// L
'la_rubrique' => 'la rubrique ',

// M
'min_len' => 'Longueur minimal du titre',

'motclefs_active' => 'Autoriser la gestion des mots-cl&eacute;s ?&nbsp;',
'motclefs_oui' => 'Les utilisateurs pourront choisir leurs mot-cl&eacute;s',
'motclefs_non' => 'Les utilisateurs ne pourront pas choisir leurs mot-cl&eacute;s',
'motclefs_explique2' => 'Pour choisir plusieurs mots-cl&eacute;s, maintenir la touche SHIFT ou CTRL (Pomme sur les Macs) enfonc&eacute;e',
'motclefs_liste' => 'Liste des mots-cl&eacute;s associ&eacute;s &agrave; votre article :',
'motclefs_ajouter' => '&nbsp;&nbsp;Ajouter un mot-cl&eacute;s',
'motclefs_explique3' => 'Cette option permet de cr&eacute;er de nouveaux mots-cl&eacute;s et de les associer &agrave; votre article.',
'motclefs_explique4' => 'Pour ajouter des mots-cl&eacute;s &agrave; votre article, saississez des mots s&eacute;par&eacute;s par un espace ci-dessous. Vous pouvez utiliser les mots-cl&eacute;s qui vous seront sugg&eacute;r&eacute;s pendant la saisie, ou bien en cr&eacute;er des nouveaux.',
'motclefs_explique5' => 'Pour des mots compos&eacute;s, saisir l\'expression entre guillemets. Exemple de saisie ',
'motclefs_explique6' => 'politique "&eacute;conomie solidaire"',
'motclefs2' => 'mots-cl&eacute;s',

// O
'op_config' => 'Configuration du plugin openPublishing',
'op_info' => 'infos &agrave; propos de la configuration du plugin openPublishing',
'configure_op' => 'Configurer le plugin openPublishing',
'op_voir_info' => '<b>Configuration actuelle de l\'openPublishing :</b>',
'op_modifier_info' => '<b>Cette page est uniquement accessible aux responsables du site.</b><p /> Elle vous permet de modifier votre configuration de l\'openPublishing. <p /> Les modifications effectu&eacute;es dans ces pages influent notablement sur le fonctionnement de votre site. Nous vous recommandons de ne pas y intervenir tant que vous n\'&ecirc;tes pas familier du fonctionnement du plugin openPublishing. <p /> <b>Plus g&eacute;n&eacute;ralement, il est fortement conseill&eacute; de laisser la charge de ces pages au webmestre principal de votre site.</b>',
'op_fonctions_info' => '<b>Cette page est uniquement accessible aux responsables du site.</b><p /> Elle vous permet d\'activer ou modifier les fonctions avanc&eacute;es de l\'openPublishing. <p /> Les modifications effectu&eacute;es dans ces pages influent notablement sur le fonctionnement de votre site. Nous vous recommandons de ne pas y intervenir tant que vous n\'&ecirc;tes pas familier du fonctionnement du plugin openPublishing. <p /> <b>Plus g&eacute;n&eacute;ralement, il est fortement conseill&eacute; de laisser la charge de ces pages au webmestre principal de votre site.</b>',
'op_effacer_info' => '<b>Cette page est uniquement accessible aux responsables du site.</b><p /> Elle vous permet de supprimer proprement le plugin openPublishing. <p /> Les modifications effectu&eacute;es dans ces pages influent notablement sur le fonctionnement de votre site. Nous vous recommandons de ne pas y intervenir tant que vous n\'&ecirc;tes pas familier du fonctionnement du plugin openPublishing. <p /> <b>Plus g&eacute;n&eacute;ralement, il est fortement conseill&eacute; de laisser la charge de ces pages au webmestre principal de votre site.</b>',
'op_modifier_info' => '<b>Cette page est uniquement accessible aux responsables du site. </b><p /> Elle vous permet de modifier les diff&eacute;rentes options de l\'openPublishing.</b>',
'op_raccourcis_documentation' => '<a href=\'https://.indymedia.org/view/Local/ImcLilleSite\'>Documentation du plugin openPublishing</a>',
'op_configuration_voir_general' => 'Configuration du plugin openPublishing',
'op_configuration_effacer' => 'D&eacute;sinstaller le plugin openPublishing',
'op_configuration_modifier' => 'Configuration du plugin openPublishing',
'op_configuration_surtitre' => 'Modules inclus dans le surtitre.',
'op_configuration_titre_principal' => 'Modules inclus dans le titre principal.',
'op_configuration_sous_titre' => 'Modules inclus dans le sous titre.',
'op_configuration_titre_lateral' => 'Modules inclus dans le titre lat&eacute;ral.',
'op_configuration_menu_principal' => 'El&eacute;ments du menu principal de navigation.',
'op_configuration_barre_laterale' => 'Modules inclus dans la barre lat&eacute;rale.',
'op_configuration_mentions_techniques' => 'Modules inclus dans le pied de page',	
'op_info_base_ok' => 'Le plugin openPublishing est correctement install&eacute;',
'op_info_base_ko' => 'Les tables de donn&eacute;e du plugin openPublishing doivent-&ecirc;tre install&eacute;es.',
'op_info_deja_ko' => 'Les tables de donn&eacute;e du plugin openPublishing a &eacute;t&eacute; d&eacute;sinstall&eacute;e',
'op_info_base_up' => 'Attention, la base de donn&eacute;e du plugin openPublishing doit &ecirc;tre upgrader. Cela est du &agrave; un changement de version du plugin openPublishing. Appuyez sur le bouton pour continuer.',
'op_info_base_ko_bis' => 'Le plugin openPublishing n&eacute;cessite l\'installation de trois tables suppl&eacute;mentaire pour fonctionner. Si vous n\'installez pas ces tables, alors le plugin ne fonctionnera pas.',
'op_info_desinstal' => '<b>Cette commande efface la base de donn&eacute;e cr&eacute;&eacute;e lors de l\'installation du plugin openPublishing.</b><p />  La base de donn&eacute;e de spip n\'est pas effac&eacute;e. Si vous souhaitez supprimer le plugin openPublishing, alors vous devrez &eacute;galement supprimer par ftp le contenu du dossier plugin openPublishing',
'op_position_info' => '1. Que voulez-vous faire ?',
'op_restriction_info' => '2. Positionnement sur le site :',
'op_titre_info' => '3. Titre de l\'&eacute;l&egrave;ment',
'op_descriptif_info' => '4. Descriptif de l\'&eacute;l&egrave;ment',
'op_texte_info' => '5. Texte &agrave; afficher ou fichier &agrave; inclure',
'op_style_info' => '6. Style attach&eacute;',
'op_modifier_creer' => 'Ajouter un &eacute;l&egrave;ment de configuration',
'op_modifier_editer' => 'Modifier un &eacute;l&egrave;ment de configuration',
'op_vide_virgule' => 'tueur de bug &agrave; cause de la virgule',
'op_voir_configuration' => 'Voir la configuration',
'op_modifier_configuration' => 'Modifier la configuration',
'op_supprimer_op' => 'Supprimer openPublishing',
'obligatoire' => 'obligatoire',
'optionel' => 'optionel',

// P



'previsualisation' => 'Pr&eacute;visualisation',
'previsualiser' => 'Pr&eacute;visualiser',
'post_traitement' => 'Post-traitement des textes',
'publier_agenda' => 'Publier en tant que br&egrave;ve dans l\'agenda',

// R
'redigez_article' => 'R&eacute;diger votre article',
'renvoi_explique' => 'Les textes de renvois sont les petites phrases que le plugin affiche lorsqu\'une publication c\'est soit d&eacute;roul&eacute;e normallement, soit termin&eacute;e par un abandon (les balises HTML sont permises).',
'renvoi_explique2' => 'Les redirections permettent de diriger l\'utilisateur vers une page de votre site (de type "/spip.php?page=sommaire").',
'renvoi_gestion' => 'Gestion des renvois',
'renvoi_normal' => 'texte de renvoi normal',
'renvoi_abandon' => 'texte de renvoi lors d\'un abandon',
'renvoi_modif' => 'modifier ce texte',
'redirection_normal' => 'redirection normale :',
'redirection_abandon' => 'redirection lors d\'un abandon :',
'redirection_modif' => 'modifier ces adresses',
'rubrique_gestion' => 'Gestion des rubriques openPublishing',
'rubrique_ajouter' => 'ajouter cette rubrique',
'rubrique_pasencore' => 'Vous n\'avez pas encore de rubriques openPublishing ...',
'rubrique_liste' => 'liste des rubriques openPublishing :',
'rubrique_explique' => 'Indiquez ici les rubriques sur lesquelles vous permettez l\'openPublishing. Attention, les rubriques doivent exister ! Cliquez sur la croix pour supprimer votre selection.',
'resultat' => 'r&eacute;sultat ...',

// S
'statut_select' => 'Quel statut pour les articles publi&eacute;s ?&nbsp;',
'statut_publie' => 'Les articles seront publi&eacute;s avec le statut "publie"',
'statut_prop' => 'Les articles seront publi&eacute;s avec le statut "prop"',
'statut_prepa' => 'Les articles seront publi&eacute;s avec le statut "prepa"',
'suprimmer_op' => 'Supprimer openPublishing',

// T
'titre_minuscule' => 'Post-traitement du titre',
'titre_impo_minuscule' => 'Les titres sont impos&eacute;s en minuscule',
'titre_non_minuscule' => 'Les titres peuvent utiliser des majuscules',
'traitement_explique' => 'Ces traitements seront appliqu&eacute;s lorsque l\'utilisateur validera son texte.',
'tagmachine_active' => 'Autoriser la gestion des mots-cl&eacute;s par le plugin Tag Machine ?&nbsp;',
'tagmachine_oui' => 'Les utilisateurs pourront utiliser le plugin Tag Machine',
'tagmachine_non' => 'Les utilisateurs ne pourront pas utiliser le plugin Tag Machine',

//U
'upload_active' => 'Autoriser l\'upload de document ?&nbsp;',
'upload_oui' => 'Les utilisateurs pourront uploader des documents',
'upload_non' => 'Les utilisateurs ne pourront pas uploader des documents',
'upgrader' => 'Upgrader les tables openPublishing',


// V
'voir_configuration' => 'Voir la configuration',
'votre_nom' => 'Votre nom ou pseudo',
'votre_mail' => 'Votre email',
'votre_groupe' => 'Votre groupe &eacute;ventuel',
'votre_phone' => 'Votre num&eacute;ro de tel'
);

?>