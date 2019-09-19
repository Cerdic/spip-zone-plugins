<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/noizetier/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_composition' => 'Activer les compositions',
	'apercu' => 'Aperçu',
	'aucun_type_noisette' => 'Aucun type de noisette chargé.',

	// B
	'bloc_sans_noisette' => 'Ajouter des noisettes en utilisant le bouton “ajouter une noisette” ou en glissant-déposant le type de noisette désiré sur cet emplacement.',
	'bouton_configurer_noisettes_objet' => 'Configurer pour ce contenu',
	'bulle_activer_composition' => 'Activer les compositions sur le type de contenu « @type@ »',
	'bulle_configurer_composition_noisettes' => 'Configurer les noisettes de la composition',
	'bulle_configurer_objet_noisettes' => 'Configurer les noisettes spécifiques à ce contenu',
	'bulle_configurer_page_noisettes' => 'Configurer les noisettes de la page',
	'bulle_creer_composition' => 'Créer une composition virtuelle de la page « @page@ »',
	'bulle_dupliquer_composition' => 'Créer une composition virtuelle copiée de la composition « @page@ »',
	'bulle_modifier_composition' => 'Editer la composition',
	'bulle_modifier_page' => 'Editer la page',

	// C
	'choisir_noisette' => 'Choisissez la noisette que vous voulez ajouter :',
	'compositions_non_installe' => '<b>Plugin compositions :</b> ce plugin n’est pas installé sur votre site. Il n’est pas nécessaire au fonctionnement du noizetier. Cependant, s’il est activé, vous pourrez déclarer des compositions directement dans le noizetier.',
	'configurer_ajax_noisette_label' => 'Inclusion Ajax',
	'configurer_balise_noisette_label' => 'Encapsulation des noisettes',
	'configurer_dynamique_noisette_label' => 'Inclusion dynamique',
	'configurer_objets_noisettes_explication' => 'Sur ces types de contenus, il sera permis de personnaliser les noisettes <strong>contenu par contenu</strong>.',
	'configurer_objets_noisettes_label' => 'Autoriser la personnalisation par contenu sur :',
	'configurer_profondeur_max_label' => 'Profondeur d’imbrication',
	'configurer_titre' => 'Configurer le plugin noiZetier',
	'configurer_types_noisettes_masques_explication' => 'Cochez les types de noisettes que vous ne <strong>souhaitez pas</strong> proposer lors de l’ajout d’une noisette.',
	'configurer_types_noisettes_masques_label' => 'Types de noisettes proposés',
	'copie_de' => 'Copie de @source@',

	// D
	'description_bloc_contenu' => 'Contenu principal de chaque page.',
	'description_bloc_extra' => 'Informations extra contextuelles pour chaque page.',
	'description_bloc_navigation' => 'Informations de navigation propres à chaque page.',
	'description_bloctexte' => 'Le titre est optionnel. Pour le texte, vous pouvez utiliser les raccourcis typographiques de SPIP.',

	// E
	'editer_composition' => 'Modifier cette composition',
	'editer_composition_heritages' => 'Définir les héritages',
	'editer_configurer_page' => 'Configurer les noisettes de cette page',
	'editer_noizetier_explication' => 'Sélectionnez la page dont vous souhaitez configurer les noisettes.',
	'editer_noizetier_explication_objets' => 'Sélectionnez le contenu dont vous souhaitez personnaliser les noisettes.',
	'editer_noizetier_titre' => 'Gérer les noisettes',
	'editer_nouvelle_page' => 'Créer une nouvelle page / composition',
	'erreur_ajout_noisette' => 'Les noisettes suivantes n’ont pas été ajoutées : @noisettes@',
	'erreur_aucune_noisette_selectionnee' => 'Vous devez sélectionner une noisette !',
	'erreur_deplacement_noisette' => 'Le noisette @noisette@ n’a pas été déplacée.',
	'erreur_doit_choisir_noisette' => 'Vous devez choisir une noisette.',
	'erreur_mise_a_jour' => 'Une erreur s’est produite pendant la mise à jour de la base de donnée.',
	'erreur_page_inactive' => 'La page est inactive car le ou les plugins suivants sont désactivés : @plugins@.',
	'erreur_saisie_css_invalide' => 'La syntaxe de saisie des CSS est erronée (mot, tiret et espace autorisés).',
	'erreur_type_noisette_indisponible' => 'Le type de noisette @type_noisette@ n’est plus disponible car le plugin qui fournit ce type noisette doit être désactivé.',
	'explication_code' => 'ATTENTION : pour utilisateur avancé. Vous pouvez saisir du code Spip (boucles et balises) qui sera interprété comme s’il s’agissait d’un squelette. La noisette aura par ailleurs accès à toutes les variables de l’environnement de la page.',
	'explication_composition' => 'Composition dérivée de la page « @type@ »',
	'explication_composition_virtuelle' => 'Composition <strong>virtuelle</strong> dérivée de la page « @type@ »',
	'explication_copie_noisette_conteneur' => 'Les paramètres de configuration s’appliqueront à la noisette conteneur et aussi à toutes les noisettes incluses qui seront aussi dupliquées.',
	'explication_copie_noisette_parametres' => 'Choisissez les paramètres de configuration de la noisette source que vous souhaitez copier sinon les valeurs par défaut seront utilisées.',
	'explication_copie_pages_compatibles' => 'Choisissez les pages dans lesquelles créer une noisette du même type que celui de la noisette source.',
	'explication_description_code' => 'À usage interne. Non affichée sur le site public.',
	'explication_dupliquer_composition_reference' => 'L’identifiant de la page dupliquée est <i>@composition@</i>.
	Vous pouvez choisir un nouvel identifiant ou suffixer l’identifiant de référence ainsi : <i>@composition@<strong>_suffixe</strong></i>',
	'explication_dupliquer_composition_suffixer' => '.',
	'explication_glisser_deposer' => 'Vous pouvez les ajouter par glisser-déposer.',
	'explication_heritages_composition' => 'La composition en cours d’édition est basée sur le type de contenu « @type@ » qui possède des types de contenu enfants. Vous pouvez définir pour chaque type de contenu enfant une composition à appliquer par défaut.',
	'explication_noisette' => 'Noisette de type « @noisette@ »',
	'explication_noisette_css' => 'Vous pouvez ajouter à la capsule englobant la noisette d’éventuelles classes CSS supplémentaires.',
	'explication_noizetier_balise' => 'Le mode par défaut d’encapsulation peut être modifié unitairement pour chaque noisette (paramètrage).',
	'explication_noizetier_cfg_constant' => 'Cette valeur est actuellement définie via une constante, elle ne peut pas être changée ici.',
	'explication_noizetier_profondeur_max' => 'Les noisettes peuvent s’imbriquer au moyen des noisettes de type conteneur. Vous pouvez définir un nombre maximal de niveaux d’imbrication.',
	'explication_objet' => 'Contenu de type « @type@ »',
	'explication_page' => 'Page autonome non liée à un type de contenu',
	'explication_page_objet' => 'Page liée au type de contenu « @type@ »',
	'explication_raccourcis_typo' => 'Vous pouvez utiliser les raccourcis typographiques de SPIP.',

	// F
	'formulaire_ajouter_noisette' => 'Ajouter une noisette',
	'formulaire_ajouter_noisette_bloc' => 'Ajouter une noisette au bloc',
	'formulaire_ajouter_noisette_conteneur' => 'Ajouter une noisette au conteneur',
	'formulaire_blocs_exclus' => 'Blocs à exclure',
	'formulaire_composition' => 'Identifiant de composition',
	'formulaire_composition_erreur' => 'Requête non aboutie pour la composition',
	'formulaire_composition_explication' => 'Mot-clé unique (minuscules, sans espace, sans tiret et sans accent) permettant d’identifier la composition.',
	'formulaire_composition_mise_a_jour' => 'Composition mise à jour',
	'formulaire_configurer_bloc' => 'Configurer le bloc :',
	'formulaire_configurer_page' => 'Configurer la page :',
	'formulaire_creer_composition' => 'Créer une composition',
	'formulaire_deplacer_bas' => 'Déplacer vers le bas',
	'formulaire_deplacer_haut' => 'Déplacer vers le haut',
	'formulaire_description' => 'Description',
	'formulaire_description_blocs_exclus' => 'Vous pouvez choisir d’exclure certains blocs de la configuration de noisettes. Les blocs contenant des noisettes ne peuvent pas être exclus, il est nécessaire de les vider au préalable.',
	'formulaire_description_explication' => 'Vous pouvez utilisez les raccourcis SPIP usuels, notamment la balise &lt;multi&gt;.',
	'formulaire_description_peuplement' => 'Vous pouvez peupler automatiquement la nouvelle composition virtuelle avec les noisettes de la page source.',
	'formulaire_dupliquer_noisette' => 'Dupliquer cette noisette',
	'formulaire_dupliquer_page' => 'Dupliquer cette composition',
	'formulaire_dupliquer_page_entete' => 'Dupliquer une page',
	'formulaire_dupliquer_page_titre' => 'Dupliquer la page « @page@ »',
	'formulaire_erreur_format_identifiant' => 'L’identifiant ne peut contenir que des minuscules sans accent, des chiffres et le caractère _ (underscore).',
	'formulaire_erreur_noisette_introuvable' => '@noisette@ est introuvable. Renommez-la ou supprimez-la.',
	'formulaire_etendre_noisette' => 'Copier dans le même bloc d’autres pages',
	'formulaire_icon' => 'Icône',
	'formulaire_icon_explication' => 'Vous pouvez saisir le chemin relatif vers une icône (par exemple : <i>images/objet-liste-contenus.png</i>).',
	'formulaire_identifiant_deja_pris' => 'Cet identifiant est déjà utilisé !',
	'formulaire_import_contenu' => 'Choisissez les éléments à importer',
	'formulaire_import_contenu_compositions_virtuelles' => 'Les compositions virtuelles',
	'formulaire_liste_compos_config' => 'L’import contient les compositions virtuelles suivantes : @liste@.',
	'formulaire_liste_pages_config' => 'Les pages et compositions explicites ainsi que les objets suivants sont associées à des noisettes : @liste@.',
	'formulaire_modifier_composition' => 'Modifier cette composition',
	'formulaire_modifier_composition_heritages' => 'Compositions héritées',
	'formulaire_modifier_noisette' => 'Modifier cette noisette',
	'formulaire_modifier_page' => 'Modifier cette page',
	'formulaire_noisette_sans_parametre' => 'Cette noisette ne dispose pas de paramètre de configuration propre.',
	'formulaire_nom' => 'Titre',
	'formulaire_nom_explication' => 'Vous pouvez utilisez la balise &lt;multi&gt;.',
	'formulaire_nouvelle_composition' => 'Nouvelle composition',
	'formulaire_obligatoire' => 'Champs obligatoire',
	'formulaire_peuplement' => 'Copier les noisettes de la page source « @page@ »',
	'formulaire_supprimer_noisette' => 'Supprimer cette noisette',
	'formulaire_supprimer_noisettes_bloc' => 'Supprimer les noisettes du bloc',
	'formulaire_supprimer_noisettes_noisette' => 'Supprimer les noisettes du conteneur',
	'formulaire_supprimer_noisettes_page' => 'Supprimer toutes les noisettes',
	'formulaire_supprimer_page' => 'Supprimer cette composition',
	'formulaire_type' => 'Type de page',
	'formulaire_type_explication' => 'Type de contenu dont hérite la composition.',
	'formulaire_type_import' => 'Type d’importation',
	'formulaire_type_import_explication' => 'Vous pouvez fusionner le fichier de configuration avec votre configuration actuelle (les noisettes de chaque page seront ajoutées à vos noisettes déjà définies) ou bien remplacer votre configuration par celle-ci.',

	// I
	'icone_introuvable' => 'Icône introuvable !',
	'ieconfig_ne_pas_importer' => 'Ne pas importer',
	'ieconfig_noizetier_export_explication' => 'Exporte la configuration du plugin et les données de production concernant les compositions virtuelles et les noisettes.',
	'ieconfig_noizetier_export_option' => 'Inclure les données du noiZetier dans l’export ?',
	'ieconfig_non_installe' => '<b>Plugin Importeur/Exporteur de configurations :</b> ce plugin n’est pas installé sur votre site. Il n’est pas nécessaire au fonctionnement du noizetier. Cependant, s’il est activé, vous pourrez exporter et importer des configurations de noisettes dans le noizetier.',
	'ieconfig_probleme_import_config' => 'Un problème a été rencontré lors de l’importation de la configuration du noiZetier.',
	'import_compositions_virtuelles_ajouter' => 'Ajouter les compositions virtuelles du fichier d’import. Les compositions virtuelles disponibles sur le site ne seront pas modifiées.',
	'import_compositions_virtuelles_avertissement1' => 'Il n’existe pas de compositions virtuelles dans la site. Il est juste possible d\\importer celles du fichier d’import.',
	'import_compositions_virtuelles_avertissement2' => 'Aucune composition virtuelle n’est disponible dans le fichier d’import. Aucune importation n’est donc possible.',
	'import_compositions_virtuelles_explication' => 'Il existe des compositions virtuelles dans le site et dans le fichier d’import.',
	'import_compositions_virtuelles_fusionner' => 'Ajouter les compositions virtuelles du fichier d’import et remplacer les compositions virtuelles du site aussi disponibles dans le fichier d’import.',
	'import_compositions_virtuelles_label' => 'Les compositions virtuelles',
	'import_compositions_virtuelles_remplacer' => 'Remplacer les compositions virtuelles disponibles sur le site par celles du fichier d’import',
	'import_configuration_avertissement' => 'La version @version@ du plugin noiZetier actif sur ce site possède un schéma @schema@ différent de celui du fichier d’import. <b>Vérifier la compatibilité des configurations avant d’importer celle du fichier</b>.',
	'import_configuration_explication' => 'La version @version@ du plugin noiZetier actif sur ce site possède le même schéma @schema@ que celui du fichier d’import.',
	'import_configuration_label' => 'La configuration du plugin',
	'import_configuration_labelcase' => 'Remplacer la configuration actuelle du noiZetier par celle du fichier d’import',
	'import_noisettes_ajouter' => 'Ajouter les noisettes du fichier d’import dans les pages ou objets concernés. Les noisettes actuellement configurées sur le site ne seront pas modifiées',
	'import_noisettes_avertissement1' => 'Il n’existe pas de pages ou objets communs dans le site et dans le fichier d’import. Aucune importation n’est donc possible.',
	'import_noisettes_avertissement2' => 'Aucune noisette n’est disponible dans le fichier d’import. Aucune importation n’est donc possible.',
	'import_noisettes_explication' => 'Il existe des pages ou objets communs dans le site et dans le fichier d’import.',
	'import_noisettes_label' => 'Les noisettes',
	'import_noisettes_remplacer' => 'Remplacer, pour les pages ou objets concernés, les noisettes actuellement configurées pour le site par les noisettes du fichier d’import',
	'import_pages_explicites_avertissement1' => 'Il n’existe pas de pages ou compositions explicites communes entre le site et le fichier d’import. Toute importation est donc inutile.',
	'import_pages_explicites_avertissement2' => 'Aucune pages ou compositions explicites disponibles sur le site. Toute importation est donc inutile.',
	'import_pages_explicites_explication' => 'Il existe des pages et compositions explicites communes entre le site et le fichier d’import.',
	'import_pages_explicites_label' => 'Les blocs exclus des pages explicites',
	'import_pages_explicites_labelcase' => 'Remplacer les blocs exclus des pages explicites du site par ceux du fichier d’import',
	'import_resume' => 'Le fichier à importer a été construit avec le noiZetier en version @version@, schéma de données @schema@.',
	'info_0_noisette' => 'Aucune noisette',
	'info_0_noisette_composition' => 'Aucune noisette pour cette composition',
	'info_0_noisette_objet' => 'Aucune noisette pour ce contenu',
	'info_0_noisette_objets' => 'Aucune noisette pour les @objets@',
	'info_1_noisette' => '1 noisette',
	'info_1_noisette_ajoutee' => '1 noisette a été ajoutée',
	'info_1_noisette_composition' => '1 noisette pour cette composition',
	'info_1_noisette_objet' => '1 noisette pour ce contenu',
	'info_1_noisette_objets' => '1 noisette pour les @objets@',
	'info_composition' => 'COMPOSITION :',
	'info_etendre_noisette' => 'Copier la noisette @noisette@ dans le bloc @bloc@ d’autres pages',
	'info_nb_noisettes' => '@nb@ noisettes',
	'info_nb_noisettes_ajoutees' => '@nb@ noisettes ont été ajoutées',
	'info_nb_noisettes_composition' => '@nb@ noisettes pour cette composition',
	'info_nb_noisettes_objet' => '@nb@ noisettes pour ce contenu',
	'info_nb_noisettes_objets' => '@nb@ noisettes pour les @objets@',
	'info_page' => 'PAGE :',
	'installation_tables' => 'Tables du plugin noiZetier installées.<br />',
	'item_titre_perso' => 'titre personnalisé',

	// L
	'label_afficher_titre_noisette' => 'Afficher un titre de noisettes ?',
	'label_code' => 'Code Spip :',
	'label_copie_noisette_balise' => 'Copier l’indicateur d’encapsulation.',
	'label_copie_noisette_css' => 'Copier les styles éventuels associés à la capsule englobante.',
	'label_copie_noisette_parametres' => 'Copier les paramètres de configuration de la noisette source.',
	'label_description_code' => 'Description :',
	'label_identifiant' => 'identifiant :',
	'label_niveau_titre' => 'Niveau du titre :',
	'label_noisette_css' => 'Classes CSS',
	'label_noisette_encapsulation' => 'Encapsulation',
	'label_noizetier_ajax' => 'Par défaut, inclure chaque noisette en Ajax',
	'label_noizetier_balise' => 'Par défaut, inclure chaque noisette dans une capsule (markup HTML)',
	'label_noizetier_dynamique' => 'Par défaut, inclure chaque noisette dynamiquement',
	'label_source_noisettes' => 'Source des noisettes',
	'label_texte' => 'Texte :',
	'label_texte_introductif' => 'Texte introductif (optionnel) :',
	'label_titre' => 'Titre :',
	'label_titre_noisette' => 'Titre de la noisette :',
	'label_titre_noisette_perso' => 'Titre personnalisé :',
	'legende_copie_noisette_parametres' => 'Paramètres de la noisette source',
	'legende_copie_pages_compatibles' => 'Pages compatibles avec le type de noisette',
	'legende_noisette_inclusion' => 'Paramètres d’inclusion',
	'legende_noisette_parametrage' => 'Paramètres de configuration',
	'liste_icones' => 'Liste d’icônes',
	'liste_objets' => 'Contenus possédant des noisettes',
	'liste_objets_configures' => 'Liste des objets',
	'liste_pages' => 'Liste des pages',
	'liste_pages_objet_non' => 'Pages autonomes',
	'liste_pages_objet_oui' => 'Pages des contenus',
	'liste_pages_toutes' => 'Toutes les pages',

	// M
	'masquer' => 'Masquer',
	'menu_blocs' => 'Blocs configurables',
	'mode_noisettes' => 'Éditer les noisettes',
	'modif_en_cours' => 'Modifications en cours',
	'modifier_dans_prive' => 'Modifier dans l’espace privé',

	// N
	'ne_pas_definir_d_heritage' => 'Ne pas définir de composition héritée',
	'noisette_numero' => 'noisette numéro :',
	'noisettes_composition' => 'Noisettes spécifiques à la composition <i>@composition@</i> :',
	'noisettes_disponibles' => 'Types de noisettes disponibles',
	'noisettes_page' => 'Spécifiques à la page <i>@type@</i> :',
	'noisettes_pour' => 'Noisettes pour : ',
	'noisettes_toutes_pages' => 'Communs à toutes les pages :',
	'noizetier' => 'noiZetier',
	'nom_bloc_contenu' => 'Contenu',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navigation',
	'nom_bloctexte' => 'Bloc de texte libre',
	'nom_codespip' => 'Code Spip libre',
	'non' => 'Non',
	'notice_enregistrer_rang' => 'Cliquez sur Enregistrer pour sauvegarder l’ordre des noisettes.',

	// O
	'operation_annulee' => 'Opération annulée.',
	'option_noisette_encapsulation_defaut' => 'Utiliser le mode par défaut configuré pour le noiZetier <em>(@defaut@)</em>',
	'option_noisette_encapsulation_non' => 'Ne jamais encapsuler la noisette',
	'option_noisette_encapsulation_oui' => 'Inclure la noisette dans une capsule',
	'option_noizetier_encapsulation_non' => 'sans encapsulation',
	'option_noizetier_encapsulation_oui' => 'avec encapsulation',
	'oui' => 'Oui',

	// P
	'page' => 'Page',
	'page_autonome' => 'Page autonome',
	'probleme_droits' => 'Vous n’avez pas les droits nécessaires pour effectuer cette modification.',

	// Q
	'quitter_mode_noisettes' => 'Quitter l’édition des noisettes',

	// R
	'recharger_composition' => 'Recharger la composition',
	'recharger_noisettes' => 'Recharger les types de noisette',
	'recharger_page' => 'Recharger la page',
	'recharger_pages' => 'Recharger les pages',
	'retour' => 'Retour',

	// S
	'suggestions' => 'Suggestions',

	// T
	'texte_noisette' => 'Noisette',
	'texte_noisettes' => 'Noisettes',

	// W
	'warning_noisette_plus_disponible' => 'ATTENTION : cette noisette n’est plus disponible.',
	'warning_noisette_plus_disponible_details' => 'Le squelette de cette noisette (<i>@squelette@</i>) n’est plus accessible. Il se peut qu’il s’agisse d’une noisette nécessitant un plugin que vous avez désactivé ou désinstallé.'
);
