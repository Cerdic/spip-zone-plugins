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
	'confimer_remplacement' => 'Replace <strong>@source@</strong> by <strong>@dest@</strong>? Be carefull, this operation can’t be undone!',
	'confirmer' => 'Confirm',
	'connexion_ok' => 'The connexion with Zotero is working.',
	'contributeurs' => 'Creators',
	'createurs' => 'Author(s)',

	// D
	'description_page-biblio' => 'Search and display of the bibliographical references from the library Zotero synchronised with ZotSpip.',
	'deselectionner_tout' => 'Unselect all',
	'droits_insuffisants' => 'You don’t have the permission to do this modification.',

	// E
	'erreur_connexion' => 'ZotSpip cannot connect to Zotero. Please check your settings. If you are using a proxy, please check if the proxy is correctly defined in Spip (Configuration > Advanced functions). Please note that ZotSpip don’t always work properly with a proxy.',
	'erreur_dom' => 'ZotSpip needs DOM extension. Please activate/install this PHP extension.',
	'erreur_openssl' => 'ZotSpip needs openSSL extension. Please activate/install this PHP extension.',
	'erreur_simplexml' => 'ZotSpip needs SimpleXML extension. Please activate/install this PHP extension.',
	'explication_api_key' => 'You can create a private Key on the <a href="https://www.zotero.org/settings/keys">Feeds/API page</a> in your Zotero settings. Don’t forget to provide adequate access to this key.',
	'explication_autoriser_modif_zotero' => 'Activate modification options (as the fusion of two authors)? If yes, who has the right to modify the Zotero library? WARNING: you should also check that your <em>API key</em> has write permissions.',
	'explication_corriger_date' => 'Zotero provides publication dates in the same way they have been captured. Due to the great variety of date format, the CSL processor is not always able to interpret the date correctly. In this situation, the publication date will not be displayed in the reference. ZotSpip could correct the publication dates before sending the reference to the CSL processor. Be careful: only the publication year will be identified, except if the date is using the formats yyyy-mm-dd or yyyy-mm. This option does not modify the original Zotero library.',
	'explication_depuis' => 'It could be a year (example: <em>2009</em>), or a period in years followed by the English word <em>years</em> (example: <em>3years</em>) or the French word <em>ans</em> (example: <em>3ans</em>).',
	'explication_id_librairie' => 'For a personal library, the <em>userID</em> is displayed on the <a href="https://www.zotero.org/settings/keys">Feed/API page</a> in your Zotero settings. For a group library, the <em>groupID</em> is displayed in the URL of the group configuration: <em>https://www.zotero.org/groups/&lt;groupID&gt;/settings</em>.',
	'explication_maj_zotspip' => 'ZotSpip is synced every 4 hours with the Zotero server. Only the last changes are taken into account. You can force a full sync of all references. If your library is large, this sync will be performed in several steps, only 50 references being synchronized in a row.',
	'explication_ordre_types' => 'You can personalise the order of document types when sorting references by type (drag and drop).',
	'explication_username' => 'For a personal library, the <em>user name</em> is displayed on the <a href="https://www.zotero.org/settings/account">Account page</a> in your Zotero settings. For a group library, the <em>group name</em> is displayed in the URL of the home page of the group library: <em>https://www.zotero.org/groups/&lt;group_name&gt;</em> (sometimes the group name is equal to the group ID).',
	'exporter' => 'Export',
	'exporter_reference' => 'Export the reference:',
	'exporter_selection' => 'Export the selection using the format',

	// F
	'filtrer' => 'Filter',

	// I
	'identifier_via_doi' => 'Identify the resource from DOI',
	'identifier_via_isbn' => 'Identify the resource from ISBN',
	'item_admin' => 'administrators (not restricted)',
	'item_admin_restreint' => 'all administrators (including restricted administrators)',
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
	'item_redacteur' => 'administrators + editors',
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
	'lien_ressource' => 'Link to the ressource',
	'liste_createurs' => 'Contributors list',
	'liste_references' => 'Zotero references list',
	'liste_tags' => 'Tags list', # Tags list

	// M
	'maj_zotspip' => 'Update ZotSpip',
	'message_erreur_style_csl' => 'The CSL style @style@.csl was not found on your server (file not existing any more or deactivated plugin).',
	'modifier_en_ligne' => 'Modify online on zotero.org',

	// N
	'nom_page-biblio' => 'Bibliography',
	'nom_prenom' => 'Last name, first name',

	// O
	'outil_explication_inserer_ref' => 'Zotero reference ID. A page or section number could be specified after the ID, separated by @.  Several references could be specified, separated by a comma.',
	'outil_explication_inserer_ref_exemple' => 'Example: 4JA2I4UC@page 16-17,FSCANX5W',
	'outil_inserer_ref' => 'Insert a bibliographic reference [ref=XXX]',

	// P
	'plusieurs_references' => '@nb@ references',
	'probleme_survenu_lors_du_remplacement' => 'A problem occurred (HTTP code @code@).',

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
	'sync_complete_demandee' => 'A full sync was requested.',
	'sync_en_cours' => 'Sync is still ongoing. Please click again the <em>Sync</em> button.',
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
