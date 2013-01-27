<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/verifier?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_code_postal' => 'Este código postal es incorrecto',
	'erreur_comparaison_egal' => 'El valor debe ser igual al campo "@nom_champ@"',
	'erreur_comparaison_egal_type' => 'El valor debe ser igual y del mismo tipo que el campo "@nom_champ@"',
	'erreur_comparaison_grand' => 'El valor debe ser superior al campo "@nom_champ@"',
	'erreur_comparaison_grand_egal' => 'El valor debe ser superior o igual al campo "@nom_champ@"',
	'erreur_comparaison_petit' => 'El valor debe ser inferior al campo "@nom_champ@"',
	'erreur_comparaison_petit_egal' => 'El valor debe ser inferior o igual al campo "@nom_champ@"',
	'erreur_couleur' => 'El código color no es válido.',
	'erreur_date' => 'La fecha no es válida',
	'erreur_date_format' => 'No se acepta este formato de fecha.',
	'erreur_decimal' => 'El valor debe ser un número decimal.',
	'erreur_decimal_nb_decimales' => 'El número no debe tener más de @nb_decimales@ cifras tras la coma.',
	'erreur_email' => 'La dirección de correo electrónico <em>@email@</em> no tiene un formato válido.',
	'erreur_email_nondispo' => 'La dirección de correo electrónico <em>@email@</em> ya está en uso.',
	'erreur_entier' => 'El valor debe ser un número entero.',
	'erreur_entier_entre' => 'El valor deber ser entre @min@ y @max@.',
	'erreur_entier_max' => 'El valor debe ser inferior a @max@.',
	'erreur_entier_min' => 'El valor debe ser superior a @min@.',
	'erreur_heure' => 'El horario indicado no existe.',
	'erreur_heure_format' => 'El formato de la hora no es válido.',
	'erreur_id_document' => 'Este identificador de documento no es válido.',
	'erreur_inconnue_generique' => 'El formato no es correcto.',
	'erreur_isbn' => 'El número ISBN no es válido (ejemplo: 978-2-1234-5680-3 o 2-1234-5680-X)',
	'erreur_isbn_13_X' => 'Un número ISBN-13 no puede terminar por X.',
	'erreur_isbn_G' => 'El primer segmento debe ser igual a 978 o 979.',
	'erreur_isbn_nb_caracteres' => 'El número ISBN debe tener 10 o 13 caracteres, sin contar los guiones (actualmente @nb@).',
	'erreur_isbn_nb_segments' => 'El número ISBN debe tener 4 o 5 segmentos (actualmente @nb@).',
	'erreur_isbn_segment' => 'El segmento "@segment@" tiene @nb@ cifra(s) como mucho.',
	'erreur_isbn_segment_lettre' => 'El segmento "@segment@" no debe contener letras.',
	'erreur_numerique' => 'El formato del número no es válido.',
	'erreur_regex' => 'El formato de la cadena no es válido.',
	'erreur_siren' => 'Este número de SIREN no es válido.',
	'erreur_siret' => 'El número de SIRET no es válido.',
	'erreur_taille_egal' => 'El valor debe tener exactamente @egal@ caracteres (actualmente @nb@).',
	'erreur_taille_entre' => 'El valor debe tener entre @min@ y @max@ caracteres (actualmente @nb@).',
	'erreur_taille_max' => 'El valor debe tener como máximo @max@ caracteres (actualmente @nb@).',
	'erreur_taille_min' => 'El valor debe tener como mínimo @min@ caracteres (actualmente @nb@).',
	'erreur_telephone' => 'El número no es válido.',
	'erreur_url' => 'La dirección <em>@url@</em> no es válida.',
	'erreur_url_protocole' => 'L\'adresse saisie <em>(@url@)</em> doit commencer par @protocole@', # NEW
	'erreur_url_protocole_exact' => 'L\'adresse saisie <em>(@url@)</em> ne commence pas par un protocole valide (http:// par exemple)', # NEW

	// N
	'normaliser_option_date' => 'Normaliser la date ?', # NEW
	'normaliser_option_date_aucune' => 'Non', # NEW
	'normaliser_option_date_en_datetime' => 'Au format «Datetime» (pour SQL)', # NEW

	// O
	'option_couleur_normaliser_label' => 'Normaliser le code couleur ?', # NEW
	'option_couleur_type_hexa' => 'Code couleur au format héxadécimal', # NEW
	'option_couleur_type_label' => 'Type de vérification à effectuer', # NEW
	'option_decimal_nb_decimales_label' => 'Nombre de décimales après la virgule', # NEW
	'option_email_disponible_label' => 'Dirección disponible',
	'option_email_disponible_label_case' => 'Verifique que la dirección no sea usada por otra persona.',
	'option_email_mode_5322' => 'La verificación más conforme a los estándares existentes',
	'option_email_mode_label' => 'Modo de comprobación de las direcciones de correo electrónico',
	'option_email_mode_normal' => 'Comprobación normal de SPIP',
	'option_email_mode_strict' => 'Comprobación no tan permisiva',
	'option_entier_max_label' => 'Valor máximo',
	'option_entier_min_label' => 'Valor mínimo',
	'option_regex_modele_label' => 'El valor debe corresponder al patrón siguiente',
	'option_siren_siret_mode_label' => '¿Qué quiere comprobar?',
	'option_siren_siret_mode_siren' => 'el SIREN',
	'option_siren_siret_mode_siret' => 'el SIRET',
	'option_taille_max_label' => 'Tamaño máximo',
	'option_taille_min_label' => 'Tamaño mínimo',
	'option_url_mode_complet' => 'Vérification complète de l\'url', # NEW
	'option_url_mode_label' => 'Mode de vérification des urls', # NEW
	'option_url_mode_php_filter' => 'Vérification complète de l\'url via le filtre FILTER_VALIDATE_URL de php', # NEW
	'option_url_mode_protocole_seul' => 'Vérification uniquement de la présence d\'un protocole', # NEW
	'option_url_protocole_label' => 'Nom du protocole à vérifier', # NEW
	'option_url_type_protocole_exact' => 'Saisir un protocole ci-dessous :', # NEW
	'option_url_type_protocole_ftp' => 'Protocoles ftp : ftp ou sftp', # NEW
	'option_url_type_protocole_label' => 'Type de protocole à vérifier', # NEW
	'option_url_type_protocole_mail' => 'Protocoles mail : imap, pop3 ou smtp', # NEW
	'option_url_type_protocole_tous' => 'Tous protocoles acceptés', # NEW
	'option_url_type_protocole_web' => 'Protocoles web : http ou https', # NEW

	// T
	'type_couleur' => 'Couleur', # NEW
	'type_couleur_description' => 'Vérifie que la valeur est un code couleur.', # NEW
	'type_date' => 'Fecha',
	'type_date_description' => 'Comprueba que el valor es una fecha con el formato DD/MM/AAAA. El separador no importa (".", "/", etc).',
	'type_decimal' => 'Nombre décimal', # NEW
	'type_decimal_description' => 'Vérifie que la valeur est un nombre décimal, avec la possibilité de restreindre entre deux valeurs et de préciser le nombre de décimales après la virgule.', # NEW
	'type_email' => 'Dirección de correo electrónico',
	'type_email_description' => 'Comprueba que la dirección de correo electrónico tiene el formato correcto.',
	'type_email_disponible' => 'Disponibilidad de una dirección de correo electrónico',
	'type_email_disponible_description' => 'Comprueba que la dirección de correo electrónico no está usada por otro usuario del sistema.',
	'type_entier' => 'Número entero',
	'type_entier_description' => 'Comprueba que el valor es un número entero, con la posibilidad de restringir entre dos valores.',
	'type_regex' => 'Expresión regular',
	'type_regex_description' => 'Comprueba que el valor corresponda al patrón indicado. Para el uso de los patrones, refiérase a <a href="http://php.net/manual/es/reference.pcre.pattern.syntax.php">la documentación en línea de PHP</a>.',
	'type_siren_siret' => 'SIREN o SIRET',
	'type_siren_siret_description' => 'Comprueba que el valor es un número valido del <a href="http://fr.wikipedia.org/wiki/SIREN">Sistema de Identificación del Repertorio de las Empresas</a> francés.',
	'type_taille' => 'Tamaño',
	'type_taille_description' => 'Comprueba que el tamaño del valor corresponde al mínimo y/o al máximo indicado.',
	'type_telephone' => 'Número de teléfono',
	'type_telephone_description' => 'Comprueba que el número de teléfono corresponde a un patrón reconocido.',
	'type_url' => 'URL', # NEW
	'type_url_description' => 'Vérifie que l\'url correspond à un schéma reconnu.' # NEW
);

?>
