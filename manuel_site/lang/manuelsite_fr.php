<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/manuel_site/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'configurer_explication' => 'Ce plugin installe une icône d’aide permettant d’afficher depuis n’importe quelle page de l’espace privé le manuel de rédaction du site. Ce manuel est @texte@. Il a pour but d’expliquer aux rédacteurs l’architecture du site, dans quelle rubrique ranger quoi, comment encoder et installer une vidéo... Bref tout ce que vous voulez et qui est spécifique à votre site.',
	'configurer_explication_l_article' => '<a href="@url@" title="Manuel de redaction">l’article #@idart@</a> de votre site.',
	'configurer_explication_un_article' => 'un article du site.',
	'configurer_titre' => 'Configurer le manuel de rédaction du site',

	// E
	'erreur_article' => 'L’article du manuel défini dans la configuration du plugin est introuvable : #@idart@',
	'erreur_article_publie' => 'L’article du manuel défini dans la configuration du plugin n’est pas publié en ligne : <a href="@url@">#@idart@</a>',
	'erreur_pas_darticle' => 'L’article du manuel n’est pas défini dans la configuration du plugin',
	'explication_afficher_bord_gauche' => 'Afficher l’icone du manuel en haut à gauche (sinon le manuel sera affiché en colonne)',
	'explication_background_color' => 'Entrez la couleur de fond de la zone d’affichage du manuel',
	'explication_cacher_public' => 'Cacher cet article dans l’espace public, flux rss compris',
	'explication_email' => 'Email de contact pour les rédacteurs',
	'explication_faq' => 'Vous trouverez ci-dessous les codes des blocs génériques utilisables pour rédiger votre manuel. Le texte correspondant à chaque code s’affiche (sans mise en forme) au survol de celui-ci. Il suffit de copier/coller le code désiré dans la zone de texte de votre article.<br />Pour ne pas afficher la question, rajouter <i>|q=non</i>.<br />Pour ajouter des paramètres, rajouter <i>|params=p1:v1 ;p2:v2</i>.',
	'explication_id_article' => 'Entrez le numéro de l’article qui contient le manuel',
	'explication_intro' => 'Texte d’introduction au manuel (sera placé avant le chapo)',
	'explication_largeur' => 'Entrez la largeur de la zone d’affichage du manuel',

	// F
	'fermer_le_manuel' => 'Fermer le manuel',

	// H
	'help' => 'Au secours : ',

	// I
	'intro' => 'Ce document a pour but d’aider les rédacteurs à l’utilisation du site. Il vient en complément du document intitulé « [Cours SPIP pour rédacteurs->@url@] » qui est une aide globale à l’utilisation de SPIP. Vous y trouverez une description de l’architecture du site, de l’aide technique sur des points particuliers...',

	// L
	'label_afficher_bord_gauche' => 'Affichage',
	'label_background_color' => 'Couleur de fond',
	'label_cacher_public' => 'Cacher',
	'label_email' => 'Email',
	'label_id_article' => 'N° de l’article',
	'label_intro' => 'Introduction',
	'label_largeur' => 'Largeur',
	'legende_apparence' => 'Apparence',
	'legende_contenu' => 'Contenu',

	// T
	'titre_faq' => 'FAQ du Manuel de rédaction',
	'titre_manuel' => 'Manuel de rédaction du site',
	'titre_menu' => 'Manuel de rédaction du site'
);

?>
