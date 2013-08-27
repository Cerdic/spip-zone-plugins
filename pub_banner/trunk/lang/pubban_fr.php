<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/pub_banner/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'actif' => 'Activé',
	'affi_txt' => 'affichage(s)',
	'apercu' => 'Aperçu',
	'apercu_indisponible' => 'Aperçu non-disponible',
	'au' => ' au ',
	'aujourdhui' => 'Aujourd\'hui',
	'auteur' => 'Auteur',

	// B
	'banner' => 'Bannière',
	'banner_banner' => 'Bannière "Banner"',
	'banniere_desactivee' => 'Cette bannière est désactivée ... apreçu impossible.',
	'banniere_pub' => 'Bannière',
	'bannieres_pub' => 'Bannière(s)',
	'btn_active' => 'Activer',
	'btn_apercu' => 'Aperçu',
	'btn_desactive' => 'Désactiver',
	'btn_details' => 'Détails',
	'btn_editer' => 'Editer',
	'btn_imprimer' => 'Imprimer',
	'btn_inverser' => 'Inverser la liste',
	'btn_lister_empl' => 'Lister les publicités de cette bannière',
	'btn_modifier' => 'Modifier',
	'btn_reabiliter' => 'Récupérer',
	'btn_see_liste' => 'Voir la liste',
	'btn_supprimer' => 'Supprimer',
	'btn_voir' => 'Visualiser',

	// C
	'cacher_bordure' => 'Cacher les bordures des bannières',
	'campagne_date_debut' => 'Début de la campagne',
	'campagne_date_fin' => 'Fin de la campagne',
	'campagne_deroulement' => 'Déroulement de la campagne',
	'campagne_donnees_suivi' => 'Données de suivi',
	'campagne_presentation' => 'Présentation de la campagne',
	'campagne_statistiques' => 'Analyse statistique',
	'cf_navigation' => 'Cf. [colonne de navigation->@url@]',
	'clics' => 'Clics',
	'clics_txt' => 'clic(s)',
	'code_pub' => 'Code ou adresse de l\'objet à afficher',
	'comment_code_pub' => '<em>Pour une publicité de type \'image\' ou \'swf\', vous ne devez indiquer ici que l\'adresse url de cette image. Pour un objet flash, vous devez en indiquer le code complet ...</em>',
	'comment_dates' => 'Notez les dates sous la forme \'AAAA-MM-JJ\'',
	'comment_illimite' => '<em>Affichages et clics illimités ; vous pouvez préciser une date de début ou de fin de période d\'affichage.</em>',
	'comment_multiple_empl' => 'Vous pouvez sélectionner plusieurs bannières en utilisant la touche \'MAJ.\' de votre clavier.',
	'comment_ratio' => '(nombre de clics / nombre d′affichages)',
	'comment_url_optionnel' => 'Vous pouvez laisser ce champ vide, un clic sur la publicité renverra alors sur la page d\'achat des encarts publicitaires.',
	'confirm_delete' => 'Attention : vous avez demandé à mettre un encart publicitaire à la poubelle ...\\n\\nCliquez sur OK pour confirmer :',
	'confirm_delete_empl' => 'Attention : vous avez demandé à mettre unz bannière publicitaire à la poubelle ...\\n\\nCliquez sur OK pour confirmer :',
	'confirm_undelete' => 'Attention : vous avez demandé la réhabilitation d un encart publicitaire ...\\n\\nCliquez sur OK pour confirmer.',
	'confirm_vider_poubelle' => 'Êtes-vous sûr de vouloir vider la poubelle ?',
	'content_trash' => 'Contenu de la poubelle',
	'cube_banner' => 'Bannière "Cube"',

	// D
	'date_add' => 'Création',
	'date_creation' => 'Date de création',
	'date_debut' => 'Date de début de validité',
	'date_fin' => 'Date de fin de validité',
	'date_maj' => 'Dernière mise à jour',
	'dates_validite_pub' => 'Dates de validité',
	'debut' => 'Début',
	'derniers_jours' => 'derniers jours',
	'details_empl' => 'Détails d\'une bannière',
	'dimensions' => 'Dimensions',
	'doc_chapo' => 'Le plugin "Pub Banner" propose une gestion de bannières publicitaires pour des squelettes SPIP.',
	'doc_en_ligne' => 'Documentation du plugin sur Spip-Contrib',
	'doc_info' => 'Nous vous renvoyons à la doc du plugin pour plus d\'informations :',
	'doc_titre_court' => 'Documentation Pub Banner',
	'doc_titre_page' => 'Documentation du plugin "Pub Banner"',
	'docskel_sep' => '----',
	'documentation_1' => 'Le plugin PUB BANNER s\'installe de la même manière que l\'ensemble des plugins SPIP ({[article dédié sur spip.net->http://www.spip.net/fr_article3396.html]}).

