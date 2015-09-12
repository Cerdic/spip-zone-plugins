<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_groupe_selectionne_annule' => "Le groupe de mot d'origine a été remis. Pour déplacer ce mot dans un autre groupe de mot, vous ne devez rien mettre dans le champ mot parent.",
	'erreur_parent_hors_groupe_selectionne' => "Vous ne pouvez pas définir un mot parent qui n'appartient pas au groupe de mot sélectionné.",
	'erreur_parent_sur_mot' => 'Le parent du mot ne peut pas être ce mot lui-même !',
	'erreur_parent_sur_mot_enfant' => 'Le parent du mot ne peut pas être un de ses enfants !',

	// I
	'icone_creation_mot_enfant' => 'Créer un mot enfant',

	// O
	'option_autoriser_mots_arborescents' => 'Mots arborescents',
	'option_autoriser_mots_arborescents_explication' => "Autoriser pour ce groupe la création d'arborescence de mots ?",
	'option_autoriser_mots_arborescents_attention' => "Attention : basculer sur «non» remettra à plat tous les mots arborescents contenus dans ce groupe !",

	// S
	'motsar_titre' => 'Mots arborescents',
	'mot_enfant' => 'Mot enfant :',
	'mot_parent' => 'Mot parent',
);
