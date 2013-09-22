<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/couteau-kiss/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_cache_taille_mini' => 'Le cache ne peut avoir une taille inférieure à 10Mo',
	'erreur_dossier_squelette_invalide' => 'Le dossier squelette ne peut pas être un chemin absolu ni contenir de référence <tt>../</tt>',
	'explication_dossier_squelettes' => 'Vous pouvez indiquer plusieurs répertoires séparés par ’ :’, qui seront pris dans l’ordre. Le répertoire intitulé "<tt>squelettes</tt>" est toujours pris en dernier si il existe.',
	'explication_image_seuil_document' => 'Les images téléchargées peuvent être automatiquement passées en mode document au dela d’une largeur prédéfinie',
	'explication_introduction_suite' => 'Les points de suite sont ajoutés par la balise <tt>#INTRODUCTION</tt> lorsqu’elle coupe un texte. Par défaut <tt> (...)</tt>',

	// L
	'label_cache_duree' => 'Durée du cache (s)',
	'label_cache_duree_recherche' => 'Durée du cache de la recherche (s)',
	'label_cache_strategie' => 'Stratégie du cache',
	'label_cache_strategie_jamais' => 'Pas de cache (cette option s’annulera au bout de 24h)',
	'label_cache_strategie_normale' => 'Cache a durée limitée',
	'label_cache_strategie_permanent' => 'Cache a durée illimitée',
	'label_cache_taille' => 'Taille du cache (Mo)',
	'label_compacte_head_ecrire' => 'Toujours comprimer CSS et javascript',
	'label_derniere_modif_invalide' => 'Mettre à jour le cache à chaque nouvelle publication',
	'label_docs_seuils' => 'Limiter la taille des documents lors du téléchargement',
	'label_dossier_squelettes' => 'Dossier <tt>squelettes</tt>',
	'label_forcer_lang' => 'Forcer la langue de l’url ou du visiteur (<tt>$forcer_lang</tt>)',
	'label_image_seuil_document' => 'Largeur des images mode document',
	'label_imgs_seuils' => 'Limiter la taille des images lors du téléchargement',
	'label_inhiber_javascript_ecrire' => 'Désactiver le javascript dans les articles',
	'label_introduction_suite' => 'Points de suite',
	'label_logo_seuils' => 'Limiter la taille des logos lors du téléchargement',
	'label_longueur_login_mini' => 'Longueur mini des logins',
	'label_max_height' => 'Hauteur maxi (pixel)',
	'label_max_size' => 'Poids maxi (ko)',
	'label_max_width' => 'Largeur maxi (pixel)',
	'label_nb_objets_tranches' => 'Nombre d’objets dans les listes',
	'label_no_autobr' => 'Désactiver la prise en compte des alinéas (retour-ligne simples) dans le texte',
	'label_no_set_html_base' => 'Pas d’ajout automatique de <tt>&lt;base href="..."&gt;</tt>',
	'label_options_ecrire_perfo' => 'Performance',
	'label_options_ecrire_secu' => 'Sécurité',
	'label_options_skel' => 'Calcul des pages',
	'label_options_typo' => 'Traitements des textes',
	'label_supprimer_numero' => 'Supprimer automatiquement les numéros des titres',
	'label_toujours_paragrapher' => 'Encapsuler tous les paragraphes dans un <tt>&lt;p&gt;</tt> (même les texte constitués d’un seul paragraphe)',
	'legend_cache_controle' => 'Contrôle du cache',
	'legend_espace_prive' => 'Espace privé',
	'legend_image_documents' => 'Images et documents',
	'legend_site_public' => 'Site public',

	// M
	'message_ok' => 'Vos réglages ont été pris en compte et enregistrés dans le fichier <tt>@file@</tt>. Ils sont maintenant appliqués.',

	// T
	'texte_boite_info' => 'Cette page vous permet de configurer facilement les réglages cachés de SPIP.

Si vous forcez certains réglages dans votre fichier <tt>config/mes_options.php</tt>, ce formulaire sera sans effet sur ceux-ci.

Quand vous aurez terminé la configuration de votre site, vous pourrez, si vous le souhaitez, copier-coller le contenu du fichier <tt>tmp/ck_options.php</tt> dans <tt>config/mes_options.php</tt> avant de désinstaller ce plugin qui ne sera plus utile.',
	'titre_page_couteau' => 'Couteau KISS'
);

?>
