<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/fabrique?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_incomprise' => 'Action @f_action@ unsupported!',
	'aide_creation_peupler_table' => 'Support for the creation of a table settlement
',
	'aide_creation_squelette_fabrique' => 'Support to the creation of skeletons Fabrique',
	'autorisation_administrateur' => 'To be at least full administrator 
', # MODIF
	'autorisation_administrateur_restreint' => 'To be at least restricted administrator
', # MODIF
	'autorisation_defaut' => 'By default  (@defaut@)',
	'autorisation_jamais' => 'Never',
	'autorisation_redacteur' => 'To be at least editor', # MODIF
	'autorisation_toujours' => 'Always',
	'autorisation_webmestre' => 'To be webmaster', # MODIF
	'avertissement_champs' => 'Do not insert here the primary key (@id_objet@),
		
nor any special fields (id_rubrique, lang, etc.) proposed in the next section.',

	// B
	'bouton_ajouter_champ' => 'Add a field',
	'bouton_ajouter_objet' => 'Add an editorial object',
	'bouton_calculer' => 'Calculate',
	'bouton_charger' => 'Load backup',
	'bouton_charger_sauvegarde_attention' => 'Load Backup erase the plugin information currently being created!',
	'bouton_creer' => 'Create the plugin',
	'bouton_exporter' => 'Export',
	'bouton_menu_edition' => 'edit menu',
	'bouton_outils_rapides' => 'Rapid tools',
	'bouton_reinitialiser_autorisations' => 'Reset permissions',
	'bouton_reinitialiser_chaines' => '
Reset language chains of this object',
	'bouton_renseigner_objet' => 'Prefill this item',
	'bouton_reset' => 'Reset the form',
	'bouton_supprimer_champ' => 'Delete this field',
	'bouton_supprimer_logo' => 'Delete this logo',
	'bouton_supprimer_objet' => 'Delete this editorial object',

	// C
	'c_fabrique_dans_plugins' => 'Simplify your tests!',
	'c_fabrique_dans_plugins_texte' => 'By creating a <code>@dir@</code> writable
in your plugins directory, the factory will make the plugin (its files, its tree)
directly into it. Then you will be able, once the plugin created, to activate it immediately in the plugin administration and test it.
		<br /><br />
		Attention otherwise the plugin is created in  <code>tmp/cache/@dir_cache@</code> ;this
directory is deleted when you empty the cache.
	',
	'c_fabrique_info' => 'Creating a plugin',
	'c_fabrique_info_texte' => 'This tool makes it easy to create a plugin code base. 
		Although the product code is functional, it probably will not be exactly what you expect,
and this is not the goal! The Fabrique creates the basis of files and codes,
but you will then vraissemblablement change depending on what you really want.
		<br /><br />
		We advise you to first understand the functioning of plugins, SPIP and skeletons,
and if you want to manage editorial objects, the operation of pipelines, permits, forms.
This plugin can however also be used to study the code generated based on the options you select.
	',
	'c_fabrique_zone' => 'It’s so easy!',
	'c_fabrique_zone_texte' => 'You will certainly appreciate being producing a plugin
managing one or more editorial objects. Good!
		<br /><br />
		Beware though! If a plugin is easy to create, maintain in time,
manage documentation, his life is much more difficult.
		The best way to maintain a plugin typically involves
two conditions: it is useful and it is shared; shared in the sense that other
Developers and contributors can operate on it and improve it.
In SPIP, shared plugins, with a free code,
can be hosted on SPIP Zone collaboration space.
		In SPIP, shared plugins, with a free code,
can be hosted on SPIP Zone collaboration space.
		<br /><br />
		So before you begin creating a new plugin, check that
does not exist in the SPIP collaboration space already equivalent plugin
where you could bring your improvements, your documentation.
		It is more interesting for everyone there are few duplicates but
