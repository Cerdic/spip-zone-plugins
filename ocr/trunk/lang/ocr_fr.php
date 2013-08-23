<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'analyser_erreur_1' => 'Appel incorrect de l\'executable d\'analyse OCR',
	'analyser_erreur_2' => 'Problème de mémoire',
	'analyser_erreur_3' => 'Impossible d\'analyser le fichier, il doit être dans un format non pris en charge.',
	'analyser_erreur_autre' => 'Erreur inconnue',
	'analyser_erreur_document_inexistant' => 'Document inexistant', 
	'analyser_erreur_fichier_resultat' => 'Le fichier de résultat de l\'analyse OCR n\'existe pas ou n\'est pas lisible.',
	
	// C
	'cfg_bouton_test' => 'Tester',
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Paramétrages',
	'cfg_titre_test' => 'Test de l\'analyse OCR',

	// E
	'erreur_binaire_indisponible' => 'Ce logiciel n\'est pas disponible sur le serveur.',
	'erreur_intervalle_cron' => 'Vous devez indiquer un intervalle supérieur à une seconde.',
	'erreur_nb_docs' => 'Vous devez indiquer un nombre de documents à traiter par itération supérieur à un.',
	'erreur_ocr_bin' => 'Vous devez renseigner le binaire à utiliser pour la reconnaissance de caractères',
	'erreur_verifier_configuration' => 'Il y a des erreurs de configuration.',
	'explication_option_readonly' => 'Cette option est forcée sur ce site et n\'est donc pas configurable.',
	
	// G
	'general' => 'Général',

	// I
	'indiquer_chemin_bin' => 'Indiquer le chemin vers le binaire traitant la reconnaissance de caractères',
	'indiquer_options_bin' => 'Indiquer les options pour la reconnaissance de caractères',
	'intervalle_cron' => 'Intervalle de temps entre deux passages du CRON (en secondes).',
	
	// M
	'message_ok_configuration' => 'Enregistrement de vos préférences terminé',

	// N
	'nombre_documents' => 'Nombre de documents à traiter par itération du CRON',
	
	// O
	'ocr_titre' => 'ocr',

	// T
	'test_label_id_document' => 'Document à analyser',
	'test_label_resultat' => 'Résultat de l\'analyse',
	'test_erreur_id_document' => 'Numéro de document non valide.',
	'test_erreur_regarder_logs' => '@message@ - voir le fichier de log pour plus de détails.',
	'test_message_resultat' => 'Voici le résultat de l\'analyse OCR.',
	'titre_page_configurer_ocr' => 'Plugin d\'analyse OCR',
);

?>
