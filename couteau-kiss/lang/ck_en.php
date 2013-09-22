<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/174?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_cache_taille_mini' => 'The cache can’t be smaller than 10MB',
	'erreur_dossier_squelette_invalide' => 'The skeleton folder can’t be an absolute path or contain reference <tt>../</tt>',
	'explication_dossier_squelettes' => 'You can specify multiple directories separated by ’:’, they will be taken in order. The directory named"<tt>squelettes</tt>" is always taken in the last position if it exists.',
	'explication_image_seuil_document' => 'Uploaded images can be automatically placed in the document mode beyond a predefined width',
	'explication_introduction_suite' => 'Dot leaders are added by the tag <tt>#INTRODUCTION</tt> when cutting a text. Default <tt> (...)</tt>',

	// L
	'label_cache_duree' => 'Cache duraction (s)',
	'label_cache_duree_recherche' => 'Search cache duration (s)',
	'label_cache_strategie' => 'Cache strategy',
	'label_cache_strategie_jamais' => 'No cache (this option will be canceled after 24 h)',
	'label_cache_strategie_normale' => 'Limited duration cache',
	'label_cache_strategie_permanent' => 'Unlimited duration cache',
	'label_cache_taille' => 'Cache size (Mb)',
	'label_compacte_head_ecrire' => 'Always compress CSS and javascript',
	'label_derniere_modif_invalide' => 'Update the cache for each new publication',
	'label_docs_seuils' => 'Limit the documents size when uploading',
	'label_dossier_squelettes' => '<tt>squelettes</tt> directory',
	'label_forcer_lang' => 'Force the language of the url or the visitor (<tt>$forcer_lang</tt>)',
	'label_image_seuil_document' => 'Images width in document mode',
	'label_imgs_seuils' => 'Limit the size of images during the upload',
	'label_inhiber_javascript_ecrire' => 'Disable javascript in articles',
	'label_introduction_suite' => 'Dot leaders',
	'label_logo_seuils' => 'Limit the logos size when uploading',
	'label_longueur_login_mini' => 'Minimal length of logins',
	'label_max_height' => 'Maximal height (pixel)',
	'label_max_size' => 'Maximal size (kb)',
	'label_max_width' => 'Maximale width (pixel)',
	'label_nb_objets_tranches' => 'Number of objects in the lists',
	'label_no_autobr' => 'Disable consideration break lines (single newline) in the text',
	'label_no_set_html_base' => 'No automatic addition of <tt>&lt;base href="..."&gt;</tt>',
	'label_options_ecrire_perfo' => 'Performance',
	'label_options_ecrire_secu' => 'Security',
	'label_options_skel' => 'Pages processing',
	'label_options_typo' => 'Texts processing',
	'label_supprimer_numero' => 'Automatically delete the numbers of titles',
	'label_toujours_paragrapher' => 'Wrap all paragraphs in a <tt><p></tt> (even the texts consisting of a single paragraph)',
	'legend_cache_controle' => 'Cache control',
	'legend_espace_prive' => 'Private space',
	'legend_image_documents' => 'Images and documents',
	'legend_site_public' => 'Public site ',

	// M
	'message_ok' => 'Your settings have been taken into account and stored in the <tt>@file@</tt> file. They are now applied.',

	// T
	'texte_boite_info' => 'This page allows you to easily configure the hidden settings of SPIP.

If you force some settings in your <tt>config/mes_options.php</tt> file, this form will not affect them.

When you finish configuring your website, you can, if you wish, copy and paste the content of <tt>tmp/ck_options</tt> into <tt>config/mes_options.php</tt> before uninstalling this plugin as it will no longer be useful.',
	'titre_page_couteau' => 'KISS knife'
);

?>
