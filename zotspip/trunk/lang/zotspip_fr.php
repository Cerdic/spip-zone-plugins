<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'afficher_masquer_details' => 'Afficher/masquer les détails',
	'ajouter_createur' => 'Ajouter un autre auteur',
	'ajouter_tag' => 'Ajouter un autre mot-clé',
	'annee_non_precisee' => 'Année non précisée',
	'bouton_forcer_maj_complete' => 'Forcer une mise à jour complète',
	'bouton_synchroniser' => 'Synchroniser',
	'configurer_zotspip' => 'Configurer ZotSpip',
	'confirmer' => 'Confirmer',
	'confimer_remplacement_auteur' => 'Remplacer <strong>@source@</strong> par <strong>@dest@</strong> ? Attention, cette opération est irréversible !',
	'connexion_ok' => 'La connexion avec Zotero est opérationnelle.',
	'createurs' => 'Auteur(s)',
	'erreur_connexion' => 'ZotSpip n\'a pas été capable de se connecter à Zotero. Veuillez vérifier vos paramètres de connexion. Si vous utilisez un proxy, veuillez vérifier qu\'il est correctement configuré dans Spip (Configuration > Fonctions avancées). À savoir, ZopSpip ne fonctionne pas toujours si un proxy est requis.',
	'erreur_openssl' => 'Pour fonctionner, ZotSpip nécessite l\'extension PHP openSSL. Veuillez activer/installer cette extension.',
	'erreur_simplexml' => 'Pour fonctionner, ZotSpip nécessite l\'extension PHP SimpleXML. Veuillez activer/installer cette extension.',
	'explication_api_key' => 'S\'obtient sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pensez à accorder des droits d\'accès suffisants à cette clé.',
	'explication_corriger_date' => 'Zotero transmets les dates de publication telles qu\'elles ont été saisies. Dès lors, le processeur CSL n\'est pas toujours en capacité de décomposer correctement ces dernières en raison de la grande variété de formats différents. Si tel est le cas, la date de publication ne sera pas affichée une fois les références mises en forme. ZotSpip peut corriger en amont les dates de publications. Attention : seule l\'année sera alors transmise au processeur CSL, sauf si la date est de la forme aaaa-mm-jj ou aaaa-mm. Cette option n\'a par contre aucune répercussion sur la librairie Zotero elle-même.',
	'explication_id_librairie' => 'Pour une librairie personnelle, le <em>userID</em> est indiqué sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pour un groupe, le <em>groupID</em> se trouve dans l\'URL de configuration du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;groupID&gt;/settings</em>.',
	'explication_maj_zotspip' => 'ZotSpip se synchronise à intervalles réguliers (environ toutes les 4 heures) avec le serveur Zotero. Seules les dernières modifications (depuis la dernière synchronisation) sont prises en compte. Au besoin, vous pouvez forcer une mise à jour complète de la base de données, toutes les références étant alors téléchargées à nouveau (si votre librairie est importante, cette synchronisation se fera en plusieurs étapes, seulement 50 référénces pouvant être mises à jour à la fois).',
	'explication_ordre_types' => 'Vous pouvez personnaliser l\'ordre utilisé pour les tris par type de référence (changez l\'ordre par glisser/déposer).',
	'explication_username' => 'Pour une librairie personnelle, le nom d\'utilisateur est indiqué sur la <a href="https://www.zotero.org/settings/account">page de configuration du compte</a>. Pour un groupe partagé, le nom du groupe se situe à la fin de l\'URL de la page d\'accueil du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;nom_du_groupe&gt;</em> (dans certain cas, le nom du groupe correspondant à son identifiant numérique).',
	'exporter_reference' => 'Exporter la référence :',
	'identifier_via_doi' => 'Identifier la ressource à partir de son DOI',
	'identifier_via_isbn' => 'Identifier la ressource à partir de son ISBN',
	'items_zotero' => 'Références Zotero',
	'item_type_librairie_group' => 'groupe',
	'item_type_librairie_user' => 'utilisateur',
	'label_api_key' => 'Clé API',
	'label_corriger_date' => 'Corriger les dates de publication',
	'label_csl_defaut' => 'Style par défaut',
	'label_identifiants_zotero' => 'Identifiants Zotero',
	'label_id_librairie' => 'Identifiant de la librairie',
	'label_options' => 'Options',
	'label_ordre_types' => 'Tri par type de référence',
	'label_tags' => 'Mots-Clés',
	'label_type_librairie' => 'Type de librairie Zotero',
	'label_username' => 'Nom d\'utilisateur ou du groupe',
	'lien_ressource' => 'Lien vers la ressource',
	'liste_createurs' => 'Listes des contributeurs',
	'maj_zotspip' => 'Mettre à jour ZotSpip',
	'modifier_en_ligne' => 'Modifier en ligne sur zotero.org',
	'nom_prenom' => 'Nom, Prénom',
	'plusieurs_references' => '@nb@ références',
	'probleme_survenu_lors_du_remplacement' => 'Un problème est survenu lors du remplacement (code HTTP @code@).',
	'remplacer_par' => 'Remplacer par',
	'resume' => 'Résumé :',
	'retour_liste_createurs' => 'Retour à la liste des contributeurs',
	'source' => 'source',
	'supprimer_createur' => 'Supprimer cet auteur',
	'supprimer_tag' => 'Supprimer ce mot-clé',
	'synchronisation_effectuee' => 'Synchronisation effectuée',
	'sync_complete_demandee' => 'Une synchronisation complète de la base a été demandée.',
	'sync_en_cours' => 'La synchronisation est en cours mais n\'est toujours pas terminée. Veuillez cliquer à nouveau sur <em>Synchroniser</em>.',
	'tags' => 'Mots-clés :',
	'une_reference' => '1 référence',
	'zotspip' => 'ZotSpip',
);
?>
