<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/verifier?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'data_choix' => 'Auswahlmöglichkeiten:',

	// E
	'erreur_code_postal' => 'Ungültige Postleitzahl',
	'erreur_comparaison_egal' => 'Der Wert muss gleich dem Feld "@nom_champ@" sein.',
	'erreur_comparaison_egal_type' => 'Der Wert muss  dem Feld "@nom_champ@" entsprechen und vom gleichen Typ sein.',
	'erreur_comparaison_grand' => 'Der Wert muss größer als das Feld "@nom_champ@" sein.',
	'erreur_comparaison_grand_egal' => 'Der Wert muss größer oder gleich dem Feld "@nom_champ@" sein.',
	'erreur_comparaison_petit' => 'Der Wert muss kleiner als das Feld "@nom_champ@" sein.',
	'erreur_comparaison_petit_egal' => 'Der Wert muss kleiner oder gleich dem Feld "@nom_champ@" sein.',
	'erreur_couleur' => 'Der Farbcode ist ungültig.',
	'erreur_date' => 'Format des Datums ungültig',
	'erreur_date_format' => 'Dieses Datumsformat wird nicht akzeptiert',
	'erreur_decimal' => 'Der Wert muß einen Dezimalzahl sein',
	'erreur_decimal_nb_decimales' => 'Die Zahl darf nicht mehr als @nb_decimales@ Nachkommastellen haben.',
	'erreur_dimension_image' => 'Die Datei « @name@ » ist zu groß: @taille@ (Maximum @taille_max@).',
	'erreur_email' => 'Die Mailadresse  <em>@email@</em> hat einen Syntaxfehler.',
	'erreur_email_nondispo' => 'Die Mailadresse <em>@email@</em>  wird bereits verwendet.',
	'erreur_entier' => 'Der Wert muß eine ganze Zahl sein.',
	'erreur_entier_entre' => 'Der Wert muß zwischen  @min@ und @max@ liegen.',
	'erreur_entier_max' => 'Der Wert muss kleiner oder gleich @max@ sein.',
	'erreur_entier_min' => 'Der Wert muss größer oder gleich @min@ sein.',
	'erreur_heure' => 'Das angegebene Datum existiert nicht.',
	'erreur_heure_format' => 'Ungültiges Zeitformat.',
	'erreur_id_document' => 'Diese Dokumenten-ID ist ungültig',
	'erreur_id_objet' => 'Ungültige Kennung.',
	'erreur_inconnue_generique' => 'Das Format ist ungültig.',
	'erreur_isbn' => 'Die ISBN Nummer ist ungültig (z.B. : 978-2-1234-5680-3  oder 2-1234-5680-X).',
	'erreur_isbn_13_X' => 'Eine ISBN-13 Nummer darf nicht auf X enden.',
	'erreur_isbn_G' => 'Das erste Segment muss 978 oder 979 sein.',
	'erreur_isbn_nb_caracteres' => 'Die ISBN Nummer muss ohne Bindestriche 10 oder 13 Stellen haben (momentan @nb@).',
	'erreur_isbn_nb_segments' => 'Die ISBN muss 4 oder 5 Segmente haben (momentan @nb@).',
	'erreur_isbn_segment' => 'Das Segment "@segment@" hat @nb@ Zeichen zu viel.',
	'erreur_isbn_segment_lettre' => 'Das Segment "@segment@" darf keine Buchstaben enthalten.',
	'erreur_numerique' => 'Zahlenformat ungültig',
	'erreur_objet' => 'Das Objekt ist ungültig.',
	'erreur_php_file_1' => 'Die Datei « @name@ » überschreitet die vom Server erlaubte Größe',
	'erreur_php_file_2' => 'Die Datei « @name@ » überschreitet die vom Formular erlaubte Größe',
	'erreur_php_file_3' => 'Die Datei « @name@ »wurde nur teilweise übertragen',
	'erreur_php_file_6' => 'Ein Serverfehler hat die Übertragung der Datei « @name@ » verhindert',
	'erreur_php_file_7' => 'Ein Serverfehler hat die Übertragung der Datei « @name@ » verhindert',
	'erreur_php_file_88' => 'Ein Serverfehler hat die Übertragung der Datei « @name@ » verhindert',
	'erreur_regex' => 'Zeichenkettenformat ungültig',
	'erreur_siren' => 'SIREN Nummer ungültig',
	'erreur_siret' => 'SIRET Nummer ungültig',
	'erreur_taille_egal' => 'Der Wert muss exakt @egal@ Zeichen haben (momentan @nb@).',
	'erreur_taille_entre' => 'Der Wert muss zwischen @min@ und @max@ Zeichen haben (momentan (@nb@).',
	'erreur_taille_fichier' => 'Die Datei « @name@ » ist zu groß : @taille@ (Maximum @taille_max@).',
	'erreur_taille_max' => 'Der Wert darf maximal @max@ Zeichen haben (momentan @nb@).',
	'erreur_taille_min' => 'Der Wert muss mindestens @min@ Zeichen haben (momentan @nb@).',
	'erreur_telephone' => 'Zahl ungültig',
	'erreur_type_image' => 'Die Datei « @name@ » ist kein für das Internet geeignetes Bild.',
	'erreur_type_non_autorise' => 'Die Datei « @name@ » ist kein erlaubte Dateityp.',
	'erreur_url' => 'Die Adresse <em>@url@</em> ist ungültig.',
	'erreur_url_protocole' => 'Die eingegebene Adresse <em>(@url@)</em> muss mit @protocole@ beginnen.',
	'erreur_url_protocole_exact' => 'Die eingegebene Adresse <em>(@url@)</em> beginnt nicht mit einem gültigen Protokoll (zum Beispiel http:// ).',

	// N
	'normaliser_option_date' => 'Das Datum normalisieren?',
	'normaliser_option_date_aucune' => 'Nein',
	'normaliser_option_date_en_datetime' => '«Datetime» Format (für SQL)',

	// O
	'option_code_postal_pays_explication' => '2-stellige Länderkennung: FR, DZ, DE, etc.',
	'option_code_postal_pays_label' => 'Land',
	'option_comparaison_champ_champ_explication' => 'Feldkennung (Attribut « name »)',
	'option_comparaison_champ_champ_label' => 'Feld',
	'option_comparaison_champ_comparaison_explication' => 'Vergleichsart',
	'option_comparaison_champ_comparaison_label' => 'Vergleich',
	'option_comparaison_champ_egal' => '== gleich',
	'option_comparaison_champ_egal_type' => '=== identisch (gleicher Typ)',
	'option_comparaison_champ_grand' => '> größer als',
	'option_comparaison_champ_grand_egal' => '>= größer gleich',
	'option_comparaison_champ_nom_champ_explication' => 'Feldname für Menschen',
	'option_comparaison_champ_nom_champ_label' => 'Feldname',
	'option_comparaison_champ_petit' => '< kleiner als',
	'option_comparaison_champ_petit_egal' => '<= kleiner gleich',
	'option_couleur_normaliser_label' => 'Farbcode nomralisieren?',
	'option_couleur_type_hexa' => 'Hexadezimaler Farbcode',
	'option_couleur_type_label' => 'Vorzunehmende Überprüfung',
	'option_decimal_nb_decimales_label' => 'Dezimalstellen nach dem Komma', # MODIF
	'option_decimal_separateur_explication' => 'Der Punkt wird auf jeden Fall akzeptiert.',
	'option_decimal_separateur_label' => 'Dezimaltrenner',
	'option_email_disponible_label' => 'Adresse verfügbar',
	'option_email_disponible_label_case' => 'Überprüfen, ob die Adresse bereits verwendet wird.',
	'option_email_mode_5322' => 'Streng standardgemäße Überprüfung',
	'option_email_mode_label' => 'Art der Mailprüfung',
	'option_email_mode_normal' => 'Normale SPIP-Prüfung',
	'option_email_mode_strict' => 'Strengere Prüfung',
	'option_entier_max_label' => 'Maximalwert',
	'option_entier_min_label' => 'Minimalwert',
	'option_fichiers_dimension_autoriser_rotation_label' => 'Soll das Bild rotiert werden?',
	'option_fichiers_dimension_autoriser_rotation_label_case' => 'Klicken um maximale Breite und Höhe zu tauschen.',
	'option_fichiers_hauteur_max_label' => 'Maximale Bildhöhe (in px)',
	'option_fichiers_largeur_max_label' => 'Maximale Bildgröße (in px)',
	'option_fichiers_mime_image_web_label' => 'Nur Web-Bilder akzeptieren (gif, jpg, png)',
	'option_fichiers_mime_label' => 'Mime Type und Dateiendung',
	'option_fichiers_mime_pas_de_verification_label' => 'Alle Typen und Endungen akzeptieren (nicht empfohlen)',
	'option_fichiers_mime_specifique_label' => 'Nur die unten ausgewählten Typen und Endungen akzeptieren',
	'option_fichiers_mime_tout_mime_label' => 'Alle von SPIP erkannten MIME Types und Endungen akzeptieren',
	'option_fichiers_taille_max_label' => 'Maximale Dateigröße (in kB)',
	'option_fichiers_type_mime_label' => 'Akzeptierte MIME Types auswählen',
	'option_id_objet_objet_label' => 'Objektname (einzigartig)',
	'option_regex_modele_label' => 'Der Wert muß mit der folgenden Maske übereinstimmen.',
	'option_siren_siret_mode_label' => 'Was möchten sie überprüfen?',
	'option_siren_siret_mode_siren' => 'SIREN (frz. Unternehmens ID)',
	'option_siren_siret_mode_siret' => 'SIRET (frz. geographische Unternehmens ID)',
	'option_taille_max_label' => 'Maximalgröße',
	'option_taille_min_label' => 'Minimalgröße',
	'option_url_mode_complet' => 'Vollständige Prüfung des URL',
	'option_url_mode_label' => 'Art der URL-Prüfung',
	'option_url_mode_php_filter' => 'Vollständige Prüfung des URL mit dem PHP-Filter FILTER_VALIDATE_URL',
	'option_url_mode_protocole_seul' => 'Nur die Angabe eines Protokolls prüfen',
	'option_url_protocole_label' => 'Names des überprüften Protokolls',
	'option_url_type_protocole_exact' => 'Geben Sie hier ein Protokoll an:',
	'option_url_type_protocole_ftp' => 'FTP-Protokolle: ftp oder sftp',
	'option_url_type_protocole_label' => 'Typ des erforderlichen Protokolls',
	'option_url_type_protocole_mail' => 'Mail-Protokolle: imap, pop3 oder smtp',
	'option_url_type_protocole_tous' => 'Alle Protokolle werden akzeptiert',
	'option_url_type_protocole_web' => 'Web-Protokolle: http oder https',
	'option_url_type_protocole_webcal' => 'Webcal Protokolle: webcal, http ou https',

	// P
	'par_defaut' => 'Standardwert:',
	'plugin_yaml_inactif' => 'Das Plugin YAML ist nicht vorhanden und/oder aktiviert. Es ist notwendig, um diese Dokumentation anzuzeigen.',

	// T
	'titre_page_verifier_doc' => 'Dokumentation der  Prüf-API',
	'type_code_postal' => 'Postleitzahl',
	'type_code_postal_description' => 'Überprüfen ob es sich um eine gültige Postleitzahl handelt',
	'type_comparaison_champ' => 'Vergleich',
	'type_comparaison_champ_description' => 'Den Wert mit einem anderen Wert von _request() vergleichen.',
	'type_couleur' => 'Farbe',
	'type_couleur_description' => 'Überprüfen ob es sich um einen Farbcode handelt.',
	'type_date' => 'Datum',
	'type_date_description' => 'Überprüft den Wert auf das Datumsformat  JJ/MM/AAAA. Verschiedene Trenner sind möglich (".", "/", etc).',
	'type_decimal' => 'Dezimalzahl',
	'type_decimal_description' => 'Prüft ob der Wert eine Dezimalzahl ist und ermöglicht, einen Wertebereich und die Anzahl der Nachkommastellen festzulegen.',
	'type_email' => 'Mailadresse',
	'type_email_description' => 'Überprüft das Format der Mailadresse',
	'type_email_disponible' => 'Verfügbarkeit einer Mailadresse',
	'type_email_disponible_description' => 'Überprüft ob die Mailadresse bereits von einem anderen Nutzer des System verwendet wird.',
	'type_entier' => 'Ganzzahl',
	'type_entier_description' => 'Überprüft ob der Wert eine Ganzzahl ist; bietet die Möglichkeit, einen Bereich zwischen zwei Zahlen anzugeben.',
	'type_fichiers' => 'Dateieigenschaften',
	'type_fichiers_description' => 'Bestimmte Eigenschaften jeder geladenen Datei prüfen',
	'type_id_document' => 'Dokumentennummer',
	'type_id_document_description' => 'Prüfen ob der Wert einer existierenden Dokumentnummer entspricht.',
	'type_id_objet' => 'Objektnummer',
	'type_id_objet_description' => 'Prüfen ob der Wert einer existierenden Objektnummer entspricht.',
	'type_isbn' => 'ISBN Nummer',
	'type_isbn_description' => 'Prüfen ob der Wert eine 10 oder 13stellige  ISBN Nummer ist.',
	'type_regex' => 'Regulärer Ausdruck',
	'type_regex_description' => 'Prüft ob der Wert mit der vorgegebenen Maske übereinstimmt. Zur Verwendung der Masken <a href="http://www.php.net/manual/de/reference.pcre.pattern.syntax.php">lesen sie bitte die PHP Dokumentation</a>.',
	'type_siren_siret' => 'SIREN oder SIRET',
	'type_siren_siret_description' => 'Prüft ob der Wert eine gültige Nummer des <a href="http://fr.wikipedia.org/wiki/SIREN">Système d’Identification du Répertoire des Entreprises</a> ist.',
	'type_taille' => 'Größe',
	'type_taille_description' => 'Überprüft ob der Wert zum geforderten Minimal- oder Maximalwert paßt.',
	'type_telephone' => 'Telefonnummer',
	'type_telephone_description' => 'Prüft ob die Telefonnummer einem bekannten Schema entspricht.',
	'type_url' => 'URL',
	'type_url_description' => 'Prüft ob der URL einem anerkannten Schema entspricht.',

	// V
	'verification_a_faire' => 'Vorzunehmende Prüfung:'
);
