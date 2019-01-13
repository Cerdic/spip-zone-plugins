<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/noizetier?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_composition' => 'Composities activeren',
	'apercu' => 'Overzicht',
	'aucun_type_noisette' => 'Geen enkel type nootje geladen.',

	// B
	'bloc_sans_noisette' => 'Nootjes toevoegen door gebruik te maken van de knop "een nootje toevoegen", of door het type nootje naar een gewenste plek te schuiven.',
	'bulle_activer_composition' => 'Composities op inhoudstype « @type@ » activeren',
	'bulle_configurer_objet_noisettes' => 'De nootjes configureren die specifiek zijn voor deze inhoud',
	'bulle_configurer_page_noisettes' => 'De nootjes van de pagina configureren',
	'bulle_creer_composition' => 'Een virtuele compositie van pagina « @page@ » maken',
	'bulle_dupliquer_composition' => 'Een virtuele compositie maken, gekopieerd van compositie « @page@ »',
	'bulle_modifier_composition' => 'De compositie bewerken',
	'bulle_modifier_page' => 'De pagina aanpassen',

	// C
	'choisir_noisette' => 'Kies het nootje dat je wilt toevoegen:',
	'compositions_non_installe' => '<b>Plugin Composities:</b> deze plugin is niet op de site geïnstalleerd. Dat is niet vereist, maar wanneer hij is geactiveerd, kun je direct composities aanwijzen.',
	'configurer_ajax_noisette_label' => 'AJAX inclusie',
	'configurer_balise_noisette_label' => 'Omhulling van nootjes',
	'configurer_objets_noisettes_explication' => 'Bij dit soort inhoud mogen de nootjes <strong>per inhoud</strong> worden aangepast.',
	'configurer_objets_noisettes_label' => 'Aanpassing op inhoud toestaan voor:',
	'configurer_profondeur_max_label' => 'Diepte',
	'configurer_titre' => 'NoiZetier configureren', # MODIF
	'copie_de' => 'Kopie van @source@',

	// D
	'description_bloc_contenu' => 'Voornaamste inhoud van iedere bladzijde.',
	'description_bloc_extra' => 'Extra contextuele informatie voor iedere bladzijde.',
	'description_bloc_navigation' => 'Informatie ten behoeve van de navigatie op iedere bladzijde.',
	'description_bloctexte' => 'De titel is optioneel. In de tekst kun je de typografische afkortingen van SPIP gebruiken.',

	// E
	'editer_composition' => 'Deze compositie aanpassen',
	'editer_composition_heritages' => 'Het erfgoed bepalen',
	'editer_configurer_page' => 'De nootjes van deze bladzijde configureren',
	'editer_noizetier_explication' => 'Kies de bladzijde waarop je de nootjes wilt configureren.',
	'editer_noizetier_explication_objets' => 'Selecteer de inhoud waarvan je de nootjes wilt personaliseren.',
	'editer_noizetier_titre' => 'Nootjes beheren',
	'editer_nouvelle_page' => 'Een nieuwe bladzijde / compositie maken',
	'erreur_ajout_noisette' => 'De volgende nootjes werden niet toegevoegd: @noisettes@',
	'erreur_aucune_noisette_selectionnee' => 'Je moet een nootje kiezen!',
	'erreur_doit_choisir_noisette' => 'Je moet een nootje kiezen.',
	'erreur_mise_a_jour' => 'Er is een fout opgetreden bij het aanpassen van de database.',
	'erreur_page_inactive' => 'De pagina is inactief omdat de volgende plugins niet actief zijn: @plugins@.',
	'erreur_type_noisette_indisponible' => 'Het type nootje @type_noisette@ is niet beschikbaar omdat de plugin die hem aanlevert inactief is.',
	'explication_code' => 'LET OP: voor gevorderde gebruikers. Je kunt SPIP code (lussen en bakens) gebruiken die als in een skelet worden geïnterpreteerd. Het nootje zal zo teogang hebben tot alle omgevingsvariabelen van de bladzijde.',
	'explication_composition' => 'Compositie afgeleid van pagina « @type@ »',
	'explication_composition_virtuelle' => '<strong>Virtuele</strong> compositie, afgeleid van pagina « @type@ »',
	'explication_copie_noisette_parametres' => 'Kies de configuratieparameters van het bronnootje dat je wil kopiëren; anders worden de standaardwaardes gebruikt.',
	'explication_copie_pages_compatibles' => 'Kies de pagina’s waarin een nootje van hetzelfde type als het bronnootje moet worden gemaakt.',
	'explication_description_code' => 'Voor intern gebruik. Wordt niet op de publieke site weergegeven.',
	'explication_dupliquer_composition_reference' => 'De identificatie van de gedupliceerde bladzijde is <i>@composition@</i>.
	Je kunt een nieuwe identificatie kozen, of de bestaande van een suffix voorzien: <i>@composition@<strong>_suffix</strong></i>',
	'explication_dupliquer_composition_suffixer' => '.',
	'explication_glisser_deposer' => 'De types nootjes die aan de blokken van deze pagina kunnen worden toegevoegd staan hieronder.',
	'explication_heritages_composition' => 'De momenteel bewerkte compositie is gebaseerd op het inhoudstype « @type@ » dat onderliggende types heeft. Je kunt voor ieder onderliggend type een standaard toe te passen compositie aangeven.',
	'explication_noisette' => 'Nootje van type « @noisette@ »',
	'explication_noisette_css' => 'Je kunt een alles omhullend baken opnemen voor aanvullende CSS classes.',
	'explication_noizetier_ajax' => 'De standaard AJAX modus kan voor ieder nootje individueel worden aangepast (YAML-bestand).',
	'explication_noizetier_balise' => 'De standaard modus in een alles omhullend baken kan voor ieder individueel nootje worden aangepast (parameters).',
	'explication_noizetier_cfg_constant' => 'Deze waarde wordt momenteel door een constante bepaald en kan hier niet worden aangepast.',
	'explication_noizetier_profondeur_max' => 'Je kunt nootjes van het type container invoegen. Bepaal het gewenste maximum aantal niveaus.',
	'explication_objet' => 'Inhoudstype « @type@ »',
	'explication_page' => 'Autonome pagina, niet gekoppeld aan een inhoudstype',
	'explication_page_objet' => 'Pagina gekoppeld aan inhoudstype « @type@ »',
	'explication_raccourcis_typo' => 'Je kunt de typografische snelkoppelingen van SPIP gebruiken.',

	// F
	'formulaire_ajouter_noisette' => 'Een nootje toevoegen',
	'formulaire_ajouter_noisette_bloc' => 'Een nootje aan het blok toevoegen',
	'formulaire_ajouter_noisette_conteneur' => 'Een nootje aan de container toevoegen',
	'formulaire_blocs_exclus' => 'Uit te sluiten blokken',
	'formulaire_composition' => 'Identificatie van de compositie',
	'formulaire_composition_erreur' => 'Geen succesvolle query voor de compositie',
	'formulaire_composition_explication' => 'Geef een uniek trefwoord (kleine letters, zonder spatie, haakje of accent) om deze compositie te kenmerken.',
	'formulaire_composition_mise_a_jour' => 'Compositie aangepast',
	'formulaire_configurer_bloc' => 'Configureer het blok:',
	'formulaire_configurer_page' => 'Configureer de bladzijde:',
	'formulaire_creer_composition' => 'Een compositie maken',
	'formulaire_deplacer_bas' => 'Omlaag verplaatsen',
	'formulaire_deplacer_haut' => 'Omhoog verplaatsen',
	'formulaire_description' => 'Omschrijving',
	'formulaire_description_blocs_exclus' => 'Je kun ervoor kiezen bepaalde blokken uit te sluiten van de configuratie van nootjes.',
	'formulaire_description_explication' => 'Je kunt de SPIP snelkoppelingen gebruiken en in het bijzonder &lt;multi&gt;.',
	'formulaire_description_peuplement' => 'Je kunt de nieuwe virtuele compositie automatisch vullen met nootjes uit de bronpagina.',
	'formulaire_dupliquer_page' => 'Deze compositie dupliceren',
	'formulaire_dupliquer_page_entete' => 'Een bladzijde dupliceren',
	'formulaire_dupliquer_page_titre' => 'Bladzijde « @page@ » dupliceren',
	'formulaire_erreur_format_identifiant' => 'De identificatie mag alleen uit kleine letters zonder accent, cijfers of een _ (underscore) bestaan.',
	'formulaire_erreur_noisette_introuvable' => '@noisette@ is onvindbaar. Pas de naam aan of verwijder hem.',
	'formulaire_etendre_noisette' => 'In hetzelfde blok van de andere pagina’s kopiëren',
	'formulaire_icon' => 'Ikoon',
	'formulaire_icon_explication' => 'Je mag het relatieve pad naar het ikoon gebruiken (bijvoorbeeld: <i>images/object-lijst-inhoud.png</i>).',
	'formulaire_identifiant_deja_pris' => 'Deze identificatie is al in gebruik!',
	'formulaire_import_contenu' => 'Kies de te importeren elementen',
	'formulaire_import_contenu_compositions_virtuelles' => 'Virtuele composities',
	'formulaire_liste_compos_config' => 'Dit configuratiebestand bepaalt de volgende composities: @liste@.',
	'formulaire_liste_pages_config' => 'Dit configuratiebestand bepaalt de nootjes op de volgende bladzijden:  @liste@.',
	'formulaire_modifier_composition' => 'Deze compositie aanpassen',
	'formulaire_modifier_composition_heritages' => 'Geërfde composities',
	'formulaire_modifier_noisette' => 'Dit nootje aanpassen',
	'formulaire_modifier_page' => 'Deze bladzijde aanpassen',
	'formulaire_noisette_sans_parametre' => 'Dit nootje heeft geen te configureren parameters.',
	'formulaire_nom' => 'Titel',
	'formulaire_nom_explication' => 'Je kunt het baken &lt;multi&gt; gebruiken.',
	'formulaire_nouvelle_composition' => 'Nieuwe compositie',
	'formulaire_obligatoire' => 'Verplicht veld',
	'formulaire_peuplement' => 'De nootjes uit bronpagina « @page@ » kopiëren',
	'formulaire_supprimer_noisette' => 'Verwijder dit nootje',
	'formulaire_supprimer_noisettes_bloc' => 'De nootjes uit het blok verwijderen',
	'formulaire_supprimer_noisettes_noisette' => 'De nootjes uit de container verwijderen',
	'formulaire_supprimer_noisettes_page' => 'Verwijder alle nootjes',
	'formulaire_supprimer_page' => 'Verwijder deze compositie',
	'formulaire_type' => 'Type bladzijde',
	'formulaire_type_explication' => 'Inhoudstype dat de compositie erft.',
	'formulaire_type_import' => 'Type import',
	'formulaire_type_import_explication' => 'Je kunt het configuratiebestand samenvoegen met de huidige configuratie (de nootjes van iedere bladzijde worden aan de al gedefinieerde nootjes toegevoegd) of de huidige configuratie vervangen.',

	// I
	'icone_introuvable' => 'Ikoon niet te vinden!',
	'ieconfig_ne_pas_importer' => 'Niet importeren',
	'ieconfig_noizetier_export_explication' => 'Exporteert de configuratie van de plugin en de productiegegevens van de virtuele composities en de nootjes.',
	'ieconfig_noizetier_export_option' => 'Gegevens in deze export opnemen?',
	'ieconfig_non_installe' => '<b>Plugin Import/Export van configuraties:</b> deze plugin is niet op de site geïnstalleerd. Dat is niet noodzakelijk, maar wanneer deze is geäctiveerd, kun je eenvoudig nootjes in- en uitvoeren.',
	'ieconfig_probleme_import_config' => 'Er is een probleem opgetreden bij het importeren van de configuratie.',
	'import_compositions_virtuelles_ajouter' => 'Virtuele composities van het importbestand toevoegen. De op de site beschikbare virtuele composities worden niet aangepast.',
	'import_compositions_virtuelles_avertissement1' => 'Er bestaan geen virtuele composities op de site. Je kunt alleen die van het importbestand importeren.',
	'import_compositions_virtuelles_avertissement2' => 'In het importbestand is geen enkele virtuele compositie beschikbaar. Importeren is dus niet mogelijk.',
	'import_compositions_virtuelles_explication' => 'Er bestaan virtuele composities op de site en in het importbestand.',
	'import_compositions_virtuelles_fusionner' => 'De virtuele composities uit het importbestand toevoegen en de virtuele composties varevangen die op de site maar ook in het importbestand bestaan.',
	'import_compositions_virtuelles_label' => 'Virtuele composities',
	'import_compositions_virtuelles_remplacer' => 'De virtuele composities die op de site bestaan vervangen door die uit het importbestand',
	'import_configuration_avertissement' => 'Versie @version@ van de op deze site actieve plugin noiZetier bevat een schema @schema@ dat anders is dan dat van het importbestand. <b>Controleer de compatibiliteit van de configuraties voordat je die uit het bestand importeert</b>.',
	'import_configuration_explication' => 'Versie @version@ van de op deze site actieve plugin noiZetier heeft hetzelfde schema @schema@ als dat van het importbestand.',
	'import_configuration_label' => 'De configuratie van de plugin',
	'import_configuration_labelcase' => 'De huidige configuratie van noiZetier vervangen door die van het importbestand',
	'import_noisettes_ajouter' => 'De nootjes uit het importbestand toevoegen aan de betroffen pagina’s en objecten. De momenteel op de site geconfigureerde nootjes worden niet aangepast',
	'import_noisettes_avertissement1' => 'Er bestaan geen gemeenschappelijke pagina’s of objecten tussen de site en het importbestand. Importeren is du niet mogelijk.',
	'import_noisettes_avertissement2' => 'In het importbestand is geen enkel nootje beschikbaar. Importeren is dus niet mogelijk.',
	'import_noisettes_explication' => 'Er bestaan overeenkomstige pagina’s of objecten tussen de site en het importbestand.',
	'import_noisettes_label' => 'De nootjes',
	'import_noisettes_remplacer' => 'De momenteel geconfigureerde nootjes (voor de betroffen pagina’s en objecten) vervangen door die van het importbestand',
	'import_pages_explicites_avertissement1' => 'Er bestaan geen gemeenschappelijke expliciete pagina’s of objecten tussen de site en het importbestand. Importeren is dus zinloos.',
	'import_pages_explicites_avertissement2' => 'Er bestaan geen expliciete pagina’s of objecten op de site. Importeren is dus zinloos.',
	'import_pages_explicites_explication' => 'Er bestaan gemeenschappelijke expliciete pagina’s of objecten tussen de site en het importbestand.',
	'import_pages_explicites_label' => 'Blokken uitgesloten van specifieke pagina’s',
	'import_pages_explicites_labelcase' => 'De uitgesloten blokken van expliciete pagina’s van de site vervangen door die van het importbestand',
	'import_resume' => 'Het te importeren bestand werd gemaakt met versie @version@, dataschema @schema@.',
	'info_1_noisette_ajoutee' => '1 nootje werd toegevoegd',
	'info_composition' => 'COMPOSITIE:',
	'info_etendre_noisette' => 'Kopieer het nootje @noisette@ in blok @bloc@ van de andere pagina’sd’autres pages',
	'info_nb_noisettes_ajoutees' => '@nb@ nootjes werden toegevoegd',
	'info_page' => 'BLADZIJDE:',
	'installation_tables' => 'Tabellen van plugin Notenboom geïnstalleerd.<br />',
	'item_titre_perso' => 'aangepaste titel',

	// L
	'label_afficher_titre_noisette' => 'Toon een titel voor de nootjes?',
	'label_code' => 'SPIP code:',
	'label_copie_noisette_balise' => 'Kopieer de indicator van het omhullende baken.',
	'label_copie_noisette_css' => 'Kopieer de eventueel aan het omhullende baken gekoppelde stijlen.',
	'label_copie_noisette_parametres' => 'Kopieer de configuratieparameters van het bronnootje.',
	'label_description_code' => 'Omschrijving:',
	'label_identifiant' => 'identificatie:',
	'label_niveau_titre' => 'Niveau van de titel:',
	'label_noisette_css' => 'CSS classes',
	'label_noisette_encapsulation' => 'Omhulling',
	'label_noizetier_ajax' => 'Ieder nootje standaard in AJAX opnemen',
	'label_noizetier_balise' => 'Neem standaard ieder nootje in een baken op (HTML markup)',
	'label_texte' => 'Tekst:',
	'label_texte_introductif' => 'Introductietekst (optioneel):',
	'label_titre' => 'Titel:',
	'label_titre_noisette' => 'Titel van het nootje:',
	'label_titre_noisette_perso' => 'Aangepaste titel:',
	'legende_copie_noisette_parametres' => 'Parameters van het bronnootje',
	'legende_copie_pages_compatibles' => 'Met het type nootje compatibele pagina’s',
	'legende_noisette_inclusion' => 'Parameters voor insluiting',
	'legende_noisette_parametrage' => 'Configuratieparameters',
	'liste_icones' => 'Lijst van ikonen',
	'liste_objets' => 'Inhoud die een configuratie van nootjes heeft',
	'liste_objets_configures' => 'Lijst van objecten',
	'liste_pages' => 'Lijst van bladzijdes',
	'liste_pages_objet_non' => 'Pagina’s niet gekoppeld aan een inhoudstype',
	'liste_pages_objet_oui' => 'Pagina’s gekoppeld aan een inhoudstype',
	'liste_pages_toutes' => 'Alle pagina’s',

	// M
	'masquer' => 'Verbergen',
	'menu_blocs' => 'Te configureren blokken',
	'mode_noisettes' => 'Nootjes aanpassen',
	'modif_en_cours' => 'Bezig met aanpassen',
	'modifier_dans_prive' => 'Aanpassen in hee privé gedeelte',

	// N
	'ne_pas_definir_d_heritage' => 'Geen geërfde compositie bepalen',
	'noisette_numero' => 'nootje nummer:',
	'noisettes_composition' => 'nootjes specifiek voor compositie <i>@composition@</i>:',
	'noisettes_configurees_aucune' => 'Geen enkel geconfigureerd nootje', # MODIF
	'noisettes_configurees_nb' => '@nb@ geconfigureerde nootjes', # MODIF
	'noisettes_configurees_une' => 'Eén geconfigureerd nootje', # MODIF
	'noisettes_disponibles' => 'Beschikbare types nootjes',
	'noisettes_page' => 'Specifiek type nootjes voor de bladzijde <i>@type@</i>:',
	'noisettes_pour' => 'Nootjes voor: ',
	'noisettes_toutes_pages' => 'Type nootjes dat voor alle bladzijdes geldt:',
	'noizetier' => 'Notenboom',
	'nom_bloc_contenu' => 'Inhoud',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navigatie',
	'nom_bloctexte' => 'Blok vrije tekst',
	'nom_codespip' => 'Vrije SPIP code',
	'non' => 'Nee',
	'notice_enregistrer_rang' => 'Klik op Vastleggen om de volgorde van de nootjes op te slaan.',

	// O
	'operation_annulee' => 'Handeling geannuleerd.',
	'option_noisette_encapsulation_defaut' => 'Gebruik de standaard voor noiZetier ingestelde modus <em>(@defaut@)</em>',
	'option_noisette_encapsulation_non' => 'Gebruik nooit omhullende bakens',
	'option_noisette_encapsulation_oui' => 'Omhul het nootje',
	'option_noizetier_encapsulation_non' => 'zonder omhulling',
	'option_noizetier_encapsulation_oui' => 'met omhulling',
	'oui' => 'Ja',

	// P
	'page' => 'Bladzijde',
	'page_autonome' => 'Autonome bladzijde',
	'probleme_droits' => 'Je hebt onvoldoende rechten om deze aanpassing uit te voeren.',

	// Q
	'quitter_mode_noisettes' => 'Verlaat het aanpassen van de nootjes',

	// R
	'recharger_composition' => 'De compositie opnieuw laden',
	'recharger_noisettes' => 'De types nootjes opnieuw laden',
	'recharger_page' => 'De pagina opnieuw laden',
	'recharger_pages' => 'De pagina’s opnieuw laden',
	'retour' => 'Terug',

	// S
	'suggestions' => 'Suggesties',

	// W
	'warning_noisette_plus_disponible' => 'LET OP: dit nootje is niet meer beschikbaar.',
	'warning_noisette_plus_disponible_details' => 'Het skelet van dit nootje (<i>@squelette@</i>) is niet langer toegankelijk. Mogelijk maakt het gebruik van een niet langer aktieve plugin.'
);
