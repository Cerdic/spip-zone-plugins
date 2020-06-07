<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/formidable?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_pages_explication' => 'Standaard is een publieke pagina van een formulier niet toegestaan',
	'activer_pages_label' => 'Activeer het aanmaken van een publieke pagina van een formulier.',
	'admin_reponses_auteur' => 'Geef de auteur van het formulier toestemming de antwoorden te wijzigen', # MODIF
	'admin_reponses_auteur_explication' => 'Alleen beheerders kunnen normaal de in een formulier ingevulde antwoorden wijzigen (naar de prullenbak, gepubliceerd, voorgesteld). Met deze optie mag de auteur van een formulier de status aanpassen (met het risico van onjuiste statistieken)', # MODIF
	'analyse_avec_reponse' => 'Niet-lege antwoorden',
	'analyse_exclure_champs_explication' => 'Voeg de naam van het uit te sluiten velden in, gescheiden door <code>|</code>. Gebruik geen <code>@</code>.',
	'analyse_exclure_champs_label' => 'Velden die van de analyse moeten worden uitgesloten',
	'analyse_exporter' => 'Analyse exporteren',
	'analyse_longueur_moyenne' => 'Gemiddelde lengte in woorden',
	'analyse_nb_reponses_total' => '@nb@ personen reageerden op dit formulier.',
	'analyse_sans_reponse' => 'Onbeantwoord',
	'analyse_une_reponse_total' => 'Een persoon reageerde op dit formulier.',
	'analyse_zero_reponse_total' => 'Niemand reageerde op dit formulier.', # MODIF
	'aucun_traitement' => 'Geen behandeling',
	'autoriser_admin_restreint' => 'Laat beperkte beheerders toe formulieren te maken en aan te passen', # MODIF
	'autoriser_admin_restreint_explication' => 'Standaard hebben alleen beheerders toegang tot het maken en aanpassen van formulieren', # MODIF

	// B
	'bouton_formulaires' => 'Formulieren',
	'bouton_revert_formulaire' => 'Terug naar de laatst opgeslagen versie',

	// C
	'cfg_analyse_classe_explication' => 'Je kunt CSS classes aangeven die worden toegevoegd aan de  container van iedere grafiek, zoals <code>gray</code>, <code>blue</code>, <code>orange</code>, <code>green</code>, enz!',
	'cfg_analyse_classe_label' => 'CSS class van de voortgangsbalk',
	'cfg_objets_explication' => 'Kies de inhoud waarin de formulieren kunnen worden gekoppeld.',
	'cfg_objets_label' => 'Koppel formulieren aan inhoud',
	'cfg_titre_page_configurer_formidable' => 'Formidable configureren',
	'champs' => 'Velden',
	'changer_statut' => 'Dit formulier is:', # MODIF
	'creer_dossier_formulaire_erreur_impossible_creer' => 'Map @dossier@ kan niet worden aangemaakt en is benodigd voor de opslag van bestanden. Controleer de toegangsrechten.',
	'creer_dossier_formulaire_erreur_impossible_ecrire' => 'Map @dossier@ kan niet worden benaderd en is benodigd voor de opslag van bestanden. Controleer de toegangsrechten.',
	'creer_dossier_formulaire_erreur_possible_lire_exterieur' => 'Het is mogelijk de inhoud van map @dossier@ op afstand te lezen. Dit is problematisch in verband met vertrouwelijkheid van informatie.',

	// E
	'echanger_formulaire_forms_importer' => 'Forms & Tables (.xml)',
	'echanger_formulaire_wcs_importer' => 'W.C.S. (.wcs)',
	'echanger_formulaire_yaml_importer' => 'Formidable (.yaml)',
	'editer_apres_choix_formulaire' => 'Het formulier, opnieuw',
	'editer_apres_choix_redirige' => 'Stuur door naar een nieuw adres',
	'editer_apres_choix_rien' => 'Niets',
	'editer_apres_choix_stats' => 'Statistieken van antwoorden',
	'editer_apres_choix_valeurs' => 'De ingevoerde waarden',
	'editer_apres_explication' => 'Toon na validatie in plaats van het formulier:', # MODIF
	'editer_apres_label' => 'Toon dan',
	'editer_css' => 'CSS classes ',
	'editer_descriptif' => 'Omschrijving',
	'editer_descriptif_explication' => 'Eenuitleg van het formulier voor het privé gedeelte.',
	'editer_globales_etapes_activer_explication' => 'Wanneer deze optie actief is, wordt iedere groep van velden van het eerste niveau omgevormd in een stap van het formulier',
	'editer_globales_etapes_activer_label_case' => 'Activeer beheer in meerdere stappen',
	'editer_globales_etapes_precedent_label' => 'Tekst van de knop vorige (standaard "Vorige")',
	'editer_globales_etapes_suivant_label' => 'Tekst van de knop volgende (standaard "Volgende")',
	'editer_globales_texte_submit_label' => 'Tekst van de validatieknop',
	'editer_identifiant' => 'Login',
	'editer_identifiant_explication' => 'Geef een korte tekstindentificatie om het formulier gemakkelijker aan te roepen. De identificatie kan uitsluitend uit cijfers, letters (zonder accent) en de "_" bestaan',
	'editer_menu_auteurs' => 'Configureer auteurs',
	'editer_menu_champs' => 'Configureer de velden',
	'editer_menu_formulaire' => 'Configureer het formulier',
	'editer_menu_traitements' => 'Configureer de behandelingen',
	'editer_message_erreur_unicite_explication' => 'Wanneer je dit veld leeg laat, wordt de standaard foutmelding van Formidable getoond',
	'editer_message_erreur_unicite_label' => 'Foutmelding wanneer een veld niet uniek is',
	'editer_message_ok' => 'Terugmelding',
	'editer_message_ok_explication' => 'Je kunt het bericht aanpassen dat de gebruiker na het insturen van een geldig formulier ontvangt. Je kunt de waarde van sommige velden weergeven met @raccourci@.', # MODIF
	'editer_modifier_formulaire' => 'Formulier aanpassen',
	'editer_nouveau' => 'Nieuw formulier',
	'editer_redirige_url' => 'Doorstuuradres na validatie',
	'editer_redirige_url_explication' => 'Laat leeg wanneer je op dezelfde bladzijde wilt blijven',
	'editer_titre' => 'Titel',
	'editer_unicite_explication' => 'Bewaar het formulier uitsluitend wanneer de waarde van een specifiek veld uniek is onder de gegeven antwoorden.',
	'editer_unicite_label' => 'Controleer of dit veld uiniek is',
	'erreur_autorisation' => 'Je hebt niet het recht website formulieren aan te passen',
	'erreur_base' => 'Bij het opslaan ontstond een fout.',
	'erreur_deplacement_fichier' => 'Bestand "@nom@" kon niet correct door het systeem worden opgeslagen. Neem contact op met de webmaster.',
	'erreur_fichier_expire' => 'De link voor de download is te oud.',
	'erreur_fichier_introuvable' => 'Het gevraagde bestand werd niet gevonden.',
	'erreur_generique' => 'Onderstaande velden bevatten fouten. Controleer ze.',
	'erreur_identifiant' => 'Deze login wordt al gebruikt.',
	'erreur_identifiant_format' => 'De identificatie kan uitsluitend bestaan uit cijfers, letters (zonder accent) en het  "_" teken',
	'erreur_importer_forms' => 'Fout bij het invoeren van het Forms&Tables formulier',
	'erreur_importer_wcs' => 'Fout bij het invoeren van het W.C.S formulier',
	'erreur_importer_yaml' => 'Fout bij het invoeren van het YAML bestand',
	'erreur_inexistant' => 'Dit formulier bestaat niet.',
	'erreur_saisies_modifiees_parallele' => 'De velden van het formulier werden elders aangepast. Jouw eigen aanpassingen werden dus niet geregistreerd. Je moet opnieuw beginnen.',
	'erreur_unicite' => 'Deze waarde is al in gebruik',
	'exporter_adresses_ip' => 'Neem IP adressen mee bij het exporteren van antwoorden',
	'exporter_adresses_ip_explication' => 'Standaard worden IP adressen niet meegenomen bij het exporteren van antwoorden',
	'exporter_formulaire_cle_ou_valeur_cle_label' => 'Sleutelwaardes',
	'exporter_formulaire_cle_ou_valeur_label' => 'Moeten voor radio buttons, drop-down lijsten, enz.  voor een mens leesbare waardes (labels) of sleutelwaardes worden geëxporteerd?',
	'exporter_formulaire_cle_ou_valeur_valeur_label' => 'Leesbare waardes (labels)',
	'exporter_formulaire_date_debut_label' => 'Vanaf (inclusief)',
	'exporter_formulaire_date_erreur' => 'De begindatum moet voor de einddatum liggen',
	'exporter_formulaire_date_fin_label' => 'Tot en met',
	'exporter_formulaire_format_label' => 'Bestandsformaat',
	'exporter_formulaire_ignorer_fichiers_explication_label' => 'Dir formulier bevat bestandsvelden. Wil je deze niet aan de export toevoegen en ze bijvoorbeeld met FTP downloaden?',
	'exporter_formulaire_ignorer_fichiers_label' => 'Geen bestanden toevoegen',
	'exporter_formulaire_statut_label' => 'Antwoorden',

	// F
	'formulaires_aucun' => 'Er is momenteel geen formulier',
	'formulaires_aucun_champ' => 'Er is momenteel geen iinvoerveld in dit formulier.',
	'formulaires_corbeille_tous' => '@nb@ formulieren in de prullenbak',
	'formulaires_corbeille_un' => 'Een formulier in de prullenbak',
	'formulaires_dupliquer' => 'Duplicaceer het formulier',
	'formulaires_dupliquer_copie' => '(kopie)',
	'formulaires_introduction' => 'Maak en configureer hier formulieren op jouw site.',
	'formulaires_nouveau' => 'Eem nieuw formulier maken',
	'formulaires_reponses_corbeille_tous' => '@nb@ formulier antwoorden in de prullenbak',
	'formulaires_reponses_corbeille_un' => 'Een formulier antwoord in de prullenbak',
	'formulaires_supprimer' => 'Het formulier verwijderen',
	'formulaires_supprimer_confirmation' => 'Let op, dit verwijdert ook de resultaten. Weet je zeker dat je het formulier wilt verwijderen?',
	'formulaires_tous' => 'Alle formulieren',

	// H
	'heures_minutes_secondes' => '@h@uur @m@min @s@sec',

	// I
	'icone_modifier_formulaires_reponse' => 'Het antwoord aanpassen',
	'icone_retour_formulaires_reponse' => 'Terug naar het antwoord',
	'id_formulaires_reponse' => 'Identificatie antwoord',
	'identification_par_cookie' => 'Met een cookie (willekeurige identificatie, slaat geen persoonlijke informatie op) ',
	'identification_par_id_auteur' => 'Met de login (id_auteur) van een aangemeld persoon',
	'identification_par_id_reponse' => 'Met de identificatie (id_formulaire_reponse) van het antwoord, expliciet doorgegeven bij het aanroepen van het formulier in een skelet',
	'identification_par_variable_php' => 'Met een PHP connectie variabele (hash)',
	'importer_formulaire' => 'Een formulier importeren',
	'importer_formulaire_fichier_label' => 'Te importeren bestand',
	'importer_formulaire_format_label' => 'Bestandsformaat',
	'info_1_formulaire' => '1 formulier',
	'info_1_reponse' => '1 antwoord',
	'info_aucun_formulaire' => 'Geen formulier',
	'info_aucune_reponse' => 'Geen antwoord',
	'info_formulaire_refuse' => 'Gearchiveerd',
	'info_formulaire_utilise_par' => 'Formulier gebruikt door:', # MODIF
	'info_nb_formulaires' => '@nb@ formulieren',
	'info_nb_reponses' => '@nb@ antwoorden',
	'info_reponse_proposee' => 'Aan te passen',
	'info_reponse_proposees' => 'Aan te passen',
	'info_reponse_publiee' => 'Gevalideerd',
	'info_reponse_publiees' => 'Gevalideerd',
	'info_reponse_refusee' => 'Verworpen',
	'info_reponse_refusees' => 'Verworpen',
	'info_reponse_supprimee' => 'Verwijderd',
	'info_reponse_supprimees' => 'Verwijderd',
	'info_reponse_toutes' => 'Alle',
	'info_utilise_1_formulaire' => 'Gebruikt formulier:', # MODIF
	'info_utilise_nb_formulaires' => 'Gebruikte formulieren:', # MODIF

	// J
	'jours_heures_minutes_secondes' => '@j@dag @h@uur @m@min @s@sec',

	// L
	'lien_expire' => 'Link vervalt over @delai@',
	'liens_ajouter' => 'Een formulier toevoegen',
	'liens_ajouter_lien' => 'Dit formulier toevoegen',
	'liens_creer_associer' => 'Een formulier maken en koppelen',
	'liens_retirer_lien_formulaire' => 'Dit formulier verwijderen',
	'liens_retirer_tous_liens_formulaires' => 'Alle formulieren verwijderen',

	// M
	'minutes_secondes' => '@m@min @s@sec',
	'modele_label_formulaire_formidable' => 'Welk formulier?',
	'modele_nom_formulaire' => 'een formulier',

	// N
	'noisette_label_afficher_titre_formulaire' => 'De titel van het formulier tonen?',
	'noisette_label_identifiant' => 'Te tonen formulier:', # MODIF
	'noisette_nom_noisette_formulaire' => 'Formulier',

	// P
	'pas_analyse_fichiers' => 'Formidable biedt (nog) geen scan van verzonden bestanden',

	// R
	'reponse_aucune' => 'Geen antwoord',
	'reponse_intro' => '@auteur@ antwoordde top formulier @formulaire@',
	'reponse_maj' => 'Laatste aanpassing',
	'reponse_numero' => 'Antwoord nummer:', # MODIF
	'reponse_statut' => 'Dit antwoord is:', # MODIF
	'reponse_supprimer' => 'Verwijder dit antwoord',
	'reponse_supprimer_confirmation' => 'Wil je dit antwoord echt verwijderen?',
	'reponse_une' => '1 antwoord',
	'reponses_analyse' => 'Analyse van antwoorden',
	'reponses_anonyme' => 'Anoniem',
	'reponses_auteur' => 'Gebruiker',
	'reponses_exporter' => 'De antwoorden exporteren',
	'reponses_exporter_format_csv' => 'Spreadsheet .CSV',
	'reponses_exporter_format_xls' => 'Excel .XLS',
	'reponses_exporter_statut_publie' => 'Gepubliceerd',
	'reponses_exporter_statut_tout' => 'Alle',
	'reponses_exporter_telecharger' => 'Downloaden',
	'reponses_ip' => 'IP adres',
	'reponses_liste' => 'Antwoordenlijst',
	'reponses_liste_prop' => 'Antwoorden ter validatie',
	'reponses_liste_publie' => 'Alle gevalideerde antwoorden',
	'reponses_nb' => '@nb@ antwoorden',
	'reponses_page_accueil' => 'Geef de antwoorden weer op de beginpagina van het privé gedeelte',
	'reponses_supprimer' => 'Verwijder alle antwoorden ',
	'reponses_supprimer_confirmation' => 'Wil je alle antwoorden van dit formulier verwijderen?',
	'reponses_voir_detail' => 'Bekijk het antwoord',
	'retour_aucun_traitement' => 'Je antwoord werd aanvaard. Maar omdat de functie van het formulier nog niet is geconfigureerd, werd niets met de gegevens gedaan!',

	// S
	'sans_reponses' => 'Onbeantwoord',
	'secondes' => '@s@sec',

	// T
	'texte_statut_poubelle' => 'verwijderd',
	'texte_statut_propose_evaluation' => 'voorgesteld',
	'texte_statut_publie' => 'gevalideerd',
	'texte_statut_refuse' => 'gearchiveerd',
	'texte_statut_refusee' => 'verworpen',
	'titre_cadre_raccourcis' => 'Shortcuts',
	'titre_formulaires_archives' => 'Archieven',
	'titre_formulaires_poubelle' => 'verwijderd',
	'titre_reponses' => 'Antwoorden',
	'traitements_actives' => 'Geactiveerde bewerkingen',
	'traitements_aide_memoire' => 'Liijst van shortcuts:',
	'traitements_avertissement_creation' => 'Wijzigingen aan de velden van het formulier werden met succes opgeslagen. Je kunt nu de uit te voeren verwerkingen definiëren.',
	'traitements_avertissement_modification' => 'Wijzigingen aan de velden van het formulier werden met succes opgeslagen. <strong>Mogelijk moeten somige verwerkingen overeenkomstig worden aangepast.</ strong>',
	'traitements_champ_aucun' => 'Geen',
	'traiter_email_AR_label' => 'Ontvangstbevestiging',
	'traiter_email_accuse_explication_texte' => 'Om de ontvangstbevestiging te activeren, moet je een verzender definiëren.',
	'traiter_email_contenu_courriel_label' => 'Inhoud van de email',
	'traiter_email_description' => 'Stuur per email het resultaat van het formulier naar een lijst van ontvangers.',
	'traiter_email_destinataires_courriel_label' => 'Ontvangers van de email',
	'traiter_email_envoyeur_courriel_label' => 'Verzender', # MODIF
	'traiter_email_horodatage' => 'Formulier "@formulaire@" verzonden op @date@ om @heure@.',
	'traiter_email_message_erreur' => 'Bij het verzenden van de email trad een fout op.',
	'traiter_email_message_ok' => 'Je bereicht werd per email verzonden.',
	'traiter_email_option_activer_accuse_label_case' => 'Stuur ook een email met een bevestigingsbericht naar de verzender.', # MODIF
	'traiter_email_option_activer_ip_label_case' => 'Stuur het IP adres van de verzender naar de ontvangers.', # MODIF
	'traiter_email_option_courriel_envoyeur_accuse_explication' => 'Geef het e-mailadres dat wordt gebruikt als verzender van de bevestiging. Geef je niets aan, dan wordt het e-mailadres van de webmaster gebruikt.',
	'traiter_email_option_courriel_envoyeur_accuse_label' => 'E-mailadres verzender van de bevestiging', # MODIF
	'traiter_email_option_destinataires_champ_form_attention' => 'Deze optie is vervallen omdat zij vraagt om SPAM.
