<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'base' => 'Base de données',
	'base_desc' => '<p>La base doit être déclarée comme base externe (-> Maintenance / Maintenance technique).</p>
		<p>Elle doit être au même format (même shéma) que la base locale (plugins identiques).<br/>
		Si la base source est une base mise à jour d\'une version 1.9 ou 2.x, il peut être nécessaire d\'ajouter :
		<pre>mysql_query("SET NAMES \'utf8\'");</pre>
		à la fin du fichier de connection.</p>',
	'bouton_importer' => 'Importer cette base',
	'bouton_supprimer' => 'Supprimer cette base',

	// D
	'dossier_existe_pas' => 'Le répertoire @dossier@ n\'existe pas',
	'dossier_pas_lisible' => 'Le répertoire @dossier@ n\'est pas accessible en lecture',

	// H

	// E
	'explications' => '
		<p><strong>Plugin expérimental, en cours de développement !</strong></p>
		<p>Importer une base de données d\'un autre site SPIP</p>
		<p>Cette manipulation peut consommer beaucoup de ressources CPU et être longue si la base source est volumineuse, à déconseiller sur un hébergement mutualisé. </p>',
	'explications_suppression' => 'Fonctionnalité à terminer',

	// I
	'img_dir' => 'Chemin physique des documents',
	'img_dir_desc' => 'Pour copier les documents localement, indiquez (par exemple) :<pre>/var/www/site_spip/IMG</pre>Si le champ est vide, copiez manuellemnent les documents',

	// M
	'maj_base' => 'Mise à jour de la base de données',
	'message_import_ok' => 'Import terminé',
	'message_import_nok' => 'Erreur lors de l\'import',
	'message_img_dir_nok' => 'Merci de préciser le chemin',
	'message_suppression_ok' => 'Objets supprimés',

	// P

	// R
	'referers' => 'Traiter les tables de referers (liens entrants)',

	// S
	'secteur' => 'Secteur',
	'secteur_desc' => 'Pour importer la base dans un secteur, sinon import à la racine',
	'stats' => 'Traiter les tables de statistiques',

	// T
	'titre_assemblage' => 'Assemblage',
	'titre_assemblage_suppression' => 'Suppression',

	// V
	'versions' => 'Traiter les tables de révisions (versions et fragments)',

);

