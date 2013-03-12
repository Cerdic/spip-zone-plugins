<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

	'action_incomprise' => 'Action @f_action@ non prise en charge !',
	'aide_creation_peupler_table' => 'Aide à la création de peuplement d\'une table',
	'aide_creation_squelette_fabrique' => 'Aide à la création de squelettes Fabrique',

	'autorisation_defaut' => 'Par défaut (@defaut@)',
	'autorisation_redacteur' => 'Être au moins rédacteur',
	'autorisation_administrateur_restreint' => 'Être au moins administrateur restreint',
	'autorisation_administrateur' => 'Être au moins administrateur complet',
	'autorisation_webmestre' => 'Être webmestre',
	'autorisation_jamais' => 'Jamais',
	'autorisation_toujours' => 'Toujours',

	'avertissement_champs' => 'N\'insérez pas ici la clé primaire (@id_objet@),
		ni aucun des champs spéciaux (id_rubrique, lang, etc.) proposés dans la partie suivante.',
	'bouton_ajouter_champ' => 'Ajouter un champ',
	'bouton_ajouter_objet' => 'Ajouter un objet éditorial',
	'bouton_exporter' => 'Exporter',
	'bouton_supprimer_champ' => 'Supprimer ce champ',
	'bouton_supprimer_objet' => 'Supprimer cet objet éditorial',
	'bouton_supprimer_logo' => 'Supprimer ce logo',
	'bouton_calculer' => 'Calculer',
	'bouton_creer' => 'Créer le plugin',
	'bouton_charger' => 'Charger la sauvegarde',
	'bouton_charger_sauvegarde_attention' => "Charger une sauvegarde efface les informations du plugin actuellement en cours de création !",
	'bouton_reinitialiser_chaines' => 'Réinitialiser les chaines de langue de cet objet',
	'bouton_reinitialiser_autorisations' => 'Réinitialiser les autorisations',
	'bouton_reset' => 'Réinitialiser le formulaire',
	'bouton_renseigner_objet' => 'Pré-remplir cet objet',
	'bouton_menu_edition' => 'Menu édition',
	'bouton_outils_rapides' => 'Outils rapides',

	'c_fabrique_info' => "Création d'un plugin",
	'c_fabrique_info_texte' => "Cet outil permet de créer facilement une base de code de plugin.
		Bien que le code produit soit fonctionnel, il ne sera probablement pas ce que vous attendez exactement,
		et ce n'est pas le but ! La Fabrique crée les bases des fichiers et des codes,
		mais il vous faudra vraissemblablement les modifier ensuite selon ce que vous souhaitez réellement.
		<br /><br />
		Nous vous conseillons donc de comprendre au préalable le fonctionnement des plugins, de SPIP et ses squelettes,
		et si vous souhaitez gérer des objets éditoriaux, le fonctionnement des pipelines, autorisations, formulaires.
		Ce plugin peut cependant aussi vous servir à étudier le code généré en fonction des options que vous cochez.
	",
	'c_fabrique_zone' => "Wow c'est trop facile !",
	'c_fabrique_zone_texte' => "Vous allez certainement apprécier le fait de produire un plugin
		gérant un ou plusieurs objets éditoriaux. Tant mieux !
		<br /><br />
		Méfiez-vous cependant ! Si créer un plugin est facile, le maintenir dans le temps,
		gérer sa documentation, sa vie, est bien plus difficile.
		Le meilleur moyen pour maintenir un plugin implique en général
		deux conditions : qu'il soit utile et qu'il soit partagé ; partagé au sens où d'autres
		développeurs et contributeurs peuvent intervenir dessus et l'améliorer.
		Chez SPIP, les plugins partagés, avec un code libre,
		peuvent être hébergés sur l'espace de collaboration SPIP Zone.
		<br /><br />
		Avant donc de vous lancer dans la création d'un nouveau plugin, vérifiez qu'il
		n'existe pas dans l'espace de collaboration de SPIP un plugin déjà équivalent
		sur lequel vous pourriez apporter vos améliorations, votre documentation.
		Il est plus intéressant pour tout le monde qu'il y ait peu de doublons mais
		des plugins fonctionnels et pérennes !
	",
	'c_fabrique_dans_plugins' => "Facilitez-vous les tests !",
	'c_fabrique_dans_plugins_texte' => "En créant un répertoire <code>@dir@</code> accessible en écriture
		dans votre répertoire de plugins, la Fabrique pourra confectionner le plugin (ses fichiers, son arborescence)
		directement dedans. Vous pourrez alors une fois le plugin créé l'activer aussitôt dans l'administration
		des plugins et le tester.
		<br /><br />
		Attention, le plugin autrement est créé dans <code>tmp/cache/@dir_cache@</code> ; ce
		répertoire est supprimé lorsqu'on vide le cache.
	",

	'calcul_effectue' => 'Calcul effectué',
	
	// objets : chats
	// mobjets : Chats
	// type : chat
	// mtype : Chat
	'chaine_titre_objets' => '@mobjets@',
	'chaine_titre_objets_feminin' => '@mobjets@',
	'chaine_titre_objet' => '@mtype@',
	'chaine_titre_objet_feminin' => '@mtype@',
	'chaine_titre_logo_objet' => 'Logo de ce @type@',
	'chaine_titre_logo_objet_feminin' => 'Logo de cette @type@',
	'chaine_icone_creer_objet' => 'Créer un @type@',
	'chaine_icone_creer_objet_feminin' => 'Créer une @type@',
	'chaine_icone_modifier_objet' => 'Modifier ce @type@',
	'chaine_icone_modifier_objet_feminin' => 'Modifier cette @type@',
	'chaine_info_aucun_objet' => 'Aucun @type@',
	'chaine_info_aucun_objet_feminin' => 'Aucune @type@',
	'chaine_info_1_objet' => 'Un @type@',
	'chaine_info_1_objet_feminin' => 'Une @type@',
	'chaine_info_nb_objets' => '@nb@ @objets@',
	'chaine_info_nb_objets_feminin' => '@nb@ @objets@',
	'chaine_info_objets_auteur' => 'Les @objets@ de cet auteur',
	'chaine_info_objets_auteur_feminin' => 'Les @objets@ de cet auteur',
	'chaine_ajouter_lien_objet' => 'Ajouter ce @type@',
	'chaine_ajouter_lien_objet_feminin' => 'Ajouter cette @type@',
	'chaine_retirer_lien_objet' => 'Retirer ce @type@',
	'chaine_retirer_lien_objet_feminin' => 'Retirer cette @type@',
	'chaine_retirer_tous_liens_objets' => 'Retirer tous les @objets@',
	'chaine_retirer_tous_liens_objets_feminin' => 'Retirer toutes les @objets@',
	'chaine_texte_ajouter_objet' => 'Ajouter un @type@',
	'chaine_texte_ajouter_objet_feminin' => 'Ajouter une @type@',
	'chaine_texte_changer_statut_objet' => 'Ce @type@ est&nbsp;:',
	'chaine_texte_changer_statut_objet_feminin' => 'Cette @type@ est&nbsp;:',
	'chaine_texte_creer_associer_objet' => 'Créer et associer un @type@',
	'chaine_texte_creer_associer_objet_feminin' => 'Créer et associer une @type@',
	'chaine_titre_langue_objet' => 'Langue de ce @type@',
	'chaine_titre_langue_objet_feminin' => 'Langue de cette @type@',
	'chaine_titre_objets_rubrique' => '@mobjets@ de la rubrique',
	'chaine_titre_objets_rubrique_feminin' => '@mobjets@ de la rubrique',

	'champ_auto_rempli' => 'Le champ sera automatiquement rempli si vous laissez vide.',
	'champ_ajoute' => 'Un champ a été ajouté',
	'champ_supprime' => 'Le champ a été supprimé',
	'champ_deplace' => 'Le champ a été déplacé',
	'chargement_effectue' => 'Chargement effectué',

	'config_exemple' => 'Exemple',
	'config_exemple_explication' => 'Explication de cet exemple',
	'config_titre_parametrages' => 'Paramétrages',

	'datalist_aide' => 'Certains navigateurs peuvent proposer une autocomplétion
		en tapant une flèche bas au clavier ou en cliquant 2 fois dans la zone de saisie.',

	'echafaudage_prive/squelettes/contenu/objets.html' => 'Ajoute un champ de recherche',
	'echafaudage_prive/objets/infos/objet.html' => 'Ajoute le lien de prévisualisation',

	'echappement_php' => '&lt;?php',
	'echappement_crochets' => '[ ]',
	'echappement_parentheses' => '( )',
	'echappement_accolades' => '{ }',
	'echappement_diese' => '#',
	'echappement_idiome' => '&lt;:',
	'echappement_inclure' => '&lt;INCLURE',
	'echappement_tag_boucle' => '&lt; de boucle',
	
	'erreurs' => 'Il y a des erreurs !',
	'erreur_envoi_fichier' => 'Erreur dans l\'envoi du fichier.',
	'erreur_chargement_fichier' => 'Le fichier envoyé n\a pas pu être compris. La restauration n\'est pas effectuée.',
	'erreur_suppression_sauvegarde' => 'L\'ancienne sauvegarde (@dir@) n\'a pu être supprimée. Le plugin par précaution n\'a pas été régénéré.
		La cause probable est la création par vous même de fichiers supplémentaires dans le plugin qui n\'ont pas des droits suffisants pour être manipulés par le serveur.',
	'erreur_copie_sauvegarde' => 'La sauvegarde de @dir@ n\'a pu être réalisée. Le plugin par précaution n\'a pas été régénéré.
		La cause probable provient de droits insufisants ce répertoire source pour le serveur.',
	'explication_fichiers' => 'Même si vous ne les activez pas ici, certains de ces fichiers seront tout de même créés
		en fonctions d\'autres options que vous aurez choisies ailleurs, notamment si vous activez un objet éditorial.',
	'explication_sauvegarde' => 'La Fabrique crée un fichier de sauvegarde (<code>fabrique_{prefixe}.php</code>) à l\'intérieur de chaque plugin qu\'il crée.
		Vous pouvez restaurer ce fichier ici en l\'envoyant sur le serveur ou utiliser un des fichiers déjà présents.',
	'explication_reinitialiser' => 'Ceci efface les informations du plugin actuellement en cours de création.
		Vous repartirez donc de zéro !',
	'explication_tables_hors_normes' => 'Une table respecte les normes par défaut de SPIP lorsqu\'elle
		est nommée avec un pluriel en «&nbsp;s&nbsp;» (comme <code>spip_choses</code>) et lorsque sa clé primaire
		est basé sur le nom de la table au singulier (comme <code>id_chose</code>). Dans les autres cas,
		vous devez compléter certaines informations ci-dessous.',
	'explication_fichiers_echafaudes' => 'SPIP génère automatiquement en cache ces fichiers
		s\'ils sont absents. Vous pouvez cependant en créer certains afin de les modifier
		pour changer le comportement par défaut de ce que propose SPIP.
		Par ailleurs, ces fichiers ont parfois des ajouts minimes de fonctionnalités, alors indiquées.',
	'explication_roles' => 'De façon expérimentale, des rôles peuvent être gérés sur les liaisons en
		utilisant le plugin «Rôles».',
	'experimental_explication' => '<strong>Partie expérimentale !</strong><br />
		La pérennité des saisies n\'est pas garantie.
		Cette partie peut évoluer ou disparaître dans de futures versions.',

	'fabrique_peuple_intro' => 'Cet outil permet d\'aider à la création d\'un fichier et d\'une fonction de peuplement
		d\'une table au moment de l\'installation du plugin',
	'fabrique_peuple_titre' => 'Peupler un objet',
	'fabrique_dev_intro' => 'Cet outil permet d\'aider à la création de squelettes pour la Fabrique',
	'fabrique_dev_titre' => 'Développement de la Fabrique',
	'fabrique_intro' => 'Outil de fabrication de plugin',
	'fabrique_titre' => 'La Fabrique',
	'fabrique_restaurer_titre' => 'Restaurer ou réinitialiser une fabrique',
	'fabrique_plugin' => 'Fabrique de @plugin@',
	'fabrique_outils' => 'Outils',
	'fichier_importation_cree_dans' => 'Fichier d\'importation créé dans le répertoire <code>@dir@</code>, fichier <code>@import@</code> avec @lignes@ lignes pour un total de @taille@',
	'fichiers_importations_compresses_cree_dans' => 'Fichier d\'importation créé dans le répertoire <code>@dir@</code>, fichiers <code>@import@</code> et <code>@donnees_compressees@</code>, avec @lignes@ lignes pour un total de @taille@',

	'image_supprimee' => 'L\'image a été supprimée',
	'insertion_code_explication' => '
		Cette partie vous permet d\'inserer du code dans certaines parties prévues
		par la Fabrique. Attention cependant à ce que ce code soit toujours valide !
	',

	'label_auteur' => 'Nom de l\'auteur',
	'label_auteur_lien' => 'URL vers l\'auteur',
	'label_auteurs_liens' => 'Lier des auteurs ?',
	'label_auteurs_liens_explication' => 'Permet d\'ajouter le formulaire de liaisons d\'auteurs sur cet objet.',
	'label_boutons' => 'Boutons',
	'label_boutons_explication' => 'Insérer des boutons dans ces lieux&nbsp;:',
	'label_caracteristiques' => 'Caractéristiques',
	'label_categorie' => 'Catégorie',
	'label_champ_est_editable' => 'Il peut être édité',
	'label_champ_est_versionne' => 'Il peut être versionné',
	'label_champ_est_obligatoire' => 'Il est obligatoire',
	'label_champ_date_publication' => 'Champ SQL de date',
	'label_champ_date_publication_explication' => 'Pour gérer une date de publication, indiquez son champ, tel que «&nbsp;date&nbsp;» ou «&nbsp;date_publication&nbsp;»',
	'label_champ_id_rubrique' => 'Créer le champ <strong>id_rubrique</strong>',
	'label_champ_id_secteur' => 'Créer le champ <strong>id_secteur</strong>',
	'label_champ_lang_et_langue_choisie' => 'Champs <strong>lang</strong> et <strong>langue_choisie</strong>',
	'label_champ_id_trad' => 'Champ <strong>id_trad</strong>',
	'label_champ_langues' => 'Gestion des langues',
	'label_champ_langues_explication' => 'Ajouter des champs pour gérer les langues de l\'objet (lang et langue_choisie) et les traductions (id_trad)&nbsp;?',
	'label_champ_rubriques' => 'id_rubrique',
	'label_champ_rubriques_explication' => 'Permet d\'affecter cet objet dans une rubrique',
	'label_champ_statut' => 'Champ <strong>statut</strong>',
	'label_champ_statut_explication' => 'Permet d\'utiliser des statuts de publication (proposé à publication, publié, poubelle…)',
	'label_champ_vue_rubrique' => 'Afficher la liste dans la rubrique',
	'label_champ_titre' => 'Calculer les titres',
	'label_champ_titre_explication' => 'Utilisez un des champs SQL que vous avez déclaré pour votre objet',
	'label_charger_depuis_table_sql' => 'Définir depuis une table SQL',
	'label_charger_depuis_table_sql_explication' => 'Vous pouvez pré-remplir votre objet en utilisant une table SQL existante connue de SPIP',
	'label_charger_depuis_table_sql_attention' => 'Cela effacera une partie des informations que vous avez saisi pour cet objet.',

	'label_cle_primaire' => 'Clé primaire',
	'label_cle_primaire_explication' => 'Exemple «&nbsp;id_chose&nbsp;»',
	'label_cle_primaire_attention' => 'Il est conseillé de mettre le nom de la table au singulier, prefixé de id_ . Ce préfixe est important. En son absence,
		certaines jointures vers les tables de liens avec des critères comme
		<code>{id_mot ?}</code> ou <code>{id_auteur ?}</code>
		sur une boucle de cet objet donneront une erreur de squelette.',

	'label_cle_primaire_sql' => 'Définition SQL pour la clé primaire',
	'label_cle_primaire_sql_explication' => 'Définition SQL pour la clé primaire',
	'label_cle_primaire_sql_attention' => 'Il est conseillé d\'indiquer une clé primaire numérique
		(<code>bigint(21) NOT NULL</code>). Lorsque le type de champ n\'est pas un entier,
		il est impossible à SPIP de créer un nouvel élément dans cet objet car la clé primaire
		ne pourra pas être affectée d\'un «&nbsp;auto increment&nbsp;».
		Par ailleurs, si votre table contient déjà des lignes
		avec des données non entières dans la clé primaire, ou des zeros à gauche (0123), ces données
		ne pourront être lues par SPIP car il applique la fonction intval (force une valeur à être un nombre entier)
		automatiquement sur tout champ préfixé de id_ et sur la clé primaire d\'un objet éditorial.',

	'label_code_squelette' => 'Code du squelette source',
	'label_code_resultat' => 'Code transformé',
	'label_colonne_sql' => 'Colonne SQL',
	'label_colonne_sql_explication' => 'Un nom de champ pour SQL. Exemple «&nbsp;post_scriptum&nbsp;»',
	'label_compatibilite' => 'Compatibilité',
	'label_inserer_paquet' => 'Au niveau des dépendances',
	'label_inserer_administrations_maj' => 'Compléter <code>$maj</code> dans la fonction <code>upgrade()</code>',
	'label_inserer_administrations_desinstallation' => 'Compléter la désinstallation dans la fonction <code>vider_table()</code>',
	'label_inserer_administrations_fin' => 'À la fin du fichier pour insérer de nouvelles fonctions',
	'label_inserer_base_tables_fin' => 'À la fin du fichier pour insérer de nouvelles fonctions',
	'label_table_compresser_donnees' => 'Compresser les données ?',
	'label_table_compresser_donnees_explication' => 'Utile si la table est volumineuse !',
	'label_table_destination' => 'Table SQL de destination',
	'label_table_destination_explication' => 'Nom de la table dans laquelle seront importées les données.
		Par défaut le même nom que la table source.',
	'label_definition_sql' => 'Définition SQL',
	'label_description' => 'Description',
	'label_documentation_url' => 'Documentation (url)',
	'label_echappements' => 'Échapper quoi ?',
	'label_etat' => 'État',
	'label_exemples' => 'Insérer des exemples',
	'label_exemples_explication' => 'Ajouter en commentaire dans les fichiers du plugin des exemples de code et des textes d\'aide&nbsp;?',
	'label_explication' => 'Phrase d\'explication pour la saisie',
	'label_fichiers' => 'Créer des fichiers&nbsp;?',
	'label_fichier_administrations' => 'Fichier d\'administrations&nbsp;?',
	'label_fichier_administrations_explication' => 'Créer le fichier d\'installation / désinstallation&nbsp;?',
	'label_fichier_autorisations' => 'Autorisations',
	'label_fichier_options' => 'Options',
	'label_fichier_fonctions' => 'Fonctions',
	'label_fichier_pipelines' => 'Pipelines',
	'label_fichier_sauvegarde' => 'Fichier de sauvegarde',
	'label_fichier_sauvegarde_ordinateur' => 'Sur votre ordinateur',
	'label_fichier_sauvegarde_serveur' => 'Sur le serveur',
	'label_formulaire_configuration' => 'Formulaire de configuration&nbsp;?',
	'label_formulaire_configuration_titre' => 'Titre de la page de configuration',
	'label_genre' => 'Genre',
	'label_genre_explication' => 'Sert au pré-calcul du texte des chaines de langues.',
	'label_genre_masculin' => 'Masculin',
	'label_genre_feminin' => 'Féminin',
	'label_libelle' => 'Libellé',
	'label_libelle_champ_explication' => 'Un nom de champ pour les humains. Exemple «&nbsp;Post-Scriptum&nbsp;»',
	'label_licence' => 'Licence',
	'label_logo' => 'Logo',
	'label_logo_taille' => 'Logo de @taille@px',
	'label_logo_variantes' => 'Variantes de logos&nbsp;?',
	'label_logo_variantes_explication' => 'Créer toutes les variantes (new, edit, del, add) de logo (tailles supérieures ou égales à 16 pixels).',
	'label_nom' => 'Nom',
	'label_nom_pluriel' => 'Nom pluriel',
	'label_nom_pluriel_explication' => 'Exemple «&nbsp;Choses&nbsp;»',
	'label_nom_singulier' => 'Nom singulier',
	'label_nom_singulier_explication' => 'Exemple «&nbsp;Chose&nbsp;»',
	'label_prefixe' => 'Préfixe',
	'label_recherche' => 'Recherche',
	'label_recherche_explication' => 'Ponderation de la recherche dans ce champ. Toute valeur comprise entre 1 et 10
		indiquera que SPIP peut chercher dans ce champ lors d\'une recherche sur l\'objet.
		Laisser vide pour ne pas chercher dedans.',
	'label_roles' => 'Liste des rôles',
	'label_roles_explication' => 'Chaque ligne décrit un rôle : <code>code du rôle,Titre du rôle</code>.
		Le premier rôle est considéré comme le rôle à appliquer par défaut. Exemple : <code>traducteur,Traducteur</code>',
	'label_slogan' => 'Slogan',
	'label_saisies' => 'Saisies',
	'label_saisies_explication' => 'Créer des saisies et leurs vues',
	'label_saisie' => 'Type de saisie',
	'label_saisie_explication' => 'Si nécessaire (pour afficher ce champ dans le formulaire), indiquez le type de saisie (du plugin saisies) souhaité.',
	'label_saisie_options' => 'Options de saisie',
	'label_saisie_options_explication' => 'Options du code de la balise #SAISIE.<br />
		Exemple pour un textarea :<br />
		<code>li_class=haut, class=inserer_barre_edition, rows=4</code><br />
		Exemple pour selection / checkbox / radio :<br />
		<code>datas=[(#ARRAY{cle1,valeur1,cle2,valeur2})]</code>',
	'label_schema' => 'Schema',
	'label_schema_explication' => 'Version de la structure des données',

	'label_scripts_pre_copie' => '<code>pre_copie</code>',
	'label_scripts_pre_copie_explication' => 'Avant de sauvegarder le plugin actuel dans <code>@destination_ancien_plugin@</code>',
	'label_scripts_post_creation' => '<code>post_creation</code>',
	'label_scripts_post_creation_explication' => 'Après la création des fichiers de votre plugin dans <code>@destination_plugin@</code>',

	'label_table' => 'Nom de la table SQL',
	'label_table_explication' => 'Par exemple «&nbsp;spip_choses&nbsp;»',
	'label_table_attention' => 'Il est conseillé de nommer sa table au pluriel, avec un s final.
		Cependant SPIP et la Fabrique savent gérer les autres cas.',

	'label_table_a_exporter' => 'Table SQL a exporter',

	'label_table_type' => 'Type de l\'objet',
	'label_table_type_explication' => 'Exemple «&nbsp;chose&nbsp;»',
	'label_table_type_attention' => 'Il est conseillé de mettre le nom de la cle primaire, sans son prefixe.',

	'label_table_liens' => 'Créer une table de liens ?',
	'label_transformer_objet' => 'Transformer les textes de cet objet',
	'label_transformer_objet_explication' => 'Changera au mieux ce qui se rapporte à un objet (articles, #ID_ARTICLE...) en utilisant la syntaxe prévue pour la fabrique',
	'label_version' => 'Version',
	'label_vue_auteurs_liens' => 'La liste sur la vue d\'un auteur&nbsp;?',
	'label_vue_auteurs_liens_explication' => 'Permet d\'afficher la liste des éléments de cet objet liés à un auteur, sur la page d\'un auteur.',
	'label_vue_liens' => 'Permettre de saisir les liens sur ces objets&nbsp;?',
	'label_vue_liens_explication' => 'Ajoute un formulaire d\'édition de liens sur les objets :',

	'legend_autorisations' => 'Autorisations',
	'legend_chaines_langues' => 'Chaînes de langue',
	'legend_champs' => 'Champs',
	'legend_champs_speciaux' => 'Champs spéciaux',
	'legend_champs_sql' => 'Champs SQL utilisé pour :',
	'legend_configuration' => 'Configuration',
	'legend_date_publication' => 'Date de publication',
	'legend_description' => 'Description',
	'legend_inserer_paquet' => 'Dans <code>paquet.xml</code>',
	'legend_inserer_administrations' => 'Dans <code>@prefixe@_administrations.php</code>',
	'legend_inserer_base_tables' => 'Dans <code>base/@prefixe@.php</code>',
	'legend_fichiers' => 'Fichiers',
	'legend_fichiers_echafaudes' => 'Fichiers échafaudés',
	'legend_installation' => 'Installation',
	'legend_insertion_code' => 'Insertion de code',
	'legend_langues_et_traductions' => 'Langues et traductions',
	'legend_liaisons' => 'Liaisons',
	'legend_liaisons_auteurs_liens' => 'spip_auteurs_liens',
	'legend_liaisons_objet_liens' => 'spip_@objet@_liens',
	'legend_logo' => 'Logos',
	'legend_logo_specifiques' => 'Logos spécifiques',
	'legend_logo_specifiques_explication' => 'Vous pouvez également fournir des logos spécifiques
		pour certaines tailles. Ces images seront sinon calculées par SPIP
		depuis la taille au-dessus la plus proche, sinon depuis le logo de base de l\'objet.',
	'legend_options' => 'Options',
	'legend_paquet' => 'Paquet',
	'legend_pre_construire' => 'Pré construire',
	'legend_resultat' => 'Résultat',
	'legend_roles' => 'Rôles',
	'legend_rubriques' => 'Rubriques',
	'legend_saisie' => 'Saisie',
	'legend_statut' => 'Statuts',
	'legend_scripts' => 'Scripts à exécuter',
	'legend_suppression' => 'Suppression',
	'legend_table' => 'Table',
	'legend_tables_hors_normes' => 'Spécificités de tables hors normes',

	'message_diff' => 'Différences avec la précédente création',
	'message_diff_explication' => 'Ce «&nbsp;diff&nbsp;» est aussi stocké dans le fichier <code>fabrique_diff.diff</code>
		du plugin généré.',
	'message_diff_suppressions' => 'Des fichiers ont été supprimés lors de cette nouvelle création.',

	'objet_ajoute' => 'Un nouvel objet éditorial a été ajouté',
	'objet_deplace' => 'L\'objet a été déplacé',
	'objet_renseigne' => 'L\'objet éditorial a été renseigné avec la table SQL indiquée',
	'objet_supprime' => 'L\'objet éditorial a été supprimé',
	'objet_autorisations_reinitialisees' => 'Les autorisations de l\'objet ont été réinitialisées.',
	'objet_chaines_reinitialisees' => 'Les chaînes de langues de l\'objet ont été réinitialisées.',
	'onglet_plugin' => 'Plugin',
	'onglet_objet' => 'Objet',
	'onglet_objet_n' => 'Objet #@nb@',
	'onglet_fabrique' => 'Fabrique à plugins',
	'onglet_fabrique_restaurer' => 'Restauration, Réinitialisation',
	'onglet_fabrique_outils' => 'Outils',

	'plugin_cree_succes' => 'Le plugin a été créé avec succès',
	'plugin_cree_succes_dans' => 'Le plugin a été créé avec succès dans <br /><code>@dir@</code>',

	'reititialiser' => 'Réinitialiser',
	'reinitialisation_effectuee' => 'Réinitialisation effectuée',
	'repertoire_plugin_fabrique' => 'Vous pouvez pour vous faciliter les tests
		créer un répertoire <code>@dir@</code> accessible en écriture dans votre
		répertoire de plugins. Ainsi, les plugins créés seront aussitôt disponibles
		sur l\'administration des plugins et activables.',
	'restaurer' => 'Restaurer',

	'saisies_objets' => 'Saisie <code>@saisie@</code> : sélecteur d\'objet simple pour tables peu peuplées.',

	'scripts_explication' => 'Du code PHP valide peut être executé
		à certains moment de la procédure de création du plugin. Cela vous permet de traiter des
		actions non prévues par la Fabrique comme remettre des fichiers que vous aviez ajoutés,
		en les déplaçant de l\'ancien plugin vers le nouveau.
		Un certain nombre de variables sont à votre disposition
		au moment de l\'exécution de ces scripts, comme <code>$destination_plugin</code>
		(le chemin vers le futur plugin), <code>$destination_ancien_plugin</code> (la
		copie de l\'ancien plugin - s\'il existait avant !), <code>$destination</code> (le
		chemin parent de ces derniers)',
	'scripts_securite_webmestres' => 'Pour des raisons de sécurité, seuls les webmestres
		de ce site peuvent exécuter les scripts écrits dans cette partie.',

	'titre_plugin' => 'Plugin « @plugin@ »',

	'valider_nom_objet_avant' => 'Pour saisir les chaines de langues, veuillez d\'abord valider
		le formulaire après avoir renseigné le nom de l\'objet. Cela permet de compléter une partie
		des chaines de langues, qu\'il vous faudra simplement vérifier.',
);
?>
