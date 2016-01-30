<?php
// This is a SPIP language file	--	Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_identifiant_changer' => 'Changer',
	'bouton_identifiant_modifier' => 'Modifier',
	'bouton_identifiant_definir' => 'Définir un identifiant',

	// C
	'cfg_titre_parametrages' => 'Paramétrages',
	'champ_cfg_objets_label' => 'Objets',
	'champ_cfg_objets_explication' => 'Choix des objets sur lesquels l\'ajout d\'identifiants est possible.
	Les tables possédant déjà une colonne « identifiant » sont exclues.',
	'champ_identifiant_label' => 'Identifiant',
	'champ_identifiant_explication' => 'Identifiant unique pour cet objet. Il s\'agit d\'un nom informatique : charactères alphanumériques ou «_».',

	// E
	'erreur_champ_identifiant_format' => 'Format incorrect : n\'utilisez pas d\'espace, ni de majuscule, ni de caractères accentués ou spéciaux.',
	'erreur_champ_identifiant_doublon' => 'Cet identifiant existe déjà pour ce type d\'objet',
	'erreur_champ_identifiant_taille' => '@nb_max@ caractères au maximum (actuellement @nb@)',

	// I
	'info_aucun_identifiant' => 'Aucun identifiant',
	'identifiants_titre' => 'Identifiants',

	// T
	'titre_page_configurer_identifiants' => 'Configuration des identifiants',
);

?>
