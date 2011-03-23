<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_code_postal' => 'Ce code postal est incorrect.', # NEW
	'erreur_date' => 'Fromat des Datums ungültig',
	'erreur_date_format' => 'Le format de la date n\'est pas accepté.', # NEW
	'erreur_decimal' => 'La valeur doit être un nombre décimal.', # NEW
	'erreur_decimal_nb_decimales' => 'Le nombre ne doit pas avoir plus de @nb_decimales@ chiffres après la virgule.', # NEW
	'erreur_email' => 'Die Mailadresse  <em>@email@</em> hat einen Syntaxfehler.',
	'erreur_email_nondispo' => 'Die Mailadresse <em>@email@</em>  wird bereits verwendet.',
	'erreur_entier' => 'Der Wert muß eine ganze Zahl sein.',
	'erreur_entier_entre' => 'Der Wert muß zwischen  @min@ und @max@ liegen.',
	'erreur_entier_max' => 'Der Wert muß kleiner als @max@ sein.',
	'erreur_entier_min' => 'Der Wert muß größer als @min@ sein.',
	'erreur_id_document' => 'Diese Dokumenten-ID ist ungültig',
	'erreur_numerique' => 'Zahlenformat ungültig',
	'erreur_regex' => 'Zeichenkettenformat ungültig',
	'erreur_siren' => 'SIREN Nummer ungültig',
	'erreur_siret' => 'SIRET Nummer ungültig',
	'erreur_taille_egal' => 'Der Wert muß exakt @egal@ Zeichen haben.',
	'erreur_taille_entre' => 'Der Wert muß zwischen @min@ und @max@ Zeichen haben.',
	'erreur_taille_max' => 'Der Wert darf maximal @max@ Zeichen haben.',
	'erreur_taille_min' => 'Der Wert muß mindestens @min@ Zeichen haben.',
	'erreur_telephone' => 'Zahl ungültig',
	'erreur_url' => 'Adresse ungültig', # MODIF
	'erreur_url_protocole' => 'L\'adresse saisie <em>(@url@)</em> doit commencer par @protocole@', # NEW
	'erreur_url_protocole_exact' => 'L\'adresse saisie <em>(@url@)</em> ne commence pas par un protocole valide (http:// par exemple)', # NEW

	// O
	'option_decimal_nb_decimales_label' => 'Nombre de décimales après la virgule', # NEW
	'option_email_disponible_label' => 'Adresse verfügbar',
	'option_email_disponible_label_case' => 'Überprüfen, ob die Adresse bereits verwendet wird.',
	'option_email_mode_5322' => 'Streng standardgemäße Überprüfung',
	'option_email_mode_label' => 'Art der Mailprüfung',
	'option_email_mode_normal' => 'Normale SPIP-Prüfung',
	'option_email_mode_strict' => 'Strengere Prüfung',
	'option_entier_max_label' => 'Maximalwert',
	'option_entier_min_label' => 'Minimalwert',
	'option_regex_modele_label' => 'Der Wert muß mit der folgenden Maske übereinstimmen.',
	'option_siren_siret_mode_label' => 'Was möchten sie überprüfen?',
	'option_siren_siret_mode_siren' => 'SIREN (frz. Unternehmens ID)',
	'option_siren_siret_mode_siret' => 'SIRET (frz. geographische Unternehmens ID)',
	'option_taille_max_label' => 'Maximalgröße',
	'option_taille_min_label' => 'Minimalgröße',
	'option_url_mode_complet' => 'Vérification complète de l\'url', # NEW
	'option_url_mode_label' => 'Mode de vérification des urls', # NEW
	'option_url_mode_php_filter' => 'Vérification complète de l\'url via le filtre FILTER_VALIDATE_URL de php', # NEW
	'option_url_mode_protocole_seul' => 'V&eacute;rification uniquement de la présence d\'un protocole', # NEW
	'option_url_protocole_label' => 'Nom du protocole à vérifier', # NEW
	'option_url_type_protocole_exact' => 'Saisir un protocole ci-dessous :', # NEW
	'option_url_type_protocole_ftp' => 'Protocoles ftp : ftp ou sftp', # NEW
	'option_url_type_protocole_label' => 'Type de protocole à vérifier', # NEW
	'option_url_type_protocole_mail' => 'Protocoles mail : imap, pop3 ou smtp', # NEW
	'option_url_type_protocole_tous' => 'Tous protocoles acceptés', # NEW
	'option_url_type_protocole_web' => 'Protocoles web : http ou https', # NEW

	// T
	'type_date' => 'Datum',
	'type_date_description' => 'Überprüft den Wert auf das Datumsformat  JJ/MM/AAAA. Verschiedene Trenner sind möglich (".", "/", etc).',
	'type_decimal' => 'Nombre décimal', # NEW
	'type_decimal_description' => 'Vérifie que la valeur est un nombre décimal, avec la possibilité de restreindre entre deux valeurs et de préciser le nombre de décimales après la virgule.', # NEW
	'type_email' => 'Mailadresse',
	'type_email_description' => 'Überprüft das Format der Mailadresse',
	'type_email_disponible' => 'Verfügbarkeit einer Mailadresse',
	'type_email_disponible_description' => 'Überprüft ob die Mailadresse bereits von einem anderen Nutzer des System verwendet wird.',
	'type_entier' => 'Ganzzahl', # MODIF
	'type_entier_description' => 'Überprüft ob der Wert eine Ganzzahl ist; bietet die Möglichkeit, einen Bereich zwischen zwei Zahlen anzugeben.',
	'type_regex' => 'Regulärer Ausdruck',
	'type_regex_description' => 'Prüft ob der Wert mit der vorgegebenen Maske übereinstimmt. Zur Verwendung der Masken <a href="http://www.php.net/manual/de/reference.pcre.pattern.syntax.php">lesen sie bitte die PHP Dokumentation</a>.',
	'type_siren_siret' => 'SIREN oder SIRET',
	'type_siren_siret_description' => 'Pr&uuml;ft ob der Wert eine g&uuml;ltige Nummer des <a href="http://fr.wikipedia.org/wiki/SIREN">Syst&egrave;me d’Identification du R&eacute;pertoire des Entreprises</a> ist.',
	'type_taille' => 'Größe',
	'type_taille_description' => 'Überprüft ob der Wert zum geforderten Minimal- oder Maximalwert paßt.',
	'type_telephone' => 'Telefonnummer',
	'type_telephone_description' => 'Prüft ob die Telefonnummer einem bekannten Schema entspricht.',
	'type_url' => 'URL', # NEW
	'type_url_description' => 'Vérifie que l\'url correspond à un schéma reconnu.' # NEW
);

?>
