<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// Description des pages
	
	'description_pagedefaut' => 'Les blocs de cette page seront affich&eacute;s par d&eacute;faut pour les blocs o&ugrave; aucune noisette n\'est d&eacute;finie.',
	'description_page_article' => 'C\'est la page utilis&eacute;e pour afficher chaque article.',
	'description_page_rubrique' => 'C\'est la page utilis&eacute;e pour afficher chaque rubrique.',
	'description_page_auteur' => 'C\'est la page utilis&eacute;e pour afficher chaque auteur.',
	'description_page_breve' => 'C\'est la page utilis&eacute;e pour afficher chaque br&egrave;ve.',
	'description_page_mot' => 'C\'est la page utilis&eacute;e pour afficher chaque mot-cl&eacute;.',
	'description_page-forum' => 'Cette page est appel&eacute;e lorsqu\'un visiteur souhaiter poster un message dans un forum.',
	'description_page-plan' => 'Cette page est appel&eacute;e pour afficher le plan du site.',
	'description_page-recherche' => 'Cette page est affich&eacute;e lorsqu\'une recherche est effectu&eacute;e sur le site.',
	'description_page-login' => 'Cette page est affich&eacute;e lorsqu\'un visiteur souhaite se connecter.',
	'description_page-spip_pass' => 'Cette page est affich&eacute;e lorsqu\'un visiteur a oubli&eacute; son mot de passe et souhaite en changer.',
	'description_page-401' => 'Cette page est affich&eacute;e lorsqu\'un visiteur demande &agrave; voir une page pour laquelle il n\'est pas autoris&eacute;.',
	'description_page-404' => 'Cette page est affich&eacute;e lorsqu\'un visiteur demande &agrave; voir une page qui n\'existe pas ou plus.',
	'description_page_site' => 'Cette page est affich&eacute;e pour chaque site web r&eacute;f&eacute;renc&eacute;.',
	
	'nom_page-sommaire' => 'Page d\'accueil du site',
	'nom_pagedefaut' => 'Page par d&eacute;faut',
	'nom_page_article' => 'Article',
	'nom_page_rubrique' => 'Rubrique',
	'nom_page_auteur' => 'Auteur',
	'nom_page_breve' => 'Br&egrave;ve',
	'nom_page_mot' => 'Mot-Cl&eacute;',
	'nom_page-forum' => 'Forum',
	'nom_page-plan' => 'Plan du site',
	'nom_page-recherche' => 'Recherche sur le site',
	'nom_page-login' => 'Se connecter',
	'nom_page-spip_pass' => 'Mot de passe oubli&eacute;',
	'nom_page-401' => 'Erreur 401',
	'nom_page-404' => 'Erreur 404',
	'nom_page_site' => 'Site r&eacute;f&eacute;renc&eacute;',
	
	// Description des noisettes
	
	'description_article-contenuprincipal' => 'Affiche logo, surtitre, titre, sous-titre, date, auteur, traduction, chapeau, texte, lien hypertexte, post-scriptum et notes. Utilisez les param&egrave;tres ci-dessous pour personnaliser les &eacute;l&eacute;ments &agrave; afficher.',
	'description_rubrique-contenuprincipal' => 'Affiche logo, date de dernier ajour et texte. Utilisez les param&egrave;tres ci-dessous pour personnaliser les &eacute;l&eacute;ments &agrave; afficher.',
	'description_documents' => 'Par d&eacute;faut, n\'affiche pas les photos, celles-ci &eacute;tant affich&eacute;es usuellement via un port-folio. Vous pouvez forcer l\'affichage des photos au cas o&ugrave; vous n\'affichez pas de port-folio.',
	'description_article-filariane' => 'Affiche l\'arborescence des rubriques jusqu\'&agrave; l\'article.',
	'description_portfolio' => 'Port-folio de la distribution par d&eacute,faut de SPIP',
	
	'nom_article-contenuprincipal' => 'Contenu principal',
	'nom_rubrique-contenuprincipal' => 'Contenu principal',
	'nom_documents' => 'Documents',
	'nom_filariane' => 'Fil d\'ariane',
	'nom_forum' => 'Forum',
	'nom_petition' => 'P&eacute;tition',
	'nom_portfolio' => 'Port-folio',
	'nom_logositespip' => 'Logo du site SPIP',
	
	'label_afficher_date' => 'Afficher la date de publication&nbsp;?',
	'label_afficher_date_modif' => 'Afficher la date de derni&egrave;re modification&nbsp;?',
	'label_afficher_date_dernier_ajout' => 'Afficher la date de dernier ajout&nbsp;?',
	'label_afficher_auteurs' => 'Afficher les auteurs&nbsp;?',
	'label_afficher_traductions' => 'Afficher les traduction&nbsp;?',
	'label_afficher_lienhypertexte' => 'Afficher le lien hypertexte&nbsp;?',
	'label_afficher_logo' => 'Afficher le logo&nbsp;?',
	'label_utiliser_logo_article_rubrique' => 'Afficher le logo de la rubrique parente si l\'article n\'a pas de logo&nbsp;?',
	'label_exclure_photos' => 'Exclure les photos&nbsp;?',
	'label_afficher_lien_accueil' => 'Lien vers la page d\'accueil&nbsp;?',
	'label_ariane_separateur' => 'S&eacute;parateur&nbsp;:',
	'label_afficher_titre_article' => 'Afficher le titre de l\'article&nbsp;?',
	'label_taille_max_image' => 'Taille maximum de l\'image (en pixels)&nbsp;:',
	'label_afficher_descriptif' => 'Afficher descriptif&nbsp;?',
	'label_afficher_recherche' => 'Afficher le texte recherch&eacute;&nbsp;?',
	'label_inclure_photos_vues' => 'Afficher les photos d&eacute;j&eacute; inclues dans la page&nbsp;?',
	
	// Description du bloc avantcontenu
	'nom_bloc_avantcontenu' => 'Avant le contenu principal',
	'description_bloc_avantcontenu' => 'Sur les pages sensibles (se connecter, mot de passe oubli&eacute;), un contenu par d&eacute;faut est maintenu. Vous pouvez utiliser ce bloc pour ins&eacute;rer des noisettes avant ce contenu par d&eacute;faut.'
	
);

?>