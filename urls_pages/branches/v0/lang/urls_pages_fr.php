<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_parametrages' => 'URLs pages personnalisées',

	// L
	'label_liste_pages' => 'Pages et urls correspondantes',
	'label_code_htaccess' => 'Code .htaccess',

	// E
	'explication_formulaire_1' => 'Ce formulaire permet de mettre en place des urls personnalisées pour les squelettes ne correspondant à aucun objet éditorial : les pages. Leurs urls seront prises en charge par la balise <tt>#URL_PAGE</tt>.',
	'explication_code_htacess' => 'Après validation du formulaire, copiez le code ci-dessous dans le fichier <tt>.htaccess</tt>.',
	'explication_rewritebase' => 'Si votre fichier <tt>.htaccess</tt> comporte une directive RewriteBase, indiquez la ici.',
	'explication_dossier' => 'Dossier contenant les squelettes',
	'erreur_url_non_libre' => 'Cette url est déjà utilisée',
	'erreur_url_doublon' => 'Une url doit être unique',
	'erreur_pages_obsoletes' => 'Les pages suivantes sont enregistrées en configuration mais ne sont plus actives. Les squelettes correspondants ont du être renommés, supprimés, ou le plugin d\'où ils proviennent désactivé. Il est recommandé de vider ces champs.',

	// I
	'info_aucun_squelette' => 'Aucun squelette n\'a été trouvé',

	// M
	'message_ok_code' => '. <br>N\'oubliez pas de copier le code présent en fin du formulaire dans le fichier .htaccess'
);

?>
