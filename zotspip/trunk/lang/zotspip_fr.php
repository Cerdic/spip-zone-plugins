<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/zotspip/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'afficher_masquer_details' => 'Afficher/masquer les détails',
	'ajouter_createur' => 'Ajouter un autre auteur',
	'ajouter_tag' => 'Ajouter un autre mot-clé',
	'annee_non_precisee' => 'Année non précisée',
	'aucune_reference' => 'Aucune référence ne correspond.',

	// B
	'bibliographie_zotero' => 'une bibliographie Zotero',
	'bouton_forcer_maj_complete' => 'Forcer une mise à jour complète',
	'bouton_synchroniser' => 'Synchroniser',

	// C
	'configurer_zotspip' => 'Configurer ZotSpip',
	'confimer_remplacement' => 'Remplacer <strong>@source@</strong> par <strong>@dest@</strong> ? Attention, cette opération est irréversible !',
	'confirmer' => 'Confirmer',
	'connexion_ok' => 'La connexion avec Zotero est opérationnelle.',
	'contributeurs' => 'Contributeurs',
	'createurs' => 'Auteur(s)',

	// D
	'description_page-biblio' => 'Recherche et affichage des références bibliographiques de la librairie Zotero synchronisée avec ZotSpip.',
	'deselectionner_tout' => 'Déselectionner tout',
	'droits_insuffisants' => 'Vous n’avez pas les droits requis pour procéder à cette modification.',

	// E
	'erreur_connexion' => 'ZotSpip n’a pas été capable de se connecter à Zotero. Veuillez vérifier vos paramètres de connexion. Si vous utilisez un proxy, veuillez vérifier qu’il est correctement configuré dans Spip (Configuration > Fonctions avancées). À savoir, ZopSpip ne fonctionne pas toujours si un proxy est requis.',
	'erreur_dom' => 'Pour fonctionner, ZotSpip nécessite l’extension PHP DOM. Veuillez activer/installer cette extension.',
	'erreur_openssl' => 'Pour fonctionner, ZotSpip nécessite l’extension PHP openSSL. Veuillez activer/installer cette extension.',
	'erreur_simplexml' => 'Pour fonctionner, ZotSpip nécessite l’extension PHP SimpleXML. Veuillez activer/installer cette extension.',
	'explication_api_key' => 'S’obtient sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pensez à accorder des droits d’accès suffisants à cette clé.',
	'explication_autoriser_modif_zotero' => 'Activer les options de modification de la librairie Zotero (par exemple, la fusion d’auteurs) ? Si oui, qui a les droits suffisants pour valider ces modifications ? ATTENTION : vous devez également vérifier que vote <em>Clé API</em> a les droits en écriture.',
	'explication_corriger_date' => 'Zotero transmets les dates de publication telles qu’elles ont été saisies. Dès lors, le processeur CSL n’est pas toujours en capacité de décomposer correctement ces dernières en raison de la grande variété de formats différents. Si tel est le cas, la date de publication ne sera pas affichée une fois les références mises en forme. ZotSpip peut corriger en amont les dates de publications. Attention : seule l’année sera alors transmise au processeur CSL, sauf si la date est de la forme aaaa-mm-jj ou aaaa-mm. Cette option n’a par contre aucune répercussion sur la librairie Zotero elle-même.',
	'explication_depuis' => 'Soit une année (par exemple : <em>2009</em>), soit une durée en année suivie du mot français <em>ans</em> (par exemple : <em>3ans</em>) ou du mot anglais <em>years</em> (par exemple : <em>3years</em>).',
	'explication_id_librairie' => 'Pour une librairie personnelle, le <em>userID</em> est indiqué sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pour un groupe, le <em>groupID</em> se trouve dans l’URL de configuration du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;groupID&gt;/settings</em>.',
	'explication_maj_zotspip' => 'ZotSpip se synchronise à intervalles réguliers (environ toutes les 4 heures) avec le serveur Zotero. Seules les dernières modifications (depuis la dernière synchronisation) sont prises en compte. Au besoin, vous pouvez forcer une mise à jour complète de la base de données, toutes les références étant alors téléchargées à nouveau (si votre librairie est importante, cette synchronisation se fera en plusieurs étapes, seulement 50 références pouvant être mises à jour à la fois).',
	'explication_ordre_types' => 'Vous pouvez personnaliser l’ordre utilisé pour les tris par type de référence (changez l’ordre par glisser/déposer).',
	'explication_username' => 'Pour une librairie personnelle, le nom d’utilisateur est indiqué sur la <a href="https://www.zotero.org/settings/account">page de configuration du compte</a>. Pour un groupe partagé, le nom du groupe se situe à la fin de l’URL de la page d’accueil du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;nom_du_groupe&gt;</em> (dans certain cas, le nom du groupe correspondant à son identifiant numérique).',
	'exporter' => 'Exporter',
	'exporter_reference' => 'Exporter la référence :',
	'exporter_selection' => 'Exporter la sélection au format',

	// F
	'filtrer' => 'Filtrer',

	// I
	'identifier_via_doi' => 'Identifier la ressource à partir de son DOI',
	'identifier_via_isbn' => 'Identifier la ressource à partir de son ISBN',
	'item_admin' => 'administrateurs non restreints',
	'item_admin_restreint' => 'tous les administrateurs (y compris restreints)',
	'item_aeres' => 'selon la classification AERES',
	'item_annee' => 'par année',
	'item_annee_type' => 'par année puis par type',
	'item_aucun' => 'aucun',
	'item_auteur' => 'par auteur',
	'item_complet' => 'tous les champs',
	'item_date_ajout' => 'par date d’ajout dans la base',
	'item_liste' => 'liste',
	'item_liste_simple' => 'liste simple',
	'item_numero' => 'par numéro',
	'item_personne' => 'personne',
	'item_premier_auteur' => 'par premier auteur',
	'item_recente' => 'publications récentes',
	'item_redacteur' => 'administrateurs + rédacteurs',
	'item_resume_tags' => 'résumé + mots-clés',
	'item_type' => 'par type de référence',
	'item_type_annee' => 'par type puis par année',
	'item_type_librairie_group' => 'groupe',
	'item_type_librairie_user' => 'utilisateur',
	'item_volume' => 'par numéro de volume',
	'item_webmestre' => 'seulement les webmestres',
	'items_zotero' => 'Références Zotero',

	// L
	'label_annee' => 'Année',
	'label_api_key' => 'Clé API',
	'label_auteur' => 'Auteur',
	'label_autoriser_modif_zotero' => 'Modifications de la librairie Zotero',
	'label_collection' => 'Collection',
	'label_conference' => 'Conférence',
	'label_corriger_date' => 'Corriger les dates de publication',
	'label_csl' => 'Style CSL (mise en forme)',
	'label_csl_defaut' => 'Style par défaut',
	'label_depuis' => 'Depuis',
	'label_details' => 'Détails',
	'label_editeur' => 'Maison d’édition',
	'label_export' => 'Afficher les options d’exportation ?',
	'label_id_librairie' => 'Identifiant de la librairie',
	'label_identifiants_zotero' => 'Identifiants Zotero',
	'label_liens' => 'Afficher les liens ?',
	'label_max' => 'Nombre maximum de références affichées',
	'label_options' => 'Options',
	'label_options_affichage' => 'Options d’affichage',
	'label_ordre_types' => 'Tri par type de référence',
	'label_page_biblio' => 'Activer la page ‘biblio’ pour Zpip ?',
	'label_publication' => 'Publication',
	'label_recherche_libre' => 'Recherche libre',
	'label_selection_references' => 'Sélection des références',
	'label_souligne' => 'Souligner l’auteur principal ?',
	'label_tag' => 'Mot-Clé',
	'label_tags' => 'Mots-Clés',
	'label_titre_page_biblio' => 'Titre de la page ‘biblio’',
	'label_tri' => 'Tri',
	'label_type_doc' => 'Type du document',
	'label_type_librairie' => 'Type de librairie Zotero',
	'label_type_ref' => 'Type de référence',
	'label_username' => 'Nom d’utilisateur ou du groupe',
	'label_variante' => 'Variante',
	'label_zcollection' => 'Collection Zotero',
	'lien_ressource' => 'Lien vers la ressource',
	'liste_createurs' => 'Liste des contributeurs',
	'liste_references' => 'Liste des références Zotero',
	'liste_tags' => 'Liste des mots-clés',

	// M
	'maj_zotspip' => 'Mettre à jour ZotSpip',
	'message_erreur_style_csl' => 'Le style CSL @style@.csl n’a pas été trouvé sur le serveur (fichier inexistant ou plugin désactivé).',
	'modifier_en_ligne' => 'Modifier en ligne sur zotero.org',

	// N
	'nom_page-biblio' => 'Biblio',
	'nom_prenom' => 'Nom, Prénom',

	// O
	'outil_explication_inserer_ref' => 'Identifiant Zotero de la référence. Dans le cas d’une citation, un nombre de page ou un numéro de section peut être précisé après l’identifiant, séparé par @. Plusieurs références peuvent être indiquées, séparées par une virgule.',
	'outil_explication_inserer_ref_exemple' => 'Exemple : 4JA2I4UC@page 16-17,FSCANX5W',
	'outil_inserer_ref' => 'Insérer une réference bibliographie [ref=XXX]',

	// P
	'plusieurs_references' => '@nb@ références',
	'probleme_survenu_lors_du_remplacement' => 'Un problème est survenu lors du remplacement (code HTTP @code@).',

	// R
	'reference_num' => 'Référence n°',
	'remplacer_par' => 'Remplacer par',
	'resume' => 'Résumé :',

	// S
	'sans_auteur' => 'Sans auteur',
	'selectionner_tout' => 'Sélectionner tout',
	'source' => 'source',
	'supprimer_createur' => 'Supprimer cet auteur',
	'supprimer_tag' => 'Supprimer ce mot-clé',
	'sync_complete_demandee' => 'Une synchronisation complète de la base a été demandée.',
	'sync_en_cours' => 'La synchronisation est en cours mais n’est toujours pas terminée. Veuillez cliquer à nouveau sur <em>Synchroniser</em>.',
	'synchronisation_effectuee' => 'Synchronisation effectuée',

	// T
	'tags' => 'Mots-clés :',
	'titre_page_biblio' => 'Références bibliographiques',

	// U
	'une_reference' => '1 référence',

	// V
	'voir_publis_auteur' => 'Voir les publications de @auteur@.',
	'voir_sur_zotero' => 'Consulter cette référence sur zotero.org',

	// Z
	'zotspip' => 'ZotSpip'
);

?>
