<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/inscription3?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'a_confirmer' => 'Nog te bevestigen',
	'activation_compte' => 'Activeer je account',
	'admin' => 'Admin',
	'afficher_tous' => 'Bekijk alle gebruikers',
	'ajouter_adherent' => 'Maak een nieuwe gebruiker',
	'aucun' => 'geen',
	'aucun_resultat_recherche' => 'Er is geen resultaat voor uw zoekopdracht.',
	'autre' => 'anders',

	// B
	'bouton_suppression_compte' => 'Verwijder uw account',

	// C
	'cfg_description' => 'Stel extra velden in voor gebruikers en andere functies.',
	'cfg_titre' => 'inschrijving 3',
	'choix_affordance_email' => 'Email',
	'choix_affordance_libre' => 'Vrije keuze (hieronder)',
	'choix_affordance_login' => 'Inloggen (standaard in SPIP)',
	'choix_affordance_login_email' => 'Inloggen en e-mailen',
	'choix_feminin' => 'Mevr',
	'choix_inscription_texte_aucun' => 'geen',
	'choix_inscription_texte_libre' => 'Vrije keuze (hieronder)',
	'choix_inscription_texte_origine' => 'De originele (defect van SPIP)',
	'choix_masculin' => 'man',
	'compte_active' => 'Uw account op @ site_naam @',
	'configuration' => 'Gebruikersvelden configureren',
	'contacts_personnels' => 'Persoonlijke contacten',

	// D
	'delete_user_select' => 'Verwijder de geselecteerde gebruiker (s)',
	'descriptif_page_inscription' => 'Registratie op de site @ site @',
	'descriptif_plugin' => 'Hier vindt u alle geregistreerde gebruikers op de site. Hun status wordt aangegeven door de kleur van hun pictogram. <br /> <br /> U kunt extra velden configureren, optioneel voor bezoekers op het moment van registratie.',
	'divers' => 'Divers',

	// E
	'email_bonjour' => 'Hallo @ naam @,',
	'erreur_chaine_valide' => 'Voer een tekenreeks in',
	'erreur_chainelettre' => '(alleen samengesteld uit letters)',
	'erreur_chainenombre' => '(samengesteld uit letters en / of cijfers)',
	'erreur_champ_obligatoire' => 'Dit veld is verplicht',
	'erreur_compte_attente' => 'Uw account is in afwachting van validatie',
	'erreur_compte_attente_mail' => 'Dit adres is gekoppeld aan een ongeldig account',
	'erreur_effacement_auto_impossible' => 'Het account kan niet automatisch worden verwijderd, neem contact met ons op.',
	'erreur_info_statut' => 'De gebruiker @ naam @ heeft de status "@ status @".',
	'erreur_inscription_desactivee' => 'Registratie is uitgeschakeld op deze site',
	'erreur_login_deja_utilise' => 'De login is al in gebruik, kies een andere.',
	'erreur_naissance_futur' => 'Ben je echt in de toekomst geboren?',
	'erreur_naissance_moins_cinq' => 'Ben je echt jonger dan 5?',
	'erreur_naissance_plus_110' => 'Ben je echt meer dan 110?',
	'erreur_numero_valide' => 'Voer een geldig nummer in',
	'erreur_numero_valide_international' => 'Dit nummer moet in internationale vorm zijn (ex: +32 475 123 456)',
	'erreur_reglement_obligatoire' => 'Je moet de regels accepteren',
	'erreur_signature_deja_utilise' => 'Deze waarde wordt al gebruikt door een andere gebruiker.',
	'erreur_suppression_compte_connecte' => 'U moet zijn aangemeld om uw account te verwijderen.',
	'erreur_suppression_compte_non_auteur' => 'U hebt onvoldoende rechten om dit account te verwijderen.',
	'erreur_suppression_compte_webmestre' => 'Het account dat u wilt verwijderen, is dat van een webmaster, u kunt het niet verwijderen.',
	'erreur_suppression_comptes_impossible' => 'Account verwijderen is mislukt',
	'exp_statut_rel' => 'Verschillende status van SPIP-status, deze dient voor de interne controle van een instelling',
	'explication_admin_notifications' => 'Keuze van de beheerder (s) die de meldingen ontvangen',
	'explication_affordance_form' => 'Veld weergegeven op de identificatieformulieren (#LOGIN_PUBLIC)',
	'explication_auto_login' => 'Als het wachtwoord in het formulier is ingevuld, wordt de gebruiker automatisch verbonden met de validatie van het formulier voor het maken van een account.',
	'explication_creation' => 'Slaat de datum waarop het account is gemaakt op in de database.',
	'explication_info_internes' => 'Opties die in de database worden opgeslagen, maar niet worden weergegeven in het nieuwe gebruikersformulier',
	'explication_inscription_texte' => 'Inleidende tekst zichtbaar aan het begin van het registratieformulier',
	'explication_modifier_logo_auteur' => 'Om het logo aan de linkerkant te wijzigen, dubbelklik erop.',
	'explication_password_complexite' => 'Voeg een javascript-controle van het wachtwoord toe wanneer gebruikers wordt gevraagd het te kiezen of te wijzigen.',
	'explication_reglement_article' => 'Het artikel "<a href="@url@" class="spip_in"> @ title @ </a>" wordt gebruikt als een betalingsitem.',
	'explication_statut' => 'Kies de status die u aan nieuwe gebruikers wilt toewijzen',
	'explication_suppression_compte' => 'Valideer het verwijderen van uw account (@ name @ - @ email @)',
	'explication_valider_compte' => 'Accounts moeten door een beheerder worden gevalideerd voordat ze kunnen worden gebruikt.',

	// F
	'fiche_adherent' => 'Gebruikersblad',
	'fiche_expl' => 'Het veld zal zichtbaar zijn op de kaart van de gebruiker (paginabeheerder van de openbare ruimte)',
	'fiche_mod_expl' => 'Het veld kan door de gebruiker vanuit de openbare interface worden bewerkt als we een bewerkingsformulier voor profielen gebruiken (#FORMUL_EDIT_AUTHOR) of door de plugin "pencils"',
	'form_expl' => 'Het veld wordt weergegeven op het formulier #FORMULAIRE_INSCRIPTION',
	'form_oblig_expl' => 'Maak de inschrijving verplicht in registratie- en wijzigingsformulieren',
	'form_retour_aconfirmer' => 'Uw account is succesvol aangemaakt. Het wacht op de validatie van een beheerder.',
	'form_retour_inscription_pass' => 'Uw account is succesvol aangemaakt. U kunt het onmiddellijk gebruiken om u aan te melden bij de site met uw registratie-e-mailadres als login.',
	'form_retour_inscription_pass_logue' => 'Uw account is succesvol aangemaakt. U bent momenteel correct geïdentificeerd.',
	'formulaire_inscription' => 'Registratieformulier',
	'formulaire_inscription_ok' => 'Er is rekening gehouden met uw registratie. U ontvangt per e-mail uw inloggegevens.',
	'formulaire_remplir_obligatoires' => 'Vul alstublieft de verplichte velden in',
	'formulaire_remplir_validation' => 'Controleer de velden die niet gevalideerd zijn. ',

	// I
	'icone_afficher_utilisateurs' => 'Toon gebruikers',
	'icone_configurer_inscription3' => 'Registratie instellen3',
	'info_aconfirmer' => 'bevestigen',
	'info_cextras_desc' => 'Extra velden al aanwezig in basis.',
	'info_connection' => 'Verbindingsinformatie',
	'info_defaut_desc' => 'Instellingsmogelijkheden',
	'info_pass_faible' => 'laag',
	'info_pass_fort' => 'sterk',
	'info_pass_moyen' => 'middelen',
	'info_pass_tres_faible' => 'Zeer laag',
	'info_pass_tres_fort' => 'Heel sterk',
	'info_perso_desc' => 'Persoonlijke informatie die van nieuwe gebruikers van de site wordt gevraagd',
	'infos_personnelles' => 'Persoonlijke informatie',

	// L
	'label_admin_notifications' => 'Wie ontvangt registratiemeldingen?',
	'label_adresse' => 'adres',
	'label_affordance_form' => 'Identificatieformulieren instellen',
	'label_affordance_form_libre' => 'Tekst bij vrije keuze',
	'label_auto_login' => 'Automatische identificatie',
	'label_bio' => 'biografie',
	'label_civilite' => 'beleefdheid',
	'label_code_postal' => 'Postcode',
	'label_commentaire' => 'commentaar',
	'label_creation' => 'Aanmaakdatum van het formulier',
	'label_email' => 'E-Mail :',
	'label_fax' => 'Fax :',
	'label_fonction' => 'functie',
	'label_inscription_depuis' => 'Lid sinds @ datum @.',
	'label_inscription_texte' => 'Introductie van het formulier',
	'label_inscription_texte_libre' => 'Tekst bij vrije keuze',
	'label_login' => 'Gebruikersnaam (login)',
	'label_logo_auteur' => 'Logo',
	'label_mobile' => 'mobiel :',
	'label_naissance' => 'Geboortedatum',
	'label_nom' => 'handtekening',
	'label_nom_famille' => 'Familienaam',
	'label_nom_site' => 'Naam van de site',
	'label_pass' => 'wachtwoord',
	'label_password_complexite' => 'Controleer de complexiteit van het wachtwoord',
	'label_password_retaper' => 'Bevestig het wachtwoord',
	'label_pays' => 'land',
	'label_pays_defaut' => 'Standaardland',
	'label_pgp' => 'PGP-sleutel',
	'label_prenom' => 'Voornaam',
	'label_profession' => 'beroep',
	'label_public_reglement' => 'Ik heb de regels gelezen en ga ermee akkoord',
	'label_public_reglement_url' => 'Ik heb de <a href="@url@" class="spip_in regulation"> regulering gelezen en geaccepteerd</a>',
	'label_public_reglement_url_mediabox' => 'Ik heb het gelezen en accepteer het <a href="@url@" @js@ class="spip_in reglement">nederzetting</a>',
	'label_reglement' => 'Regeling moet worden gevalideerd',
	'label_reglement_article' => 'Origineel artikel van de site dat overeenkomt met de regels',
	'label_reglement_explication' => 'Toon een regelkast en forceer de validatie ervan',
	'label_secteur' => 'sector',
	'label_sexe' => 'beleefdheid',
	'label_societe' => 'Bedrijf / vereniging',
	'label_statut' => 'staat',
	'label_supprimer_logo' => 'Huidig ​​logo verwijderen',
	'label_surnom' => 'bijnaam',
	'label_telephone' => 'telefoon :',
	'label_travail' => 'professioneel',
	'label_url_site' => 'URL van de site',
	'label_url_societe' => 'Bedrijfswebsite',
	'label_validation_numero_international' => 'Dwing telefoonnummers om in internationale vorm te zijn',
	'label_valider_comptes' => 'Accounts valideren',
	'label_ville' => 'stad',
	'label_website' => 'website:',
	'legend_oubli_pass' => 'Geen wachtwoord / wachtwoord vergeten',
	'legende' => 'legende',
	'legende_affordance_form' => 'Identificatieformulier',
	'legende_cextras' => 'Extra velden',
	'legende_formulaire_inscription' => 'Registratieformulier',
	'legende_info_defaut' => 'Verplichte informatie',
	'legende_info_internes' => 'Interne informatie',
	'legende_info_perso' => 'Persoonlijke informatie',
	'legende_password' => 'wachtwoord',
	'legende_reglement' => 'Regulering van de site',
	'legende_validation' => 'validaties',
	'lisez_mail' => 'Er is zojuist een e-mail verzonden naar het opgegeven adres. Volg de instructies om uw account te activeren.',
	'liste_adherents' => 'Zie de lijst met gebruikers',
	'liste_comptes_titre' => 'Lijst met gebruikers',

	// M
	'menu_info_inscription3' => 'Link naar een siteregistratiepagina',
	'menu_nom_inscription3' => 'Link naar registratie',
	'menu_titre_lien_inscription' => 'inschrijving',
	'message_auteur_inscription_confirmer_contenu_admin' => '@name @ gevraagd om een ​​account op de site te hebben. U kunt dit verzoek valideren of ongeldig maken.',
	'message_auteur_inscription_confirmer_contenu_user' => 'uw account wacht momenteel op validatie van een sitebeheerder.',
	'message_auteur_inscription_confirmer_titre_admin' => '[@ spsp_site_name @] Accountvalidatieverzoek voor @ naam @',
	'message_auteur_inscription_confirmer_titre_user' => '[@ spsp_site_name @] Uw account is in afwachting van validatie',
	'message_auteur_inscription_pass' => 'uw account is succesvol aangemaakt. U hebt zelf uw wachtwoord gekozen.',
	'message_auteur_inscription_pass_rappel_login' => 'Herinnering: uw login is "@ login @".',
	'message_auteur_inscription_pass_titre_user' => '[@nom_site_spip@] Je account is aangemaakt',
	'message_auteur_inscription_valider_contenu_user' => 'uw account is gevalideerd door een sitebeheerder.',
	'message_auteur_inscription_valider_titre_user' => '[@nom_site_spip@] Uw account is gevalideerd',
	'message_auteur_inscription_verifier_contenu_plusieurs' => 'Verschillende accounts zijn in behandeling :',
	'message_auteur_inscription_verifier_contenu_un' => 'Een account is in behandeling :',
	'message_auteur_inscription_verifier_titre_plusieurs' => '[@nom_site_spip@] Verschillende gebruikersaccounts om te valideren',
	'message_auteur_inscription_verifier_titre_un' => '[@nom_site_spip@] Een gebruikersaccount om te valideren',
	'message_auteur_invalide_contenu_admin' => '@admin@ weigerde het account van @nom@.',
	'message_auteur_invalide_contenu_user' => 'een beheerder heeft geweigerd om uw account te valideren.',
	'message_auteur_invalide_titre_admin' => '[@nom_site_spip@] Account van @ naam @ geweigerd',
	'message_auteur_invalide_titre_user' => '[@nom_site_spip@] Uw account is geweigerd',
	'message_auteur_valide_contenu_admin' => '@admin@ gevalideerd de account van @nom@.',
	'message_auteur_valide_titre_admin' => '[@nom_site_spip@] Account van @nom@ gevalideerd',
	'message_auto' => '(zegt is een automatisch bericht)',
	'message_compte_efface' => 'Uw account is verwijderd.',
	'message_modif_email_ok' => 'Uw e-mailadres is correct gewijzigd.',
	'message_users_supprimes_nb' => '@nb@ verwijderde gebruiker (s).',
	'message_users_supprimes_un' => 'Een gebruiker is verwijderd.',
	'modif_pass_titre' => 'Wijzig uw wachtwoord',
	'mot_passe_reste_identique' => 'Uw wachtwoord is niet gewijzigd.',

	// N
	'no_user_selected' => 'U hebt geen gebruikers geselecteerd.',
	'nom_explication' => 'je naam of je bijnaam',
	'non_renseigne' => 'niet op de hoogte.',
	'non_renseignee' => 'niet gespecificeerd.',

	// O
	'option_choisissez' => 'kiezen',

	// P
	'par_defaut' => 'Dit veld is verplicht',
	'pass_indiquez_cidessous' => 'Geef hieronder het e-mailadres op waaronder u je bent eerder geregistreerd. je ontvangt een e-mail waarin staat hoe dat moet wijzig uw toegang.',
	'pass_oubli_mot' => 'Uw wachtwoord wijzigen',
	'pass_rappel_email' => 'Herinnering: uw e-mailadres is "@email@".',
	'pass_rappel_login_email' => 'Herinnering: uw login is "@login@" en je e-mailadres is "@email@".',
	'pass_recevoir_mail' => 'U ontvangt een e-mail waarin staat hoe u uw toegang tot de site kunt wijzigen.',
	'password_obligatoire' => 'Wachtwoord is verplicht.',
	'probleme_email' => 'E-mailprobleem: de activerings-e-mail kan niet worden verzonden.',
	'profil_droits_insuffisants' => 'Sorry, je hebt niet het recht om deze auteur te wijzigen<br/>',
	'profil_modifie_ok' => 'Er is rekening gehouden met wijzigingen in uw profiel.',

	// R
	'raccourcis' => 'snelkoppelingen',
	'recherche_case' => 'In het veld :',
	'recherche_utilisateurs' => 'Zoek een gebruiker',

	// S
	'statut_rel' => 'Interne status',
	'statuts_actifs' => 'De kleuren van de pictogrammen komen overeen met de volgende statussen : ',
	'supprimer_adherent' => 'Verwijder gebruikers',

	// T
	'table_expl' => 'Het veld wordt weergegeven in de lijst met gebruikers (privégedeelte)',
	'texte_email_confirmation' => 'Uw account is actief. U kunt nu verbinding maken met de site door uw persoonlijke identificatiegegevens te gebruiken.


