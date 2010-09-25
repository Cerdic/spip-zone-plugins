<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/verifier/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_date' => 'Le format de la date n\'est pas accept&eacute;.',
	'erreur_email' => 'L\'adresse de courriel <em>@email@</em> n\'a pas un format valide.',
	'erreur_email_nondispo' => 'L\'adresse de courriel <em>@email@</em> est d&eacute;j&agrave; utilis&eacute;e.',
	'erreur_entier' => 'La valeur doit &ecirc;tre un entier.',
	'erreur_entier_entre' => 'La valeur doit &ecirc;tre comprise entre @min@ et @max@.',
	'erreur_entier_max' => 'La valeur doit &ecirc;tre inf&eacute;rieure &agrave; @max@.',
	'erreur_entier_min' => 'La valeur doit &ecirc;tre sup&eacute;rieure &agrave; @min@.',
	'erreur_id_document' => 'Cet identifiant de document n\'est pas valide.',
	'erreur_numerique' => 'Le format du nombre n\'est pas valide.',
	'erreur_regex' => 'Le format de la cha&icirc;ne n\'est pas valide.',
	'erreur_siren' => 'Le num&eacute;ro de SIREN n\'est pas valide.',
	'erreur_siret' => 'Le num&eacute;ro de SIRET n\'est pas valide.',
	'erreur_taille_egal' => 'La valeur doit comprendre exactement @egal@ caract&egrave;res.',
	'erreur_taille_entre' => 'La valeur doit comprendre entre @min@ et @max@ caract&egrave;res.',
	'erreur_taille_max' => 'La valeur doit comprendre au maximum @max@ caract&egrave;res.',
	'erreur_taille_min' => 'La valeur doit comprendre au minimum @min@ caract&egrave;res.',
	'erreur_telephone' => 'Le numéro n\'est pas valide.',
	'erreur_url' => 'L\'adresse n\\\'est pas valide.',

	// O
	'option_email_disponible_label' => 'Adresse disponible',
	'option_email_disponible_label_case' => 'V&eacute;rifier que l\'adresse n\'est pas d&eacute;j&agrave; utilis&eacute;e par un utilisateur',
	'option_email_mode_5322' => 'V&eacute;rification la plus conforme aux standards disponibles',
	'option_email_mode_label' => 'Mode de v&eacute;rification des courriels',
	'option_email_mode_normal' => 'V&eacute;rification normale de SPIP',
	'option_email_mode_strict' => 'V&eacute;rification moins permissive',
	'option_entier_max_label' => 'Valeur maximum',
	'option_entier_min_label' => 'Valeur minimum',
	'option_regex_modele_label' => 'La valeur doit correspondre au masque suivant',
	'option_siren_siret_mode_label' => 'Que voulez-vous vérifier ?',
	'option_siren_siret_mode_siren' => 'le SIREN',
	'option_siren_siret_mode_siret' => 'le SIRET',
	'option_taille_max_label' => 'Taille maximum',
	'option_taille_min_label' => 'Taille minimum',

	// T
	'type_date' => 'Date',
	'type_date_description' => 'V&eacute;rifie que la valeur est une date au format JJ/MM/AAAA. Le s&eacute;parateur est libre (&quot;.&quot;, &quot;/&quot;, etc).',
	'type_email' => 'Adresse de courriel',
	'type_email_description' => 'V&eacute;rifie que l\'adresse de courriel a un format correct.',
	'type_email_disponible' => 'Disponibilit&eacute; d\'une adresse de courriel',
	'type_email_disponible_description' => 'V&eacute;rifie que l\'adresse de courriel n\'est pas d&eacute;j&agrave; utilis&eacute; par un autre utilisateur du syst&egrave;me.',
	'type_entier' => 'Entier',
	'type_entier_description' => 'V&eacute;rifie que la valeur est un entier, avec la possibilit&eacute; de restreindre entre deux valeurs.',
	'type_regex' => 'Expression r&eacute;guli&egrave;re',
	'type_regex_description' => 'V&eacute;rifie que la valeur correspond au masque demand&eacute;. Pour l\'utilisation des masques, reportez-vous à <a href="http://fr2.php.net/manual/fr/reference.pcre.pattern.syntax.php">l\'aide en ligne de PHP</a>.',
	'type_siren_siret' => 'SIREN ou SIRET',
	'type_siren_siret_description' => 'V&eacute;rifie que la valeur est un num&eacute;ro valide du <a href="http://fr.wikipedia.org/wiki/SIREN">Syst&egrave;me d&rsquo;Identification du R&eacute;pertoire des ENtreprises</a> fran&ccedil;ais.',
	'type_taille' => 'Taille',
	'type_taille_description' => 'V&eacute;rifie que la taille de la valeur correspond au minimum et/ou au maximum demand&eacute;.',
	'type_telephone' => 'Num&eacute;ro de t&eacute;l&eacute;phone',
	'type_telephone_description' => 'V&eacute;rifie que le num&eacute;ro de t&eacute;l&eacute;phone correspond &agrave; un sch&eacute;ma reconnu.'
);

?>
