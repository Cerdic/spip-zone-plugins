<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/plugonet?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_lancer' => 'Launch',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Check all',
	'bouton_tout_decocher' => 'Uncheck all',

	// D
	'details_generation_paquetxml_erreur' => 'The paquet.xml of the plugin listed below has not been generated because of errors occurred during generation. Please see the information below to make the necessary corrections.',
	'details_generation_paquetxml_erreur_pluriel' => 'The paquet.xml of the @nb@ plugins listed below were not generated because of errors occurred during generation. Please see the information below to make the necessary corrections.',
	'details_generation_paquetxml_notice' => 'Le paquet.xml du plugin listé ci-après a été correctement généré mais son plugin.xml source contient des erreurs. Veuillez donc vérifier le plugin.xml et les fichiers résultant (paquet.xml, fichiers de langue) pour déterminer si des corrections doivent être apportées.', # NEW
	'details_generation_paquetxml_notice_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-après ont été correctement générés mais leurs plugin.xml source contiennent des erreurs. Veuillez donc vérifier les plugin.xml et les fichiers résultant (paquet.xml, fichiers de langue) pour déterminer si des corrections doivent être apportées.', # NEW
	'details_generation_paquetxml_succes' => 'The paquet.xml of the plugin listed below has been generated correctly.',
	'details_generation_paquetxml_succes_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-après ont été correctement générés.', # NEW
	'details_validation_paquetxml_erreur' => 'La validation formelle du plugin.xml listé ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter les corrections.', # NEW
	'details_validation_paquetxml_erreur_pluriel' => 'La validation formelle des @nb@ plugin.xml listés ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections.', # NEW
	'details_validation_paquetxml_succes' => 'La validation formelle du plugin.xml listé ci-après n\'a révélé aucune erreur.', # NEW
	'details_validation_paquetxml_succes_pluriel' => 'La validation formelle des @nb@ plugin.xml listés ci-après n\'a révélé aucune erreur.', # NEW
	'details_verification_pluginxml_erreur' => 'La vérification du plugin.xml listé ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections si besoin (toutes les erreurs liées à l\'utilisation de balise a, code, br... dans la description ne sont pas à considérer).', # NEW
	'details_verification_pluginxml_erreur_pluriel' => 'La vérification des @nb@ plugin.xml listés ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections si besoin (toutes les erreurs liées à l\'utilisation de balise a, code, br... dans la description ne sont pas à considérer).', # NEW
	'details_verification_pluginxml_succes' => 'The verification of the plugin.xml listed below revealed no mistake.',
	'details_verification_pluginxml_succes_pluriel' => 'The verification of the @nb@ plugin.xml listed below revealed no mistake.',

	// I
	'index_aide_paqxmlaut' => 'The <code>auteur</code> tag',
	'index_aide_paqxmlbout' => 'The <code>menu</code> and <code>onglet</code> tags',
	'index_aide_paqxmlcopy' => 'The <code>copyright</code> tag',
	'index_aide_paqxmlcred' => 'The <code>credit</code> tag',
	'index_aide_paqxmldesc' => 'The slogan and the description',
	'index_aide_paqxmlexe' => 'paquet.xml examples',
	'index_aide_paqxmlfoi' => 'Functions, options and administrations',
	'index_aide_paqxmlgen' => 'The description file of a plugin: <code>paquet.xml</code>',
	'index_aide_paqxmllib' => 'The <code>lib</code> tag',
	'index_aide_paqxmllic' => 'The <code>licence</code> tag',
	'index_aide_paqxmlnec' => 'The <code>necessite</code> and <code>utilise</code> tags',
	'index_aide_paqxmlnom' => 'The <code>nom</code> tag',
	'index_aide_paqxmlpaquet' => 'The <code>paquet</code> tag',
	'index_aide_paqxmlpath' => 'The <code>chemin</code> tag',
	'index_aide_paqxmlpipe' => 'The <code>pipeline</code> tag',
	'index_aide_paqxmlproc' => 'The <code>procure</code> tag',
	'index_aide_paqxmlspip' => 'The <code>spip</code> tag',
	'index_aide_paqxmltrad' => 'The <code>traduire</code> tag',
	'info_choisir_paquetxml_valider' => 'Choisissez les fichiers paquet.xml que vous souhaitez valider. Vous pouvez aussi cliquer sur le nom d\'un paquet.xml pour lancer directement sa validation formelle.', # NEW
	'info_choisir_pluginxml_generer' => 'Choisissez les fichiers que vous souhaitez convertir parmi ceux présents dans le dossier <code>plugins/</code> de ce site. Vous pouvez aussi cliquer sur le nom d\'un plugin.xml pour lancer directement la génération forcée de son paquet.xml dans le dossier temporaire du site.', # NEW
	'info_choisir_pluginxml_verifier' => 'Choisissez les fichiers plugin.xml que vous souhaitez vérifier. Vous pouvez aussi cliquer sur le nom d\'un plugin.xml pour lancer directement sa vérification.', # NEW
	'info_forcer_paquetxml' => 'Par défaut, le fichier paquet.xml n\'est écrit que si son contenu est valide selon la nouvelle DTD. Vous pouvez cependant forcer son écriture quel que soit le résultat de la validation.', # NEW
	'info_generer' => 'Cette option vous permet de générer le nouveau fichier paquet.xml de description d\'un plugin à partir du fichier plugin.xml existant.<br />Outre le fichier paquet.xml, les fichiers de langue des items slogan et description du plugin ainsi qu\'un fichier de commandes Unix sont créés dans des dossiers propres à chaque plugin.', # NEW
	'info_simuler_paquetxml' => 'Par défaut, les fichiers résultat sont créés dans le dossier d\'installation de chaque plugin. Vous pouvez cependant choisir de les créer dans un dossier temporaire du site.', # NEW
	'info_valider' => 'Cette option vous permet de valider formellement le fichier paquet.xml de description d\'un plugin selon sa DTD. Ce formulaire propose la liste des fichiers paquet.xml présents dans tous les dossiers de ce site.', # NEW
	'info_verifier' => 'Cette option vous permet de vérifier le fichier plugin.xml de description d\'un plugin afin d\'anticiper des problèmes lors de génération du fichier paquet.xml. Ce formulaire propose la liste des fichiers plugin.xml présents dans tous les dossiers de ce site.', # NEW

	// L
	'label_choisir_xml' => '@dtd@.xml available',
	'label_forcer_non' => 'No, respect the validation results',
	'label_forcer_oui' => 'Yes, force writing',
	'label_generer_paquetxml' => 'Result files',
	'label_simuler_non' => 'No, write in the plugins/ folder of the site',
	'label_simuler_oui' => 'Yes, write in the temporary folder tmp/plugonet/',
	'legende_resultats' => 'Detailed results per plugin',

	// M
	'message_nok_aucun_xml' => 'No @dtd@.xml found in the folders of the plugins of this site.',
	'message_nok_information_pluginxml' => '@nb@ unreadable plugin.xml',
	'message_nok_information_pluginxml_pluriel' => '@nb@ unreadable plugin.xml',
	'message_nok_lecture_pluginxml' => '@nb@ unreadable plugin.xml',
	'message_nok_lecture_pluginxml_pluriel' => '@nb@ unreadable plugin.xml',
	'message_nok_validation_paquetxml' => '@nb@ paquet.xml not conforming to the DTD',
	'message_nok_validation_paquetxml_pluriel' => '@nb@ paquet.xml not conforming to the DTD',
	'message_nok_validation_pluginxml' => '@nb@ plugin.xml not conforming to the DTD',
	'message_nok_validation_pluginxml_pluriel' => '@nb@ plugin.xml not conforming to the DTD',
	'message_notice_validation_pluginxml' => 'dont @nb@ est issu d\'un plugin.xml non conforme', # NEW
	'message_notice_validation_pluginxml_pluriel' => 'dont @nb@ sont issus de plugin.xml non conformes', # NEW
	'message_ok_generation_paquetxml' => '@nb@ paquet.xml generated correctly',
	'message_ok_generation_paquetxml_pluriel' => '@nb@ paquet.xml generated correctly',
	'message_ok_validation_paquetxml' => '@nb@ valid paquet.xml',
	'message_ok_validation_paquetxml_pluriel' => '@nb@ valid paquet.xml',
	'message_ok_verification_pluginxml' => '@nb@ valid plugin.xml',
	'message_ok_verification_pluginxml_pluriel' => '@nb@ valid plugin.xml',

	// O
	'onglet_generer' => 'Generate paquet.xml',
	'onglet_valider' => 'Validate paquet.xml',
	'onglet_verifier' => 'Check plugin.xml',

	// R
	'resume_generation_paquetxml' => '@nb@ plugin.xml traité (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_generation_paquetxml_pluriel' => '@nb@ plugin.xml traités (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_validation_paquetxml' => '@nb@ paquet.xml validé (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_validation_paquetxml_pluriel' => '@nb@ paquet.xml validés (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_verification_pluginxml' => '@nb@ plugin.xml vérifié (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_verification_pluginxml_pluriel' => '@nb@ plugin.xml vérifiés (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW

	// T
	'titre_boite_aide_paquetxml' => 'Help on paquet.xml',
	'titre_form_generer' => 'Génération des fichiers paquet.xml', # NEW
	'titre_form_valider' => 'Validation formelle des fichiers paquet.xml', # NEW
	'titre_form_verifier' => 'Verification of plugin.xml files',
	'titre_page' => 'PlugOnet',
	'titre_page_navigateur' => 'PlugOnet'
);

?>
