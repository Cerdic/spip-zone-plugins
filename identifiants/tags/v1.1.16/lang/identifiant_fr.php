<?php
// This is a SPIP language file -- Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_identifiant_changer' => 'Changer',
	'bouton_identifiant_modifier' => 'Modifier',
	'bouton_identifiant_definir' => 'Définir un identifiant',

	// C
	'cfg_titre_parametrages' => 'Paramétrages',
	'champ_cfg_objets_label' => 'Objets',
	'champ_cfg_objets_explication' => 'Choix des types d\'objets sur lesquels l\'ajout d\'un identifiant est possible.',
	'champ_id_objet_label' => 'N°',
	'champ_identifiant_label' => 'Identifiant',
	'champ_objet_label' => 'Objet',
	'champ_titre_label' => 'Titre',
	'champ_identifiant_explication' => 'Identifiant unique pour cet objet. Il s\'agit d\'un nom informatique : caractères alphanumériques ou «_».',

	// E
	'erreur_champ_identifiant_format' => 'Format incorrect : n\'utilisez pas d\'espace, ni de majuscule, ni de caractères accentués ou spéciaux.',
	'erreur_champ_identifiant_doublon' => 'Cet identifiant existe déjà pour ce type d\'objet',
	'erreur_champ_identifiant_doublon_objet' => 'Cet identifiant est déjà utilisé par un même type d\'objet : @objet@ @id_objet@',
	'erreur_champ_identifiant_taille' => '@nb_max@ caractères au maximum (actuellement @nb@)',

	// I
	'info_1_identifiant' => 'Un identifiant',
	'info_aucun_identifiant' => 'Aucun identifiant',
	'info_nb_identifiants' => '@nb@ identifiants',
	'identifiants_titre' => 'Identifiants',
	'info_identifiants_objets_exclus' => 'Les objets suivants ont déjà un champ «identifiant» et ne sont donc pas affichés',
	'info_identifiants_objets_manquants' => 'Les squelettes actuels du site suggèrent d\'activer les objets suivants',

	// T
	'titre_page_configurer_identifiants' => 'Configuration des identifiants',

	// U
	'utiles_explication' => 'Les squelettes actuels du site peuvent utiliser ces identifiants pour les @objets@.',
	'utiles_generer_identifiant' => 'Attribuer l\'identifiant <strong>@identifiant@</strong>',
	'utiles_titre' => 'Identifiants utiles',

);
