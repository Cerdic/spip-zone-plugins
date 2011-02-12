<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_code_postal' => 'Ce code postal est incorrect.', # NEW
	'erreur_date' => 'El format de la data no &eacute;s correcte.', # MODIF
	'erreur_date_format' => 'Le format de la date n\'est pas accept&eacute;.', # NEW
	'erreur_decimal' => 'La valeur doit &ecirc;tre un nombre d&eacute;cimal.', # NEW
	'erreur_decimal_nb_decimales' => 'Le nombre ne doit pas avoir plus de @nb_decimales@ chiffres apr&egrave;s la virgule.', # NEW
	'erreur_email' => 'L\'adre&ccedil;a de correu electr&ograve;nic <em>@email@</em> no t&eacute; un format v&agrave;lid.',
	'erreur_email_nondispo' => 'L\'adre&ccedil;a de correu electr&ograve;nic <em>@email@</em> ja s\'utilitza.',
	'erreur_entier' => 'El valor ha de ser un nombre enter.',
	'erreur_entier_entre' => 'El valor ha d\'estar compr&egrave;s entre @min@ i @max@.',
	'erreur_entier_max' => 'El valor ha de ser inferior a @max@.',
	'erreur_entier_min' => 'El valor ha de ser superior a @min@.',
	'erreur_id_document' => 'Aquest identificador de document no &eacute;s v&agrave;lid.',
	'erreur_numerique' => 'El format del n&uacute;mero no &eacute;s v&agrave;lid.',
	'erreur_regex' => 'El format de la cadena no &eacute;s v&agrave;lida.',
	'erreur_siren' => 'N&uacute;mero SIREN no v&agrave;lid.',
	'erreur_siret' => 'N&uacute;mero SIRET no v&agrave;lid.',
	'erreur_taille_egal' => 'El valor ha de tenir exactament @egal@ car&agrave;cters.',
	'erreur_taille_entre' => 'El valor ha d\'estar compr&egrave;s entre @min@ i @max@ car&agrave;cters.',
	'erreur_taille_max' => 'El valor ha de comprendre com a molt @max@ car&agrave;cters.',
	'erreur_taille_min' => 'El valor ha de tenir com a m&iacute;nim @min@ car&agrave;cters.',
	'erreur_telephone' => 'El n&uacute;mero no &eacute;s v&agrave;lid.',
	'erreur_url' => 'L\'adre&ccedil;a no &eacute;s v&agrave;lida.', # MODIF
	'erreur_url_protocole' => 'L\'adresse saisie <em>(@url@)</em> doit commencer par @protocole@', # NEW
	'erreur_url_protocole_exact' => 'L\'adresse saisie <em>(@url@)</em> ne commence pas par un protocole valide (http:// par exemple)', # NEW

	// O
	'option_decimal_nb_decimales_label' => 'Nombre de d&eacute;cimales apr&egrave;s la virgule', # NEW
	'option_email_disponible_label' => 'Adre&ccedil;a disponible',
	'option_email_disponible_label_case' => 'Verificar que l\'adre&ccedil;a no l\'utilitzi ja un altre usuari',
	'option_email_mode_5322' => 'Verificaci&oacute; la m&eacute;s compatible amb els est&agrave;ndards disponibles ',
	'option_email_mode_label' => 'Mitj&agrave; de verificaci&oacute; dels correus electr&ograve;nics',
	'option_email_mode_normal' => 'Verificaci&oacute; normal d\'SPIP',
	'option_email_mode_strict' => 'Verificaci&oacute; menys permissiva',
	'option_entier_max_label' => 'Valor m&agrave;xim',
	'option_entier_min_label' => 'Valor m&iacute;nim',
	'option_regex_modele_label' => 'El valor ha de coincidir amb la m&agrave;scara de la seg&uuml;ent',
	'option_siren_siret_mode_label' => 'Qu&egrave; voleu verificar?',
	'option_siren_siret_mode_siren' => 'el SIREN',
	'option_siren_siret_mode_siret' => 'el SIRET',
	'option_taille_max_label' => 'Mida m&agrave;xima',
	'option_taille_min_label' => 'Mida m&iacute;nima',
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
	'type_date' => 'Data',
	'type_date_description' => 'Verifica que el valor &eacute;s una data en format JJ/MM/AAAA. El separador &eacute;s lliure (".", "/", etc.).',
	'type_decimal' => 'Nombre d&eacute;cimal', # NEW
	'type_decimal_description' => 'V&eacute;rifie que la valeur est un nombre d&eacute;cimal, avec la possibilit&eacute; de restreindre entre deux valeurs et de pr&eacute;ciser le nombre de d&eacute;cimales apr&egrave;s la virgule.', # NEW
	'type_email' => 'Adre&ccedil;a de correu electr&ograve;nic',
	'type_email_description' => 'Verifica que el format de l\'adre&ccedil;a de correu electr&ograve;nica sigui correcte.',
	'type_email_disponible' => 'Disponibilitat d\'una adre&ccedil;a de correu electr&ograve;nic',
	'type_email_disponible_description' => 'Verifica que l\'adre&ccedil;a de correu electr&ograve;nica no sigui utilitzada ja per un altre usuari del sistema.',
	'type_entier' => 'N&uacute;mero enter', # MODIF
	'type_entier_description' => 'Verifica que el valor sigui un n&uacute;mero enter, amb la possibilitat de restringir entre dos valors.',
	'type_regex' => 'Expressi&oacute; regular ',
	'type_regex_description' => 'Verifica que el valor correspon a la m&agrave;scara demanada. Per l\'&uacute;s de m&agrave;scares, aneu a <a href="http://fr2.php.net/manual/fr/reference.pcre.pattern.syntax.php">l\'ajuda en l&iacute;nia de PHP</a>.',
	'type_siren_siret' => 'SIREN o SIRET',
	'type_siren_siret_description' => 'Verifica queel valor &eacute;s un n&uacute;mero v&agrave;lid del <a href="http://fr.wikipedia.org/wiki/SIREN">Syst&egrave;me d’Identification du R&eacute;pertoire des ENtreprises</a> franc&egrave;s.',
	'type_taille' => 'Mida',
	'type_taille_description' => 'Verifica que la mida del valor correspon al m&iacute;nim i/o al m&agrave;xim demanat.',
	'type_telephone' => 'N&uacute;mero de tel&egrave;fon',
	'type_telephone_description' => 'Verifica que el n&uacute;mero de tel&egrave;fon correspon a un esquema reconegut.',
	'type_url' => 'URL', # NEW
	'type_url_description' => 'V&eacute;rifie que l\'url correspond &agrave; un sch&eacute;ma reconnu.' # NEW
);

?>
