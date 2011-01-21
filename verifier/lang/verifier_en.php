<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_code_postal' => 'This post code is invalid.',
	'erreur_date' => 'The date is invalid.',
	'erreur_date_format' => 'The date format is invalid.',
	'erreur_decimal' => 'The value must be a decimal number.',
	'erreur_decimal_nb_decimales' => 'The number must have more than @nb_decimales@ digits after the decimal point.',
	'erreur_email' => 'The email address <em>@email@</em> is not correctly formatted.',
	'erreur_email_nondispo' => 'The email address <em>@email@</em> has already been used.',
	'erreur_entier' => 'The value must be an integer.',
	'erreur_entier_entre' => 'The value must be between @min@ and @max@.',
	'erreur_entier_max' => 'The value must be less than @max@.',
	'erreur_entier_min' => 'The value must be greater than @min@.',
	'erreur_id_document' => 'This document identifier is not valid.',
	'erreur_numerique' => 'The number format is invalid.',
	'erreur_regex' => 'The regex string is incorrectly formatted.',
	'erreur_siren' => 'The SIREN number is invalid.',
	'erreur_siret' => 'The SIRET number is invalid.',
	'erreur_taille_egal' => 'The value must have exactly @egal@ characters.',
	'erreur_taille_entre' => 'The value must have between @min@ and @max@ characters.',
	'erreur_taille_max' => 'The value must have no more than @max@ characters.',
	'erreur_taille_min' => 'The value must have no less than @min@ characters.',
	'erreur_telephone' => 'The telephone number is invalid.',
	'erreur_url' => 'The URL address is invalid.', # MODIF
	'erreur_url_protocole' => 'L\'adresse saisie <em>(@url@)</em> doit commencer par @protocole@', # NEW
	'erreur_url_protocole_exact' => 'L\'adresse saisie <em>(@url@)</em> ne commence pas par un protocole valide (http:// par exemple)', # NEW

	// O
	'option_decimal_nb_decimales_label' => 'Number of decimal places',
	'option_email_disponible_label' => 'Available address',
	'option_email_disponible_label_case' => 'Check that the address has not already be used by another user',
	'option_email_mode_5322' => 'Check against the strictest standards available',
	'option_email_mode_label' => 'Email checking mode',
	'option_email_mode_normal' => 'Normal SPIP checking',
	'option_email_mode_strict' => 'Less permissive checking',
	'option_entier_max_label' => 'Maximum value',
	'option_entier_min_label' => 'Minimum value',
	'option_regex_modele_label' => 'The value must match the following expression',
	'option_siren_siret_mode_label' => 'Are you sure you wish to confirm?',
	'option_siren_siret_mode_siren' => 'SIREN number',
	'option_siren_siret_mode_siret' => 'SIRET number',
	'option_taille_max_label' => 'Maximum size',
	'option_taille_min_label' => 'Minimum size',
	'option_url_mode_complet' => 'V&eacute;rification compl&egrave;te de l\'url', # NEW
	'option_url_mode_label' => 'Mode de v&eacute;rification des urls', # NEW
	'option_url_mode_php_filter' => 'V&eacute;rification compl&egrave;te de l\'url via le filtre FILTER_VALIDATE_URL de php', # NEW
	'option_url_mode_protocole_seul' => 'V&eacute;rification uniquement de la présence d\'un protocole', # NEW
	'option_url_protocole_label' => 'Nom du protocole &agrave; v&eacute;rifier', # NEW
	'option_url_type_protocole_exact' => 'Saisir un protocole ci-dessous&nbsp;:', # NEW
	'option_url_type_protocole_ftp' => 'Protocoles ftp : ftp ou sftp', # NEW
	'option_url_type_protocole_label' => 'Type de protocole &agrave; v&eacute;rifier', # NEW
	'option_url_type_protocole_mail' => 'Protocoles mail : imap, pop3 ou smtp', # NEW
	'option_url_type_protocole_tous' => 'Tous protocoles accept&eacute;s', # NEW
	'option_url_type_protocole_web' => 'Protocoles web : http ou https', # NEW

	// T
	'type_date' => 'Date',
	'type_date_description' => 'Check that the value is date in the DD/MM/YYYY format. The separator character can be anything (".", "/", etc).',
	'type_decimal' => 'Decimal number',
	'type_decimal_description' => 'Check that the value is a decimal number, with options to restrict the value to a given range and to specify the required number of decmial places.',
	'type_email' => 'Email address',
	'type_email_description' => 'Check that the email address is correctly formatted.',
	'type_email_disponible' => 'Availability of an email address',
	'type_email_disponible_description' => 'Check that the email address has not already been used by another system user.',
	'type_entier' => 'Integer',
	'type_entier_description' => 'Check that the value is an integer, with the option of being restricted between two range values.',
	'type_regex' => 'Regular expression',
	'type_regex_description' => 'Check that the value matches the defined expression. For more information on using regular expressions, please refer to <a href="http://fr2.php.net/manual/en/reference.pcre.pattern.syntax.php">the online PHP help</a>.',
	'type_siren_siret' => 'SIREN or SIRET',
	'type_siren_siret_description' => 'Check that the value is a valid number from the French <a href="http://fr.wikipedia.org/wiki/SIREN">Syst&egrave;me d’Identification du R&eacute;pertoire des ENtreprises</a> (Company Registry ID System).',
	'type_taille' => 'Size',
	'type_taille_description' => 'Check that the size of the value corresponds to the minimum and/or maximum specified.',
	'type_telephone' => 'Telephone number',
	'type_telephone_description' => 'Check that the telephone number matches a recognised telephone number format.',
	'type_url' => 'URL', # NEW
	'type_url_description' => 'V&eacute;rifie que l\'url correspond &agrave; un sch&eacute;ma reconnu.', # NE
);

?>