functional and sustainable plugins!
	',
	'calcul_effectue' => 'Calculated',
	'chaine_ajouter_lien_objet' => 'Add this @type@',
	'chaine_ajouter_lien_objet_feminin' => 'Add this @type@',
	'chaine_confirmer_supprimer_objet' => 'Do you confirm the deletion of this @type@?',
	'chaine_confirmer_supprimer_objet_feminin' => 'Do you confirm the deletion of this @type@ ?',
	'chaine_icone_creer_objet' => 'Create a @type@',
	'chaine_icone_creer_objet_feminin' => 'Create a @type@',
	'chaine_icone_modifier_objet' => 'Edit this @type@',
	'chaine_icone_modifier_objet_feminin' => 'Edit this @type@',
	'chaine_info_1_objet' => 'a @type@',
	'chaine_info_1_objet_feminin' => 'a @type@',
	'chaine_info_aucun_objet' => 'No @type@',
	'chaine_info_aucun_objet_feminin' => 'No @type@',
	'chaine_info_nb_objets' => '@nb@ @objets@',
	'chaine_info_nb_objets_feminin' => '@nb@ @objets@',
	'chaine_info_objets_auteur' => '@objets@ of this author',
	'chaine_info_objets_auteur_feminin' => '@objets@ of this author',
	'chaine_retirer_lien_objet' => 'Remove this @type@',
	'chaine_retirer_lien_objet_feminin' => 'Remove this @type@',
	'chaine_retirer_tous_liens_objets' => 'Remove all @objets@',
	'chaine_retirer_tous_liens_objets_feminin' => 'Remove all @objets@',
	'chaine_supprimer_objet' => 'Delete this @type@',
	'chaine_supprimer_objet_feminin' => 'Delete this @type@',
	'chaine_texte_ajouter_objet' => 'Add a @type@',
	'chaine_texte_ajouter_objet_feminin' => 'Add a @type@',
	'chaine_texte_changer_statut_objet' => 'This @type@ is:',
	'chaine_texte_changer_statut_objet_feminin' => 'This @type@ is:',
	'chaine_texte_creer_associer_objet' => 'Create and associate a @type@',
	'chaine_texte_creer_associer_objet_feminin' => 'Create and associate a @type@',
	'chaine_texte_definir_comme_traduction_objet' => 'This @type@ is a translation of the @type@ number:',
	'chaine_texte_definir_comme_traduction_objet_feminin' => 'This @type@ is a translation of the @type@ number:',
	'chaine_titre_langue_objet' => 'Lang of this @type@',
	'chaine_titre_langue_objet_feminin' => 'Lang of this @type@',
	'chaine_titre_logo_objet' => 'Logo of this @type@',
	'chaine_titre_logo_objet_feminin' => 'Logo of this @type@',
	'chaine_titre_objet' => '@mtype@',
	'chaine_titre_objet_feminin' => '@mtype@',
	'chaine_titre_objets' => '@mobjets@',
	'chaine_titre_objets_feminin' => '@mobjets@',
	'chaine_titre_objets_rubrique' => '@mobjets@ of this section',
	'chaine_titre_objets_rubrique_feminin' => '@mobjets@ of this section',
	'champ_ajoute' => 'A field was added',
	'champ_auto_rempli' => 'If you leave it empty the field will automatically field in.',
	'champ_deplace' => 'The field has been moved',
	'champ_supprime' => 'The field was deleted',
	'chargement_effectue' => 'Loading completed',
	'config_exemple' => 'Example',
	'config_exemple_explication' => 'Explanations of this example',
	'config_titre_parametrages' => 'Settings',

	// D
	'datalist_aide' => 'Some browsers may propose auto completion by typing a down arrow on the keyboard or clicking 2 times in the entry box.',

	// E
	'echappement_accolades' => '{ }',
	'echappement_crochets' => '[ ]',
	'echappement_diese' => '#',
	'echappement_idiome' => '&lt; :',
	'echappement_inclure' => '&lt;INCLURE',
	'echappement_parentheses' => '( )',
	'echappement_php' => '&lt; ?php',
	'echappement_tag_boucle' => '&lt; from loop', # RELIRE
	'erreur_chargement_fichier' => 'The uploaded file could not be understood. The restoration is not done.',
	'erreur_copie_sauvegarde' => 'The backup of @dir@ could not be performed. By precaution the plugin has not been regenerated.
		The probable cause is the lack of sufficient rights on this source directory on the server.',
	'erreur_envoi_fichier' => 'Error in sending the file.',
	'erreur_suppression_sauvegarde' => 'The old backup  (@dir@) n’a pu être supprimée. could not be deleted. By precaution the plugin has not been regenerated.
		The probable cause is the creation by yourself of added files to the plugin that do not have sufficient rights to be handled by the server.',
	'erreurs' => 'There are errors!',
	'experimental_explication' => '<strong>Experimental! </strong><br />
		The sustainability of entries can not be guaranteed.
