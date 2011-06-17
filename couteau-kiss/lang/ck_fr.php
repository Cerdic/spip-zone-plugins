<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/acces_restreint/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'titre_page_couteau' => 'Couteau KISS',
	'texte_boite_info' => 'Cette page vous permet de configurer facilement les r&eacute;glages cach&eacute;s de SPIP.

Si vous forcez certains r&eacute;glages dans votre fichier <tt>config/mes_options.php</tt>, ce formulaire sera sans effet sur ceux-ci.

Quand vous aurez termin&eacute; la configuration de votre site, vous pourrez, si vous le souhaitez, copier-coller le contenu du fichier <tt>tmp/ck_options</tt> dans <tt>config/mes_options.php</tt> avant de d&eacute;sinstaller ce plugin qui ne sera plus utile.',
	

	'legend_cache_controle' => 'Contr&ocirc;le du cache',
	'label_cache_strategie' => 'Strat&eacute;gie du cache',

	'label_cache_strategie_permanent' => 'Cache a durée illimit&eacute;e',
	'label_cache_strategie_jamais' => 'Pas de cache (cette option s\'annulera au bout de 24h)',
	'label_cache_strategie_normale' => 'Cache a dur&eacute;e limit&eacute;e',
	'label_derniere_modif_invalide' => 'Mettre &agrave; jour le cache &agrave; chaque nouvelle publication',
	'label_cache_duree' => 'Dur&eacute;e du cache (s)',
	'label_cache_taille' => 'Taille du cache (Mo)',
	'label_cache_duree_recherche' => 'Dur&eacute;e du cache de la recherche (s)',

	'legend_image_documents' => 'Images et documents',
	'label_image_seuil_document' => 'Largeur des images mode document',
	'explication_image_seuil_document' => 'Les images t&eacute;l&eacute;charg&eacute;es peuvent &ecirc;tre automatiquement pass&eacute;es en mode document au dela d\'une largeur pr&eacute;d&eacute;finie',

	'label_logo_seuils' => 'Limiter la taille des logos lors du t&eacute;l&eacute;chargement',
	'label_max_size' => 'Poids maxi (ko)',
	'label_max_width' => 'Largeur maxi (pixel)',
	'label_max_height' => 'Hauteur maxi (pixel)',
	'label_docs_seuils' => 'Limiter la taille des documents lors du t&eacute;l&eacute;chargement',
	'label_imgs_seuils' => 'Limiter la taille des images lors du t&eacute;l&eacute;chargement',

	'legend_site_public' => 'Site public',
	'label_dossier_squelettes' => 'Dossier <tt>squelettes</tt>',
	'explication_dossier_squelettes' => 'Vous pouvez indiquer plusieurs r&eacute;pertoires s&eacute;par&eacute;s par \':\', qui seront pris dans l\'ordre. Le r&eacute;pertoire intitulé "<tt>squelettes</tt>" est toujours pris en dernier si il existe.',
	'label_options_typo' => 'Traitements des textes',
	'label_supprimer_numero' => 'Supprimer automatiquement les num&eacute;ros des titres',
	'label_toujours_paragrapher' => 'Encapsuler tous les paragraphes dans un <tt>&lt;p&gt;</tt> (m&ecirc;me les texte constitu&eacute;s d\'un seul paragraphe)',
	'label_options_skel' => 'Calcul des pages',
	'label_forcer_lang' => 'Forcer la langue de l\'url ou du visiteur (<tt>$forcer_lang</tt>)',
	'label_no_set_html_base' => 'Pas d\'ajout automatique de <tt>&lt;base href="..."&gt;</tt>',
	'label_introduction_suite' => 'Points de suite',
	'explication_introduction_suite' => 'Les points de suite sont ajout&eacute;s par la balise <tt>#INTRODUCTION</tt> lorsqu\'elle coupe un texte. Par défaut <tt>&amp;nbsp;(...)</tt>',

	'legend_espace_prive' => 'Espace priv&eacute;',
	'label_longueur_login_mini' => 'Longueur mini des&nbsp;logins',
	'label_nb_objets_tranches' => 'Nombre d\'objets dans les listes',
	'label_options_ecrire_perfo' => 'Performance',
	'label_compacte_head_ecrire' => 'Toujours comprimer CSS et javascript',
	'label_options_ecrire_secu' => 'S&eacute;curit&eacute;',
	'label_inhiber_javascript_ecrire' => 'D&eacute;sactiver le javascript dans les articles',

	'erreur_cache_taille_mini' => 'Le cache ne peut avoir une taille inf&eacute;rieure &agrave 10Mo',
	'erreur_dossier_squelette_invalide' => 'Le dossier squelette ne peut pas être un chemin absolu ni contenir de référence <tt>../</tt>',
	'message_ok' => 'Vos r&eacute;glages ont &eacute;t&eacute; pris en compte et enregistr&eacute;s dans le fichier <tt>@file@</tt>. Ils sont maintenant appliqu&eacute;s.',
);

?>