<br /> - Om naar een auteur van de site te verzenden, gebruik je de optie "Ontvangers" (hierboven).
<br /> - Om naar de invuller van het formulier te verzenden, configureer je de ontvangstbevestiging (onder).
<br />
Deze optie is aangehouden voor compatibiliteit met oudere versies. Ze verschijnt niet op nieuwe formulieren.', # MODIF
	'traiter_email_option_destinataires_champ_form_explication' => 'Is een van de velden een e-mailadres en wil je het formulier hier naartoe laten sturen, selecteer dan het veld.',
	'traiter_email_option_destinataires_champ_form_label' => 'De ontvanger staat in een van de velden van het formulier',
	'traiter_email_option_destinataires_explication' => 'Kies het veld dat overeen komt met de ontvangers van de email. <br />
Dit is "Ontvangers" of een "Verborgen Veld", inclusief de numerike identificatie van een auteur van de site.',
	'traiter_email_option_destinataires_label' => 'Ontvangers',
	'traiter_email_option_destinataires_plus_explication' => 'Een lijst van adressen, gescheiden door een komma.',
	'traiter_email_option_destinataires_plus_label' => 'Extra ontvangers',
	'traiter_email_option_destinataires_selon_champ_explication' => 'Maakt het mogelijk een of meer ontvangers aan te geven op basis van de waarde van een veld.