This part can change or disappear in future versions.versions.',
	'explication_fichiers' => 'Even if you do not activate them here, some of these files will still be created
depending on other options you have chosen elsewhere, especially if you activate an editorial object.',
	'explication_fichiers_echafaudes' => 'SPIP automatically generates these files in the cache if they are absent. However, you can create some in order to change the default behavior that SPIP offers.
		Furthermore, these files may have minimal functionality additions, then indicated.',
	'explication_fichiers_explicites' => 'These files do not exist by default in SPIP but can be generated as needed for your comfort.',
	'explication_reinitialiser' => 'This action delete plugin information currently being created. So you will start again from scratch!',
	'explication_roles' => 'Experimentally, roles can be managed on connections using the "Roles" plugin.',
	'explication_sauvegarde' => 'La Fabrique creates a backup file (<code>fabrique_{prefixe}.php</code>) within each plugin it creates. 
                You can restore this file by sending it to the server or use one of the files already present.',
	'explication_tables_hors_normes' => 'A table respects the SPIP default standards when it
is named with a plural "s" (like <code>spip_choses</code>) and when its primary key
is based on the table name in the singular (like <code>id_chose</code>). In other cases,
you must complete certain information below.',

	// F
	'fabrique_dev_intro' => 'This tool can help create skeletons for Fabrique',
	'fabrique_dev_titre' => 'Development of la Fabrique',
	'fabrique_intro' => 'plugin manufacturing tool',
	'fabrique_outils' => 'Tools',
	'fabrique_peuple_intro' => 'This tool can help create a file and the settlement of a table at the plugin installation',
	'fabrique_peuple_titre' => 'Populate an object',
	'fabrique_plugin' => 'Making of @plugin@',
	'fabrique_restaurer_titre' => 'Restore or reset a fabrique',
	'fabrique_titre' => 'La Fabrique',
	'fichier_echafaudage_prive/objets/infos/objet.html' => 'Add the preview link',
	'fichier_echafaudage_prive/squelettes/contenu/objets.html' => 'Add a search field',
	'fichier_explicite_action/supprimer_objet.php' => 'Action of removing the object (this file is created automatically if the object does not manage status).',
	'fichier_importation_cree_dans' => 'Import file created in the directory <code>@dir@</code>, file <code>@import@</code> with @lignes@ lines for a total of @taille@',
	'fichiers_importations_compresses_cree_dans' => 'import file created in the directory <code>@dir@</code>, files <code>@import@</code> and <code>@donnees_compressees@</code>, with @lignes@ lines for a total of @taille@',

	// I
	'image_supprimee' => 'The image has been removed',
	'insertion_code_explication' => 'This section allows you to insert code in parts provided by la Fabrique. Be careful though that this code is still valid!
	', # MODIF

	// L
	'label_auteur' => 'Author Name',
	'label_auteur_lien' => 'URL to the author',
	'label_auteurs_liens' => 'Liase authors?',
	'label_auteurs_liens_explication' => 'Adds the author connection form on this item.',
	'label_boutons' => 'Buttons',
	'label_boutons_explication' => 'Insert buttons in these places:',
	'label_caracteristiques' => 'Caracteristics',
	'label_categorie' => 'Category',
	'label_champ_date_publication' => 'SQL date field',
	'label_champ_date_publication_explication' => 'To manage a publication date, specify its field, such as "date" or "publish_date"',
	'label_champ_est_editable' => 'It can be edited',
	'label_champ_est_obligatoire' => 'It is mandatory',
	'label_champ_est_versionne' => 't can be versioned',
	'label_champ_id_rubrique' => 'Create the field <strong>id_rubrique</strong>',
	'label_champ_id_secteur' => 'Create the field <strong>id_secteur</strong>',
	'label_champ_id_trad' => 'Field <strong>id_trad</strong>',
	'label_champ_lang_et_langue_choisie' => 'Fields <strong>lang</strong> and <strong>langue_choisie</strong>',
	'label_champ_langues' => 'Language administration',
	'label_champ_langues_explication' => 'Add fields to manage the languages of the object (lang and langue_choisie) and translations (id_trad)?',
	'label_champ_plan_rubrique' => 'List the object in the sitemap?',
	'label_champ_rubriques' => 'id_rubrique',
	'label_champ_rubriques_explication' => 'Enables to allocate this item to a section',
	'label_champ_statut' => 'Field <strong>statut</strong>',
	'label_champ_statut_explication' => 'Allows the use of publication status (proposed to publication, published, trash ...)',
	'label_champ_statut_rubrique' => 'Affect the status of the sections if this element is present',
	'label_champ_titre' => 'Calculate titles',
	'label_champ_titre_explication' => 'Use SQL fields you have declared to your object',
	'label_champ_vue_rubrique' => 'Display the list in a section',
	'label_charger_depuis_table_sql' => 'Define from a SQL table',
	'label_charger_depuis_table_sql_attention' => 'This erases some of the information you entered for that object.',
	'label_charger_depuis_table_sql_explication' => 'You can pre-fill your object using an existing SQL table known in SPIP',
	'label_cle_primaire' => 'Primary Key',
	'label_cle_primaire_attention' => 'It is advisable to put the table name in the singular, prefixed to id_. This prefix is important. In his absence,
