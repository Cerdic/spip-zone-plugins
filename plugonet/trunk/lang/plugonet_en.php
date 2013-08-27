<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/plugonet?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_lancer' => 'Launch',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Check all',
	'bouton_tout_decocher' => 'Uncheck all',

	// D
	'details_generation_paquetxml_erreur' => 'The paquet.xml of the plugin has not been generated because of errors occurred during generation. Please see the information below to make the necessary corrections.',
	'details_generation_paquetxml_erreur_pluriel' => 'The paquet.xml of the @nb@ plugins were not generated because of errors occurred during generation. Please see the information below to make the necessary corrections.',
	'details_generation_paquetxml_notice' => 'The paquet.xml of the plugin has been successfully created but its source plugin.xml contains errors. Please check the plugin.xml and the resulting files (paquet.xml, language files) to determine if corrections are necessary.',
	'details_generation_paquetxml_notice_pluriel' => 'The paquet.xml of the @nb@ plugins were successfully created but their plugin.xml source contain errors. Please check the plugin.xml and the resulting files (paquet.xml, language files) to determine if corrections are necessary.',
	'details_generation_paquetxml_succes' => 'The paquet.xml of the plugin has been generated correctly.',
	'details_generation_paquetxml_succes_pluriel' => 'The paquet.xml of the @nb@ plugins were successfully created.',
	'details_validation_paquetxml_erreur' => 'The formal validation of the plugin.xml revealed errors. Please see the information below to make corrections.',
	'details_validation_paquetxml_erreur_pluriel' => 'The formal validation of the @nb@ plugin.xml revealed errors. Please see the information below to make corrections.',
	'details_validation_paquetxml_succes' => 'The formal validation of the plugin.xml showed no error.',
	'details_validation_paquetxml_succes_pluriel' => 'The formal validation of the @nb@ plugin.xml showed no error.',
	'details_verification_pluginxml_erreur' => 'The verification of the plugin.xml revealed errors. Please see the information below to make corrections if necessary (all errors related to the use of a, code, br tags... in the description are not to be considered).',
	'details_verification_pluginxml_erreur_pluriel' => 'The verification of the @nb@ plugin.xml revealed errors. Please see the information below to make corrections if necessary (all errors related to the use of a, code, br tags... in the description are not to be considered).',
	'details_verification_pluginxml_succes' => 'The verification of the plugin.xml revealed no mistake.',
	'details_verification_pluginxml_succes_pluriel' => 'The verification of the @nb@ plugin.xml revealed no mistake.',

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
	'info_choisir_paquetxml_valider' => 'Choose the paquet.xml files you want to validate. You can also click on the name of a paquet.xml to directly launch its formal validation.',
	'info_choisir_pluginxml_generer' => 'Choose the files you want to convert from those present in the <code>plugins/</code> directory of this site. You can also click on the name of a plugin.xml to launch directly the forced generation of its paquet.xml in the temporary directory of the site.',
	'info_choisir_pluginxml_verifier' => 'Choose the plugin.xml files you want to check. You can also click on the name of a plugin.xml to launch directly its verification.',
	'info_forcer_paquetxml' => 'By default, the paquet.xml file is only written if its content is valid according to the new DTD. You can force its writing whatever the result of the validation.',
	'info_generer' => 'This option allows you to generate the new paquet.xml description file of a plugin from an existing plugin.xml.<br />In addition of the paquet.xml file, the language files of slogan and description items from the plugin and a file of Unix commands are created in the specific folders of each plugin.',
	'info_paquet_existant' => 'The paquet.xml already exists',
	'info_simuler_paquetxml' => 'By default, the result files are created in the installation folder of each plugin. You can however choose to create them in a temporary folder of the site.',
	'info_valider' => 'This option allows you to formally validate the paquet.xml file  description of a plugin according to its DTD. This form provides a list of paquet.xml files present in all files directories of this site.',
	'info_verifier' => 'This option allows you to check the plugin.xml description file of a plugin to anticipate problems during the file generation of the paquet.xml. This form provides a list of the plugin.xml files present in all directories of this site.',

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
	'message_notice_validation_pluginxml' => 'which @nb@ come from not compliant plugin.xml',
	'message_notice_validation_pluginxml_pluriel' => 'which @nb@ come from not compliant plugin.xml',
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
	'resume_generation_paquetxml' => '@nb@ plugin.xml processed (@duree@s): @details@.<br />Please refer to detailed results below.',
	'resume_generation_paquetxml_pluriel' => '@nb@ plugin.xml processed (@duree@s): @details@.<br />Please refer to detailed results below.',
	'resume_validation_paquetxml' => '@nb@ paquet.xml validated (@duree@s): @details@.<br />Please refer to detailed results below.',
	'resume_validation_paquetxml_pluriel' => '@nb@ validated paquet.xml (@duree@s): @details@.<br />Please refer to detailed results below.',
	'resume_verification_pluginxml' => '@nb@ plugin.xml verified (@duree@s): @details@.<br />Please refer to detailed results below.',
	'resume_verification_pluginxml_pluriel' => '@nb@ plugin.xml verified (@duree@s): @details@.<br />Please refer to detailed results below.',

	// T
	'titre_boite_aide_paquetxml' => 'Help on paquet.xml',
	'titre_form_generer' => 'Creation of paquet.xml files',
	'titre_form_valider' => 'Formal validation of paquet.xml files',
	'titre_form_verifier' => 'Verification of plugin.xml files',
	'titre_page' => 'PlugOnet',
	'titre_page_navigateur' => 'PlugOnet'
);

?>
