<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file
// Module: plugonet
// Langue: fr

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// B
	'bouton_lancer' => 'Lancer',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Tout cocher',
	'bouton_tout_decocher' => 'Tout décocher',

// D
	'details_generation_paquetxml_erreur_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-dessous n\'ont pas été générés car des erreurs se sont produites pendant la génération. Veuillez consulter les informations ci-dessous pour apporter les corrections nécessaires.',
	'details_generation_paquetxml_erreur' => 'Le paquet.xml du plugin listé ci-dessous n\'a pas été généré car des erreurs se sont produites pendant la génération. Veuillez consulter les informations ci-dessous pour apporter les corrections nécessaires.',
	'details_generation_paquetxml_notice_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-après ont été correctement générés mais leurs plugin.xml source contiennent des erreurs. Veuillez donc vérifier les plugin.xml et les fichiers résultant (paquet.xml, fichiers de langue) pour déterminer si des corrections doivent être apportées.',
	'details_generation_paquetxml_notice' => 'Le paquet.xml du plugin listé ci-après a été correctement généré mais son plugin.xml source contient des erreurs. Veuillez donc vérifier le plugin.xml et les fichiers résultant (paquet.xml, fichiers de langue) pour déterminer si des corrections doivent être apportées.',
	'details_generation_paquetxml_succes_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-après ont été correctement générés.',
	'details_generation_paquetxml_succes' => 'Le paquet.xml du plugin listé ci-après a été correctement généré.',
	'details_validation_paquetxml_erreur_pluriel' => 'La validation formelle des @nb@ plugin.xml listés ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections.',
	'details_validation_paquetxml_erreur' => 'La validation formelle du plugin.xml listé ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter les corrections.',
	'details_validation_paquetxml_succes_pluriel' => 'La validation formelle des @nb@ plugin.xml listés ci-après n\'a révélé aucune erreur.',
	'details_validation_paquetxml_succes' => 'La validation formelle du plugin.xml listé ci-après n\'a révélé aucune erreur.',
	'details_verification_pluginxml_erreur_pluriel' => 'La vérification des @nb@ plugin.xml listés ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections si besoin (toutes les erreurs liées à l\'utilisation de balise a, code, br... dans la description ne sont pas à considérer).',
	'details_verification_pluginxml_erreur' => 'La vérification du plugin.xml listé ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections si besoin (toutes les erreurs liées à l\'utilisation de balise a, code, br... dans la description ne sont pas à considérer).',
	'details_verification_pluginxml_succes_pluriel' => 'La vérification des @nb@ plugin.xml listés ci-après n\'a révélé aucune erreur.',
	'details_verification_pluginxml_succes' => 'La vérification du plugin.xml listé ci-après n\'a révélé aucune erreur.',

// I
	'index_aide_paqxmlgen' => 'Le fichier de description d\'un plugin : <code>paquet.xml</code>',
	'index_aide_paqxmlpaquet' => 'La balise <code>paquet</code>',
	'index_aide_paqxmlnom' => 'La balise <code>nom</code>',
	'index_aide_paqxmldesc' => 'Le slogan et la description',
	'index_aide_paqxmlaut' => 'La balise <code>auteur</code>',
	'index_aide_paqxmlcred' => 'La balise <code>credit</code>',
	'index_aide_paqxmlcopy' => 'La balise <code>copyright</code>',
	'index_aide_paqxmllic' => 'La balise <code>licence</code>',
	'index_aide_paqxmltrad' => 'La balise <code>traduire</code>',
	'index_aide_paqxmlbout' => 'Les balises <code>menu</code> et <code>onglet</code>',
	'index_aide_paqxmlnec' => 'Les balises <code>necessite</code> et <code>utilise</code>',
	'index_aide_paqxmlpipe' => 'La balise <code>pipeline</code>',
	'index_aide_paqxmllib' => 'La balise <code>lib</code>',
	'index_aide_paqxmlproc' => 'La balise <code>procure</code>',
	'index_aide_paqxmlpath' => 'La balise <code>chemin</code>',
	'index_aide_paqxmlspip' => 'La balise <code>spip</code>',
	'index_aide_paqxmlfoi' => 'Les fonctions, options et initialisations',
	'index_aide_paqxmldtd' => 'Expression formelle de la DTD',
	'info_choisir_paquetxml_valider' => 'Choisissez les fichiers paquet.xml que vous souhaitez valider. Vous pouvez aussi cliquer sur le nom d\'un paquet.xml pour lancer directement sa validation formelle.',
	'info_choisir_pluginxml_generer' => 'Choisissez les fichiers que vous souhaitez convertir parmi ceux présents dans le dossier <code>plugins/</code> de ce site. Vous pouvez aussi cliquer sur le nom d\'un plugin.xml pour lancer directement la génération forcée de son paquet.xml dans le dossier temporaire du site.',
	'info_choisir_pluginxml_verifier' => 'Choisissez les fichiers plugin.xml que vous souhaitez vérifier. Vous pouvez aussi cliquer sur le nom d\'un plugin.xml pour lancer directement sa vérification.',
	'info_forcer_paquetxml' => 'Par défaut, le fichier paquet.xml n\'est écrit que si son contenu est valide selon la nouvelle DTD. Vous pouvez cependant forcer son écriture quel que soit le résultat de la validation.',
	'info_generer' => 'Cette option vous permet de générer le nouveau fichier paquet.xml de description d\'un plugin à partir du fichier plugin.xml existant.<br />Outre le fichier paquet.xml, les fichiers de langue des items slogan et description du plugin ainsi qu\'un fichier de commandes Unix sont créés dans des dossiers propres à chaque plugin.',
	'info_simuler_paquetxml' => 'Par défaut, les fichiers résultat sont créés dans le dossier d\'installation de chaque plugin. Vous pouvez cependant choisir de les créer dans un dossier temporaire du site.',
	'info_valider' => 'Cette option vous permet de valider formellement le fichier paquet.xml de description d\'un plugin selon sa DTD. Ce formulaire propose la liste des fichiers paquet.xml présents dans tous les dossiers de ce site.',
	'info_verifier' => 'Cette option vous permet de vérifier le fichier plugin.xml de description d\'un plugin afin d\'anticiper des problèmes lors de génération du fichier paquet.xml. Ce formulaire propose la liste des fichiers plugin.xml présents dans tous les dossiers de ce site.',