some joints to link tables using criteria such as
		<code>{id_mot ?}</code> or <code>{id_auteur ?}</code>
		on a loop of this item will give a skeleton error.',
	'label_cle_primaire_explication' => 'Example "id_chose"',
	'label_cle_primaire_sql' => 'SQL definition for the primary key',
	'label_cle_primaire_sql_attention' => 'It is advisable to specify a numeric primary key
		(<code>bigint(21) NOT NULL</code>). When the field type is not an integer,
it is impossible to SPIP to create a new element in this item because the primary key
can not be assigned an "auto increment".
		Also, if your table already contains rows
with non-integer data in the primary key, or zeros to the left (0123), these data
can not be read by SPIP as it applies intval function (force a value to be an integer)
automatically on all prefixed field id_ and the primary key of an editorial object.',
	'label_cle_primaire_sql_explication' => 'SQL definition for the primary key',
	'label_code_resultat' => 'transformed code',
	'label_code_squelette' => 'Source code skeleton',
	'label_colonne_sql' => 'SQL Column',
	'label_colonne_sql_explication' => 'A field name for SQL. Example "post_scriptum"',
	'label_compatibilite' => 'Compatibility',
	'label_definition_sql' => 'SQL Definition',
	'label_description' => 'Description',
	'label_documentation_url' => 'Documentation (url)',
	'label_echappements' => 'Escape what?',
	'label_etat' => 'condition',
	'label_exemples' => 'Insert examples',
	'label_exemples_explication' => 'Add as comments in the files of the plugin code examples and help text?',
	'label_explication' => 'explanatory sentence for entering',
	'label_fichier_administrations' => 'administrations file?',
	'label_fichier_administrations_explication' => 'Create the installation file / uninstall?',
	'label_fichier_autorisations' => 'Authorizations',
	'label_fichier_fonctions' => 'Functions',
	'label_fichier_options' => 'Options',
	'label_fichier_pipelines' => 'Pipelines',
	'label_fichier_sauvegarde' => 'backup file',
	'label_fichier_sauvegarde_ordinateur' => 'On your computer',
	'label_fichier_sauvegarde_serveur' => 'On the server',
	'label_fichiers' => 'Files',
	'label_fichiers_echafaudes' => 'scaffolded files', # RELIRE
	'label_fichiers_explicites' => 'specific files',
	'label_formulaire_configuration' => 'configuration form?',
	'label_formulaire_configuration_titre' => 'Title of the configuration page',
	'label_genre' => 'Gender',
	'label_genre_explication' => 'Used for pre-calculation of the text language chains.',
	'label_genre_feminin' => 'Female',
	'label_genre_masculin' => 'Male',
	'label_inserer_administrations_desinstallation' => 'Complete uninstallation in the function <code>vider_table()</code>',
	'label_inserer_administrations_fin' => 'A the end of the file to insert new functions',
	'label_inserer_administrations_maj' => 'Complete <code>$maj</code> in the function <code>upgrade()</code>',
	'label_inserer_base_tables_fin' => 'At the end of the file to insert new functions',
	'label_inserer_paquet' => 'At dependencies level',
	'label_libelle' => 'Wording',
	'label_libelle_champ_explication' => 'A field name for humans.
 Example « Post-Scriptum »',
	'label_licence' => 'Licence',
	'label_logo' => 'Logo',
	'label_logo_taille' => 'Logo of @taille@px',
	'label_logo_variantes' => 'logos variants?',
	'label_logo_variantes_explication' => 'Create all variants (new, edit, del, add) logo (larger sizes or equal to 16 pixels).',
	'label_nom' => 'Name',
	'label_nom_pluriel' => 'Plural Name',
	'label_nom_pluriel_explication' => 'Example "Things"',
	'label_nom_singulier' => 'Singular Name',
	'label_nom_singulier_explication' => 'Example "Thing"',
	'label_prefixe' => 'Prefix',
	'label_recherche' => 'Search',
	'label_recherche_explication' => 'Weighting of research in this field. Any value between 1 and 10
