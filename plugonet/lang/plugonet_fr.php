<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file
// Module: plugonet
// Langue: fr

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// B
	'bouton_generer' => 'Générer',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Tout cocher',
	'bouton_tout_decocher' => 'Tout décocher',
	'bouton_verifier' => 'Vérifier',

// I
	'info_choisir_pluginxml' => 'Choisissez les fichiers que vous souhaitez convertir parmi ceux présents dans le dossier <code>plugins/</code> de ce site.',
	'info_forcer_paquetxml' => 'Par défaut, le fichier paquet.xml n\'est écrit que si son contenu est valide selon la nouvelle DTD. Vous pouvez cependant forcer son écriture quel que soit le résultat de la validation.',
	'info_generer' => 'Cette option vous permet de générer le nouveau fichier paquet.xml de description d\'un plugin à partir du fichier plugin.xml existant.<br />Outre le fichier paquet.xml, les fichiers de langue des items slogan et description du plugin ainsi qu\'un fichier de commandes Unix sont créés dans des dossiers propres à chaque plugin.',
	'info_simuler_paquetxml' => 'Par défaut, les fichiers résultat sont créés dans le dossier d\'installation de chaque plugin. Vous pouvez cependant choisir de les créer dans un dossier temporaire du site.',
	'info_verifier_pluginxml' => 'Choisissez le fichier plugin.xml que vous souhaitez vérifier.',
	'info_verifier' => 'Cette option vous permet de vérifier le fichier plugin.xml de description d\'un plugin afin d\'anticiper des problèmes lors de génération du fichier paquet.xml. Ce formulaire propose la liste des fichiers plugin.xml présents dans le dossier <code>plugins/</code> de ce site.',

// L
	'label_choisir_pluginxml' => 'plugin.xml disponibles',
	'label_forcer_non' => 'Non, respecter les résultats de la validation',
	'label_forcer_oui' => 'Oui, forcer l\'écriture',
	'label_generer_paquetxml' => 'Fichiers résultat',
	'label_simuler_non' => 'Non, écrire dans le dossier plugins/ du site',
	'label_simuler_oui' => 'Oui, écrire dans le dossier temporaire tmp/plugonet/',

// M
	'message_generation_paquetxml' => '@nb_fichiers@ plugin.xml traité(s) : @details@',
	'message_nok_aucun_pluginxml' => 'Aucun fichier plugin.xml trouvé dans le dossier des plugins de ce site.',
	'message_nok_information_pluginxml' => '@nb_fichiers@ plugin.xml dont le contenu XML est illisible',
	'message_nok_lecture_pluginxml' => '@nb_fichiers@ plugin.xml inaccessible(s) en lecture',
	'message_nok_validation_paquetxml' => '@nb_fichiers@ paquet.xml non conforme(s) à la nouvelle DTD',
	'message_nok_validation_pluginxml' => '@nb_fichiers@ plugin.xml non conforme(s) : vérifiez le(s) paquet.xml correspondant(s) !',
	'message_ok_generation_paquetxml' => '@nb_fichiers@ paquet.xml correctement généré(s)',

// O
	'onglet_generer' => 'Générer paquet.xml',
	'onglet_verifier' => 'Vérifier plugin.xml',
	'option_aucun_pluginxml' => 'aucun plugin.xml sélectionné',

// T
	'titre_form_generer' => 'Génération des fichiers paquet.xml',
	'titre_form_verifier' => 'Vérification des fichiers plugin.xml',
	'titre_page_navigateur' => 'PlugOnet',
	'titre_page' => 'PlugOnet',
);
?>