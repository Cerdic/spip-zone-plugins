<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// A
	'aide_choix_champs' => 'Liste des noms des champs SQL séparés par des ","<br/><em>Laisser vide pour sélectionner tous les champs principaux.</em>',
	'action_dupliquer_article' => "Dupliquer cet article",
	'action_dupliquer_rubrique' => "Dupliquer cette rubrique",
	'action_dupliquer_rubrique_arbo' => 'Dupliquer l\'arborescence de la rubrique',
	'autorisations' => 'Autorisations',
	'autorisations_article' => 'Qui peut dupliquer des articles&nbsp;?',
	'autorisations_rubriques' => 'Qui peut dupliquer des rubriques&nbsp;?',
	
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
	'configurer_objets_label' => 'Contenus à dupliquer',
	'configurer_personnaliser_champs_label' => 'Personnaliser les champs à dupliquer pour ces contenus',
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
