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
	'champ_cfg_objets_label' => 'Contenus identifiables',
	'champ_cfg_objets_explication' => 'Choix des types de contenus où activer les identifiants.',
	'champ_id_objet_label' => 'N°',
	'champ_identifiant_label' => 'Identifiant',
	'champ_objet_label' => 'Contenu',
	'champ_objets_label' => 'Contenus',
	'champ_titre_label' => 'Titre',
	'champ_date_label' => 'Date',
	'champ_identifiant_explication' => 'Identifiant textuel unique pour ce contenu. Ne peut contenir que des chiffres, lettres latines non accentuées et le caractère "_".',

	// E
	'erreur_champ_identifiant_format' => 'Format incorrect : n’utilisez pas d’espace, ni de majuscule, ni de caractères accentués ou spéciaux.',
	'erreur_champ_identifiant_doublon' => 'Cet identifiant est déjà utilisé par un même type de contenu',
	'erreur_champ_identifiant_doublon_objet' => 'Cet identifiant est déjà utilisé par un même type de contenu : @objet@ n°@id_objet@',
	'erreur_champ_identifiant_taille' => '@nb_max@ caractères au maximum (actuellement @nb@)',

	// F
	'filtre_tous' => 'Tous',

	// I
	'info_1_identifiant' => 'Un identifiant',
	'info_aucun_identifiant' => 'Aucun identifiant',
	'info_nb_identifiants' => '@nb@ identifiants',
	'info_identifiants_objets_exclus' => 'Les contenus suivants ont nativement un champ « identifiant » et ne sont donc pas affichés',
	'info_identifiants_objets_manquants' => 'Les squelettes actuels du site suggèrent d\’activer les contenus suivants',

	// M
	'message_confirmer_suppression' => '@nb@ identifiant(s) vont être supprimé(s) suite à cette nouvelle configuration, êtes vous sûr⋅e ? Revalidez le formulaire pour confirmer.',
	'message_ok_adapter_tables' => '@action@ de la colonne « identifiant » sur les tables suivantes : @tables@',
	'message_erreur_adapter_tables' => 'Echec @action@ de la colonne « identifiant » sur les tables suivantes : @tables@',

	// T
	'titre_page_configurer_identifiants' => 'Configuration des identifiants',
	'titre_identifiant' => 'Identifiant',
	'titre_identifiants' => 'Identifiants',

	// U
	'utiles_explication' => 'Les squelettes actuels du site peuvent utiliser ces identifiants pour les @objets@.',
	'utiles_generer_identifiant' => 'Attribuer l’identifiant <strong>@identifiant@</strong>',
	'utiles_titre' => 'Identifiants utiles',

	'verifier_identifiant_titre' => 'Identifiant',
	'verifier_identifiant_description' => 'Vérifier que la chaîne de caractère est valable pour un identifiant informatique : chiffres, lettres latines non accentuées et le caractère "_"',
	'verifier_identifiant_unicite_label' => 'Vérifier l’unicité de l’identifiant (aucune autre utilisation par un même type de contenu)',
	'verifier_identifiant_unicite_objet_label' => 'Type de contenu',

);
