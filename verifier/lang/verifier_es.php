<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_code_postal' => 'Este c&oacute;digo postal es incorrecto',
	'erreur_date' => 'La fecha es inv&aacute;lida',
	'erreur_date_format' => 'No se acepta este formato de fecha.',
	'erreur_decimal' => 'La valeur doit &ecirc;tre un nombre d&eacute;cimal.', # NEW
	'erreur_decimal_nb_decimales' => 'Le nombre ne doit pas avoir plus de @nb_decimales@ chiffres apr&egrave;s la virgule.', # NEW
	'erreur_email' => 'La direcci&oacute;n de correo <em>@email@</em> no tiene un formato v&aacute;lido.',
	'erreur_email_nondispo' => 'La direcci&oacute;n de correo <em>@email@</em> ya est&aacute; en uso.',
	'erreur_entier' => 'El valor debe ser un n&uacute;mero entero.',
	'erreur_entier_entre' => 'El valor deber ser entre @min@ y @max@.',
	'erreur_entier_max' => 'El valor debe ser inferior a @max@.',
	'erreur_entier_min' => 'El valor debe ser superior a @min@.',
	'erreur_id_document' => 'Este identificador de documento es inv&aacute;lido.',
	'erreur_numerique' => 'El formato del n&uacute;mero es inv&aacute;lido.',
	'erreur_regex' => 'El formato de la cadena es inv&aacute;lido.',
	'erreur_siren' => 'Este n&uacute;mero de SIREN es inv&aacute;lido.',
	'erreur_siret' => 'El n&uacute;mero de SIRET es inv&aacute;lido.',
	'erreur_taille_egal' => 'El valor debe tener exactamente @egal@ car&aacute;cteres.',
	'erreur_taille_entre' => 'El valor debe tener entre @min@ y @max@ car&aacute;cteres.',
	'erreur_taille_max' => 'El valor debe tener como m&aacute;ximo @max@ car&aacute;cteres.',
	'erreur_taille_min' => 'El valor debe tener al m&iacute;nimo @min@ car&aacute;cteres.',
	'erreur_telephone' => 'El n&uacute;mero es inv&aacute;lido.',
	'erreur_url' => 'La direcci&oacute;n es inv&aacute;lida.',

	// O
	'option_decimal_nb_decimales_label' => 'Nombre de d&eacute;cimales apr&egrave;s la virgule', # NEW
	'option_email_disponible_label' => 'Direcci&oacute;n disponible',
	'option_email_disponible_label_case' => 'Verifique que la direcci&oacute;n no sea usada por otra persona.',
	'option_email_mode_5322' => 'La verificaci&oacute;n m&aacute;s conforme a los est&aacute;ndares existentes',
	'option_email_mode_label' => 'Modo de comprobaci&oacute;n de las direcciones de correo',
	'option_email_mode_normal' => 'Comprobaci&oacute;n normal de SPIP',
	'option_email_mode_strict' => 'Comprobaci&oacute;n no tan permisiva',
	'option_entier_max_label' => 'Valor m&aacute;ximo',
	'option_entier_min_label' => 'Valor m&iacute;nimo',
	'option_regex_modele_label' => 'El valor debe corresponder al patr&oacute;n siguiente',
	'option_siren_siret_mode_label' => '&iquest;Qu&eacute; quiere comprobar?',
	'option_siren_siret_mode_siren' => 'el SIREN',
	'option_siren_siret_mode_siret' => 'el SIRET',
	'option_taille_max_label' => 'Tama&ntilde;o m&aacute;ximo',
	'option_taille_min_label' => 'Tama&ntilde;o m&iacute;nimo',

	// T
	'type_date' => 'Fecha',
	'type_date_description' => 'Comprueba que el valor es una fecha con el formato JJ/MM/AAAA. El separador no importa (".", "/", etc).',
	'type_decimal' => 'Nombre d&eacute;cimal', # NEW
	'type_decimal_description' => 'V&eacute;rifie que la valeur est un nombre d&eacute;cimal, avec la possibilit&eacute; de restreindre entre deux valeurs et de pr&eacute;ciser le nombre de d&eacute;cimales apr&egrave;s la virgule.', # NEW
	'type_email' => 'Direcci&oacute;n de correo electr&oacute;nico',
	'type_email_description' => 'Comprueba que la direcci&oacute;n de correo tiene el formato correcto.',
	'type_email_disponible' => 'Disponibilidad de una direcci&oacute;n de correo',
	'type_email_disponible_description' => 'Comprueba que la direcci&oacute;n de correo no est&aacute; usadapor otro usuario del sistema.',
	'type_entier' => 'N&uacute;mero entero', # MODIF
	'type_entier_description' => 'Comprueba que el valor es un n&uacute;mero entero, con la posibilidad de restringir entre dos valores.',
	'type_regex' => 'Expresi&oacute;n regular',
	'type_regex_description' => 'Comprueba que el valor corresponda al patr&oacute;n indicado. Para el uso de los patrones, referirse a <a href="http://php.net/manual/es/reference.pcre.pattern.syntax.php">la documentaci&oacute;n en linea de PHP</a>.',
	'type_siren_siret' => 'SIREN o SIRET',
	'type_siren_siret_description' => 'Comprueba que el valor es un n&uacute;mero valido del <a href="http://fr.wikipedia.org/wiki/SIREN">Sistema de Identificaci&oacute;n del Repertorio de las Empresas</a> franc&eacute;s.',
	'type_taille' => 'Tama&ntilde;o',
	'type_taille_description' => 'Comprueba que el tama&ntilde;o del valor corresponde al m&iacute;nimo y/o al m&aacute;ximo indicado.',
	'type_telephone' => 'N&uacute;mero de tel&eacute;fono',
	'type_telephone_description' => 'Comprueba que el n&uacute;mero de tel&eacute;fono corresponde a un patr&oacute;n reconocido.'
);

?>