indicate that SPIP can search in this field during a search on the subject.
Leave blank for not looking into it.',
	'label_roles' => 'List of roles',
	'label_roles_explication' => 'Each line describes a role: <code> code of the role, title role </code>.
		The first role is considered the role to be applied by default. Example: <code>translator, translator </code>',
	'label_saisie' => 'Type of entries',
	'label_saisie_explication' => 'If necessary (to display this field in the form), indicate the type of entry (from the plugin "saisies") you want.',
	'label_saisie_options' => 'Entries options',
	'label_saisie_options_explication' => 'Options of the tag #SAISIE.<br />
		Example for a textarea :<br />
		<code>conteneur_class=pleine_largeur, class=inserer_barre_edition, rows=4</code><br />
		Example for selection / checkbox / radio :<br />
		<code>datas=[(#ARRAY{cle1,valeur1,cle2,valeur2})]</code>',
	'label_saisies' => 'Entries',
	'label_saisies_explication' => 'Create entries and their views',
	'label_schema' => 'Scheme',
	'label_schema_explication' => 'Version of the data structure',
	'label_scripts_post_creation' => '<code>post_creation</code>',
	'label_scripts_post_creation_explication' => 'After the creation of the files of your plugin in <code>@destination_plugin@</code>',
	'label_scripts_pre_copie' => '<code>pre_copie</code>',
	'label_scripts_pre_copie_explication' => 'Before you save the current plug-in in <code>@destination_ancien_plugin@</code>',
	'label_slogan' => 'Slogan',
	'label_table' => 'SQL Table Name',
	'label_table_a_exporter' => 'SQL Table to export',
	'label_table_attention' => 'It is advisable to name the table in the plural, with a final s.
However SPIP and la Fabrique can manage other cases.',
	'label_table_compresser_donnees' => 'Compress the data?',
	'label_table_compresser_donnees_explication' => 'Useful if the table is large!',
	'label_table_destination' => 'SQL Destination Table',
	'label_table_destination_explication' => 'Name of the table that will be imported data.
By default the same name as the source table.',
	'label_table_explication' => 'For example « spip_choses »',
	'label_table_liens' => 'Create a table of links?',
	'label_table_type' => 'Object type',
	'label_table_type_attention' => 'It is advisable to put the name of the primary key, without the prefix.',
	'label_table_type_explication' => 'Example "Thing"',
	'label_transformer_objet' => 'Transforming text of this object',
	'label_transformer_objet_explication' => 'Change at best that refers to an object (articles, #ID_ARTICLE ...) using the syntax expected for La Fabrique',
	'label_version' => 'Version',
	'label_vue_auteurs_liens' => 'The list on Author View?',
	'label_vue_auteurs_liens_explication' => 'Displays the list of elements of the object related to an author,  on the Author page',
	'label_vue_liens' => 'Allow to capture the links on these objects?',
	'label_vue_liens_explication' => 'Add an edit form of links on objects:',
	'legend_autorisations' => 'Authorizations',
	'legend_chaines_langues' => 'language chains',
	'legend_champs' => 'Fields',
	'legend_champs_speciaux' => 'Special fields',
	'legend_champs_sql' => 'SQL fields used for:',
	'legend_configuration' => 'Configuration',
	'legend_date_publication' => 'Publication date',
	'legend_description' => 'Description',
	'legend_fichiers' => 'Files',
	'legend_fichiers_supplementaires' => 'Supplementary files',
	'legend_inserer_administrations' => 'In
<code>@prefixe@_administrations.php</code>',
	'legend_inserer_base_tables' => 'In <code>base/@prefixe@.php</code>',
	'legend_inserer_paquet' => 'In <code>paquet.xml</code>',
	'legend_insertion_code' => 'Code insertion',
	'legend_installation' => 'Installation',
	'legend_langues_et_traductions' => 'Langs and translations',
	'legend_liaisons' => 'Liaisons',
	'legend_liaisons_auteurs_liens' => 'spip_auteurs_liens',
	'legend_liaisons_objet_liens' => 'spip_@objet@_liens',
	'legend_logo' => 'Logos',
	'legend_logo_specifiques' => 'Specific Logos',
	'legend_logo_specifiques_explication' => 'You can also provide specific logos
for certain sizes. Otherwise these images will be calculated by SPIP based on the size above the closest, or from the basic logo of the object.',
	'legend_options' => 'Options',
	'legend_paquet' => 'Paquet',
	'legend_pre_construire' => 'pre build',
	'legend_resultat' => 'Result',
	'legend_roles' => 'Roles',
	'legend_rubriques' => 'Sections',
	'legend_saisie' => 'Entry',
	'legend_scripts' => 'Scripts to run',
	'legend_statut' => 'Status',
	'legend_suppression' => 'Deletion',
	'legend_table' => 'Table',
	'legend_tables_hors_normes' => 'Specificities of non-satandard tables ',

	// M
	'message_diff' => 'Differences with the previous creation',
	'message_diff_explication' => 'The "diff" is also stored in the file  <code>fabrique_diff.diff</code>
		of the generated plugin',
	'message_diff_suppressions' => 'Files were deleted during this new creation.',

	// O
	'objet_ajoute' => 'A new editorial object was added',
	'objet_autorisations_reinitialisees' => 'The permissions of the object have been reset.',
	'objet_chaines_reinitialisees' => 'Language chains of the object have been reset.',
	'objet_deplace' => 'The object has been moved',
	'objet_renseigne' => 'The editorial object is populated with the SQL table indicated',
	'objet_supprime' => 'The editorial object was deleted',
	'onglet_fabrique' => 'Making of plugins',
	'onglet_fabrique_outils' => 'Tools',
	'onglet_fabrique_restaurer' => 'Restoration, Reset',
	'onglet_objet' => 'Object',
	'onglet_objet_n' => 'Object #@nb@',
	'onglet_plugin' => 'Plugin',

	// P
	'plugin_cree_succes' => 'The plugin was successfully created',
	'plugin_cree_succes_dans' => 'The plugin was successfully created in <br /><code>@dir@</code>',

	// R
	'reinitialisation_effectuee' => 'reset done',
	'reititialiser' => 'Reset',
	'repertoire_plugin_fabrique' => 'To failitate your tests you can create a directory <code>@dir@</code> readable in your plugin directory. Thus, the created plugins will be available soon through Administration plugins and activated.',
	'restaurer' => 'Restore',

	// S
	'saisies_objets' => 'Entry <code>@saisie@</code>: Simple Object Selector to sparsely populated tables.',
	'scripts_explication' => 'Valid PHP code can be executed
at some time of the plugin creation process. This allows you to process
actions not provided by the Fabrique as recover files that you have added,
moving them from the old to the new plugin.
		A number of variables are available
when running these scripts, such as <code>$destination_plugin</code>
		(The path to the future plugin), <code>$destination_ancien_plugin</code> (the
copy of the old plugin - if it existed before), <code>$destination</code> (the
parent path of these)',
	'scripts_securite_webmestres' => 'For safety reasons only this site webmasters can execute scripts in this section.',

	// T
	'titre_plugin' => 'Plugin « @plugin@ »',

	// V
	'valider_nom_objet_avant' => 'To enter the language chains, please first validate 
the form after entering the name of the object. This enables to complete part of language chains, it will take you just checking out.'
);
