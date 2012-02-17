<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/plugonet?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_lancer' => 'Spustiť',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Označiť všetko',
	'bouton_tout_decocher' => 'Odznačiť všetko',

	// D
	'details_generation_paquetxml_erreur' => 'Le paquet.xml du plugin listé ci-dessous n\'a pas été généré car des erreurs se sont produites pendant la génération. Veuillez consulter les informations ci-dessous pour apporter les corrections nécessaires.', # NEW
	'details_generation_paquetxml_erreur_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-dessous n\'ont pas été générés car des erreurs se sont produites pendant la génération. Veuillez consulter les informations ci-dessous pour apporter les corrections nécessaires.', # NEW
	'details_generation_paquetxml_notice' => 'Le paquet.xml du plugin listé ci-après a été correctement généré mais son plugin.xml source contient des erreurs. Veuillez donc vérifier le plugin.xml et les fichiers résultant (paquet.xml, fichiers de langue) pour déterminer si des corrections doivent être apportées.', # NEW
	'details_generation_paquetxml_notice_pluriel' => 'Les paquet.xml des @nb@ plugins listés ci-après ont été correctement générés mais leurs plugin.xml source contiennent des erreurs. Veuillez donc vérifier les plugin.xml et les fichiers résultant (paquet.xml, fichiers de langue) pour déterminer si des corrections doivent être apportées.', # NEW
	'details_generation_paquetxml_succes' => 'Súbor paquet.xml zásuvného modulu uvedeného nižšie bol úspešne vytvorený.',
	'details_generation_paquetxml_succes_pluriel' => 'Súbory paquet.xml @nb@ zásuvných modulov uvedených nižšie boli úspešne vytvorené.',
	'details_validation_paquetxml_erreur' => 'La validation formelle du plugin.xml listé ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter les corrections.', # NEW
	'details_validation_paquetxml_erreur_pluriel' => 'La validation formelle des @nb@ plugin.xml listés ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections.', # NEW
	'details_validation_paquetxml_succes' => 'Pri formálnom schvaľovaní súboru plugin.xml uvedeného nižšie sa nenašla žiadna chyba.',
	'details_validation_paquetxml_succes_pluriel' => 'Pri formálnom schvaľovaní @nb@ súborov plugin.xml uvedených nižšie sa nenašla žiadna chyba.',
	'details_verification_pluginxml_erreur' => 'La vérification du plugin.xml listé ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections si besoin (toutes les erreurs liées à l\'utilisation de balise a, code, br... dans la description ne sont pas à considérer).', # NEW
	'details_verification_pluginxml_erreur_pluriel' => 'La vérification des @nb@ plugin.xml listés ci-dessous a révélé des erreurs. Veuillez consulter les informations ci-dessous pour apporter des corrections si besoin (toutes les erreurs liées à l\'utilisation de balise a, code, br... dans la description ne sont pas à considérer).', # NEW
	'details_verification_pluginxml_succes' => 'Kontrola súboru plugin.xml uvedeného nižšie nenašla žiadnu chybu.',
	'details_verification_pluginxml_succes_pluriel' => 'Kontrola @nb@ súborov plugin.xml uvedených nižšie nenašla žiadnu chybu.',

	// I
	'index_aide_paqxmlaut' => 'Tag <code>auteur</code>',
	'index_aide_paqxmlbout' => 'Tagy <code>menu</code> a <code>onglet</code>',
	'index_aide_paqxmlcopy' => 'Tag <code>copyright</code>',
	'index_aide_paqxmlcred' => 'Tag <code>credit</code>',
	'index_aide_paqxmldesc' => 'Slogan a popis',
	'index_aide_paqxmlexe' => 'Príklady súboru paquet.xml',
	'index_aide_paqxmlfoi' => 'Funkcie, možnosti spravovania',
	'index_aide_paqxmlgen' => 'Súbor s popisom zásuvného modulu: <code>paquet.xml</code>',
	'index_aide_paqxmllib' => 'Tag <code>lib</code>',
	'index_aide_paqxmllic' => 'Tag <code>licence</code>',
	'index_aide_paqxmlnec' => 'Tagy <code>necessite</code> a <code>utilise</code>',
	'index_aide_paqxmlnom' => 'Tag <code>nom</code>',
	'index_aide_paqxmlpaquet' => 'Tag <code>paquet</code>',
	'index_aide_paqxmlpath' => 'Tag <code>chemin</code>',
	'index_aide_paqxmlpipe' => 'Tag <code>pipeline</code>',
	'index_aide_paqxmlproc' => 'Tag <code>procure</code>',
	'index_aide_paqxmlspip' => 'Tag <code>spip</code>',
	'index_aide_paqxmltrad' => 'Tag <code>traduire</code>',
	'info_choisir_paquetxml_valider' => 'Choisissez les fichiers paquet.xml que vous souhaitez valider. Vous pouvez aussi cliquer sur le nom d\'un paquet.xml pour lancer directement sa validation formelle.', # NEW
	'info_choisir_pluginxml_generer' => 'Choisissez les fichiers que vous souhaitez convertir parmi ceux présents dans le dossier <code>plugins/</code> de ce site. Vous pouvez aussi cliquer sur le nom d\'un plugin.xml pour lancer directement la génération forcée de son paquet.xml dans le dossier temporaire du site.', # NEW
	'info_choisir_pluginxml_verifier' => 'Choisissez les fichiers plugin.xml que vous souhaitez vérifier. Vous pouvez aussi cliquer sur le nom d\'un plugin.xml pour lancer directement sa vérification.', # NEW
	'info_forcer_paquetxml' => 'Par défaut, le fichier paquet.xml n\'est écrit que si son contenu est valide selon la nouvelle DTD. Vous pouvez cependant forcer son écriture quel que soit le résultat de la validation.', # NEW
	'info_generer' => 'Cette option vous permet de générer le nouveau fichier paquet.xml de description d\'un plugin à partir du fichier plugin.xml existant.<br />Outre le fichier paquet.xml, les fichiers de langue des items slogan et description du plugin ainsi qu\'un fichier de commandes Unix sont créés dans des dossiers propres à chaque plugin.', # NEW
	'info_simuler_paquetxml' => 'Par défaut, les fichiers résultat sont créés dans le dossier d\'installation de chaque plugin. Vous pouvez cependant choisir de les créer dans un dossier temporaire du site.', # NEW
	'info_valider' => 'Cette option vous permet de valider formellement le fichier paquet.xml de description d\'un plugin selon sa DTD. Ce formulaire propose la liste des fichiers paquet.xml présents dans tous les dossiers de ce site.', # NEW
	'info_verifier' => 'Cette option vous permet de vérifier le fichier plugin.xml de description d\'un plugin afin d\'anticiper des problèmes lors de génération du fichier paquet.xml. Ce formulaire propose la liste des fichiers plugin.xml présents dans tous les dossiers de ce site.', # NEW

	// L
	'label_choisir_xml' => 'dostupných: @dtd@.xml',
	'label_forcer_non' => 'Nie, rešpektovať výsledky schvaľovania',
	'label_forcer_oui' => 'Áno, vynútiť si zápis',
	'label_generer_paquetxml' => 'Výsledné súbory',
	'label_simuler_non' => 'Nie, zapisovať do priečinka plugins/',
	'label_simuler_oui' => 'Áno, zapisovať do dočasného priečinka tmp/plugonet/',
	'legende_resultats' => 'Podrobné výsledky podľa zásuvných modulov',

	// M
	'message_nok_aucun_xml' => 'Aucun @dtd@.xml trouvé dans les dossiers des plugins de ce site.', # NEW
	'message_nok_information_pluginxml' => '@nb@ nečítateľný súbor plugin.xml',
	'message_nok_information_pluginxml_pluriel' => 'nečítateľných súborov plugin.xml: @nb@ ',
	'message_nok_lecture_pluginxml' => '@nb@ súbor plugin.xml neprístupný na čítanie',
	'message_nok_lecture_pluginxml_pluriel' => 'neprístupných súborov plugin.xml na čítanie: @nb@',
	'message_nok_validation_paquetxml' => '@nb@ súbor paquet.xml nevyhovuje DTD',
	'message_nok_validation_paquetxml_pluriel' => '@nb@ súborov paquet.xml nevyhovuje DTD',
	'message_nok_validation_pluginxml' => '@nb@ súbor plugin.xml nevyhovuje DTD',
	'message_nok_validation_pluginxml_pluriel' => '@nb@ súborov plugin.xml nevyhovuje DTD',
	'message_notice_validation_pluginxml' => 'dont @nb@ est issu d\'un plugin.xml non conforme', # NEW
	'message_notice_validation_pluginxml_pluriel' => 'dont @nb@ sont issus de plugin.xml non conformes', # NEW
	'message_ok_generation_paquetxml' => '@nb@ úspešne vytvorený súbor paquet.xml',
	'message_ok_generation_paquetxml_pluriel' => 'úspešne vytvorených súborov paquet.xml: @nb@',
	'message_ok_validation_paquetxml' => '@nb@ správny súbor paquet.xml',
	'message_ok_validation_paquetxml_pluriel' => 'správnych súborov paquet.xml: @nb@',
	'message_ok_verification_pluginxml' => '@nb@ správny súbor plugin.xml',
	'message_ok_verification_pluginxml_pluriel' => 'správnych súborov plugin.xml @nb@',

	// O
	'onglet_generer' => 'Vytvoriť paquet.xml',
	'onglet_valider' => 'Potvrdiť paquet.xml',
	'onglet_verifier' => 'Overiť plugin.xml',

	// R
	'resume_generation_paquetxml' => '@nb@ plugin.xml traité (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_generation_paquetxml_pluriel' => '@nb@ plugin.xml traités (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_validation_paquetxml' => '@nb@ paquet.xml validé (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_validation_paquetxml_pluriel' => '@nb@ paquet.xml validés (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_verification_pluginxml' => '@nb@ plugin.xml vérifié (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW
	'resume_verification_pluginxml_pluriel' => '@nb@ plugin.xml vérifiés (@duree@s) : @details@.<br />Veuillez consulter les résultats détaillés ci-après.', # NEW

	// T
	'titre_boite_aide_paquetxml' => 'Pomocník k súboru paquet.xml',
	'titre_form_generer' => 'Vytvorenie súborov paquet.xml',
	'titre_form_valider' => 'Formálne potvrdenie súborov paquet.xml',
	'titre_form_verifier' => 'Overenie súborov plugin.xml',
	'titre_page' => 'PlugOnet',
	'titre_page_navigateur' => 'PlugOnet'
);

?>
