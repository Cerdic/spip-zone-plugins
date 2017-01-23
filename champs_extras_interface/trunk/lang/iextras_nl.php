<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/iextras?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_associer' => 'beheer dit veld',
	'action_associer_title' => 'Beheer de weergave van dit extra veld',
	'action_desassocier' => 'ontkoppelen',
	'action_desassocier_title' => 'Stop het beheer van de weergave van dit extra veld',
	'action_descendre' => 'omlaag',
	'action_descendre_title' => 'Verplaats het veld één positie omlaag',
	'action_modifier' => 'aanpassen',
	'action_modifier_title' => 'De parameters van dit extra veld aanpassen',
	'action_monter' => 'omhoog',
	'action_monter_title' => 'Verplaats het veld één positie omhoog',
	'action_supprimer' => 'verwijderen',
	'action_supprimer_title' => 'Verwijder dit veld volledig van de database',

	// B
	'bouton_importer' => 'Importeren',

	// C
	'caracteres_autorises_champ' => 'Mogelijke tekens: letters zonder accent, cijfers, - en _',
	'caracteres_interdits' => 'Sommige gebruikte tekens mogen niet worden gebruikt voor dit veld.',
	'champ_deja_existant' => 'Er bestaat al een veld met deze naam voor deze tabel.',
	'champ_sauvegarde' => 'Extra veld opgeslagen!',
	'champs_extras' => 'Extra Velden (Champs Extras)',
	'champs_extras_de' => 'Extra Velden van: @objet@',

	// E
	'erreur_action' => 'Onbekende actie @action@.',
	'erreur_enregistrement_champ' => 'Probleem bij het aanmaken van het extra veld.',
	'erreur_format_export' => 'Onbekend exportformaat @format@.',
	'erreur_nom_champ_mysql_keyword' => 'Deze veldnaam is door SQL gereserveerd en mag niet worden gebruikt.',
	'erreur_nom_champ_utilise' => 'Deze veldnaam wordt al door SPIP of een actieve plugin gebruikt.',
	'exporter_objet' => 'Exporteer alle extra velden van: @objet@',
	'exporter_objet_champ' => 'Exporteer de extra velden: @objet@ / @nom@',
	'exporter_tous' => 'Exporteer alle extra velden',
	'exporter_tous_explication' => 'Exporteer alle extra velden in YAML formaat voor het gebruik in het importformulier',
	'exporter_tous_php' => 'PHP export',
	'exporter_tous_php_explication' => 'Exporteer in PHP formaat voor hergebruik in een van Champs Extras Core afhankelijke plugin.',

	// I
	'icone_creer_champ_extra' => 'Maak een nieuw extra veld',
	'importer_explications' => 'Het importeren van extra velden in deze site vervolledigt
		alle reeds aanwezige extra velden met de nieuwe uit het importbestand.
		De nieuwe velden vormen een aanvulling op de reeds bestaande.',
	'importer_fichier' => 'Importbestand',
	'importer_fichier_explication' => 'Exportbestand in YAML formaat',
	'importer_fusionner' => 'Pas de reeds aanwezige velden aan',
	'importer_fusionner_explication' => 'Wanneer de te importeren extra velden al op de site bestaan,
		zal het importproces deze (standaard) negeren. Toch kun je aangeven dat alle veldinformatie
		uit dit bestand moet worden geïmporteerd.',
	'importer_fusionner_non' => 'Pas de reeds op de site aanwezige velden niet aan',
	'importer_fusionner_oui' => 'Pas de gemeenschappelijke extra velden bij de import aan',
	'info_description_champ_extra' => 'Op deze pagina kunnen extra velden worden beheerd, 
						dat wil zeggen: velden in aanvulling op de tabellen van de SPIP database,
						die worden weergegeven in de bewerkingsformulieren.',
	'info_description_champ_extra_creer' => 'Je kunt nieuwe velden aanmaken die vervolgens op deze pagina worden
						weergegeven in het kader «Lijst van editoriale objecten», alsmede in de formulieren.',
	'info_description_champ_extra_presents' => 'En als bepaalde extra velden al bestaan in je database,
						maar niet zijn gedeclareerd (door een plugin of skelet), kun je deze plugin vragen ze te beheren.
						Deze velden zullen dan verschijnen in een kader « Lijst van onbeheerde velden».',
	'info_modifier_champ_extra' => 'Pas dit extra veld aan',
	'info_nouveau_champ_extra' => 'Nieuw extra veld',
	'info_saisie' => 'Invoer:',

	// L
	'label_attention' => 'Zeer belangrijke uitleg',
	'label_champ' => 'Naam van het veld',
	'label_class' => 'CSS class',
	'label_conteneur_class' => 'CSS classes van de container',
	'label_datas' => 'Lijst van waardes',
	'label_explication' => 'Uitleg van de invoer',
	'label_label' => 'Label van de invoer',
	'label_obligatoire' => 'Verplicht veld?',
	'label_rechercher' => 'Zoeken',
	'label_rechercher_ponderation' => 'Weging van de zoekopdracht',
	'label_restrictions_auteur' => 'Per auteur',
	'label_restrictions_branches' => 'Per tak',
	'label_restrictions_groupes' => 'Per groep',
	'label_restrictions_secteurs' => 'Per sector',
	'label_saisie' => 'Type invoer',
	'label_sql' => 'SQL definitie',
	'label_table' => 'Object',
	'label_traitements' => 'Automatische verwerking',
	'label_versionner' => 'Versiebeheer van de veldinhoud',
	'legend_declaration' => 'Declaratie',
	'legend_options_saisies' => 'Invoeropties',
	'legend_options_techniques' => 'Technische opties',
	'legend_restriction' => 'Beperkingen',
	'legend_restrictions_modifier' => 'Pas de invoer aan',
	'legend_restrictions_voir' => 'Bekijk het invoerveld',
	'liste_des_extras' => 'Lijst van extra velden',
	'liste_des_extras_possibles' => 'Lijst van onbeheerde velden',
	'liste_objets_applicables' => 'Lijst van editoriale objecten',

	// N
	'nb_element' => '1 element',
	'nb_elements' => '@nb@ elementen',

	// P
	'precisions_pour_attention' => 'Om iets zeer belangrijks aan te geven.
		Met voorbehoud gebruiken!
		Mag een taalstring bevatten «plugin:string».',
	'precisions_pour_class' => 'Voeg CSS classes aan het element toe,
		gescheiden door een spatie. Voorbeeld: "inserer_barre_edition" voor een blok
		in plugin Porte Plume',
	'precisions_pour_conteneur_class' => 'Voeg CSS classes aan de container toe,
		gescheiden door een spatie. Voorbeeld: "pleine_largeur" voor de volledige breedte van het formulier',
	'precisions_pour_datas' => 'Bepaalde types velden hebben een lijst van toegelaten waardes nodig: geef ze regel voor regel aan, gevolgd door een komma en omschrijving. Een lege regel voor de standaardwaarde. De omschrijving mag een taalstring zijn.',
	'precisions_pour_explication' => 'Je kunt meer informatie over de invoer geven. 
		Dit mag een taalstring zijn «plugin:string».',
	'precisions_pour_label' => 'Dit mag een taalstring zijn «plugin:string».',
	'precisions_pour_nouvelle_saisie' => 'Maakt de aanpassing van het gebruikte type invoer voor dit veld mogelijk',
	'precisions_pour_nouvelle_saisie_attention' => 'Let wel op: door een verandering van type gaan de niet gemeenschappelijke delen van de huidige configuratie verloren!',
	'precisions_pour_rechercher' => 'Dit veld aan de zoekmachine toevoegen?',
	'precisions_pour_rechercher_ponderation' => 'SPIP weegt een zoekopdracht naar gewicht per kolom.
		Bepaalde kolommen wegen zwaarder (bijvoorbeeld de titel) dan andere.
		De standaard waarde voor extra velden is 2. Om een indruk te geven: SPIP gebruikt 8 voor de titel en 1 voor de tekst.',
	'precisions_pour_restrictions_branches' => 'Identificatie van te beperken rubrieken (gescheiden door «:»)',
	'precisions_pour_restrictions_groupes' => 'Identificatie van te beperken groepen (gescheiden door «:»)',
	'precisions_pour_restrictions_secteurs' => 'Identificatie van te beperken hoofdrubrieken (gescheiden door «:»)',
	'precisions_pour_saisie' => 'Geef een invoerveld weer van het type:',
	'precisions_pour_traitements' => 'Pas automatisch een behandeling toe voor het resulterende baken #NAAM_VAN_HET_VELD:',
	'precisions_pour_versionner' => 'Versiebeheer is uitsluitend van toepassing wanneer de plugin
		«révisions» actief is en het editoriale veld zelf onder het versiebeheer valt',

	// R
	'radio_restrictions_auteur_admin' => 'Uitsluitend beheerders (ook beperkte)',
	'radio_restrictions_auteur_admin_complet' => 'Alleen volledige beheerders',
	'radio_restrictions_auteur_aucune' => 'Iedereen',
	'radio_restrictions_auteur_webmestre' => 'Alleen webmasters',
	'radio_traitements_aucun' => 'Niemand',
	'radio_traitements_raccourcis' => 'Behandeling van SPIP snelkoppelingen (propre)',
	'radio_traitements_typo' => 'Uitsluitend typografische behandeling (typo)',

	// S
	'saisies_champs_extras' => 'Van «Champs Extras»',
	'saisies_saisies' => 'Van «Saisies»',
	'supprimer_reelement' => 'Dit veld verwijderen?',

	// T
	'titre_iextras' => 'Extra Velden (Champs Extras)',
	'titre_iextras_exporter' => 'Extra velden exporteren',
	'titre_iextras_exporter_importer' => 'Extra velden exporteren of importeren',
	'titre_iextras_importer' => 'Extra velden exporteren',
	'titre_page_iextras' => 'Extra Velden (Champs Extras)',

	// V
	'veuillez_renseigner_ce_champ' => 'Controleer dit veld!'
);
