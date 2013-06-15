<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'base' => 'Site source',
	'base_desc' => '<p>La base de données du site source doit être <a href="'.generer_url_ecrire('admin_tech').'">déclarée comme base externe</a>.</p>
		<p>Le site source doit être dans la même version que le site hôte.</p>',
	'bouton_importer' => 'Démarrer la fusion',
	'bouton_supprimer' => 'Supprimer la fusion',

	// C
	'confirme_warning' => 'Confirmer la fusion des bases ?',

	// D
	'dossier_existe_pas' => 'Le répertoire @dossier@ n\'existe pas',
	'dossier_pas_lisible' => 'Le répertoire @dossier@ n\'est pas accessible en lecture',

	// H

	// E
	'explications' => '
		<p><strong>Plugin expérimental, en cours de développement !</strong></p>
		<p>La fusion de bases de données peut consommer beaucoup de ressources CPU et être longue si la base source est volumineuse, à déconseiller sur un hébergement mutualisé. </p>',
	'explications_suppression' => 'Fonctionnalité à terminer',
	'erreur_versions' => 'Le site hôte et le site source ne sont pas dans la même version :
		<br/>- hôte est en v @vhote@
		<br/>- source est en v @vsource@
		<br/>Faites d\'abord une mise à jour du site source.',

	// I
	'img_dir' => 'Chemin physique des documents',
	'img_dir_desc' => 'Pour copier les documents du site source dans le site hôte, indiquez leur chemin physique, par exemple :<pre>/var/www/site_spip/IMG</pre>Si le champ est vide, aucun document ne sera importé, vous devrez les copier manuellemnent.',

	// M
	'maj_base' => 'Mise à jour de la base de données',
	'manque_champs_source' => 'Les champs "@diff@" manquent dans la table "@table@" de la base source',
	'manque_table_source' => 'La table "@table@" est absente dans la base source',
	'message_import_ok' => 'Import terminé',
	'message_import_nok' => 'Erreur lors de l\'import',
	'message_img_dir_nok' => 'Merci de préciser le chemin',
	'message_suppression_ok' => 'Objets supprimés',

	// P

	// R
	'referers' => 'Traiter les tables de referers (liens entrants)',

	// S
	'secteur' => 'Secteur',
	'secteur_desc' => 'Pour importer le site source dans un secteur, sinon il sera importé à la racine',
	'stats' => 'Traiter les tables de statistiques',

	// T
	'titre_fusion_spip' => 'Fusion de sites Spip',
	'titre_fusion_spip_suppression' => 'Suppression',

	// V
	'versions' => 'Traiter les tables de révisions (versions et fragments)',

);

