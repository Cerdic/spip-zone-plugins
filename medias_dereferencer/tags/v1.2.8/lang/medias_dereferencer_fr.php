<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'adresse_ip_explication' => 'Si vous avez activé la création du fichier <code>.htaccess</code> dans le répertoire <code>IMG/</code>, vous pouvez renseigner ci-dessous la liste des adresses IP qui pourront consulter les documents physiques.<br/><strong>Veuillez saisir les adresses IP séparées par un point-virgule ";"</strong>',
	'adresse_ip_label' => 'Liste des adresses IP autorisées',
	'apache_explication' => 'Avez-vous une version d\'Apache supérieure ou égale à 2.4 ?',
	'apache_label' => 'Version d\'Apache',

	// C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Paramétrages',

	// H
	'htaccess_content' => 'Contenu du fichier .htaccess',
	'htaccess_explication' => 'Désirez-vous interdire la consultation des documents non-publiés ? Si oui, une tâche de fond créera un fichier <code>.htaccess</code> dans le répertoire <code>IMG/</code> du site contenant tous les fichiers non consultables.',
	'htaccess_label' => 'Créer le fichier <em>.htaccess</em>',
	'htaccess_legend' => 'Fichier <em>.htaccess</em>',

	// L
	'lier_document_choix_non' => 'Non',
	'lier_document_choix_oui' => 'Oui',
	'lier_document_explication' => 'Il arrive parfois qu\'un document soit utilisé en raccourcis typographique sans être lié à l\'objet (articles, rubriques, etc.). Lorsque ce cas se présente, désirez-vous que le plugin "Déréférencer les médias" lie le document à l\'objet éditorial ?',
	'lier_document_label' => 'Désirez-vous lier les documents ?',

	// M
	'medias_dereferencer_titre' => 'Déréférencer les médias',

	// R
	'robots_txt_content' => 'Inclusion pour le fichier robots.txt',

	// T
	'titre_page_configurer_medias_dereferencer' => 'Déréférencer les médias',
	'titre_page_medias_lister_disallow' => 'Liste des Médias interdits aux moteurs de recherche.',
);
