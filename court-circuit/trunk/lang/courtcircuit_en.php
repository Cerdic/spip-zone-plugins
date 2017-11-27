<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/courtcircuit?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aide_en_ligne' => 'Online help',

	// C
	'configurer_courtcircuit' => 'Configure the rules of sections short circuit',
	'courtcircuit' => 'Short circuit',

	// E
	'explication_compositions_exclure' => 'List of compositions to ignore in redirection processing (separated by commas).',
	'explication_liens_rubriques' => 'Change the URL of redirected sections directly into the skeletons?',
	'explication_regles' => 'The different rules below are tested in that order. If no rule defines a redirection, then the section will be displayed normally.',
	'explication_restreindre_langue' => 'When this option is enabled, only the sections of the active language will be taken into account for the calculation of the redirection. This option is useful if your sections contain articles in different languages. Do not use if your site is organized into language sections or if you use multi fields.',
	'explication_sousrubrique' => 'Browse the first subsection (sort by title number and date)? Redirection rules will be tested again in this subsection.',
	'explication_variantes_squelettes' => 'Example: skeletons of type rubrique-2.html or rubrique=3.html.',

	// I
	'item_appliquer_redirections' => 'Apply the redirection rules',
	'item_jamais_rediriger' => 'Never redirect',
	'item_ne_pas_rediriger' => 'Do not redirect',
	'item_rediriger_sur_article' => 'Redirect on this article',

	// L
	'label_article_accueil' => 'Home article of the section',
	'label_composition_rubrique' => 'Section with composition',
	'label_compositions_exclure' => 'Compositions to exclude',
	'label_exceptions' => 'Exceptions',
	'label_liens' => 'URL of sections',
	'label_liens_rubriques' => 'Act on the #URL_RUBRIQUE tag?',
	'label_plus_recent' => 'The most recent article of the section',
	'label_plus_recent_branche' => 'The most recent article of the branch',
	'label_rang_un' => 'First article (numbered articles)',
	'label_regles' => 'Redirection rules of the sections',
	'label_restreindre_langue' => 'Only take into account the articles of the language?',
	'label_sousrubrique' => 'Subsections',
	'label_un_article' => 'The only article of the section',
	'label_variantes_squelettes' => 'Section with alternative skeletons'
);