Des valeurs par défaut sont entrées dans les tables, à savoir :
-* quatre bannières "type", les plus répandues sur le web :
-** {{[skyscraper->#skyscraper]}} : la longue bannière verticale, de 160 ou 180 sur 600 pixels,
-** {{[leaderboard->#leaderboard]}} : la longue bannière horizontale, de 728 sur 90 pixels,
-** {{[banner->#banner]}} : la bannière horizontale {standard}, de 468 sur 60 pixels,
-** {{[cube->#cube]}} : le carré, de 250 sur 250 pixels, bannière privilégiée pour les encarts flash,
-* cinq exemples d\'encart publicitaires, un pour chaque bannière, deux pour la "banner", avec divers options quand à la limite d\'affichage (nombre d\'affichages, de clics et dates encadrantes).

Ces bannières s\'intègrent dans vos squelettes en indiquant simplement la balise :
<cadre class="spip">
// identifiant "banner_id" de la banniere
#PUBBAN{banner_id}

// nom de la banniere
#PUBBAN{nom_de_la_banniere}

// ou ID de la banniere
#PUBBAN{id_banniere}
</cadre>
Suivie du nom de la bannière que vous souhaitez afficher. La balise est remplacée par une frame de la taille de la bannière.',
	'documentation_2' => 'Pour une raison pratique [[Spip Bonux est utilisé par Pub Banner pour sa fonctionnalité \'POUR\', qui permet de créer des boucles depuis des array PHP (ndlr) ...]], le plugin PUB BANNER nécessite d\'avoir installé au préalable le plugin {{Spip Bonux}} en version 1.3 au minimum.

-* Vous pouvez lire une description de ce plugin sur le site Spip-Contrib à l\'adresse : [->http://www.spip-contrib.net/SPIP-Bonux].
-* Vous pouvez le télécharger à l\'adresse : [->http://zone.spip.org/trac/spip-zone/browser/_plugins_/spip-bonux-2?rev=31575] ({ici en version 2}).
',
	'documentation_3' => 'Le plugin propose de suivre l\'efficacité des campagnes de plusieurs façons :
-* via la page de "statistiques" de l\'espace privé, qui présente différents graphes de suivi des affichages et des clics, selon plusieurs périodes au choix, pour chaque bannière,
-* via une page publique qui résume les valeurs de chaque publicité ({toutes, une seule ou plusieurs}) et permet de les exporter au fromat CSV ({[voir une exemple pour les pubs 1 et 2->@url_exemple@]}).',
	'documentation_info' => 'Documentation/Information',
	'download_flash_player' => 'La visualisation de cet objet nécessite le logiciel Adobe Flash Player. Cliquez ici pour l\'obtenir gratuitement.',
	'droits' => 'Droits ouverts sur la pub',
	'droits_aff_pub' => 'Nombre d\'affichages',
	'droits_clic_pub' => 'Nombre de clics',
	'droits_dates_pub' => 'Dates',

	// E
	'edit_pub_ok_bannieres_differents' => 'OK - Valeurs enregistrées mais les bannières sélectionnées pour la publicité ont des tailles différentes ... Cela pourra générer des erreurs d\'affichage.',
	'empl_is' => 'Cette bannière est',
	'en_pixels' => '<em>(en pixels)</em>',
	'en_pourcent' => '<em>(en %)</em>',
	'en_secondes' => '<em>(en secondes)</em>',
	'erreur_code' => 'Veuillez saisir le code de la publicité',
	'erreur_empl' => 'Vous n\'avez pas choisi de bannière pour votre publicité ...',
	'erreur_img_not_img' => 'L\'url saisie ne semble pas correspondre à une image ...',
	'erreur_img_not_url' => 'L\'adresse web saisie est inaccessible ...',
	'erreur_nb_aff' => 'Vous n\'avez pas précisé de nombre d\'affichage ...',
	'erreur_titre' => 'Vous devez indiquer un titre pour votre publicité (<em>il apparaitra au passage de la souris</em>)',
	'erreur_url' => 'Vous devez indiquer une adresse URL de redirection de la publicité',
	'erreur_url_no_response' => 'L\'adresse saisie ne répond pas ... êtes vous sûr qu\'elle soit valide ?',
	'erreur_url_not_url' => 'L\'adresse saisie ne semble pas être une adresse web ...',
	'error_dimensions_missing_empl' => 'Vous devez définir des dimensions pour votre bannière',
	'error_dimensions_numeric_empl' => 'Il semble qu\'il y ait eu une erreur de dimensions',
	'error_global' => 'Il semble qu\'une erreur soit survenue ...',
	'error_refresh_numeric_empl' => 'Il semble qu\'il y ait eu une erreur de valeurs, vous devez indiquer un nombre de secondes',
	'error_titre_empl' => 'Vous devez indiquer un titre pour votre bannière',
	'evo_empl' => 'Évolution des performances',
	'exemples_par_defaut' => 'Exemples (bannières par défaut)',
	'exporter' => 'Exporter',
	'exporter_csv' => 'Exporter les données au format CSV',
	'exporter_donnees' => 'Exporter ces données',

	// F
	'fermer' => 'Fermer',
	'fiche' => 'Fiche',
	'fin' => 'Fin',

	// G
	'gestion_pubban' => 'Gestion de bannières publicitaires',

	// H
	'height' => 'Hauteur',
	'home' => 'Retour au gestionnaire de pub',

	// I
	'icone_banniere' => 'Bannière',
	'icone_bannieres' => 'Bannières',
	'icone_modifier_banniere' => 'Modifier cette bannière',
	'icone_modifier_publicite' => 'Modifier cette publicité',
	'icone_nouvelle_banniere' => 'Créer une nouvelle bannière',
	'icone_nouvelle_publicite' => 'Créer une nouvelle publicité',
	'icone_publicite' => 'Publicité',
	'icone_publicites' => 'Publicités',
	'illimite' => 'Droits illimités',
	'imprimer' => 'Imprimer',
	'inactif' => 'Inactivé',
	'inactive' => 'Inactivée',
	'info_1_banniere' => 'Une bannière a été trouvée',
	'info_1_publicite' => 'Une publicité a été trouvée',
	'info_aucune_banniere' => 'Aucune bannière n\'a été trouvée',
	'info_aucune_publicite' => 'Aucune publicité n\'a été trouvée',
	'info_banniere' => 'Statut de la bannière',
	'info_banniere_active' => 'Bannière active',
	'info_banniere_inactive' => 'Bannière inactive',
	'info_banniere_poubelle' => 'Bannière à la poubelle',
	'info_doc' => 'Si vous rencontrez des problèmes pour afficher cette page, [cliquez-ici->@link@].',
	'info_doc_titre' => 'Note concernant l\'affichage de cette page',
	'info_evo' => '10 blocs * 10 jours (100 derniers jours)',
	'info_nb_bannieres' => '@nb@ bannières ont été trouvées',
	'info_nb_publicites' => '@nb@ publicités ont été trouvées',
	'info_publicite_active' => 'Publicité active',
	'info_publicite_creee' => 'Publicité créée',
	'info_publicite_inactive' => 'Publicité inactive',
	'info_publicite_obsolete' => 'Publicité obsolète',
	'info_publicite_poubelle' => 'Publicité à la poubelle',
	'info_publicite_rompue' => 'Publicité rompue',
	'info_ratio' => 'Ratio (clics/affichages)',
	'info_ratio_banniere' => 'Ratio de la bannière (<em>optionnel</em>)',
	'info_refresh_banniere' => 'Délai de rafraîchissement',
	'info_search_box' => '<em>Rechercher</em> > saisissez une référence, un mot ou groupe de mots à rechercher',
	'info_skel_doc' => 'Cette page de documentation est conçue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du répertoire "squelettes-dist/"}). Si vous ne parvenez pas à visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de gérer son affichage :

-* [Mode "texte simple"->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode "squelette Zpip"->@mode_zpip@] ({squelette Z compatible})
-* [Mode "squelette SPIP"->@mode_spip@] ({compatible distribution})',
	'info_stats' => 'Quelques chiffres ...',
	'info_statut_banniere_1' => 'Cette bannière est :',
	'info_statut_publicite_1' => 'Cette pub est :',
	'info_taille_banniere' => 'Dimensions de la bannière',
	'info_titre_banniere' => 'Titre de la bannière',
	'info_titre_banniere_active' => 'Active',
	'info_titre_banniere_inactive' => 'Inactive',
	'info_titre_banniere_poubelle' => 'À la poubelle',
	'info_titre_id_banniere' => 'Banner_ID',
	'info_titre_id_comment' => 'Si le champs est vide, cette valeur sera générée à partir du titre. <em>Il est fortement déconseillé d\'utiliser des caractères accentués ou spéciaux, cela pourrait provoquer une erreur lors de l\'appel de la balise PUBBAN ...</em>',
	'info_titre_publicite_active' => 'Active',
	'info_titre_publicite_creee' => 'Créée',
	'info_titre_publicite_inactive' => 'Inactive',
	'info_titre_publicite_obsolete' => 'Obsolète',
	'info_titre_publicite_poubelle' => 'À la poubelle',
	'info_titre_publicite_rompue' => 'Lien rompu',
	'infos_pub' => 'Contenu de la publicité',
	'infos_pubban' => 'Informations et conseils ...',
	'infos_texte' => '{{Le rendement d\'un espace publicitaire dépend principalement de deux composantes :
-* son format,
-* sa position sur la page.}}

{{{Format}}}

Généralement, les formats de publicités plus larges que hauts obtiennent un rendement supérieur grâce à leur grande convivialité.
Les informations y sont assimilées plus facilement car le lecteur peut lire davantage de texte sans avoir à changer de ligne.

{{{Positionnement}}}

Les nombreuses études statistiques montrent que les bannières publicitaires présentes en haut d\'une page web, haut de page et haut de contenu, ont de meilleures performances.

{{Les standards publicitaires apportent les clés pour proposer des bannières ou construire des publicités.}}

{{{Tailles classiques des bannières publicitaires & poids maximum}}}

-* {{la bannière}} : 468x60 px | 35 Ko
-* {{le skyscraper}} : 120x600 px | 50 Ko
-* {{le pavé}} : 300x250 px | 50 Ko
-* {{le carré}} : 250x250 px | 50 Ko
-* {{le bouton}} (logos ...) : jusqu\'à 120 px (120x60 px)

{{{Conseils}}}

-* Les fichiers proposés pour les publicités doivent faire moins de 50 Ko, pour ne pas gêner le chargement du contenu de la page.
-* Pour les animations, il est préférable de recommander des images de 15 secondes maximum.

{{{Les tarifs}}}
{{Deux principales méthodes de tarification :}}

-* {{CPM - Le coût pour mille}} ({affichages}) est la méthode la plus utilisée,  elle semble être devenue un standard en la matière.
-* {{CPC - Le coût au clic}}, juste derrière, est plus complexe à chiffrer.

{{Le forfait}} est également utilisé pour les campagnes hors normes : incrustation dans les pages, objet publicitaire intrusif, campagne active ...
',
	'installation' => 'Installation',
	'integer_edit' => 'Édition d\'une bannière',
	'intro_admin' => 'Gestion des <em>Bannières Publicitaires</em>',
	'intro_integer' => 'Les <em>bannières</em> : bannières publicitaires',
	'intro_integer_edit' => 'Édition de bannières',
	'intro_integer_edit_texte' => 'Pour appeler une bannière dans vos squelettes, indiquez la balise : <br /><center><b># PUBBAN{banner_id}</b></center><br />Si vous laissez le champ "Banner ID" vide, il sera généré automatiquement en utilisant le titre et en remplaçant les espaces par des <b>underscore</b>.<br /><br /><em>Nous attirons votre attention sur le commentaire concernant le "banner_id" des bannières : <u>évitez les caractères spéciaux</u> ! Si vous souhaitez les utiliser, faites de nombreux tests avant mise en ligne ...</em>',
	'intro_integer_texte' => 'Voici la liste des bannières inscrites sur le site.<br />Les bannières se caractérisent principalement par leurs dimensions et leur positionnement dans vos squelettes.<br /><br />Vous pouvez ici les <b>activer</b> ou les <b>désactiver</b>, les <em>jeter à la poubelle</em>, ainsi que les <b>modifier</b> ...<br />',
	'intro_pub' => 'Encarts publicitaires',
	'intro_pub_edit' => 'Édition d\'encarts publicitaires',
	'intro_pub_edit_texte' => 'Cette page vous permet d\'insérer ou de modifier une publicité selon des conditions particulières :<ul><li>pour <b>un nombre de clics</b> précis,</li><li>pour un <b>nombre d\'affichages</b> défini,</li><li>selon des <b>dates de validité précises</b>.</li></ul>',
	'intro_pub_texte' => 'Voici la liste des publicités inscrites sur le site.<br /><br />Vous pouvez ici les <b>activer</b> ou les <b>désactiver</b>, les <em>jeter à la poubelle</em>, ainsi que les <b>modifier</b> ou <b>obtenir un aperçu</b> ...<br /><br />Les publicités <em>obsolètes</em> ont une validité dépassée : nombre de clics ou d\'affichages atteints, dates révolues.',
	'intro_stats' => 'Statistiques',
	'intro_stats_banner' => 'Lecture des statistiques',
	'intro_stats_pub' => 'Lecture des statistiques',
	'intro_texte_stats_banner' => 'Les chiffres des statistiques permettent d\'estimer l\'efficacité des encarts publicitaires : en particulier, <b>le ratio global</b> indique le nombre de clic par rapport au nombre d\'affichage des encarts.<br /><br />Il est intéressant de comparer les ratios des bannières en fonction de leur positionnement notamment.<br /><br /><em>(cf. LICENSES en bas de page)</em><br />',
	'intro_texte_stats_pub' => '',

	// L
	'leaderboard_banner' => 'Bannière "Leaderboard"',
	'licence' => 'Copyright © 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] distribué sous licence [Creative Commons BY-SA|Creative Commons - Paternite - Distribution a l\'Identique->http://creativecommons.org/licenses/by-sa/3.0/].',
	'licence_stats' => '{{LICENCES :}}<br />{{\'wz_jsgraphics.js\'}} :: v. 2.33 - (c) 2002-2004 Walter Zorn ([www.walterzorn.com->http://www.walterzorn.com])<br />{{\'graph.js\', \'line.js\' & \'pie.js\'}} :: (c) Balamurugan S. 2005 ([www.jexp.com->http://www.jexp.com])',
	'lien_page' => 'Voir la page',
	'list_empl' => 'Liste des bannières',
	'liste_pub' => 'Liste des publiclités',
	'listing_empl' => 'Liste des publicités de cette bannière',

	// M
	'manque_date_fin' => 'Veuillez préciser une date de fin',

	// N
	'nb_affichages' => 'Nombre total d\'affichages',
	'nb_affires_pub' => 'Nombre d\'affichages restant',
	'nb_bannieres' => 'Nombre de bannières',
	'nb_clicres_pub' => 'Nombre de clics restant',
	'nb_clics' => 'Nombre total de clics',
	'nb_pub' => 'Nombre total de publicités',
	'nb_pub_actives' => 'Dont actives',
	'nb_pub_inactives' => 'Dont inactives',
	'nb_pub_obsoletes' => 'Dont obsolètes',
	'new_window' => 'Nouvelle fenêtre',
	'no_clic_for_emp' => 'Certains emplacements ne sont pas représentés dans le graphique car il n\'ont eu aucun clic dans la période choisie.',
	'no_clic_in_period' => 'Il n\'y a eu aucun clic dans la période choisie.',
	'no_datas_yet' => 'Il n\'y a pas encore de donnée statistique exploitable ...',
	'no_empl_found' => 'Bannière introuvable ...',
	'no_empl_yet' => 'Il n\'y a pas encore de bannière configurée ...',
	'no_limit' => 'illimité',
	'no_pub_active_yet' => 'Il n\'y a pas encore de publicité activée ...',
	'no_pub_found' => 'Publicité introuvable ...',
	'no_pub_yet' => 'Il n\'y a pas encore de publicité enregistrée ...',
	'no_results_match' => 'Aucune entrée ne correspond à votre recherche.',
	'non' => 'Non',
	'nouveau_empl' => 'Créer une nouvelle bannière',
	'nouveau_pub' => 'Créer une nouvelle publicité',
	'nouveau_pub_dans_banniere' => 'Ajouter une nouvelle publicité',
	'num_version_base' => 'Version des tables SQL',

	// O
	'obsolete' => 'Obsolète',
	'open_trash' => 'Ouvrir la poubelle',
	'oui' => 'Oui',
	'outils' => 'Outils',

	// P
	'page_infos' => 'Conseils et informations',
	'page_stats' => 'Page de statistiques',
	'pas_banniere_selectionne' => 'Vous n\'avez pas sélectionné de bannière ...',
	'perf_empl' => 'Performance des bannières',
	'period' => 'Période du ',
	'plugin_spip' => 'un plugin pour <b>SPIP 3.0+</b>',
	'poubelle' => 'À la poubelle',
	'poubelle_contenu' => 'Contenu de la poubelle',
	'pour' => 'Pour',
	'pratique' => 'Dans la pratique',
	'prerequis' => 'Pré-requis',
	'pub_actives' => 'Liste des publicités actives',
	'pub_edit' => 'Édition d\'un encart publicitaire',
	'pub_inactives' => 'Liste des publicités inactives',
	'pub_is' => 'Cette publicité est',
	'pub_obsoletes' => 'Liste des publicités obsolètes',
	'pubban' => 'Pub Banner',
	'pubban_stats_banner' => 'Statistiques des bannières',
	'pubban_stats_pub' => 'Statistiques des publicités',
	'pubban_titre' => 'Bannières publicitaires',
	'publicite_0' => '0 publicité',
	'publicite_1' => '1 publicité',
	'publicite_apercu' => 'Aperçu d\'une publicité',
	'publicites' => '@nb@ publicités',

	// R
	'ratio' => 'Ratio (clics/affichages)',
	'ratio_comment' => 'Quotient pages présentant la bannière / pages totales.',
	'ratio_pages' => 'Ratio de pages (visibilité)',
	'ratio_txt' => 'Ratio',
	'refresh_comment' => 'Délai après lequel le contenu de la bannière est rafraîchit ; pour annuler cette option, mettez la valeur <code>0</code>.',
	'refresh_time' => 'Délai de rafraîchissement',
	'reponse_form_def_droits' => 'Veuillez saisir des droits pour le publicité (une seule ligne)',
	'result_match' => 'entrée correspond à votre recherche.',
	'resultats_du' => 'Résultats de la derniere analyse au ',
	'results_match' => 'entrées correspondent à votre recherche.',
	'retirer_arg' => 'Retirer de la page',
	'retour_liste_empl' => 'Retour à la liste des bannières',
	'retour_liste_pub' => 'Retour à la liste complète des publicités',
	'retour_search' => 'Retour à la recherche',

	// S
	'search_pubban' => 'Rechercher dans les bannières',
	'search_results' => 'Résultats de votre recherche',
	'secondes' => 'secondes',
	'see_doc' => 'Voir la documentation (en interne)',
	'see_doc_in_new_window' => 'Ouvrir la doc dans une nouvelle fenêtre',
	'see_doc_in_texte_brut' => 'Voir la doc en texte brut (problèmes de squelette)',
	'select_articles_choose' => '&gt; liste de vos articles',
	'site_web' => 'Site web',
	'skyscraper_banner' => 'Bannière "Skyscraper"',
	'statistiques' => 'Statistiques',
	'statistiques_pubban' => 'Statistiques des bannières publicitaires',
	'stats' => 'Données statistiques',
	'stats_pubban' => 'Statistiques des publicités',
	'statut' => 'Statut',
	'statut_actuel' => 'Statut actuel',

	// T
	'target_blank' => 'Redirection dans une nouvelle fenêtre',
	'target_parent' => 'Redirection en fenêtre courante',
	'testing_page_code' => 'Code : ',
	'texte_admin' => 'Les publicités s\'organisent en différentes <b>bannières</b>, l\'objet affiché dans les squelettes, comportant chacune un pannel de publicités, les <b>encarts</b>, qui s\'affichent alternativement à chaque appel de bannière.<br /><br />Le plugin enregistre les nombres d\'affichages et de clics sur chaque encart, permettant de présenter des <b>statistiques détaillées</b>, utiles pour estimer le rendement des bannières.',
	'texte_brut' => 'Texte brut',
	'titre' => 'Intitulé',
	'titre_cadre_ajouter_empl' => 'Création d\'une bannière',
	'titre_cadre_ajouter_pub' => 'Création d\'une nouvelle publicité',
	'titre_cadre_modifier_empl' => 'Modification d\'une bannière',
	'titre_cadre_modifier_pub' => 'Édition d\'une publicité',
	'titre_info_empl' => 'BANNIÈRE NUMÉRO :',
	'titre_info_pub' => 'PUBLICITÉ NUMÉRO :',
	'titre_nouvel_empl' => 'NOUVELLE BANNIÈRE',
	'titre_tablo_banniere' => 'Bannière',
	'titre_tablo_code' => 'Code html de l\'objet à afficher',
	'titre_tablo_date' => 'Date d\'ajout',
	'titre_tablo_nom' => 'Titre de la publicité',
	'titre_tablo_url' => 'URL de redirection (au clic)',
	'trash_is_empty' => 'La poubelle est vide',
	'type' => 'Type',
	'type_empl' => 'Bannière',
	'type_encart' => 'Encart',
	'type_flash' => 'Objet flash autre',
	'type_img' => 'Objet de type image',
	'type_swf' => 'Objet flash .swf',

	// U
	'url_pub' => 'URL de redirection (au clic)',
	'url_stats_banniere' => 'URL publique de statistiques de la bannière :',
	'url_stats_publicite' => 'URL publique de statistiques de la publicité :',
	'url_traceur' => 'URL du traceur de développement SVN (spip-zone)',
	'url_update' => 'URL de téléchargement',

	// V
	'valider_pour_forcer' => 'Validez à nouveau pour forcer l\'enregistrement de cette valeur ...',
	'vider_trash' => 'Vider la poubelle',
	'view_pub' => 'Détails d\'un encart publicitaire',
	'voir_bordure' => 'Voir les bordures des bannières',
	'voir_les_statistiques' => 'Voir les statistiques',
	'voir_page' => '<br /><b>Voir la page :</b>',
	'voir_un_apercu' => 'Voir un aperçu',

	// W
	'width' => 'Largeur'
);

?>
