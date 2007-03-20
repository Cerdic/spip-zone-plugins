<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP nommé admin_lang genere le NOW()
// langue / language = fr

$GLOBALS[$GLOBALS['idx_lang']] = array(


// A
'accueil_aide' => 'Aide : vous &ecirc;tes perdu ? Trouvez de l\'aide dans cette rubrique.                                                                                                                                     ',
'accueil_commentaire' => 'Vous g&eacute;rez ici les habillages de votre site. Choisissez dans les rubriques suivantes :                                                                                                                                                 ',
'accueil_extras' => 'Extras : personnalisation de votre site avec des &laquo;options&raquo; (comportements) telles que : encadr&eacute;s arrondis, typographie particuli&egrave;re etc...                                                                                         ',
'accueil_general' => '                                                                                                   Vous modifiez ici les habillages de votre site. Nous vous conseillons de lire l\'aide (bouton Aide dessus).                                                      ',
'accueil_general_logos' => 'logo(s) actuel(s) de votre site                                                                                                                                             ',
'accueil_general_maintenance' => 'Nettoyage de la base de donn&eacute;es (toujours faire cette op&eacute;ration avant de d&eacute;sactiver le plugin habillages). &lt;strong&gt;Attention, ceci remet tout &agrave; z&eacute;ro (squelettes, styles, etc.). [Nettoyer]                                                                                                                                                          ',
'accueil_general_maintenance_titre' => 'Maintenance                                                                                                                                                    ',
'accueil_general_squelettes' => 'Jeu de squelettes actuel de votre site                                                                        ',
'accueil_general_styles' => 'Th&egrave;me actuel de votre site                                                                       ',
'accueil_icones' => 'Ic&ocirc;nes : d\'autres ic&ocirc;nes pour agr&eacute;menter l\'espace priv&eacute; de votre site SPIP.                                                                                                                                     ',
'accueil_infos' => 'Les &laquo; habillages &raquo; sont les &eacute;l&eacute;ments qui structurent et colorent un site SPIP, partie publique et partie priv&eacute;e : couleurs, position des textes, typographie, formes des encadr&eacute;s, logos , ic&ocirc;nes, etc...                                             ',
'accueil_infos_deux' => 'vous pouvez changer l\'habillage de votre site public, c\'est-&agrave;-dire les pages qui sont visibles par les internautes via les rubriques &laquo; squelettes &raquo; et &laquo; th&egrave;mes &raquo; (si cette derni&egrave;re rubrique n\'est pas visible, c\'est qu\'il n\'y a pas de th&egrave;mes pour le squelette choisi).                                       ',
'accueil_infos_titre' => 'Bienvenue                                            ',
'accueil_infos_trois' => 'vous pouvez changer les ic&ocirc;nes de l\'espace priv&eacute; via la rubrique &laquo; ic&ocirc;nes &raquo;                                      ',
'accueil_logos' => 'Logoth&egrave;que : des gammes de logos pour habiller votre site.                                                                                                                                                       ',
'accueil_squelettes' => 'Jeu de squelettes : c\'est l\'armature (disposition des textes) de votre site. Ici diff&eacute;rents jeux de squelettes propos&eacute;s.                                                                                                                                                       ',
'accueil_themes' => 'Th&egrave;mes : palette de couleurs et typographies adapt&eacute;es au jeu de squelettes que vous avez choisi.                                                                                                                                       ',
'accueil_titre' => 'G&eacute;rer l\'apparence de votre site                                                                                                                                                          ',
'accueil_titre_prive' => 'Habillages espace priv&eacute; :                                          ',
'accueil_titre_public' => 'Habillages site :                                          ',
'aide' => 'Ce plugin est en d&eacute;veloppement. Par cons&eacute;quent tout son fonctionnement et les commentaires qui lui sont associ&eacute;s sont &agrave; consid&eacute;rer comme truff&eacute;s d\'erreurs. Merci de revenir quand ce plugin sera en test : il sera plus fiable.
Squelettes, styles et logos pr&ecirc;ts &agrave; l\'emploi. Choisissez d\'abord un jeu de squelettes dans la rubrique du m&ecirc;me nom. Le squelette est l\'armature de toute page de votre site, le jeu de squelettes l\'ensemble des squelettes de vore site.  Le squelette correspond &agrave; la disposition de vos textes dans votre site. Choisissez ensuite un th&egrave;me dans la rubrique du m&ecirc;me nom. A chaque jeu de squelettes correspondent aucun, un ou plusieurs th&egrave;mes. Des th&egrave;mes diff&eacute;rents s\'affichent donc en fonction du jeu de squelettes choisi. Le th&egrave;me correspond &agrave; la fa&ccedil;on dont telle partie sera mise en relief ou non, &agrave; la taille des caract&egrave;res, des titres, aux effets de styles, etc... La logoth&egrave;que rassemble des boutons que vous pourrez ins&eacute;rer dans votre site selon votre choix.

Personnalisation et gestion du plugin Habillages : qu\'est-ce qu\'il y a sous le capot ?
Vous etes concepteur/trice et vous souhaitez faire des th&egrave;mes compatibles avec ce plugin, la d&eacute;marche est expliqu&eacute;e ci-dessous. Si vous ne comprenez pas les termes &laquo; tranfert FTP &raquo; ou &laquo; syst&egrave;me de fichiers &raquo;, vous ne pourrez pas mettre en oeuvre les conseils pr&eacute;conis&eacute;s ici.
- Personnaliser les squelettes
- Personnaliser les styles
- Personnaliser les images/logos
-le logo du site
-&gt; les th&egrave;mes de logos
Si vous ne voulez pas donner la possibilit&eacute; de personnaliser s&eacute;par&eacute;ment les logos de rubriques et les logos d\'articles, vous pouvez fournir des th&egrave;mes. Un th&egrave;me regroupe d\'autorit&eacute; les logos des rubriques et ceux des articles : le choix d\'un th&egrave;me s&eacute;lectionne toutes les images pr&eacute;selectionn&eacute;es. Par contre, vous avez la possibilit&eacute; de donner la possibilit&eacute; de ne personnaliser que les logos de rubriques ou d\'articles de facon s&eacute;par&eacute;e. Les &eacute;tapes sont indiqu&eacute;es ci-dessous.
* les logos de rubriques
* les logos d\'articles
- Personnaliser les ic&ocirc;nes                                                                                                                                                        ',
'aide_avance_deux' => 'Si le module que vous souhaitez ajouter fonctionne en plugin (i.e. si vous obtenez les r&eacute;sultats escompt&eacute;s lorsque vous le mettez dans votre dossier \&quot;plugins\&quot; et que vous l\'activez), vous devez mettre dans le dossier de votre plugin un fichier theme.xml.                                                                                                                      ',
'aide_avance_titre' => 'Comment ajouter ses propres squelettes, ses propres th&egrave;mes, ses propres logos et extras ?                                                                                                                      ',
'aide_avance_un' => 'Assurez-vous d\'abord que ce que vous souhaitez ajouter (jeu de squelettes, th&egrave;me, extras, logos, icones) fonctionne en plugin dans le sens de SPIP.                                                                                                                      ',
'aide_debutant_deux' => 'Choisissez ensuite un style dans la rubrique du m&ecirc;me nom. A chaque squelette correspondent un ou plusieurs styles. Des styles diff&eacute;rents s\'affichent donc en fonction du squelette choisi. Le style correspond &agrave; la fa&ccedil;on dont telle partie sera mise en relief ou non, &agrave; la taille des caract&egrave;res, des titres, aux effets de styles, etc...                                                                                                                    ',
'aide_debutant_titre' => 'Squelettes, th&egrave;mes, logos, et extras pr&ecirc;ts &agrave; l\'emploi                                                                                                                    ',
'aide_debutant_un' => 'Choisissez d\'abord un jeu de squelettes dans la rubrique du m&ecirc;me nom. Le squelette est l\'armature d\'une page de votre site, ou la disposition de vos textes dans votre site. Le jeu de squelettes est l\'ensemble des squelettes de votre site.                                                                                                                    ',
'aide_habillages_icones' => 'Aide  ',
'aide_infos' => 'S&eacute;lectionnez ci-contre les rubriques d\'aide qui correspondent le mieux &agrave; ce que vous voulez faire. ',
'aide_infos_titre' => 'Consultez l\'aide en ligne ',

// C
'config_base' => 'configuration',
'config_base_acc' => 'Configuration (utilisatrices et utilisateurs avanc&eacute;(e)s)',

// E
'extras_base' => 'extras                                                                 ',
'extras_base_acc' => 'Extras (si disponible pour le jeu de squelettes choisi)                                                             ',
'extras_definition' => 'Un extra est un comportement ajout&eacute; aux pages de votre site. Ces comportements peuvent &ecirc;tre multiples et sont r&eacute;alis&eacute;s au gr&eacute; de la cr&eacute;ativit&eacute; de leurs d&eacute;veloppeurs : effets d\'ombre et de lumi&egrave;re, d&eacute;roulement de menus en fondu, ajout de     &laquo; petits plus &raquo; en tout genre &eacute;tant li&eacute;s &agrave; la pr&eacute;sentation visuelle des pages de votre site.                   ',
'extras_intro' => 'Vous pouvez choisir plusieurs extras si vous le souhaitez.                  ',
'extras_titre' => 'G&eacute;rez les extras de votre site                  ',
'extras_titre_boitinfo' => 'Qu\'est-ce qu\'un extra ?                    ',


// I
'icone_config_habillages' => 'Gestion des habillages                                                                                                                                                          ',
'icone_habillages_extras' => 'Gestion des extras                     ',
'icone_habillages_icones' => 'Gestion des ic&ocirc;nes de l\'espace priv&eacute;                                                                                                                                                          ',
'icone_habillages_images' => 'Gestion des logos                                                                                                                                                          ',
'icone_habillages_squelettes' => 'Gestion des squelettes.                                                                                                                                                          ',
'icone_habillages_styles' => 'Gestion des styles                                                                                                                                                          ',
'icone_habillages_themes' => 'Gestion des th&egrave;mes                            ',
'icones_base' => 'icones                                                                 ',
'icones_base_acc' => 'Ic&ocirc;nes de l\'espace priv&eacute;                                                             ',
'icones_commentaires' => 'Un choix d\'ic&ocirc;nes pour agr&eacute;menter votre interface priv&eacute;e. En user sans mod&eacute;ration.                                                                                                                                                          ',
'icones_defaut_titre' => 'Revenir aux ic&ocirc;nes d\'origines                                          ',
'icones_infos' => 'Choisissez un th&egrave;me d\'ic&ocirc;nes et validez : vos ic&ocirc;nes changeront automatiquement.                                  ',
'icones_infos_titre' => 'Changez les ic&ocirc;nes de votre espace priv&eacute;                                  ',
'intro_select_gestionnaire' => 'Vous pouvez choisir ci-dessous les gestionnaires que vous pouvez activer. Par d&eacute;faut, tous les gestionnaires sont activ&eacute;s.                                                    ',


// L
'lien_aide_on' => 'Lien vers la rubrique d\'aide.                                                                                                                                    ',
'lien_extras_off' => 'On est dans la modif des extras.                                                                                                                                      ',
'lien_extras_on' => 'Lien vers la modif des extras.                                                                                                                                      ',
'lien_icones_on' => 'Lien vers la rubrique de gestion des ic&ocirc;nes.                                                                                                                                    ',
'lien_logos_off' => 'Vous &ecirc;tes actuellement dans la rubrique de choix de logos.                                                                                                                                                          ',
'lien_logos_on' => 'Cliquez ici pour modifier vos logos                                                                                                                                                          ',
'lien_squelettes_off' => 'Vous &ecirc;tes actuellement dans la rubrique de choix de squelettes.                                                                                                                                                          ',
'lien_squelettes_on' => 'Cliquez ici pour modifier vos squelettes.                                                                                                                                                          ',
'lien_styles_off' => '                                                                                                                                                        ',
'lien_styles_on' => '                                                                                                                                                       ',
'lien_themes_on' => 'Lien th&egrave;me vers rubrique th&egrave;mes                                                                                                                                           ',
'logos_base' => 'logos                                                                 ',
'logos_base_acc' => 'Logos (non fonctionnel pour l\'instant)                                                              ',


// M
'manager_plugin' => 'S&eacute;lectionner les gestionnaires que vous souhaitez utiliser                                  .                              ',


// N
'navigation_images' => 'Vous pouvez changer plusieurs types d\'images sur votre site. Si vous ne souhaitez pas faire ces d&eacute;marches une par une, choisissez juste un th&egrave;me ci-contre. Sinon cliquez sur les liens correspondants aux &eacute;l&eacute;ments que vous souhaitez modifier :&lt;br /&gt;&lt;br /&gt;
* Le logo de votre site&lt;br /&gt;
* Les logos de vos rubriques et articles&lt;br /&gt;
- Les logos de vos rubriques&lt;br /&gt;
- Les logos de vos articles&lt;br /&gt;&lt;br /&gt;                                                                                                                                                          ',


// P
'plugin_etat_developpement' => 'Cet habillage est en d&eacute;veloppement et sa stabilit&eacute;e n\'est pas assur&eacute;e.                                                                                                                                                          ',
'plugin_etat_experimental' => 'Attention : cet habillage est exp&eacute;rimental et il pourrait alt&eacute;rer le fonctionnement normal de votre site SPIP !                                                                                                                                                          ',
'plugin_etat_stable' => 'Cet habillage est stable.                                                                                                                                                          ',
'plugin_etat_test' => 'Cet habillage est en test. Si vous observez des disfonctionnements, vous pouvez en avertir son auteur.                                                                                                                                                          ',


// S
'squelettes_avance' => 'Les jeux de squelettes marqu&eacute;s de ce sigle n&eacute;cessitent d\'autres manipulations que leur seule activation. Celles-ci sont g&eacute;n&eacute;ralement indiqu&eacute;es dans le descriptif du jeu de squelettes. ATTENTION : si vous ne comprenez pas les instructions donn&eacute;es dans le descriptif d\'un jeu de squelettes avanc&eacute;s, il est pr&eacute;f&eacute;rable de ne pas l\'activer.                                                                                       ',
'squelettes_avance_titre' => 'Usage avanc&eacute;                                                                                                                        ',
'squelettes_base' => 'squelettes                                                                 ',
'squelettes_base_acc' => 'Squelettes (armature de votre site)                                                               ',
'squelettes_commentaire' => 'Dans cette rubrique, vous g&eacute;rez les squelettes de votre de votre site. Un squelette est l\'armature d\'une page. Il g&egrave;re quelles informations sont pr&eacute;sent&eacute;es sur quelles pages et o&ugrave; elles apparaissent.&lt;br /&gt;&lt;br /&gt;
Les captures d\'&eacute;cran son volontairement en noir et blanc. Pour mettre de la couleur, allez dans la rubrique &lt;i&gt;style apr&egrave;s avoir choisi un squelette ci-contre. &lt;/i&gt;                                                                                                                                                          ',
'squelettes_debutant' => 'Les squelettes marqu&eacute;s d\'un rouage sont activ&eacute;s en cochant simplement la case correspondant &agrave; leur nom.                                                                                                                    ',
'squelettes_debutant_titre' => 'Usage d&eacute;butant                                                                                                                        ',
'squelettes_defaut_description' => 'Ceci est le jeu de squelettes affich&eacute; par d&eacute;faut sur votre site.  S&eacute;lectionnez-le pour  revenir &agrave; la situation initiale, c\'est-&agrave;-dire pour habiller votre site avec votre squelette  original.                                                                                      ',
'squelettes_defaut_titre' => 'Squelettes par d&eacute;faut                                                                                                                             ',
'squelettes_dev' => 'Cet habillage est en d&eacute;veloppement, l\'utiliser peut mettre en panne votre site.                               ',
'squelettes_dist_description' => 'Vous trouverez ici les squelettes originaux de SPIP tels qu\'ils sont fournis dans la version originale.                                                                                                                ',
'squelettes_dist_titre' => 'Squelettes originaux de SPIP                                                                                                                ',
'squelettes_extras' => 'Lorsqu\'un jeu de squelettes est accompagn&eacute; de cette ic&ocirc;ne, cela signifie qu\'il y a un ou plusieurs comportement (extra) disponible. Le fait d\'activer ce jeu de squelettes g&eacute;n&egrave;re un bouton extras en haut de la page.                                                        ',
'squelettes_extras_titre' => 'Jeu de squelettes avec des extras                                                        ',
'squelettes_intro' => 'Choisissez le jeu de squelettes que vous souhaitez appliquer &agrave; votre site. Par d&eacute;faut, nous gardons votre jeu de squelettes actuel. Nous vous conseillons de lire la l&eacute;gende ci-contre.                             ',
'squelettes_stable' => 'Cet habillage est stable, vous pouvez l\'utiliser sans risque.                               ',
'squelettes_test' => 'Cet habillage est en test, il peut poser des probl&egrave;mes &agrave; l\'utilisation.                               ',
'squelettes_themes' => 'Lorsqu\'un jeu de squelettes est accompagn&eacute; de cette ic&ocirc;ne, cela signifie qu\'il y a une ou plusieurs variation graphique (th&egrave;me) disponible. Le fait d\'activer ce jeu de squelettes g&eacute;n&egrave;re un bouton th&egrave;mes en haut de la page.                                                        ',
'squelettes_themes_titre' => 'jeu de squelettes avec des th&egrave;mes                                                        ',
'squelettes_titre' => 'G&eacute;rer les squelettes de votre site                                                                                                                                                          ',
'squelettes_titre_boitinfo' => 'L&eacute;gende                                ',
'styles_titre' => 'G&eacute;rer les styles de votre site                                                                                                                                                          ',


// T
'tdb_titre' => 'Tableau de bord                                 ',
'texte_logos_article' => 'Choisissez un logo d\'articles ci-dessous. Si aucun ne vous convient, vous pouvez en t&eacute;l&eacute;charger un &agrave; partir de votre ordinateur.                                                                                                                                                          ',
'texte_logos_rubrique' => 'Choisissez un logo de rubriques ci-dessous. Si aucun ne vous convient, vous pouvez en t&eacute;l&eacute;charger un &agrave; partir de votre ordinateur.                                                                                                                                                          ',
'texte_logos_site' => 'Choisissez un logo de site ci-dessous. Si aucun ne vous convient, vous pouvez en t&eacute;l&eacute;charger un &agrave; partir de votre ordinateur.                                                                                                                                                          ',
'texte_logos_themes' => 'S&eacute;lectionnez un th&egrave;me de logos ci-dessous, ceux-ci se placeront automatiquement dans votre site. Un th&egrave;me correspond &agrave; un ensemble de logos d&eacute;clin&eacute;s pour un site, ses rubriques et ses articles. Si vous souhaitez des logos hors th&egrave;mes, choisissez-les ci-dessous.                                                                                          ',
'themes_base' => 'Th&egrave;mes                                                                 ',
'themes_base_acc' => 'Th&egrave;mes (si disponible pour le jeu de squelettes choisi)                                                               ',
'themes_defaut_description' => 'L\'option par d&eacute;faut ne change rien au squelette que vous avez choisi tant que vous ne choisissez pas un th&egrave;me ci-dessous. Si vous souhaitez donc garder l\'apparence graphique du squelette que vous avez choisi, cochez la case Th&egrave;me par d&eacute;faut.                         ',
'themes_defaut_titre' => 'Th&egrave;me par d&eacute;faut                         ',
'themes_intro' => 'Choisissez le th&egrave;me que vous souhaitez appliquer &agrave; votre site. Par d&eacute;faut, nous gardons le th&egrave;me par d&eacute;faut du jeu de squelettes que vous avez choisi.                         ',
'themes_titre' => 'G&eacute;rez les th&egrave;mes de votre site                           ',
'titre_logos_article' => 'Choix du logo de vos articles                                                                                                                                                          ',
'titre_logos_rubrique' => 'Choix du logo de vos rubriques                                                                                                                                                          ',
'titre_logos_secteurs' => 'Choix de vos logos par site, rubriques, et articles s&eacute;par&eacute;ment.                                                                                                                                                          ',
'titre_logos_site' => 'Choix du logo de votre site                                                                                                                                                          ',
'titre_logos_themes' => 'Choix de vos logos par th&egrave;me                                                                                                                                                          '


);

?>