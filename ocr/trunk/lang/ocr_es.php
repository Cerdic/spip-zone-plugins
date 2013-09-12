<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/ocr?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'analyser_erreur_1' => 'Llamada incorrecta del ejecutable de análisis OCR',
	'analyser_erreur_2' => 'Problema de memoria',
	'analyser_erreur_3' => 'Imposible analizar el archivo, debe estar en un formato no encargado.',
	'analyser_erreur_autre' => 'Error desconocida',
	'analyser_erreur_document_inexistant' => 'El documento no se encuentra',
	'analyser_erreur_executable_introuvable' => 'El programa no se encuentra',
	'analyser_erreur_fichier_resultat' => 'El archivo de resultado del análisis OCR no existe o no se puede leer.',

	// C
	'cfg_bouton_test' => 'Probar',
	'cfg_exemple' => 'Ejemplo',
	'cfg_exemple_explication' => 'Explicación de este ejemplo',
	'cfg_titre_parametrages' => 'Configuración',
	'cfg_titre_test' => 'Prueba del análisis OCR',
	'configuration_ocr' => 'Análisis OCR',

	// E
	'erreur_binaire_indisponible' => 'Este programa no esta disponible en el servidor.',
	'erreur_intervalle_cron' => 'El intervalo tiene que ser superior a un segundo.',
	'erreur_nb_docs' => 'El número de documentos a procesar por iteración tiene que ser superior a uno.',
	'erreur_ocr_bin' => 'Tiene que llenar el binario a utilizar para el reconocimiento de caracteres',
	'erreur_taille_texte_max' => 'El número de caracteres tiene que ser superior a uno.',
	'erreur_verifier_configuration' => 'Hay errores en la configuración.',
	'explication_option_readonly' => 'Esta opción esta forzada en este sitio, entonces no puede ser configurada.',

	// G
	'general' => 'General',

	// I
	'indiquer_chemin_bin' => 'Indicar el camino hacía el binario de reconocimiento de caracteres',
	'indiquer_options_bin' => 'Indicar las opciones para el reconocimiento de caracteres',
	'intervalle_cron' => 'Intervalo de tiempo entre dos pasajes del CRON (en segundos).',

	// M
	'message_ok_configuration' => 'Sus preferencias fueron grabadas',

	// N
	'nombre_documents' => 'Número de documentos a procesar por iteración CRON',

	// O
	'ocr_titre' => 'ocr',

	// S
	'statistiques_bouton_tout' => 'Volver a procesar todo',
	'statistiques_label_nb_err' => 'Error durante el análisis, o no se pueden analizar',
	'statistiques_label_nb_non' => 'No analizados todavía',
	'statistiques_label_nb_oui' => 'Analizados',
	'statistiques_message_relance' => 'El análisis a sido lanzado de nuevo sobre todos los documentos',
	'statistiques_titre' => 'Estadísticas',

	// T
	'taille_texte_max' => 'Límite de caracteres en el texto extraído',
	'test_erreur_id_document' => 'Número de documento invalido.',
	'test_erreur_regarder_logs' => '@message@ - ver el archivo de log para más detalles.',
	'test_label_id_document' => 'Documento a analizar',
	'test_label_resultat' => 'Resultado del análisis',
	'test_message_resultat' => 'Ver el resultado del análisis OCR.',
	'titre_page_configurer_ocr' => 'Plugin de análisis OCR'
);

?>
