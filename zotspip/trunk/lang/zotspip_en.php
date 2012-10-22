<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/zotspip?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'afficher_masquer_details' => 'Show/hide details',
	'ajouter_createur' => 'Add a new author',
	'ajouter_tag' => 'Add another tag',
	'annee_non_precisee' => 'Year not specified',
	'aucune_reference' => 'No reference found.',

	// B
	'bibliographie_zotero' => 'a Zotero bibliography',
	'bouton_forcer_maj_complete' => 'Force full sync',
	'bouton_synchroniser' => 'Sync',

	// C
	'configurer_zotspip' => 'Configure ZotSpip',
	'confimer_remplacement' => 'Replace <strong>@source@</strong> by <strong>@dest@</strong>? Be carefull, this operation can\'t be undone!',
	'confirmer' => 'Confirm',
	'connexion_ok' => 'The connexion width Zotero is working.',
	'contributeurs' => 'Creators',
	'createurs' => 'Author(s)',

	// D
	'deselectionner_tout' => 'Unselect all',
	'droits_insuffisants' => 'You don\'t have the permission to do this modification.',

	// E
	'erreur_connexion' => 'ZotSpip n\'a pas été capable de se connecter à Zotero. Veuillez vérifier vos paramètres de connexion. Si vous utilisez un proxy, veuillez vérifier qu\'il est correctement configuré dans Spip (Configuration > Fonctions avancées). À savoir, ZopSpip ne fonctionne pas toujours si un proxy est requis.', # NEW
	'erreur_dom' => 'ZotSpip needs DOM extension. Please activate/install this PHP extension.',
	'erreur_openssl' => 'ZotSpip needs openSSL extension. Please activate/install this PHP extension.',
	'erreur_simplexml' => 'ZotSpip needs SimpleXML extension. Please activate/install this PHP extension.',
	'explication_api_key' => 'S\'obtient sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pensez à accorder des droits d\'accès suffisants à cette clé.', # NEW
	'explication_autoriser_modif_zotero' => 'Activer les options de modification de la librairie Zotero (par exemple, la fusion d\'auteurs) ? Si oui, qui a les droits suffisants pour valider ces modifications ? ATTENTION : vous devez également vérifier que vote <em>Clé API</em> a les droits en écriture.', # NEW
	'explication_corriger_date' => 'Zotero transmets les dates de publication telles qu\'elles ont été saisies. Dès lors, le processeur CSL n\'est pas toujours en capacité de décomposer correctement ces dernières en raison de la grande variété de formats différents. Si tel est le cas, la date de publication ne sera pas affichée une fois les références mises en forme. ZotSpip peut corriger en amont les dates de publications. Attention : seule l\'année sera alors transmise au processeur CSL, sauf si la date est de la forme aaaa-mm-jj ou aaaa-mm. Cette option n\'a par contre aucune répercussion sur la librairie Zotero elle-même.', # NEW
	'explication_depuis' => 'Soit une année (par exemple : <em>2009</em>), soit une durée en année suivie du mot <em>ans</em> (par exemple : <em>3ans</em>).', # NEW
	'explication_id_librairie' => 'Pour une librairie personnelle, le <em>userID</em> est indiqué sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pour un groupe, le <em>groupID</em> se trouve dans l\'URL de configuration du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;groupID&gt;/settings</em>.', # NEW
	'explication_maj_zotspip' => 'ZotSpip se synchronise à intervalles réguliers (environ toutes les 4 heures) avec le serveur Zotero. Seules les dernières modifications (depuis la dernière synchronisation) sont prises en compte. Au besoin, vous pouvez forcer une mise à jour complète de la base de données, toutes les références étant alors téléchargées à nouveau (si votre librairie est importante, cette synchronisation se fera en plusieurs étapes, seulement 50 références pouvant être mises à jour à la fois).', # NEW
	'explication_ordre_types' => 'Vous pouvez personnaliser l\'ordre utilisé pour les tris par type de référence (changez l\'ordre par glisser/déposer).', # NEW
	'explication_username' => 'Pour une librairie personnelle, le nom d\'utilisateur est indiqué sur la <a href="https://www.zotero.org/settings/account">page de configuration du compte</a>. Pour un groupe partagé, le nom du groupe se situe à la fin de l\'URL de la page d\'accueil du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;nom_du_groupe&gt;</em> (dans certain cas, le nom du groupe correspondant à son identifiant numérique).', # NEW
	'exporter' => 'Export',
	'exporter_reference' => 'Export the reference:',
	'exporter_selection' => 'Export the selection using the format',

	// F
	'filtrer' => 'Filter',

	// I
	'identifier_via_doi' => 'Identify the resource from DOI',
	'identifier_via_isbn' => 'Identify the resource from ISBN',
	'item_admin' => 'administrateurs non restreints', # NEW
	'item_admin_restreint' => 'tous les administrateurs (y compris restreints)', # NEW
	'item_aeres' => 'by AERES classification',
	'item_annee' => 'by year',
	'item_annee_type' => 'by year and by type',
	'item_aucun' => 'none',
	'item_auteur' => 'by author',
	'item_complet' => 'all fields',
	'item_date_ajout' => 'by date added',
	'item_liste' => 'list',
	'item_liste_simple' => 'simple list',
	'item_numero' => 'by number/issue',
	'item_personne' => 'nobody',
	'item_premier_auteur' => 'by first author',
	'item_recente' => 'recent publications',
	'item_redacteur' => 'administrateurs + rédacteurs', # NEW
	'item_resume_tags' => 'abstract + tags',
	'item_type' => 'by reference type',
	'item_type_annee' => 'by type and by year',
	'item_type_librairie_group' => 'group',
	'item_type_librairie_user' => 'user',
	'item_volume' => 'by volume',
	'item_webmestre' => 'webmasters only',
	'items_zotero' => 'Zotero References',

	// L
	'label_annee' => 'Year',
	'label_api_key' => 'API key',
	'label_auteur' => 'Author',
	'label_autoriser_modif_zotero' => 'Rights to modify the Zotero library',
	'label_collection' => 'Collection',
	'label_conference' => 'Conference',
	'label_corriger_date' => 'Automatic correction of date published',
	'label_csl' => 'Citation style (CSL)',
	'label_csl_defaut' => 'Default style',
	'label_depuis' => 'Since',
	'label_details' => 'Details',
	'label_editeur' => 'Publisher',
	'label_export' => 'Display export options?',
	'label_id_librairie' => 'Library ID',
	'label_identifiants_zotero' => 'Zotero ID',
	'label_liens' => 'Display links?',
	'label_max' => 'Maximum number of displayed references',
	'label_options' => 'Options',
	'label_options_affichage' => 'Display options',
	'label_ordre_types' => 'Sort by reference type',
	'label_page_biblio' => 'Activate the ‘biblio’ page for Zpip?',
	'label_publication' => 'Publication',
	'label_recherche_libre' => 'Open search',
	'label_selection_references' => 'References selection',
	'label_souligne' => 'Underline main author?',
	'label_tag' => 'Tag',
	'label_tags' => 'Tags',
	'label_titre_page_biblio' => 'Title of the ‘biblio’ page',
	'label_tri' => 'Sorting',
	'label_type_doc' => 'Document type',
	'label_type_librairie' => 'Zotero library type',
	'label_type_ref' => 'Reference type',
	'label_username' => 'User/Group name',
	'label_variante' => 'Variant',
	'label_zcollection' => 'Zotero Collection',
	'lien_ressource' => 'Lien vers la ressource', # NEW
	'liste_createurs' => 'Liste des contributeurs', # NEW
	'liste_references' => 'Liste des références Zotero', # NEW
	'liste_tags' => 'Liste des mots-clés', # NEW

	// M
	'maj_zotspip' => 'Update ZotSpip',
	'message_erreur_style_csl' => 'Le style CSL @style@.csl n\'a pas été trouvé sur le serveur (fichier inexistant ou plugin désactivé).', # NEW
	'modifier_en_ligne' => 'Modify online on zotero.org',

	// N
	'nom_prenom' => 'Last name, first name',

	// O
	'outil_explication_inserer_ref' => 'Identifiant Zotero de la référence. Dans le cas d\'une citation, un nombre de page ou un numéro de section peut être précisé après l\'identifiant, séparé par @. Plusieurs références peuvent être indiquées, séparées par une virgule. Exemple : 4JA2I4UC@page 16-17,FSCANX5W', # NEW
	'outil_inserer_ref' => 'Insert a bibliographic reference [ref=XXX]',

	// P
	'plusieurs_references' => '@nb@ references',
	'probleme_survenu_lors_du_remplacement' => 'Un problème est survenu lors du remplacement (code HTTP @code@).', # NEW

	// R
	'reference_num' => 'Reference n°',
	'remplacer_par' => 'Replace by',
	'resume' => 'Abstract:',

	// S
	'sans_auteur' => 'No author',
	'selectionner_tout' => 'Select all',
	'source' => 'source',
	'supprimer_createur' => 'Delete this author',
	'supprimer_tag' => 'Delete this tag',
	'sync_complete_demandee' => 'Une synchronisation complète de la base a été demandée.', # NEW
	'sync_en_cours' => 'La synchronisation est en cours mais n\'est toujours pas terminée. Veuillez cliquer à nouveau sur <em>Synchroniser</em>.', # NEW
	'synchronisation_effectuee' => 'Sync performed',

	// T
	'tags' => 'Tags:',
	'titre_page_biblio' => 'Bibliographic references',

	// U
	'une_reference' => '1 reference',

	// V
	'voir_publis_auteur' => 'All publications of @auteur@.',
	'voir_sur_zotero' => 'View this reference on zotero.org',

	// Z
	'zotspip' => 'ZotSpip'
);

?>