Uw login is : @login@
en je kiest gewoon je wachtwoord.

Bedankt voor je vertrouwen

Het team @nom_site@
@url_site@',
	'texte_email_inscription' => 'u staat op het punt uw registratie voor de site te bevestigen @nom_site@.

Klik op de onderstaande link om uw account te activeren en uw wachtwoord te kiezen.

@link_activation@



Bedankt voor je vertrouwen.

Het team @nom_site@.
@url_site@


Als u deze registratie niet hebt aangevraagd of als u niet langer deel wilt uitmaken van onze site, klikt u op de onderstaande koppeling.
@link_suppresion@


',
	'thead_fiche' => 'plug',
	'thead_fiche_mod' => 'veranderlijk',
	'thead_form' => 'formulier',
	'thead_obligatoire' => 'verplicht',
	'thead_table' => 'tafel',
	'titre_modifier_auteur' => 'Bewerk het profiel van deze gebruiker',
	'titre_modifier_auteur_nom' => 'Bewerk profiel van @nom@',
	'titre_modifier_profil' => 'Bewerk je profiel',
	'titre_supprimer_compte' => 'Verwijder uw account',

	// V
	'vos_articles_auteur' => 'Jouw artikelen',
	'vos_contacts_personnels' => 'Uw persoonlijke contacten',
	'votre_adresse' => 'Uw thuisadres',
	'votre_login_mail' => 'Uw login of e-mail :',
	'votre_mail' => 'Uw e-mail :',
	'votre_nom_complet' => 'Je volledige naam'
);
