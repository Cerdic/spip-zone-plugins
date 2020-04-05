<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'action_dupliquer_contenu' => 'Dupliquer ce contenu',
	'action_dupliquer_contenu_enfants' => 'Dupliquer ce contenu et ses enfants',
	'action_dupliquer_contenu_enfants_confirmer' => 'Êtes-vous sûrs de vouloir dupliquer ce contenu et tous ses enfants ?',
	
	// B
	'bouton_confirmer' => 'Confirmer',
	'bouton_confirmer_rub' => "Tout dupliquer (arborescence+articles)",
	'bouton_confirmer_arbo' => "Dupliquer seulement l'arborescence (pas les articles)",

	// C
	'choix_type' => 'Sélectionner le ou les mode(s) de duplication',
	'configurer_autorisation_choix_administrateur' => 'Administrateur',
	'configurer_autorisation_choix_redacteur' => 'Rédacteur',
	'configurer_autorisation_choix_webmestre' => 'Webmestre uniquement',
	'configurer_autorisation_label' => 'Autorisation minimale',
	'configurer_autorisation_option_intro' => 'Autorisation par défaut',
	'configurer_champs_label' => 'Champs à dupliquer',
	'configurer_enfants_label' => 'Quels contenus enfants seront dupliqués ?',
	'configurer_explication_objets_texte' => 'Même si seulement les contenus ci-dessus seront proposés dans l’interface, tous les types de contenus peuvent potentiellement être dupliqués s’ils sont enfants d’autres contenus. On permet donc de configurer en permanence l’ensemble des types de contenus, et non seulement ceux choisis pour les boutons.',
	'configurer_objets_explication' => 'Le plugin va ajouter des boutons sur la page d’admin de ces contenus pour permettre de les dupliquer et parfois de dupliquer aussi leurs enfants s’il y en a.',
	'configurer_objets_label' => 'Contenus à dupliquer dans l’interface',
	'configurer_personnaliser_champs_label' => 'Personnaliser les champs à dupliquer pour ces contenus',
	'configurer_personnaliser_enfants_label' => 'Personnaliser les contenus enfants qui seront dupliqués (par défaut tous)',
	'configurer_titre' => 'Configuration de Duplicator',
	'configurer_statut_label' => 'Statut après duplication',
	'configurer_statut_option_intro' => 'Garder le même',

	// D
	'dupli_art' => "Duplication sur les articles",
	'dupli_art_etat_pub' => "Etat des articles publiés&nbsp;:",
	'dupli_art_etat_pub_expl' => "Status des articles dupliqués. Par défaut, les nouveaux articles sont \"en cours de rédaction\"",
	'dupli_art_etat_pub_label' => "Les articles dupliqués sont publiés en ligne si l'original est publié",
	'dupli_rub' => "Duplication sur les rubriques",
	
	// E
	'etat_article' => "Choix du status des articles dupliqués",

	// I
	'icone_dupliquer' => 'Dupliquer la rubrique',

	// L
	'label_art_champs' => 'Liste des champs à dupliquer pour chaque article :',
	'label_rub_champs' => 'Liste des champs à dupliquer pour chaque rubrique :',

	// M
	'message_annuler' => 'Annuler',
	'message_avertissement_article' => 'Êtes-vous sûr de vouloir dupliquer cet article&nbsp;?',
	'message_avertissement_rubrique' => 'Êtes-vous sûr de vouloir dupliquer cette rubrique&nbsp;?',
	'message_confirmer' => 'Confirmer',	
	
	'operation_executee' => "L'opération a bien été exécutée.",
	'operation_annulee' => "L'opération a été annulée.",
	'operation_retour_ok' => "Se rendre dans la rubrique copiée.",
	'operation_retour_ko' => "Retour aux rubriques.",

	'icone_dupliquer_article' => "Dupliquer l'article",

	'operation_retour_ok_article' => "Se rendre dans l'article dupliqué.",
	'operation_retour_ko_article' => "Retour aux articles.",

	'texte_duplicator' => "Appliquer la duplication aux rubriques et/ou articles"
);