// L
	'label_choisir_xml' => '@dtd@.xml disponibles',
	'label_forcer_non' => 'Non, respecter les résultats de la validation',
	'label_forcer_oui' => 'Oui, forcer l\'écriture',
	'label_generer_paquetxml' => 'Fichiers résultat',
	'label_simuler_non' => 'Non, écrire dans le dossier plugins/ du site',
	'label_simuler_oui' => 'Oui, écrire dans le dossier temporaire tmp/plugonet/',
	'legende_resultats' => 'Résultats détaillés par plugin',

// M
	'message_nok_aucun_xml' => 'Aucun @dtd@.xml trouvé dans les dossiers des plugins de ce site.',
	'message_nok_information_pluginxml_pluriel' => '@nb@ plugin.xml illisibles',
	'message_nok_information_pluginxml' => '@nb@ plugin.xml illisible',
	'message_nok_lecture_pluginxml_pluriel' => '@nb@ plugin.xml inaccessibles en lecture',
	'message_nok_lecture_pluginxml' => '@nb@ plugin.xml inaccessible en lecture',
	'message_nok_validation_paquetxml_pluriel' => '@nb@ paquet.xml non conformes à la DTD',
	'message_nok_validation_paquetxml_pluriel' => '@nb@ paquet.xml non conformes à la DTD',
	'message_nok_validation_paquetxml' => '@nb@ paquet.xml non conforme à la DTD',
	'message_nok_validation_paquetxml' => '@nb@ paquet.xml non conforme à la DTD',
	'message_nok_validation_pluginxml_pluriel' => '@nb@ plugin.xml non conformes à la DTD',
	'message_nok_validation_pluginxml' => '@nb@ plugin.xml non conforme à la DTD',
	'message_notice_validation_pluginxml_pluriel' => 'dont @nb@ sont issus de plugin.xml non conformes',
	'message_notice_validation_pluginxml' => 'dont @nb@ est issu d\'un plugin.xml non conforme',
	'message_ok_generation_paquetxml_pluriel' => '@nb@ paquet.xml correctement générés',
	'message_ok_generation_paquetxml' => '@nb@ paquet.xml correctement généré',
	'message_ok_validation_paquetxml_pluriel' => '@nb@ paquet.xml corrects',
	'message_ok_validation_paquetxml' => '@nb@ paquet.xml correct',
	'message_ok_verification_pluginxml_pluriel' => '@nb@ plugin.xml corrects',
	'message_ok_verification_pluginxml' => '@nb@ plugin.xml correct',

// O
	'onglet_generer' => 'Générer paquet.xml',
	'onglet_valider' => 'Valider paquet.xml',
	'onglet_verifier' => 'Vérifier plugin.xml',

// R
	'resume_generation_paquetxml_pluriel' => '@nb@ plugin.xml traités (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.',
	'resume_generation_paquetxml' => '@nb@ plugin.xml traité (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.',
	'resume_validation_paquetxml_pluriel' => '@nb@ paquet.xml validés (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.',
	'resume_validation_paquetxml' => '@nb@ paquet.xml validé (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.',
	'resume_verification_pluginxml_pluriel' => '@nb@ plugin.xml vérifiés (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.',
	'resume_verification_pluginxml' => '@nb@ plugin.xml vérifié (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.',

// T
	'titre_boite_aide_paquetxml' => 'Aide sur paquet.xml',
	'titre_form_generer' => 'Génération des fichiers paquet.xml',
	'titre_form_valider' => 'Validation formelle des fichiers paquet.xml',
	'titre_form_verifier' => 'Vérification des fichiers plugin.xml',
	'titre_page_navigateur' => 'PlugOnet',
	'titre_page' => 'PlugOnet',
);
?>