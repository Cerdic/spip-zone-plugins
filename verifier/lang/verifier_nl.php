<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/verifier?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_code_postal' => 'Deze postcode is ongeldig.',
	'erreur_comparaison_egal' => 'De waarde moet gelijk zijn aan het veld "@nom_champ@"', # MODIF
	'erreur_comparaison_egal_type' => 'De waarde moet gelijk zijn aan en hetzelfde type hebben als het veld "@nom_champ@"', # MODIF
	'erreur_comparaison_grand' => 'De waarde moet groter zijn dan het veld "@nom_champ@"', # MODIF
	'erreur_comparaison_grand_egal' => 'De waarde moet gelijk zijn aan of groter zijn dan het veld "@nom_champ@"', # MODIF
	'erreur_comparaison_petit' => 'De waarde moet kleiner zijn dan het veld "@nom_champ@"', # MODIF
	'erreur_comparaison_petit_egal' => 'De waarde moet gelijk zijn aan of kleiner zijn dan het veld "@nom_champ@"', # MODIF
	'erreur_couleur' => 'De kleurcode is ongeldig.',
	'erreur_date' => 'De datum is ongeldig.',
	'erreur_date_format' => 'Het datumformaat is ongeldig.',
	'erreur_decimal' => 'De waarde moet een decimaal getal zijn.',
	'erreur_decimal_nb_decimales' => 'Het getal moet meer dan @nb_decimales@ decimalen na de punt hebben.',
	'erreur_email' => 'Het e-mailadres <em>@email@</em> is niet correct geformatteerd.',
	'erreur_email_nondispo' => 'Het e-mailadres <em>@email@</em> werd al gebruikt.',
	'erreur_entier' => 'De waarde moet een geheel getal zijn.',
	'erreur_entier_entre' => 'De waarde moet liggen tussen @min@ en @max@.',
	'erreur_entier_max' => 'De waarde moet kleiner zijn dan @max@.',
	'erreur_entier_min' => 'De waarde moet groter zijn dan @min@.',
	'erreur_heure' => 'De vermelde tijd bestaat niet.',
	'erreur_heure_format' => 'Het tijdformaat is niet geldig.',
	'erreur_id_document' => 'Deze document-identificatie is niet geldig.',
	'erreur_id_objet' => 'Deze identificatie is ongeldig.',
	'erreur_inconnue_generique' => 'Het formaat is ongeldig.',
	'erreur_isbn' => 'de ISBN code is niet geldig (bv: 978-2-1234-5680-3 of 2-1234-5680-X)', # MODIF
	'erreur_isbn_13_X' => 'Een ISBN-13 code kan niet eindigen met een X.',
	'erreur_isbn_G' => 'Het eerste segment moet gelijk zijn aan 978 of 979.',
	'erreur_isbn_nb_caracteres' => 'de ISBN code moet 10 of 13 tekens bevatten (momenteel @nb@), de streepjes niet meegeteld.',
	'erreur_isbn_nb_segments' => 'de ISBN code moet uit 4 of 5 segmenten bestaan (momenteel @nb@).',
	'erreur_isbn_segment' => 'het segment "@segment@" heeft @nb@ teveel teken(s).',
	'erreur_isbn_segment_lettre' => 'het segment "@segment@" mag geen letters bevatten.',
	'erreur_numerique' => 'Het nummerformaat is ongeldig.',
	'erreur_objet' => 'Dit object is niet geldig', # MODIF
	'erreur_regex' => 'De regex string is incorrect geformatteerd.',
	'erreur_siren' => 'Het SIREN nummer is ongeldig.',
	'erreur_siret' => 'Het SIRET number is ongeldig.',
	'erreur_taille_egal' => 'De waarde moet uit exact @egal@ tekens bestaan (momenteel @nb@).',
	'erreur_taille_entre' => 'De waarde moet tussen de @min@ en @max@ tekens bevatten (momenteel @nb@).',
	'erreur_taille_max' => 'De waarde mag niet meer dan @max@ tekens bevatten (momenteel @nb@).',
	'erreur_taille_min' => 'De waarde moet minimaal @min@ ctekens bevatten (momenteel @nb@).',
	'erreur_telephone' => 'Het telefoonnummer is ongeldig.',
	'erreur_url' => 'Het URL-adres <em>@url@</em> is ongeldig.',
	'erreur_url_protocole' => 'Het ingevoerde adres <em>(@url@)</em> moet beginnen met @protocole@', # MODIF
	'erreur_url_protocole_exact' => 'Het ingevoerde adres <em>(@url@)</em> begint niet met een geldig protocol (bv. http://)', # MODIF

	// N
	'normaliser_option_date' => 'De datum normaliseren?',
	'normaliser_option_date_aucune' => 'Nee',
	'normaliser_option_date_en_datetime' => '"Datetime" formaat (voor SQL)',

	// O
	'option_code_postal_pays_explication' => 'Tweeletterige landcode: FR, DE, NL, enz.',
	'option_code_postal_pays_label' => 'Land',
	'option_comparaison_champ_champ_explication' => 'Veld identificatie («name» attribuut)',
	'option_comparaison_champ_champ_label' => 'Veld',
	'option_comparaison_champ_comparaison_explication' => 'Soort vergelijking',
	'option_comparaison_champ_comparaison_label' => 'Vergelijking',
	'option_comparaison_champ_egal' => '== Gelijk',
	'option_comparaison_champ_egal_type' => '=== Identiek (zelfde soort)',
	'option_comparaison_champ_grand' => '> Groter dan',
	'option_comparaison_champ_grand_egal' => '>= Groter dan of gelijk aan ',
	'option_comparaison_champ_nom_champ_explication' => 'Veldnaam voor mensen',
	'option_comparaison_champ_nom_champ_label' => 'Veldnaam',
	'option_comparaison_champ_petit' => '< Kleiner dan',
	'option_comparaison_champ_petit_egal' => '<= Kleiner dan of gelijk aan',
	'option_couleur_normaliser_label' => 'De kleurcode normaliseren?',
	'option_couleur_type_hexa' => 'Kleurcode in hexadecimaal formaat',
	'option_couleur_type_label' => 'Uit te voeren type verificatie',
	'option_decimal_nb_decimales_label' => 'Aantal decimale plaatsen',
	'option_email_disponible_label' => 'Beschikbaar adres',
	'option_email_disponible_label_case' => 'Controleer of het adres niet al door een andere gebruiker wordt gebruikt',
	'option_email_mode_5322' => 'Controleer tegen de meest stricte beschikbare standaard',
	'option_email_mode_label' => 'Email verificatiemethode',
	'option_email_mode_normal' => 'Normale SPIP verificatie',
	'option_email_mode_strict' => 'Minder tolerante verificatie',
	'option_entier_max_label' => 'Maximum waarde',
	'option_entier_min_label' => 'Minimum waarde',
	'option_regex_modele_label' => 'De waarde moet met de volgende expressie overeen komen',
	'option_siren_siret_mode_label' => 'Wat controleer je?',
	'option_siren_siret_mode_siren' => 'SIREN nummer',
	'option_siren_siret_mode_siret' => 'SIRET nummer',
	'option_taille_max_label' => 'Maximum grootte',
	'option_taille_min_label' => 'Minimum grootte',
	'option_url_mode_complet' => 'Volledige verificatie van de URL',
	'option_url_mode_label' => 'URL verificatiemethode',
	'option_url_mode_php_filter' => 'Volledige URL verificatie met het PHP filter FILTER_VALIDATE_URL',
	'option_url_mode_protocole_seul' => 'Uitsluitend verificatie van het protocol',
	'option_url_protocole_label' => 'Naam van het te verifiëren protocol',
	'option_url_type_protocole_exact' => 'Geef hieronder een protocol aan:',
	'option_url_type_protocole_ftp' => 'File transfer protocols: FTP of SFTP',
	'option_url_type_protocole_label' => 'Te verifiëren type protocol',
	'option_url_type_protocole_mail' => 'Mail protocols: IMAP, POP3 of SMTP',
	'option_url_type_protocole_tous' => 'Alle toegelaten protocols',
	'option_url_type_protocole_web' => 'Web protocols: HTTP of HTTPS',

	// T
	'type_code_postal' => 'Postcode',
	'type_code_postal_description' => 'Controleer of de waarde een geldige postcode is.',
	'type_comparaison_champ' => 'Vergelijking',
	'type_comparaison_champ_description' => 'Vergelijk de waarde met een ander veld uit _request().',
	'type_couleur' => 'Kleur',
	'type_couleur_description' => 'Controleer of de waarde een kleurcode is.',
	'type_date' => 'Datum',
	'type_date_description' => 'Controleer of de waarde een datum is in formaat DD/MM/YYYY. Het scheidingsteken kan van alles zijn (".", "/", enz).',
	'type_decimal' => 'Decimaal getal',
	'type_decimal_description' => 'Controleer of de waarde een decimaal getal is, met opties voor een bepaalde reeks en het aaantal decimale plaatsen.',
	'type_email' => 'E-mailadres',
	'type_email_description' => 'Controleer of het e-mailadres juist is geformatteerd.',
	'type_email_disponible' => 'Beschikbaarheid van een e-mailadres',
	'type_email_disponible_description' => 'Controleer of het e-mailadres niet al door een andere systeemgebruiker wordt gebruikt.',
	'type_entier' => 'Geheel getal',
	'type_entier_description' => 'Controleer of de waarde een geheel getal is, met de optie van een bepaalde reeks.',
	'type_id_document' => 'Documentnummer',
	'type_id_document_description' => 'Controleer of de waarde met een bestaand documentnummer overeen komt.',
	'type_isbn' => 'ISBN',
	'type_isbn_description' => 'Controleer of de waarde een 10 of 13 teks lange ISBN code is',
	'type_regex' => 'Regular expression',
	'type_regex_description' => 'Controleer of de waarde met de expressie overeen komt.',
	'type_siren_siret' => 'SIREN or SIRET',
	'type_siren_siret_description' => 'Check that the value is a valid number from the French <a href="http://fr.wikipedia.org/wiki/SIREN">Système d’Identification du Répertoire des ENtreprises</a> (Company Registry ID System).',
	'type_taille' => 'Grootte',
	'type_taille_description' => 'Controleer of de grootte van de waarde binnen een bepaald minimum en/of maximum valt.',
	'type_telephone' => 'Telefoonnumber',
	'type_telephone_description' => 'Controleer of het telefoonnummer met een herkend formaat overeen komt.',
	'type_url' => 'URL',
	'type_url_description' => 'Controleer of de URL een herkend formaat heeft.'
);