Geef de naam van het field, de waarde, en e-mailadres(sen) (gescheiden door een komma) in een formaat zoals: "@selection_1@/choix1 : mail@example.tld". Je kunt meerdere tests vermelden, elk op een nieuwe regel.', # MODIF
	'traiter_email_option_destinataires_selon_champ_label' => 'Ontvangers op basis van een veld',
	'traiter_email_option_envoyeur_courriel_explication' => 'Geef het veldaan dat het e-mailadres van de verzender bevat.',
	'traiter_email_option_envoyeur_courriel_label' => 'E-mailadres verzender', # MODIF
	'traiter_email_option_envoyeur_nom_explication' => 'Bouw de naam op met de @raccourcis@ (zie notities).Geef je nies aan, dan wordt het de naam van de site.',
	'traiter_email_option_envoyeur_nom_label' => 'Naam van de verzender',
	'traiter_email_option_exclure_champs_email_explication' => 'Wil je bepaalde velden niet in de emails laten verschijnen (zoals verborgen velden), vermeld ze dan hier, gescheiden door een komma.',
	'traiter_email_option_exclure_champs_email_label' => 'Velden die niet in het bericht moeten verschijnen',
	'traiter_email_option_masquer_champs_vide_label_case' => 'Verberg lege velden',
	'traiter_email_option_masquer_liens_label_case' => 'Verberg administratieve links in de email.',
	'traiter_email_option_masquer_valeurs_accuse_label_case' => 'Geef geen waarden van de antwoorden in de ontvangstbevestiging',
	'traiter_email_option_modification_reponse_label_case' => 'Stuur geen email bij aanpassing van een al geregistreerde reactie.',
	'traiter_email_option_nom_envoyeur_accuse_explication' => 'Geef de naam van de verzender van de bevestiging. Geef je niets aan, dan wordt de naam van de site gebruikt.', # MODIF
	'traiter_email_option_nom_envoyeur_accuse_label' => 'Naam van de verzender van de bevestiging', # MODIF
	'traiter_email_option_pj_explication' => 'Wanneer de grootte van de geposte documenten minder is dan _FORMIDABLE_TAILLE_MAX_FICHIERS_EMAIL Mb (constante kan door de webmaster worden aangepast).',
	'traiter_email_option_pj_label' => 'Voeg de bestanden aan de email toe',
	'traiter_email_option_sujet_accuse_label' => 'Onderwerp van de ontvangstbevestiging',
	'traiter_email_option_sujet_explication' => 'Bouw op met @raccourcis@. Geef je niets aan, dan wordt het onderwerp automatisch aangemaakt.',
	'traiter_email_option_sujet_label' => 'Onderwerp van het bericht',
	'traiter_email_option_sujet_valeurs_brutes_label' => 'Ruwe waardes',
	'traiter_email_option_sujet_valeurs_brutes_label_case' => 'De email is voor een robot en niet voor een mens. In het onderwerp worden ruwe waardes geplaatst (die de robot begrijpt) en niet de geïnterpreteerde waardes (die een mens begrijpt).',
	'traiter_email_option_texte_accuse_explication' => 'Bouw de tekst op met behulp van @raccourcis@. vermeld je niets, dan wordt het retourbericht van het formulier gebruikt.',
	'traiter_email_option_texte_accuse_label' => 'Tekst van de ontvangstbevestiging',
	'traiter_email_option_vrai_envoyeur_explication' => 'Sommige SMTP servers staan geen arbitrair e-mailadres in het "From" veld toe. Daarom vult Formidable standaard het e-mailadres van de verzender in bij het "Reply-To" veld en gebruikt het het e-mailadres van de webmaster in het "From" veld. Geef hier aan dat hete-mailadres in het "From" veld moet worden gezet. ',
	'traiter_email_option_vrai_envoyeur_label' => 'Plaats het e-mailadres van de verzender in het "From" veld',
	'traiter_email_page' => '<a href="@url@">Van deze pagina</a>.',
	'traiter_email_sujet' => '@nom@ heeft jou geschreven.',
	'traiter_email_sujet_accuse' => 'Bedankt voor je antwoord.',
	'traiter_email_sujet_courriel_label' => 'Onderwerp bericht',
	'traiter_email_titre' => 'Per email verzenden',
	'traiter_email_url_enregistrement' => 'Je kunt alle antwoorden beheren  <a href="@url@">op deze pagina</a>.',
	'traiter_email_url_enregistrement_precis' => 'Je kunt de antwoorden zien <a href="@url@">op deze pagina</a>.',
	'traiter_enregistrement_description' => 'Sla de resultaten van het formulier op in de database',
	'traiter_enregistrement_divers' => 'Diverse',
	'traiter_enregistrement_donnees_personelles' => 'Persoonlijke gegevens',
	'traiter_enregistrement_erreur_base' => 'Bij het schrijven naar de database trad een fout op',
	'traiter_enregistrement_erreur_deja_repondu' => 'Je hebt dit formulier al ingevuld.',
	'traiter_enregistrement_erreur_edition_reponse_inexistante' => 'Het aan te passen antwoord kan niet worden gevonden.',
	'traiter_enregistrement_identification_reponses' => 'Identificatie van reacties',
	'traiter_enregistrement_message_ok' => 'Bedankt. Je antwoorden werdenopgeslagen.',
	'traiter_enregistrement_option_anonymiser_label' => 'Bewaar de ID van de aangesloten persoon niet.',
	'traiter_enregistrement_option_auteur' => 'Koppel auteurs aan de formulieren', # MODIF
	'traiter_enregistrement_option_auteur_explication' => 'Verbind een of meer auteurs aan een formulier. Wanneer actief zorgt deze optie ervoor dat anderen de configuratie of resultaten van het formulier kan benaderen.', # MODIF
	'traiter_enregistrement_option_effacement_delai_label' => 'Aantal dagen voor verzijdering',
	'traiter_enregistrement_option_effacement_label' => 'Verwijder regelmatig de oudste resultaten',
	'traiter_enregistrement_option_identification_explication' => 'Welk proces moet eerst worden gebruikt om eerder verstrekte antwoorden van de gebruiker te vinden? ',
	'traiter_enregistrement_option_identification_label' => 'Identificatiemethode ',
	'traiter_enregistrement_option_identification_variable_php_explication' => 'Benodigd een PHP/Server identificatie die niet standaard in SPIP zit.',
	'traiter_enregistrement_option_identification_variable_php_label' => 'PHP variabele',
	'traiter_enregistrement_option_invalider_explication' => 'Wanneer de antwoorden op dit formulier publiekelijk worden gebruikt, kun je de cache vervangen bij een nieuwe inzending.',
	'traiter_enregistrement_option_invalider_label' => 'Ververs de cache',
	'traiter_enregistrement_option_ip_label' => 'Sla IPs  op (verborgen na een retentie-periode)',
	'traiter_enregistrement_option_moderation_label' => 'Moderatie',
	'traiter_enregistrement_option_modifiable_explication' => 'Aanpasbaar: Bezoekerskunnen hun antwoorden achteraf aanpassen.', # MODIF
	'traiter_enregistrement_option_modifiable_label' => 'Antwoorden kunnen worden aangepast',
	'traiter_enregistrement_option_multiple_explication' => 'Meermaals: Een enkele persoon mag meermaals antwoorden.',
	'traiter_enregistrement_option_multiple_label' => 'Meermaals',
	'traiter_enregistrement_option_php_auth_user_label' => 'Server variabele: PHP_AUTH_USER',
	'traiter_enregistrement_option_remote_user_label' => 'Server variabele: REMOTE_USER',
	'traiter_enregistrement_option_resume_reponse_explication' => 'Deze string wordt gebruikt om een opsomming van elk antwoord in de lijsten te geven. Velden als <tt>@input_1@</tt> worden vervangen zoals aangegeven in de hulptekst hiernaast.',
	'traiter_enregistrement_option_resume_reponse_label' => 'Opsomming van het antwoord',
	'traiter_enregistrement_reglages_generaux' => 'Algemene instellingen',
	'traiter_enregistrement_titre' => 'Registreer de resultaten',
	'traiter_enregistrement_unicite_champ' => 'Uniekheid van antwoorden',

	// V
	'voir_exporter' => 'Exporteer het formulier',
	'voir_numero' => 'Formuliernummer:',
	'voir_reponses' => 'Bekijk de antwoorden',
	'voir_traitements' => 'Behandelingen'
);
