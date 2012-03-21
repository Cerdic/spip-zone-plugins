<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/174?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_cache_taille_mini' => 'Le cache ne peut avoir une taille inférieure &agrave 10Mo', # NEW
	'erreur_dossier_squelette_invalide' => 'Le dossier squelette ne peut pas être un chemin absolu ni contenir de référence <tt>../</tt>', # NEW
	'explication_dossier_squelettes' => 'Vous pouvez indiquer plusieurs répertoires séparés par \':\', qui seront pris dans l\'ordre. Le répertoire intitulé "<tt>squelettes</tt>" est toujours pris en dernier si il existe.', # NEW
	'explication_image_seuil_document' => 'Les images téléchargées peuvent être automatiquement passées en mode document au dela d\'une largeur prédéfinie', # NEW
	'explication_introduction_suite' => 'Les points de suite sont ajoutés par la balise <tt>#INTRODUCTION</tt> lorsqu\'elle coupe un texte. Par défaut <tt> (...)</tt>', # NEW

	// L
	'label_cache_duree' => 'Cache duraction (s)',
	'label_cache_duree_recherche' => 'Search cache duration (s)',
	'label_cache_strategie' => 'Cache strategy',
	'label_cache_strategie_jamais' => 'No cache (this option will be canceled after 24 h)',
	'label_cache_strategie_normale' => 'Cache a durée limitée', # NEW
	'label_cache_strategie_permanent' => 'Cache a durée illimitée', # NEW
	'label_cache_taille' => 'Cache size (Mb)',
	'label_compacte_head_ecrire' => 'Always compress CSS and javascript',
	'label_derniere_modif_invalide' => 'Mettre à jour le cache à chaque nouvelle publication', # NEW
	'label_docs_seuils' => 'Limiter la taille des documents lors du téléchargement', # NEW
	'label_dossier_squelettes' => '<tt>squelettes</tt> directory',
	'label_forcer_lang' => 'Forcer la langue de l\'url ou du visiteur (<tt>$forcer_lang</tt>)', # NEW
	'label_image_seuil_document' => 'Images width in document mode',
	'label_imgs_seuils' => 'Limit the size of images during the upload',
	'label_inhiber_javascript_ecrire' => 'Désactiver le javascript dans les articles', # NEW
	'label_introduction_suite' => 'Points de suite', # NEW
	'label_logo_seuils' => 'Limiter la taille des logos lors du téléchargement', # NEW
	'label_longueur_login_mini' => 'Minimal length of logins',
	'label_max_height' => 'Maximal height (pixel)',
	'label_max_size' => 'Maximal size (kb)',
	'label_max_width' => 'Maximale width (pixel)',
	'label_nb_objets_tranches' => 'Number of objects in the lists',
	'label_no_set_html_base' => 'Pas d\'ajout automatique de <tt>&lt;base href="..."&gt;</tt>', # NEW
	'label_options_ecrire_perfo' => 'Performance',
	'label_options_ecrire_secu' => 'Security',
	'label_options_skel' => 'Pages processing',
	'label_options_typo' => 'Traitements des textes', # NEW
	'label_supprimer_numero' => 'Supprimer automatiquement les numéros des titres', # NEW
	'label_toujours_paragrapher' => 'Encapsuler tous les paragraphes dans un <tt>&lt;p&gt;</tt> (même les texte constitués d\'un seul paragraphe)', # NEW
	'legend_cache_controle' => 'Cache control',
	'legend_espace_prive' => 'Private space',
	'legend_image_documents' => 'Images and documents',
	'legend_site_public' => 'Public site ',

	// M
	'message_ok' => 'Vos réglages ont été pris en compte et enregistrés dans le fichier <tt>@file@</tt>. Ils sont maintenant appliqués.', # NEW

	// T
	'texte_boite_info' => 'Cette page vous permet de configurer facilement les réglages cachés de SPIP.

Si vous forcez certains réglages dans votre fichier <tt>config/mes_options.php</tt>, ce formulaire sera sans effet sur ceux-ci.

Quand vous aurez terminé la configuration de votre site, vous pourrez, si vous le souhaitez, copier-coller le contenu du fichier <tt>tmp/ck_options</tt> dans <tt>config/mes_options.php</tt> avant de désinstaller ce plugin qui ne sera plus utile.', # NEW
	'titre_page_couteau' => 'KISS knife'
);

?>
