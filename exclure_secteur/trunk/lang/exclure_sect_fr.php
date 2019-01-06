<?php
// Ceci est un fichier langue de SPIP -- This is a SPIP language file

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'configurer_form_titre'	=> 'Configuration',
	'configurer_page_titre'	=> 'Plugin Exclure-secteur',
	'configurer_menu_entree' =>'Exclure des secteurs',

	// E
	'secteurs_exclus_fieldset' => 'Secteurs à exclure',
	'secteurs_exclus_explication'	=> 'Choisissez les secteurs à exclure. Ceux-ci n\'apparaîtront pas sur le site public, à moins d\'utiliser le critère <code>{tout_voir}</code>',

	// I
	'id_explicite_label' => 'Identifiant explicite',
	'id_explicite_explication' => 'Le critère <code>{tout}</code> est-il équivalent au critère <code>{tout_voir}</code> ?',

	// R
	'reglages_avances_fieldset'	=> 'Réglages avancés du plugin',

	// T
	'tout_label' => 'Critère <code>{tout}</code>',
	'tout_explication' => 'Ignorer les boucles sur lesquelles l\'identifiant de l\'objet est explicité ou pris dans le contexte ? <small>(Permet de ne pas modifier certains squelettes comme article.html)</small>',
);
