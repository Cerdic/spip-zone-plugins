<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_cache_taille_mini' => 'Cache nemôže byť menšia ako 10 MB',
	'erreur_dossier_squelette_invalide' => 'Le dossier squelette ne peut pas être un chemin absolu ni contenir de référence <tt>../</tt>', # NEW
	'explication_dossier_squelettes' => 'Vous pouvez indiquer plusieurs répertoires séparés par \':\', qui seront pris dans l\'ordre. Le répertoire intitulé "<tt>squelettes</tt>" est toujours pris en dernier si il existe.', # NEW
	'explication_image_seuil_document' => 'Les images téléchargées peuvent être automatiquement passées en mode document au dela d\'une largeur prédéfinie', # NEW
	'explication_introduction_suite' => 'Les points de suite sont ajoutés par la balise <tt>#INTRODUCTION</tt> lorsqu\'elle coupe un texte. Par défaut <tt> (...)</tt>', # NEW

	// L
	'label_cache_duree' => 'Trvanie cache (v s)',
	'label_cache_duree_recherche' => 'Trvanie cache vyhľadávania (v s)',
	'label_cache_strategie' => 'Stratégia cache',
	'label_cache_strategie_jamais' => 'Bez cache (táto možnosť bude zrušená po 24 hodinách)',
	'label_cache_strategie_normale' => 'Obmedzené trvanie chache',
	'label_cache_strategie_permanent' => 'Neobmedzené trvanie chache',
	'label_cache_taille' => 'Veľkosť cache (v MB)',
	'label_compacte_head_ecrire' => 'Vždy komprimovať CSS a javascript',
	'label_derniere_modif_invalide' => 'Aktualizovať cache vždy po novom publikovaní',
	'label_docs_seuils' => 'Obmedziť veľkosť dokumentov pri sťahovaní',
	'label_dossier_squelettes' => 'Priečinok <tt>squelettes</tt>',
	'label_forcer_lang' => 'Forcer la langue de l\'url ou du visiteur (<tt>$forcer_lang</tt>)', # NEW
	'label_image_seuil_document' => 'Largeur des images mode document', # NEW
	'label_imgs_seuils' => 'Obmedziť veľkosť obrázkov pri sťahovaní',
	'label_inhiber_javascript_ecrire' => 'Deaktivovať javascript v článkoch',
	'label_introduction_suite' => 'Points de suite', # NEW
	'label_logo_seuils' => 'Obmedziť veľkosť log pri sťahovaní',
	'label_longueur_login_mini' => 'Minimálna dĺžka prihlasovacích mien',
	'label_max_height' => 'Maximálna výška (v pixeloch)',
	'label_max_size' => 'Maximálna veľkosť (kB)',
	'label_max_width' => 'Maximálna šírka (v pixeloch)',
	'label_nb_objets_tranches' => 'Počet objektov v zoznamoch',
	'label_no_set_html_base' => 'Pas d\'ajout automatique de <tt>&lt;base href="..."&gt;</tt>', # NEW
	'label_options_ecrire_perfo' => 'Výkon',
	'label_options_ecrire_secu' => 'Zabezpečenie',
	'label_options_skel' => 'Calcul des pages', # NEW
	'label_options_typo' => 'Traitements des textes', # NEW
	'label_supprimer_numero' => 'Automaticky odstrániť čísla názvov',
	'label_toujours_paragrapher' => 'Encapsuler tous les paragraphes dans un <tt>&lt;p&gt;</tt> (même les texte constitués d\'un seul paragraphe)', # NEW
	'legend_cache_controle' => 'Ovládanie cache',
	'legend_espace_prive' => 'Súkromná stránka',
	'legend_image_documents' => 'Obrázky a dokumenty',
	'legend_site_public' => 'Verejne prístupná stránka',

	// M
	'message_ok' => 'Vos réglages ont été pris en compte et enregistrés dans le fichier <tt>@file@</tt>. Ils sont maintenant appliqués.', # NEW

	// T
	'texte_boite_info' => 'Cette page vous permet de configurer facilement les réglages cachés de SPIP.

Si vous forcez certains réglages dans votre fichier <tt>config/mes_options.php</tt>, ce formulaire sera sans effet sur ceux-ci.

Quand vous aurez terminé la configuration de votre site, vous pourrez, si vous le souhaitez, copier-coller le contenu du fichier <tt>tmp/ck_options</tt> dans <tt>config/mes_options.php</tt> avant de désinstaller ce plugin qui ne sera plus utile.', # NEW
	'titre_page_couteau' => 'Couteau KISS'
);

?>
