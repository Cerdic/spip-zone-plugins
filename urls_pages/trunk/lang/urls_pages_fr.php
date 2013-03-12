<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_parametrages' => 'URLs personnalisées étendues',

	// L
	'label_rewritebase' => 'RewriteBase',
	'label_liste_pages' => 'Pages et urls correspondantes',
	'label_code_htaccess' => 'Code .htaccess',

	// E
	'explication_formulaire_1' => 'Ce formulaire permet de mettre en place des urls personnalisées pour les squelettes ne correspondant à aucun objet éditorial. Ces urls seront prises en charge par la balise <tt>#URL_PAGE</tt>.',
	'explication_code_htacess' => 'Copiez le code ci-dessous dans le fichier <tt>.htaccess</tt> afin d\'activer la redirection.',
	'explication_rewritebase' => 'Si présente, copier la directive RewriteBase du fichier <tt>.htacess</tt>',
	'explication_ecriture_url' => 'N\'indiquez que la partie figurant après la racine du site :<br>
	<tt>@url_site@/<ins>url personnalisée</ins></tt>',
	'explication_dossier' => 'Dossier contenant les squelettes',
	'erreur_url_non_libre' => 'Cette url est déjà utilisée',
	'erreur_url_doublon' => 'Une url doit être unique',

	// I
	'info_aucun_squelette' => 'Aucun squelette n\'a été trouvé',

	// M
	'message_ok_code' => '. <br>Copiez le code présent en fin du formulaire dans le fichier .htaccess'
);

?>
