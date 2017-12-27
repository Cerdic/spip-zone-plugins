<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/plugonet?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_lancer' => 'Start',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Alles selecteren',
	'bouton_tout_decocher' => 'Niets selecteren',

	// D
	'details_generation_paquetxml_erreur' => 'De paquet.xml van de plugin werd niet aangemaakt vanwege fouten opgetreden tijdens het aanmaken. Bekijk onderstaande informatie om de nodige correcties te maken.',
	'details_generation_paquetxml_erreur_pluriel' => 'De paquet.xml of the @nb@ plugins werd niet aangemaakt vanwege fouten opgetreden tijdens het aanmaken. Bekijk onderstaande informatie om de nodige correcties te maken.',
	'details_generation_paquetxml_notice' => 'De paquet.xml van plugin werd met succes aangemaakt maar zijn bron plugin.xml bevat fouten. Bekijk de plugin.xml en de resulterende bestanden (paquet.xml, taalbestanden) om te controleren of correcties moeten worden toegepast.',
	'details_generation_paquetxml_notice_pluriel' => 'De paquet.xml of the @nb@ plugins werd werd met succes aangemaakt maar zijn plugin.xml bevat fouten. Bekijk de plugin.xml en de resulterende bestanden (paquet.xml, taalbestanden) om te controleren of correcties moeten worden toegepast.',
	'details_generation_paquetxml_succes' => 'De paquet.xml van de plugin werd correct aangemaakt.',
	'details_generation_paquetxml_succes_pluriel' => 'De paquet.xml van @nb@ plugins werd met succes aangemaakt.',
	'details_validation_paquetxml_erreur' => 'De formele validatie van plugin.xml constateerde fouten. Bekijk onderstaande informatie om correcties te maken.',
	'details_validation_paquetxml_erreur_pluriel' => 'De formele validatie van @nb@ plugin.xml bestanden constateerde fouten. Bekijk onderstaande informatie om correcties te maken.',
	'details_validation_paquetxml_succes' => 'De formele validation van de plugin.xml gaf geen fout.',
	'details_validation_paquetxml_succes_pluriel' => 'De formele validatie van de @nb@ plugin.xml bestanden gaf geen fout.',
	'details_verification_pluginxml_erreur' => 'De verificatie van de plugin.xml constateerde fouten. Bekijk onderstaande informatie om correcties te maken (alle fouten met betrekking tot a, code, br bakens... in de omschrijving moeten niet worden meegenomen).',
	'details_verification_pluginxml_erreur_pluriel' => 'De verificatie van de @nb@ plugin.xml bestanden constateerde fouten. Bekijk onderstaande informatie om correcties te maken (alle fouten met betrekking tot a, code, br bakens... in de omschrijving moeten niet worden meegenomen).',
	'details_verification_pluginxml_succes' => 'De verification van de plugin.xml gaf geen fouten.',
	'details_verification_pluginxml_succes_pluriel' => 'De verificatie van @nb@ plugin.xml bestanden gaf geen fouten.',

	// I
	'index_aide_paqxmlaut' => 'Het <code>auteur</code> baken',
	'index_aide_paqxmlbout' => 'De <code>menu</code> en <code>onglet</code> bakens',
	'index_aide_paqxmlcopy' => 'Het <code>copyright</code> baken',
	'index_aide_paqxmlcred' => 'Het <code>credit</code> baken',
	'index_aide_paqxmldesc' => 'De slogan en zijn omschrijving',
	'index_aide_paqxmlexe' => 'paquet.xml voorbeelden',
	'index_aide_paqxmlfoi' => 'Functies, opties en beheer',
	'index_aide_paqxmlgen' => 'Het bestand met de beschrijving van een plugin: <code>paquet.xml</code>',
	'index_aide_paqxmlgenie' => 'Het baken <code>genie</code>',
	'index_aide_paqxmllib' => 'Het <code>lib</code> baken',
	'index_aide_paqxmllic' => 'Het <code>licence</code> baken',
	'index_aide_paqxmlnec' => 'De <code>necessite</code> en <code>utilise</code> bakens',
	'index_aide_paqxmlnom' => 'Het <code>nom</code> baken',
	'index_aide_paqxmlpaquet' => 'Het <code>paquet</code> baken',
	'index_aide_paqxmlpath' => 'Het <code>chemin</code> baken',
	'index_aide_paqxmlpipe' => 'Het <code>pipeline</code> baken',
	'index_aide_paqxmlproc' => 'Het <code>procure</code> baken',
	'index_aide_paqxmlscript' => 'De <code>script</code> en <code>style</code> bakens',
	'index_aide_paqxmlspip' => 'Het <code>spip</code> baken',
	'index_aide_paqxmltrad' => 'Het <code>traduire</code> baken',
	'info_choisir_paquetxml_valider' => 'Kies de paquet.xml bestanden die je wilt valideren. Je kunt ook op de naam van een paquet.xml klikken om de validatie te starten.',
	'info_choisir_pluginxml_generer' => 'Kies de te converteren bestanden uit deze in de <code>plugins/</code> map van deze site. Je kunt ook op de naam van een plugin.xml klikken om het aanmaken van de paquet.xml in de map met tijdelijke bestanden te forceren.',
	'info_choisir_pluginxml_verifier' => 'Kies de plugin.xml bestanden die je wilt controleren. Je kunt ook op de naam van een plugin.xml klikken om de controle direct te starten.',
	'info_forcer_paquetxml' => 'Standaard wordt de paquet.xml alleen geschreven wanneer de inhoud geldig is volgens de nieuwe DTD. Je kunt het schrijven forceren ongeacht het resultaat.',
	'info_generer' => 'Met deze optie maak je het nieuwe paquet.xml bestand van een plugin uit de bestaande plugin.xml.<br />Daarnaast worden het taalbestand van slogan en omschrijving en Unix opdrachten aangemaakt in de betreffende mappen van iedere plugin.',
	'info_paquet_existant' => 'De paquet.xml bestaat al',
	'info_simuler_paquetxml' => 'Standaard worden de resulterende bestanden aangemaalt in de installatiemap van iedere plugin. Je kunt ervoor kiezen ze in de map met tijdelijke bestanden van de site te plaatsen.',
	'info_valider' => 'Met deze optie doe je een formele validatie van het paquet.xml bestand van een plugin volgens zijn DTD. Dit formulier biedt een lijst van alle op deze site aanwezige paquet.xml bestanden.',
	'info_verifier' => 'Met deze optie doe je een controle van het plugin.xml bestand van een plugin om problemen bij het aanmaken van het paquet.xml bestand te voorkomen. Dit formulier biedt een lijst van alle op deze site aanwezige plugin.xml bestanden.',

	// L
	'label_choisir_xml' => '@dtd@.xml beschikbaar',
	'label_forcer_non' => 'Nee, respecteer de resultaten van de validatie',
	'label_forcer_oui' => 'Ja, forceer het wegschrijven',
	'label_generer_paquetxml' => 'Resultaatbestanden',
	'label_simuler_non' => 'Nee, schrijf naarde plugins/ map van de site',
	'label_simuler_oui' => 'Ja, schrijf naar de map met tijdelijke bestanden tmp/plugonet/',
	'legende_resultats' => 'Gedetaileerde resultaten per plugin',

	// M
	'message_nok_aucun_xml' => 'Geen @dtd@.xml gevonden in de mappen van de plugins op deze site.',
	'message_nok_information_pluginxml' => '@nb@ onleesbaar plugin.xml bestand',
	'message_nok_information_pluginxml_pluriel' => '@nb@ onleesbare plugin.xml bestanden',
	'message_nok_lecture_pluginxml' => '@nb@ onleesbare plugin.xml',
	'message_nok_lecture_pluginxml_pluriel' => '@nb@ onleesbare plugin.xml bestanden',
	'message_nok_validation_paquetxml' => '@nb@ paquet.xml niet conform de DTD',
	'message_nok_validation_paquetxml_pluriel' => '@nb@ paquet.xml bestanden niet conform de DTD',
	'message_nok_validation_pluginxml' => '@nb@ plugin.xml niet conform de DTD',
	'message_nok_validation_pluginxml_pluriel' => '@nb@ plugin.xml bestanden niet conform de DTD',
	'message_notice_validation_pluginxml' => 'waarvan @nb@ komt van een incorrecte plugin.xml',
	'message_notice_validation_pluginxml_pluriel' => 'waarvan @nb@ komen van een incorrecte plugin.xml',
	'message_ok_generation_paquetxml' => '@nb@ paquet.xml correct aangemaakt',
	'message_ok_generation_paquetxml_pluriel' => '@nb@ paquet.xml bestanden correct aangemaakt',
	'message_ok_validation_paquetxml' => '@nb@ geldige paquet.xml',
	'message_ok_validation_paquetxml_pluriel' => '@nb@ geldige paquet.xml bestanden',
	'message_ok_verification_pluginxml' => '@nb@ geldige plugin.xml',
	'message_ok_verification_pluginxml_pluriel' => '@nb@ geldige plugin.xml bestanden',

	// O
	'onglet_generer' => 'Maak paquet.xml',
	'onglet_valider' => 'Valideer paquet.xml',
	'onglet_verifier' => 'Controleer plugin.xml',

	// R
	'resume_generation_paquetxml' => '@nb@ plugin.xml verwerkt (@duree@s): @details@.<br />Bekijk hieronder de resultaten in detail.',
	'resume_generation_paquetxml_pluriel' => '@nb@ plugin.xml bestanden verwerkt (@duree@s): @details@.<br />Bekijk hieronder de resultaten in detail.',
	'resume_validation_paquetxml' => '@nb@ paquet.xml gevalideerd (@duree@s): @details@.<br />Bekijk hieronder de resultaten in detail.',
	'resume_validation_paquetxml_pluriel' => '@nb@ gevalideerde paquet.xml bestanden (@duree@s): @details@.<br />Bekijk hieronder de resultaten in detail.',
	'resume_verification_pluginxml' => '@nb@ plugin.xml gecontroleerd (@duree@s): @details@.<br />Bekijk hieronder de resultaten in detail.',
	'resume_verification_pluginxml_pluriel' => '@nb@ plugin.xml bestanden gecontroleerd (@duree@s): @details@.<br />Bekijk hieronder de resultaten in detail.',

	// T
	'titre_boite_aide_paquetxml' => 'Hulp voor paquet.xml',
	'titre_form_generer' => 'Aanmaken van paquet.xml bestanden',
	'titre_form_valider' => 'Formele validatie van paquet.xml bestanden',
	'titre_form_verifier' => 'Verificatie van plugin.xml bestanden',
	'titre_page' => 'PlugOnet',
	'titre_page_navigateur' => 'PlugOnet'
);
